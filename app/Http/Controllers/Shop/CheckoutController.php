<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\StockItemHoldings;
use App\Services\LocationAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Helpers\PricingHelper;
use App\Mail\OrderConfirmationMail;
use App\Mail\FulfillmentNotificationMail;
use App\Notifications\NewOrderNotification;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {
        // Get cart from session
        $cart = Session::get('cart', []);


        if (empty($cart)) {
            return redirect()->route('shop.cart.show')->with('error', 'Your cart is empty');
        }
        $user = Auth::user();
        $customer = $user->customer;
        $currentCompany = $user->currentCompany();

        if (!$customer) {
            return redirect()->route('shop.home')->with('error', 'Customer profile not found');
        }

        // Calculate cart totals with customer pricing
        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) continue;

            $pricing = PricingHelper::getProductPricing($product);
            $lineTotal = $pricing['price'] * $item['quantity'];

            $cartItems[] = [
                'product' => $product,
                'quantity' => $item['quantity'],
                'pricing' => $pricing,
                'line_total' => $lineTotal,
            ];

            $subtotal += $lineTotal;
        }

        // VAT calculation
        // TODO: link to VAT table
        $vatRate = 0.15;
        $vatAmount = $subtotal * $vatRate;
        //$total = $subtotal + $vatAmount;
        $total = $subtotal;

        // Check credit limit and hold status
        $creditInfo = $this->checkCreditLimit($customer, $total);

        return view('shop.checkout.index', compact(
            'cartItems',
            'customer',
            'currentCompany',
            'subtotal',
            'vatAmount',
            'vatRate',
            'total',
            'creditInfo'
        ));
    }

        /**
         * Process the checkout
         */
    public function process(Request $request)
    {
        $user = Auth::user();
        $customer = $user->customer;
        $currentCompany = $user->currentCompany();

        // TODO: extract validation to ProcessCartRequest
        $rules = [
            'delivery_method' => 'required|in:delivery,collection',
            'customer_po_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'terms_accepted' => 'accepted'
        ];

        if ($request->fulfillment_method === 'delivery' &&
            !CompanySetting::getSetting('ecommerce_delivery_enabled', $currentCompany->id)) {
            return back()->withErrors(['fulfillment_method' => 'Delivery is currently not available.']);
        }

        // delivery specific validation
        if($request->delivery_method === 'delivery') {
            $rules = array_merge($rules, [
                'delivery_address_line1' => 'required|string|max:255',
                'delivery_city' => 'required|string|max:100',
                'delivery_postal_code' => 'required|string|max:20',
                'preferred_delivery_date' => 'nullable|date|after:today'
            ]);
        }

        $request->validate($rules);

        // Get cart from session
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.cart.show')->with('error', 'Your cart is empty');
        }

        $user = Auth::user();
        $customer = $user->customer;
        $currentCompany = $user->currentCompany();

        // Get or create default e-commerce salesperson
        $ecommerceSalesperson = $this->getEcommerceSalesperson();

        // Calculate totals
        $cartItems = [];
        $total = 0;

        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) continue;

            $pricing = PricingHelper::getProductPricing($product);
            $lineTotal = $pricing['price'] * $item['quantity'];

            $cartItems[] = [
                'product' => $product,
                'quantity' => $item['quantity'],
                'pricing' => $pricing,
                'line_total' => $lineTotal
            ];

            $total += $lineTotal;
        }

        foreach ($cartItems as $item) {
            $assignedLocation = LocationAssignmentService::assignLocation(
                $item['product']->StockCode,
                $item['quantity'],
                null
            );

            $availableStock = StockItemHoldings::getQuantityAtLocation(
                $item['product']->StockCode,
                $assignedLocation
            );

            if ($availableStock < $item['quantity']) {
                return back()->withErrors([
                    'stock' => "Sorry, {$item['product']->StockItemName} is no longer available in the requested quantity. Available: {$availableStock}, Requested: {$item['quantity']}. Please update your cart."
                ])->withInput();
            }
        }

        // VAT calculation
        $vatRate = 0.15;
        $subtotal = $total / (1 + $vatRate);
        $vatAmount = $total - $subtotal;
        //$total = $subtotal + $vatAmount;

        // Check credit limit
        $creditInfo = $this->checkCreditLimit($customer, $total);

        $orderStatus = 1;
        $orderComments = $request->notes ?? '';

        if ($creditInfo['on_hold'] || $creditInfo['over_limit']) {
            $orderStatus = 5; // Set to "On Hold" status
            $creditMessage = $creditInfo['on_hold'] ? 'Customer on credit hold. ' : '';
            $creditMessage .= $creditInfo['over_limit'] ? "Order exceeds credit limit by " . PricingHelper::formatPrice($creditInfo['over_amount']) : '';
            $orderComments = trim($creditMessage . ' ' . $orderComments);
        }

        try {

            // Convert amounts to cents for database storage
            $subtotalCents = round($subtotal * 100);
            $totalCents = round($total * 100);

            // Create the sales order
            $order = Order::create([
                'OrderDate' => now(),
                'OrderNumber' => Order::getNextOrderNumber(),
                'CustomerPurchaseOrderNumber' => $request->customer_po_number,
                'CustomerID' => $customer->acc_code,
                'company_id' => $currentCompany->id,
                'SalesPersonID' => $ecommerceSalesperson->id,
                'LastEditedBy' => Auth::id(),
                'OrderStatusID' => $orderStatus, // New
                'Authorisation' => 0,
                'sub_total' => $subtotalCents,
                'discount_type' => 'percent',
                'discount_val' => 0,
                'total' => $totalCents,
                'Comments' => $orderComments,
                'InternalComments' => $this->buildInternalComments($request, $user, $creditInfo),
                'tax_per_item' => false,
                'discount_per_item' => false,
                'delivery_method' => $request->delivery_method,
                'preferred_delivery_date' => $request->preferred_delivery_date,
            ]);

            // Add order items
            foreach ($cartItems as $item) {

                $unitPriceCents = round($item['pricing']['price'] * 100);
                $lineTotalCents = round($item['line_total'] * 100);

                // Assign location for this item
                $assignedLocation = \App\Services\LocationAssignmentService::assignLocation(
                    $item['product']->StockCode,
                    $item['quantity'],
                    null // TODO: Could pass customer's preferred location if available
                );

                $orderItem = $order->items()->create([
                    'OrderID' => $order->OrderNumber,
                    'company_id' => $currentCompany->id,
                    'StockItem' => $item['product']->StockCode,
                    'LocationCode' => $assignedLocation,
                    'discount_type' => 'percent',
                    'discount_val' => 0,
                    'Quantity' => $item['quantity'],
                    'UnitPrice' => $unitPriceCents,
                    'total' => $lineTotalCents,
                    'LastEditedBy' => Auth::id(),
                    'ContractDiscount' => 0,
                ]);

                // Reduce stock at the assigned location
                try {
                    StockItemHoldings::reduceStock(
                        $item['product']->StockCode,
                        $assignedLocation,
                        $item['quantity'],
                        Auth::id(),
                        'Order',
                        $order->id,
                        "E-commerce order #{$order->OrderNumber}"
                    );
                } catch (\Exception $e) {
                    \Log::error('Stock reduction failed', [
                        'order_id' => $order->id,
                        'order_item_id' => $orderItem->id,
                        'stock_code' => $item['product']->StockCode,
                        'location' => $assignedLocation,
                        'error' => $e->getMessage()
                    ]);

                    throw $e;
                }
            }

            // Update customer delivery address if provided and requested
            if ($request->delivery_method === 'delivery' && $request->update_delivery_address) {
                $customer->update([
                    'DeliveryAddressLine1' => $request->delivery_address_line1,
                    'DeliveryAddressLine2' => $request->delivery_address_line2,
                    'DeliveryCity' => $request->delivery_city,
                    'DeliveryPostalCode' => $request->delivery_postal_code,
                ]);
            }

            // Send notifications (only if not on hold)
            if ($orderStatus == 1) {
                $this->sendOrderNotifications($order, $currentCompany);
            }

            // Clear the cart
            Session::forget('cart');
            Session::forget('cart_location');
            if (Auth::check()) {
                \App\Models\UserCart::where('user_id', Auth::id())->delete();
            }
            Session::put('order_just_completed', true);

            /*\Log::info('Order completed with location assignments', [
                'user_id' => Auth::id(),
                'order_id' => $order->id,
                'items_count' => $order->items->count()
            ]);*/

            // Redirect to success page with appropriate message
            $successMessage = 'Your order has been placed successfully!';
            if ($creditInfo['on_hold'] || $creditInfo['over_limit']) {
                $successMessage = 'Your order has been received and is pending credit approval.';
            }
            return redirect()->route('shop.checkout.success', $order->id)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            \Log::error('Checkout error: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'There was an error processing your order. Please try again.');
        }
    }

    /**
     * Show order success page
     */
    public function success($orderId)
    {
        $user = Auth::user();
        $currentCompany = $user->currentCompany();

        $order = Order::with(['customer', 'items.product', 'salesperson'])
            ->where('id', $orderId)
            ->where('CustomerID', Auth::user()->customer->acc_code)
            ->firstOrFail();


        return view('shop.checkout.success', compact('order', 'currentCompany'));
    }

    /**
     * Get or create default e-commerce salesperson
     */
    private function getEcommerceSalesperson()
    {
        // Try to find existing e-commerce salesperson
        $salesperson = User::where('email', 'ecommerce@system.local')
            ->orWhere('PreferredName', 'E-commerce System')
            ->first();

        if (!$salesperson) {
            // Create default e-commerce salesperson
            $salesperson = User::create([
                'PreferredName' => 'E-commerce System',
                'email' => 'ecommerce@system.local',
                'password' => bcrypt('random-password-' . str_random(10)),
                'IsSalesperson' => 1,
                'RepCode' => 99,
                'email_verified_at' => now(),
            ]);
        }

        return $salesperson;
    }

    /**
     * Send order confirmation notifications
     */
    private function sendOrderNotifications($order, $currentCompany)
    {
        try {
            // Get notification settings
            $orderCustomerConfirmation = (boolean) $currentCompany->getSetting('order_customer_confirmation');
            $orderFulfillmentNotification = (boolean) $currentCompany->getSetting('order_fulfillment_notification');
            $fulfillmentEmail = $currentCompany->getSetting('fulfillment_mailbox');

            // Send confirmation email to customer
            if ($orderCustomerConfirmation && $order->customer->GeneralEmailAddress) {
                Mail::to($order->customer->GeneralEmailAddress)
                    ->send(new OrderConfirmationMail($order));
            }

            // Notify fulfillment team via email
            if ($orderFulfillmentNotification && $fulfillmentEmail) {
                Mail::to($fulfillmentEmail)
                    ->send(new FulfillmentNotificationMail($order));
            }

            // Notify fulfillment team via system notifications
            $fulfillmentTeam = User::whereHas('roles', function ($query) {
                $query->where('title', 'Fulfillment');
            })->get();

            if ($fulfillmentTeam->count() > 0) {
                Notification::send($fulfillmentTeam, new NewOrderNotification($order));
            }

        } catch (\Exception $e) {
            // Log notification errors but don't fail the order
            \Log::warning('Order notification error: ' . $e->getMessage());
        }
    }

    /**
     * Check customer credit limit and hold status
     */
    public function checkCreditLimit($customer, $orderTotal)
    {
        $creditLimit = $customer->CreditLimit ?? 0;
        $isOnHold = (boolean) $customer->IsOnCreditHold;

        $outstandingBalance = $customer->customerSubBalance->AgedBalance1 ?? 0; // TODO: Calculate actual outstanding balance from invoices

        $availableCredit = $creditLimit - $outstandingBalance;
        $overLimit = ($outstandingBalance + $orderTotal) > $creditLimit;
        $overAmount = $overLimit ? ($outstandingBalance + $orderTotal) - $creditLimit : 0;

        return [
            'credit_limit' => $creditLimit,
            'outstanding_balance' => $outstandingBalance,
            'available_credit' => $availableCredit,
            'order_total' => $orderTotal,
            'on_hold' => $isOnHold,
            'over_limit' => $overLimit,
            'over_amount' => $overAmount,
            'can_proceed' => !$isOnHold && !$overLimit,
            'requires_approval' => $isOnHold || $overLimit
        ];
    }

    /**
     * Build internal comments for the order
     */
    public function buildInternalComments($request, $user, $creditInfo)
    {
        $comments = [];

        $comments[] = 'E-commerce order placed by ' . $user->PreferredName;

        if ($request->delivery_method === 'collection') {
            $comments[] = 'Customer will collect order';
        } elseif ($request->preferred_delivery_date) {
            $comments[] = 'Preferred delivery date: ' . \Carbon\Carbon::parse($request->preferred_delivery_date)->format('d M Y');
        }

        if ($creditInfo['on_hold']) {
            $comments[] = 'CREDIT HOLD: Customer account on credit hold';
        }

        if ($creditInfo['over_limit']) {
            $comments[] = 'CREDIT LIMIT: Order exceeds available credit by ' . PricingHelper::formatPrice($creditInfo['over_amount']);
        }

        return implode('. ', $comments);
    }

}
