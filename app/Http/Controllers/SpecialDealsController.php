<?php


namespace App\Http\Controllers;

use App\Http\Requests\MassDestroySpecialDealsRequest;
use App\Http\Requests\StoreSpecialDealsRequest;
use App\Http\Requests\UpdateSpecialDealsRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\BuyingGroup;
use App\Models\Customer;
use App\Models\CustomerCategory;
use App\Models\SpecialDeals;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Imports\SpecialDealsImport;
use App\Exports\SpecialDealsExport;
use Illuminate\Support\Facades\DB;

class SpecialDealsController extends Controller
{
    public function index()
    {
        //abort_if(Gate::denies('specialdeal_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deals = SpecialDeals::all();
        $categories     = ProductCategory::all()->pluck('StockGroupName', 'id');
        $products       = Product::all()->pluck('StockItemName', 'id');
        $buyinggroups   = BuyingGroup::all()->pluck('BuyingGroupName', 'id');
        $customers      = Customer::all()->pluck('CustomerName', 'id');
        $customergroups = CustomerCategory::all()->pluck('CustomerCategoryName', 'id');

        return view('specialdeals.index', compact('deals', 'categories', 'products', 'buyinggroups', 'customers', 'customergroups'));

    }

    public function create()
    {
        abort_if(Gate::denies('specialdeal_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories     = ProductCategory::all()->pluck('StockGroupName', 'id');
        //$products       = Product::all()->pluck('StockItemName', 'id');
        //$products       = Product::all();
        $buyinggroups   = BuyingGroup::all()->pluck('BuyingGroupName', 'id');
        //$customers      = Customer::all()->pluck('CustomerName', 'id');
        $customers      = Customer::all();
        $customergroups = CustomerCategory::all()->pluck('CustomerCategoryName', 'AccountType');

        return view('specialdeals.create', compact('categories',  'buyinggroups', 'customers', 'customergroups'));
    }

    public function store(StoreSpecialDealsRequest $request)
    {
        //dd($request);
        $deal = SpecialDeals::create($request->validated());

        return redirect()->route('deals.index')->with('success', 'Deal created successfully');
    }

    public function show($id)
    {
        abort_if(Gate::denies('specialdeal_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deal = SpecialDeals::with([
            'products',
            'customer',
            'buyingGroup',
            'customerGroup',
            'productCategory'
        ])->findOrFail($id);

        return response()->json([
            'id' => $deal->id,
            'DealDescription' => $deal->DealDescription,

            // Product info
            'product_name' => $deal->products
                ? intval(ltrim($deal->products->StockCode, '0')) . ' - ' . $deal->products->StockItemName
                : null,
            'department_name' => $deal->productCategory->StockGroupName ?? null,

            // Customer info
            'buygroup_name' => $deal->buyingGroup->BuyingGroupName ?? null,
            'customergroup_name' => $deal->customerGroup->CustomerCategoryName ?? null,
            'customer_name' => $deal->customer
                ? $deal->customer->acc_main . ' - ' . $deal->customer->CustomerName
                : null,

            // Pricing
            'DiscountAmount' => $deal->DiscountAmount,
            'DiscountPercentage' => $deal->DiscountPercentage,
            'UnitPrice' => $deal->UnitPrice,

            // Dates
            'StartDate' => $deal->StartDate,
            'EndDate' => $deal->EndDate,
        ]);
        /*$where = ['special_deals.id' => $id];

        $deal = SpecialDeals::where($where)
            ->join('products', 'products.stockCode', '=', 'special_deals.StockItemID')
            ->join('customers', 'customers.acc_main', '=', 'special_deals.CustomerID')
            ->select('special_deals.*', 'products.StockItemName', 'customers.CustomerName', 'customers.acc_main')
            ->first();

        if ( is_null($deal)) {
            $deal = SpecialDeals::where($where)
                ->join('products', 'products.stockCode', '=', 'special_deals.StockItemID')
                ->join('customers', 'customers.BuyingGroupID', '=', 'special_deals.BuyingGroupID')
                ->select('special_deals.*', 'products.StockItemName', 'customers.CustomerName', 'customers.acc_main')
                ->get();

                return response()->json($deal);

        } else {
            return response()->json($deal);
        }*/

    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function importExcel(Request $request)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        SpecialDeals::truncate();

        \Excel::import(new SpecialDealsImport,$request->import_file);

        DB::statement('UPDATE special_deals SET CustomerID = TRIM(CustomerID)');
        DB::statement('UPDATE special_deals SET BuyingGroupID = CustomerID WHERE CustomerID NOT REGEXP "^[0-9]+$"');
        DB::statement('UPDATE special_deals SET DealDescription = CustomerID WHERE CustomerID NOT REGEXP "^[0-9]+$"');
        DB::statement('DELETE t1 FROM special_deals t1 INNER JOIN special_deals t2 WHERE t1.id < t2.id AND t1.StockItemID = t2.StockItemID AND t1.BuyingGroupID = t2.BuyingGroupID AND t1.BuyingGroupID != "" AND (DATE(t1.created_at) BETWEEN t1.StartDate AND t1.EndDate)');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        \Session::put('success', 'File imported successfully');

        return back();
    }

    /**
     * @return \Illuminate\Support\Collection
     *
     */
    public function exportExcel($type)
    {
        return \Excel::download(new SpecialDealsExport, 'specialdeals.'.$type);
    }

    public function destroy(SpecialDeals $deal)
    {
        abort_if(Gate::denies('specialdeal_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deal->delete();

        return back();

    }





}
