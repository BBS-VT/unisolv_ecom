<?php

namespace App\Http\Controllers\Shop;

use App\Helpers\Features;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrdersItem;
use App\Helpers\PricingHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display paginated list of customer orders
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $customer = $user->customer;

        $query = Order::where('CustomerID', $customer->acc_code)
            ->with(['salesperson', 'items.product']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('OrderStatusID', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('OrderNumber', 'like', "%{$request->search}%")
                    ->orWhere('CustomerPurchaseOrderNumber', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->where('OrderDate', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('OrderDate', '<=', $request->date_to);
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'OrderDate');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSorts = ['OrderDate', 'OrderNumber', 'total', 'OrderStatusID'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $orders = $query->paginate(15)->withQueryString();

        // Get status options for filter dropdown
        // TODO: link this to order_status table
        $statusOptions = [
            1 => 'New',
            2 => 'Downloaded',
            3 => 'Delivery',
            4 => 'Invoiced',
            5 => 'On Hold'
        ];

        return view('shop.account.orders.index', compact(
            'orders',
            'statusOptions'
        ));
    }

    /**
     * Display specific order details
     */
    public function show($orderId)
    {
        $customer = Auth::user()->customer;

        // Load order with authorization check
        $order = Order::with(['items.product', 'salesperson', 'customer'])
            ->where('id', $orderId)
            ->where('CustomerID', $customer->acc_code)
            ->firstOrFail();

        // Ensure customer can only view their own orders
        if ($order->CustomerID !== $customer->acc_code) {
            abort(403, 'You can only view your own orders.');
        }


        // Get order status history
        $statusHistory = $this->getOrderStatusHistory($order);

        return view('shop.account.orders.show', compact('order', 'statusHistory'));
    }

    /**
     * Cancel an order (only if status is "New")
     */
    public function cancel(Request $request, Order $order)
    {
        $customer = Auth::user()->customer;

        // Ensure customer can only cancel their own orders
        if ($order->CustomerID !== $customer->acc_code) {
            abort(403, 'You can only cancel your own orders.');
        }

        // Only allow cancellation of "New" orders
        if ($order->OrderStatusID !== 1) {
            return redirect()->back()
                ->with('error', 'Only orders with "New" status can be cancelled.');
        }

        $request->validate([
            'cancel_reason' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Update order status to cancelled (assuming 6 = Cancelled)
            // You may need to adjust this based on your status system
            $order->update([
                'OrderStatusID' => 5, // On Hold - adjust as needed
                'Comments' => ($order->Comments ? $order->Comments . "\n\n" : '') .
                    "CANCELLED: " . $request->cancel_reason . " (by customer on " . now()->format('Y-m-d H:i') . ")"
            ]);

            // TODO: Add any additional cancellation logic here
            // - Send notification emails
            // - Update inventory if needed
            // - Log the cancellation

            DB::commit();

            return redirect()->route('shop.account.orders.index')
                ->with('success', 'Order has been cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to cancel order. Please contact support.');
        }
    }

    /**
     * Reorder - add order items to cart
     */
    public function reorder($orderId)
    {
        $customer = Auth::user()->customer;

        $order = Order::with('items.product')
            ->where('id', $orderId)
            ->firstOrFail();

        // Ensure customer can only reorder their own orders
        if ($order->CustomerID !== $customer->acc_code) {
            abort(403, 'You can only reorder your own orders.');
        }


        try {
            $addedItems = 0;
            $skippedItems = [];
            $locationMismatchItems = [];

            // Get current cart location lock
            $cartLocation = session('cart_location');

            foreach ($order->items as $item) {
                $product = $item->product;

                // Check if product is still available
                if (!$product || !$product->status || $product->SellingType === 'instore') {
                    $skippedItems[] = $item->StockItem;
                    continue;
                }

                // Check location compatibility
                $productLocation = $product->categories()
                    ->whereNotNull('location_id')
                    ->first()
                    ?->location_id;

                // If cart is locked and product is from different location, skip it
                if ($cartLocation && $productLocation && $cartLocation !== $productLocation) {
                    $locationMismatchItems[] = $product->StockItemName;
                    continue;
                }

                // Lock cart to this product's location if not already locked
                if (!$cartLocation && $productLocation) {
                    session(['cart_location' => $productLocation]);
                    $cartLocation = $productLocation;
                }

                // Get current pricing for the product
                $pricing = $this->getPriceForCustomer($product);

                // Add to cart with complete structure
                $cart = session()->get('cart', []);

                if (isset($cart[$product->id])) {
                    // Product already in cart, increase quantity
                    $cart[$product->id]['quantity'] += $item->Quantity;
                } else {
                    // Add new item to cart - match your addToCart structure
                    $cart[$product->id] = [
                        'product_id' => $product->id,
                        'name' => $product->StockItemName,
                        'quantity' => $item->Quantity,
                        'price' => $pricing,
                        'added_at' => now()->timestamp ,
                    ];
                }

                session()->put('cart', $cart);
                $addedItems++;

            }

            // Build response message
            $message = '';
            if ($addedItems > 0) {
                $message = "{$addedItems} item(s) from order #{$order->OrderNumber} have been added to your cart.";
            }

            if (!empty($locationMismatchItems)) {
                $message .= " Some items were skipped because they're from a different location: " . implode(', ', $locationMismatchItems);
            }

            if (!empty($skippedItems)) {
                $message .= " Some items are no longer available: " . implode(', ', $skippedItems);
            }

            if ($addedItems > 0) {
                return redirect()->route('shop.cart.show')
                    ->with('success', $message);
            } else {
                return redirect()->back()
                    ->with('warning', 'No items could be added to cart. Products may no longer be available or are from a different location than items currently in your cart.');
            }

        } catch (\Exception $e) {
            \Log::error('Reorder failed', [
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to add items to cart. Please try again.');
        }
    }

    /**
     * Get order status history (placeholder)
     */
    private function getOrderStatusHistory(Order $order)
    {
        // This is a placeholder.
        // TODO: implement actual logic to retrieve order status history

        return [
            [
                'status' => 'New',
                'date' => $order->OrderDate,
                'user' => 'Customer',
                'notes' => 'Order placed'
            ]
        ];
    }

    /**
     * Get status badge class for styling
     */
    public static function getStatusBadgeClass($statusId)
    {
        // TODO: link this to order_status table for dynamic classes
        return match($statusId) {
            1 => 'bg-primary',     // New
            2 => 'bg-info',        // Downloaded
            3 => 'bg-warning',     // Delivery
            4 => 'bg-success',     // Invoiced
            5 => 'bg-danger',      // On Hold
            default => 'bg-secondary'
        };
    }

    /**
     * Get status name
     */
    public static function getStatusName($statusId)
    {
        // TODO: link this to order_status table for dynamic classes

        return match($statusId) {
            1 => 'New',
            2 => 'Downloaded',
            3 => 'Delivery',
            4 => 'Invoiced',
            5 => 'On Hold',
            default => 'Unknown'
        };
    }

    private function getPriceForCustomer($product)
    {
        // If user is not logged in, use default price
        if (!Auth::check()) {
            return Features::showPrices() ? $product->price : 0;
        }

        $customer = Auth::user();

        // Check if user has custom pricing
        switch ($customer->price_level) {
            case 'wholesale':
                return $product->wholesale_price ?: $product->price;
            case 'distributor':
                return $product->distributor_price ?: $product->price;
            default:
                return $product->price;
        }
    }

    public function print(Order $order)
    {
        // Get the authenticated user's customer account code
        $userCustomerCode = auth()->user()->customer->acc_code ?? null;

        if ($order->CustomerID !== $userCustomerCode) {
            abort(403, 'Unauthorized access to this order');
        }

        $currentCompany = auth()->user()->currentCompany();

        return view('shop.orders.print', compact('order', 'currentCompany'));
    }
}
