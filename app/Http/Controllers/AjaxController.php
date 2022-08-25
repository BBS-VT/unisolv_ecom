<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\SpecialDeals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DB;

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

        $matchCustomer = $request->customer_id;

        $dealDate = Carbon::today()->toDateString();

        $search = $request->search;

        $specialDeal = SpecialDeals::where('CustomerID', "=", $matchCustomer)
            ->whereDate('StartDate', '<=', Carbon::today()->toDateString())
            ->whereDate('EndDate', '>=', Carbon::today()->toDateString())
            ->get('StockItemID')
            ->pluck('StockItemID');

        $buyingGroup = Customer::where('acc_main', "=", $matchCustomer)
            ->value('BuyingGroupID');

        $buyingGroupDeal = SpecialDeals::where('BuyingGroupID', '=', $buyingGroup)
                ->whereDate('StartDate', '<=', Carbon::today()->toDateString())
                ->whereDate('EndDate', '>=', Carbon::today()->toDateString())
                ->get('StockItemID')
                ->pluck('StockItemID');

        if ($search == '') {
            if (count($buyingGroupDeal) > 0) {
                $products = Product::select('products.id AS id', 'products.StockItemName AS text', 'products.StockCode', 'products.SellingPrice AS price',
                    'products.DiscountPercentage AS discount', 'special_deals.BuyingGroupID', 'special_deals.StartDate', 'special_deals.EndDate', 'special_deals.DiscountPercentage', 'special_deals.UnitPrice')
                    ->distinct()
                    ->leftJoin('special_deals', function ($join) use ($buyingGroup, $dealDate) {
                        $join->on('products.StockCode', '=', 'special_deals.StockItemID');
                        $join->on('special_deals.BuyingGroupID', '=', DB::raw("'" . $buyingGroup . "'"));
                        $join->on('special_deals.StartDate', '<=', DB::raw("'" . $dealDate . "'"));
                        $join->on('special_deals.EndDate', '>=', DB::raw("'" . $dealDate . "'"));
                    })
                    ->orderBy('products.StockItemName')
                    ->with('taxes')
                    ->get();
            } elseif (count($specialDeal) > 0) {
                $products = Product::select('products.id AS id', 'products.StockItemName AS text', 'products.StockCode', 'products.SellingPrice AS price',
                    'products.DiscountPercentage AS discount', 'special_deals.StartDate', 'special_deals.EndDate', 'special_deals.DiscountPercentage', 'special_deals.UnitPrice')
                    ->distinct()
                    ->leftJoin('special_deals', function ($join) use ($matchCustomer, $dealDate) {
                        $join->on('products.StockCode', '=', 'special_deals.StockItemID');
                        $join->on('special_deals.CustomerID', '=', DB::raw("'" . $matchCustomer . "'"));
                        $join->on('special_deals.StartDate', '<=', DB::raw("'" . $dealDate . "'"));
                        $join->on('special_deals.EndDate', '>=', DB::raw("'" . $dealDate . "'"));
                    })
                    ->orderBy('products.StockItemName')
                    ->with('taxes')
                    ->get();
            } else {
                $products = Product::findByCompany($currentCompany->id)
                    ->select('products.id AS id', 'products.StockItemName AS text', 'products.StockCode', 'products.SellingPrice AS price',
                        'products.DiscountPercentage AS discount', 'special_deals.StartDate', 'special_deals.EndDate', 'special_deals.DiscountPercentage', 'special_deals.UnitPrice')
                    ->distinct()
                    ->leftJoin('special_deals', function ($join) use ($matchCustomer, $dealDate) {
                        $join->on('products.StockCode', '=', 'special_deals.StockItemID');
                        $join->on('special_deals.CustomerID', '=', DB::raw("'" . $matchCustomer . "'"));
                        $join->on('special_deals.StartDate', '<=', DB::raw("'" . $dealDate . "'"));
                        $join->on('special_deals.EndDate', '>=', DB::raw("'" . $dealDate . "'"));
                    })
                    ->orderBy('products.StockItemName')
                    ->with('taxes')
                    ->get();
            }
        } else {
            if (count($buyingGroupDeal) > 0) {
                $products = Product::select('products.id AS id', 'products.StockItemName AS text', 'products.StockCode', 'products.SellingPrice AS price',
                    'products.DiscountPercentage AS discount', 'special_deals.BuyingGroupID', 'special_deals.StartDate', 'special_deals.EndDate', 'special_deals.DiscountPercentage', 'special_deals.UnitPrice')
                    ->distinct()
                    ->leftJoin('special_deals', function ($join) use ($buyingGroup, $dealDate) {
                        $join->on('products.StockCode', '=', 'special_deals.StockItemID');
                        $join->on('special_deals.BuyingGroupID', '=', DB::raw("'" . $buyingGroup . "'"));
                        $join->on('special_deals.StartDate', '<=', DB::raw("'" . $dealDate . "'"));
                        $join->on('special_deals.EndDate', '>=', DB::raw("'" . $dealDate . "'"));
                    })
                    ->where('StockItemName', 'like', '%'.$search.'%')
                    ->orWhere('Barcode', 'like', '%'.$search.'%')
                    ->orWhere('AltBarcode', 'like', '%'.$search.'%')
                    ->orderBy('products.StockItemName')
                    ->with('taxes')
                    ->get();
            } elseif (count($specialDeal) > 0) {
                $products = Product::select('products.id AS id', 'products.StockItemName AS text', 'products.StockCode', 'products.SellingPrice AS price',
                    'products.DiscountPercentage AS discount', 'special_deals.StartDate', 'special_deals.EndDate', 'special_deals.DiscountPercentage', 'special_deals.UnitPrice')
                    ->distinct()
                    ->leftJoin('special_deals', function ($join) use ($matchCustomer, $dealDate) {
                        $join->on('products.StockCode', '=', 'special_deals.StockItemID');
                        $join->on('special_deals.CustomerID', '=', DB::raw("'" . $matchCustomer . "'"));
                        $join->on('special_deals.StartDate', '<=', DB::raw("'" . $dealDate . "'"));
                        $join->on('special_deals.EndDate', '>=', DB::raw("'" . $dealDate . "'"));
                    })
                    ->where('StockItemName', 'like', '%'.$search.'%')
                    ->orWhere('Barcode', 'like', '%'.$search.'%')
                    ->orWhere('AltBarcode', 'like', '%'.$search.'%')
                    ->orderBy('products.StockItemName')
                    ->with('taxes')
                    ->get();
            } else {
                $products = Product::findByCompany($currentCompany->id)
                    ->select('products.id AS id', 'products.StockItemName AS text', 'products.StockCode', 'products.SellingPrice AS price',
                        'products.DiscountPercentage AS discount', 'special_deals.StartDate', 'special_deals.EndDate', 'special_deals.DiscountPercentage', 'special_deals.UnitPrice')
                    ->distinct()
                    ->leftJoin('special_deals', function ($join) use ($matchCustomer, $dealDate) {
                        $join->on('products.StockCode', '=', 'special_deals.StockItemID');
                        $join->on('special_deals.CustomerID', '=', DB::raw("'" . $matchCustomer . "'"));
                        $join->on('special_deals.StartDate', '<=', DB::raw("'" . $dealDate . "'"));
                        $join->on('special_deals.EndDate', '>=', DB::raw("'" . $dealDate . "'"));
                    })
                    ->where('StockItemName', 'like', '%'.$search.'%')
                    ->orWhere('Barcode', 'like', '%'.$search.'%')
                    ->orWhere('AltBarcode', 'like', '%'.$search.'%')
                    ->orderBy('products.StockItemName')
                    ->with('taxes')
                    ->get();
            }
        }

        return response()->json($products);
    }
}
