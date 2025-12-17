<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\SpecialDeals;
use App\Services\PricingService;
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
                "id"    => $customer->acc_code,
                "text"  => $customer->CustomerName,
                "currency" => $customer->currency,
                "default_price" => $customer->price_level ?? '1'
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

        $customer = Customer::where('acc_code', '=', $matchCustomer)->firstOrFail();
        $discountAllowed = $customer->discount_allowed ?? false;

        $specialDeal = [];
        $buyingGroupDeal = [];

        if ($discountAllowed) {
            $specialDeal = SpecialDeals::where('CustomerID', "=", $matchCustomer)
                ->whereDate('StartDate', '<=', $dealDate)
                ->whereDate('EndDate', '>=', $dealDate)
                ->get('StockItemID')
                ->pluck('StockItemID');

            $buyingGroup = $customer->BuyingGroupID;

            if ($buyingGroup) {
                $buyingGroupDeal = SpecialDeals::where('BuyingGroupID', '=', $buyingGroup)
                    ->whereDate('StartDate', '<=', $dealDate)
                    ->whereDate('EndDate', '>=', $dealDate)
                    ->get('StockItemID')
                    ->pluck('StockItemID');
            }
        }

        // Base query with common fields
        $baseQuery = function($query) use ($discountAllowed, $matchCustomer, $dealDate, $customer) {
            $query->select('products.id AS id', 'products.StockItemName AS text', 'products.StockCode',
                'products.SellingPrice AS SellingPrice', 'products.SellingPrice2 AS SellingPrice2',
                'products.SellingPrice3 AS SellingPrice3', 'products.SellingPrice4 AS SellingPrice4',
                'products.AverageCostPrice AS avgcost', 'products.DiscountPercentage AS discount',
                'stock_item_holdings.QuantityOnHand as stock', 'stock_item_holdings.LastCostPrice AS lastcost');

            // Only join with special_deals if discounts are allowed
            if ($discountAllowed) {
                $query->leftJoin('special_deals', function ($join) use ($matchCustomer, $dealDate) {
                    $join->on('products.StockCode', '=', 'special_deals.StockItemID');
                    $join->on('special_deals.CustomerID', '=', DB::raw("'" . $matchCustomer . "'"));
                    $join->on('special_deals.StartDate', '<=', DB::raw("'" . $dealDate . "'"));
                    $join->on('special_deals.EndDate', '>=', DB::raw("'" . $dealDate . "'"));
                })->addSelect('special_deals.StartDate', 'special_deals.EndDate',
                    'special_deals.DiscountPercentage', 'special_deals.UnitPrice');
            }

            $query->leftJoin('stock_item_holdings', 'products.StockCode', '=', 'stock_item_holdings.StockCode')
                ->distinct()
                ->orderBy('products.StockItemName')
                ->with('taxes');

            return $query;
        };

        // Search conditions
        $searchCondition = function($query) use ($search) {
            if ($search != '') {
                $query->where('StockItemName', 'like', '%'.$search.'%')
                    ->orWhere('Barcode', 'like', '%'.$search.'%')
                    ->orWhere('AltBarcode', 'like', '%'.$search.'%');
            }
            return $query;
        };

        // Get products based on discount and deal conditions
        if ($discountAllowed && count($buyingGroupDeal) > 0) {
            $products = $baseQuery(Product::query())
                ->leftJoin('special_deals', function ($join) use ($customer, $dealDate) {
                    $join->on('products.StockCode', '=', 'special_deals.StockItemID');
                    $join->on('special_deals.BuyingGroupID', '=', DB::raw("'" . $customer->BuyingGroupID . "'"));
                    $join->on('special_deals.StartDate', '<=', DB::raw("'" . $dealDate . "'"));
                    $join->on('special_deals.EndDate', '>=', DB::raw("'" . $dealDate . "'"));
                })
                ->addSelect('special_deals.BuyingGroupID');
            $products = $searchCondition($products)->get();
        } elseif ($discountAllowed && count($specialDeal) > 0) {
            $products = $baseQuery(Product::query());
            $products = $searchCondition($products)->get();
        } else {
            // Standard pricing without special deals
            $products = $baseQuery(Product::findByCompany($currentCompany->id));
            $products = $searchCondition($products)->get();
        }

        // Calculate final prices based on PricingService and discount settings
        foreach ($products as $product) {
            $product->base_price = PricingService::getPrice($product, $customer);

            // Apply special deal discounts if allowed
            if ($discountAllowed && isset($product->UnitPrice) && $product->UnitPrice > 0) {
                $product->final_price = $product->UnitPrice;
            } elseif ($discountAllowed && isset($product->DiscountPercentage) && $product->DiscountPercentage > 0) {
                $product->final_price = $product->base_price * (1 - ($product->DiscountPercentage / 100));
            } else {
                $product->final_price = $product->base_price;
            }
        }

        return response()->json($products);
    }

    public function maxdiscount(Request $request)
    {
        $user = $request->user();
        $currentCompany = $user->currentCompany();

        $maxdiscount = Product::findByCompany($currentCompany->id)
            ->where('id', $request->id)
            ->value('DiscountPercentage');

        return response()->json(['discValidate' => $maxdiscount]);
    }

    public function search(Request $request)
    {
        $user = $request->user();
        $currentCompany = $user->currentCompany();

        \Log::info('Product search initiated', [
            'search_term' => $request->get('q'),
            'page' => $request->get('page', 1),
            'user_id' => auth()->id(),
            'company_id' => $currentCompany->id() ?? 'NO COMPANY'
        ]);

        $search = $request->get('q');
        $page = $request->get('page', 1);
        $perPage = 10;

        if (!auth()->check()) {
            \Log::error('Product search - User not authenticated');
            return response()->json([
                'items' => [],
                'has_more' => false,
                'error' => 'Not authenticated'
            ]);
        }

        // Check if user has company
        if (!auth()->user()->company_id) {
            \Log::error('Product search - User has no company_id', [
                'user_id' => auth()->id()
            ]);
            return response()->json([
                'items' => [],
                'has_more' => false,
                'error' => 'No company assigned'
            ]);
        }

        $query = Product::query()
            ->where('company_id', auth()->user()->currentCompany->id);

        // Log total products for this company before search
        $totalCompanyProducts = Product::where('company_id', auth()->user()->company_id)->count();
        \Log::info('Total products for company', [
            'company_id' => auth()->user()->company_id,
            'total_products' => $totalCompanyProducts
        ]);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('StockCode', 'like', "%{$search}%")
                    ->orWhere('StockItemName', 'like', "%{$search}%");
            });

            \Log::info('Search filter applied', [
                'search_term' => $search,
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);
        }

        $total = $query->count();

        \Log::info('Search query count', [
            'total_matches' => $total,
            'search_term' => $search
        ]);

        $products = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get(['id', 'StockCode', 'StockItemName']);

        \Log::info('Products retrieved', [
            'count' => $products->count(),
            'products' => $products->toArray()
        ]);

        return response()->json([
            'items' => $products,
            'has_more' => ($page * $perPage) < $total
        ]);
    }
}
