<?php

namespace App\Http\Controllers\Shop;

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
    public function reorder(Order $order)
    {
        $customer = Auth::user()->customer;

        // Ensure customer can only reorder their own orders
        if ($order->CustomerID !== $customer->acc_code) {
            abort(403, 'You can only reorder your own orders.');
        }

        $order->load('items.product');

        try {
            $addedItems = 0;

            foreach ($order->items as $item) {
                // Check if product is still available
                if ($item->product && $item->product->IsActive) {
                    // TODO: Add to cart logic here - adjust based on cart


                    // Example cart addition (adjust to match your cart implementation):
                    $cartItem = [
                        'product_id' => $item->ProductID,
                        'quantity' => $item->Quantity,
                        'price' => PricingHelper::getCustomerPrice($item->product, $customer)
                    ];

                    // Add to session cart or use your cart service
                    $cart = session()->get('cart', []);
                    $cart[$item->ProductID] = $cartItem;
                    session()->put('cart', $cart);

                    $addedItems++;
                }
            }

            if ($addedItems > 0) {
                return redirect()->route('shop.cart')
                    ->with('success', "{$addedItems} items from order #{$order->OrderNumber} have been added to your cart.");
            } else {
                return redirect()->back()
                    ->with('warning', 'No items could be added to cart. Products may no longer be available.');
            }

        } catch (\Exception $e) {
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
}
