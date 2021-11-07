<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    /**
     * Get Customers Ajax Request
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return json
     */
    public function customers(Request $request)
    {
        $user = $request->user();
        $currentCompany = $user->currentCompany();
        $salesrep = auth()->user();

        $search = $request->search;

        if ($salesrep->IsSalesperson == '1') {
            if ($search == '') {
                $customers = Customer::findByCompany($currentCompany->id)
                    ->where('SalesRepID', auth()->user()->RepCode)->limit(10)->get();
            } else {
                $customers = Customer::findByCompany($currentCompany->id)
                    ->where('SalesRepID', auth()->user()->RepCode)
                    ->where('CustomerName', 'like', '%'.$search.'%')->limit(10)->get();
            }
        } else {
            if ($search == '') {
                $customers = Customer::findByCompany($currentCompany->id)->limit(10)->get();
            } else {
                $customers = Customer::findByCompany($currentCompany->id)->where('CustomerName', 'like',
                    '%'.$search.'%')->limit(10)->get();
            }
        }

        $response = collect();
        foreach($customers as $customer){
            $response->push([
                "id"    => $customer->acc_main,
                "text"  => $customer->CustomerName,
            ]);
        }

        return response()->json($response);
    }

    /**
     * Get Products Ajax Request
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return json
     */
    public function products(Request $request)
    {
        $user = $request->user();
        $currentCompany = $user->currentCompany();

        $products = Product::findByCompany($currentCompany->id)
            ->select('id', 'StockItemName AS text', 'SellingPrice AS price', 'DiscountPercentage AS discount')
            ->where('status', '1')
            ->with('taxes')
            ->get();

        return response()->json($products);
    }
}
