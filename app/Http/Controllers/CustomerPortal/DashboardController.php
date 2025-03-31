<?php

namespace App\Http\Controllers\CustomerPortal;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display Customer Dashboard
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$currentCustomer = Customer::findByUid($request->customer);
        //$company = $currentCustomer->company;

        // Dashboard Stats

        return view('customer_portal.dashboard.index');
    }
}
