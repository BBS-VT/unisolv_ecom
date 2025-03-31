<?php

namespace App\Http\Controllers;

use App\Models\CustomerBalance;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $customer = $user->customer;

        $customerBalance = null;
        $orderS = [];

        if ($customer) {
            $customerBalance = CustomerBalance::where('AccMain', $customer->acc_main)
                ->where('AccCode', $customer->acc_code)
                ->first();

            $orders = Order::where('CustomerID', $customer->acc_code)
                ->orderBy('OrderDate', 'desc')
                ->limit(10) // Get the 10 most recent orders
                ->get();

            $allOrders = Order::where('CustomerID', $customer->acc_code)
                ->orderBy('OrderDate', 'desc')
                ->paginate(10); // Paginate the rest of the orders
        }

        return view('customers.dashboard', compact('customer', 'customerBalance', 'orders', 'allOrders'));
    }
}
