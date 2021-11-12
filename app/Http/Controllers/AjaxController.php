<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\SpecialDeals;
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
                "currency" => $customer->currency,
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

        /*

        if(count($specialDeal) > 0){

            $products = DB::select(DB::raw("SELECT DISTINCT products.id, products.StockItemName, products.StockCode, products.SellingPrice,
            products.DiscountPercentage AS Discount, products.TaxRateID, special_deals.StartDate, special_deals.EndDate, special_deals.DiscountPercentage, special_deals.UnitPrice
            FROM products
            LEFT OUTER JOIN special_deals on products.StockCode = special_deals.StockItemID
            AND special_deals.CustomerID = '$matchCustomer'
            AND '$dealDate' BETWEEN special_deals.StartDate AND special_deals.EndDate
            ORDER BY products.StockItemName"));

        } else {
            $products = Product::findByCompany($currentCompany->id)
                ->select('id', 'StockItemName AS text', 'SellingPrice AS price', 'DiscountPercentage AS discount')
                ->where('status', '1')
                ->orderBy("StockItemName")
                ->with('taxes')
                ->get();
        }*/

        $products = Product::findByCompany($currentCompany->id)
            ->select('id', 'StockItemName AS text', 'SellingPrice AS price', 'DiscountPercentage AS discount', 'StockCode AS sku' )
            ->where('status', '1')
            ->orderBy("StockItemName")
            ->with('taxes')
            ->get();

        return response()->json($products);
    }

    public function specialDeal(Request $request)
    {
        $matchCustomer = $request->customer_id;

        $specialDeal = SpecialDeals::where('CustomerID', "=", $matchCustomer)
            ->whereDate('StartDate', '<=', Carbon::today()->toDateString())
            ->whereDate('EndDate', '>=', Carbon::today()->toDateString())
            ->get('StockItemID')
            ->pluck('StockItemID');

        /*$dealDate = Carbon::today()->toDateString();

        if(count($specialDeal) > 0){

            $products = DB::select(DB::raw("SELECT DISTINCT products.id, products.StockItemName, products.StockCode, products.SellingPrice,
            products.DiscountPercentage AS Discount, products.TaxRateID, special_deals.StartDate, special_deals.EndDate, special_deals.DiscountPercentage, special_deals.UnitPrice
            FROM products
            LEFT OUTER JOIN special_deals on products.StockCode = special_deals.StockItemID
            AND special_deals.CustomerID = '$matchCustomer'
            AND '$dealDate' BETWEEN special_deals.StartDate AND special_deals.EndDate
            ORDER BY products.StockItemName"));*/

        return response()->json($specialDeal);
    }
}
