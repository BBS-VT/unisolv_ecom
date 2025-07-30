<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    /**
     * Display the customer account dashboard
     */
    public function index()
    {
        //$customer = Auth->user()->customer;
        $user = Auth::user();
        $customer = $user->customer;
        $currentCompany = $user->currentCompany();

        // Fetch recent orders
        $recentOrders = Order::where('CustomerID', $customer->acc_code)
            ->orderBy('OrderDate', 'desc')
            ->take(5)
            ->with(['salesperson'])
            ->get();

        // Calculate order statistics
        $orderStats = [
            'total_orders' => Order::where('CustomerID', $customer->acc_code)->count(),
            'new_orders' => Order::where('CustomerID', $customer->acc_code)->where('OrderStatusID', 1)->count(),
            'delivered_orders' => Order::where('CustomerID', $customer->acc_code)->where('OrderStatusID', 4)->count(),
            'on_hold_orders' => Order::where('CustomerID', $customer->acc_code)->where('OrderStatusID', 5)->count(),
        ];

        // Calculate total spending (last 12 months)
        $totalSpending = Order::where('CustomerID', $customer->acc_code)
            ->where('OrderDate', '>=', now()->subYear())
            ->where('OrderStatusID', '!=', 5) // Exclude cancelled orders
            ->sum('total');

        // Credit utilization calculation
        $creditUtilization = 0;
        if ($customer->CreditLimit > 0) {
            // This would need to be calculated based on outstanding invoices
            // For now, using a placeholder calculation
            $outstandingAmount = 0; // TODO: Calculate from invoices
            $creditUtilization = ($outstandingAmount / $customer->CreditLimit) * 100;
        }

        return view('shop.account.index', compact(
            'customer',
            'recentOrders',
            'orderStats',
            'totalSpending',
            'creditUtilization'
        ));
    }

    /**
     * Show the customer profile page
     */
    public function profile()
    {
        $customer = Auth::user()->customer;

        return view('shop.account.profile', compact('customer'));
    }

    /**
     * Update customer profile
     */
    public function updateProfile(Request $request)
    {
        $customer = Auth::user()->customer;
        $user = Auth::user();

        $request->validate([
            'DeliveryAddressLine1' => 'required|string|max:255',
            'DeliveryAddressLine2' => 'nullable|string|max:255',
            'DeliveryCity' => 'required|string|max:100',
            'DeliveryPostCode' => 'required|string|max:20',
            'DeliveryState' => 'required|string|max:50',
            'GeneralEmailAddress' => 'required|email|max:255',
            'current_password' => 'nullable|current_password',
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        // Update customer delivery address
        $customer->update([
            'DeliveryAddressLine1' => $request->DeliveryAddressLine1,
            'DeliveryAddressLine2' => $request->DeliveryAddressLine2,
            'DeliveryCity' => $request->DeliveryCity,
            'DeliveryPostCode' => $request->DeliveryPostCode,
            'DeliveryState' => $request->DeliveryState,
            'GeneralEmailAddress' => $request->GeneralEmailAddress,
        ]);

        // Update user email if changed
        if ($user->email !== $request->GeneralEmailAddress) {
            $user->update(['email' => $request->GeneralEmailAddress]);
        }

        // Update password if provided
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('shop.account.profile')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Get order status statistics for dashboard
     */
    private function getOrderStatusStats($customerId)
    {
        $statusCounts = Order::where('CustomerID', $customerId)
            ->selectRaw('OrderStatusID, count(*) as count')
            ->groupBy('OrderStatusID')
            ->pluck('count', 'OrderStatusID')
            ->toArray();

        return [
            'new' => $statusCounts[1] ?? 0,
            'downloaded' => $statusCounts[2] ?? 0,
            'delivery' => $statusCounts[3] ?? 0,
            'invoiced' => $statusCounts[4] ?? 0,
            'on_hold' => $statusCounts[5] ?? 0,
        ];
    }
}
