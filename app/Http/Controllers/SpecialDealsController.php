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
        abort_if(Gate::denies('specialdeal_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $deals = SpecialDeals::all();
        $categories     = ProductCategory::all()->pluck('StockGroupName', 'id');
        $products       = Product::all()->pluck('StockItemName', 'id');
        $buyinggroups   = BuyingGroup::all()->pluck('BuyingGroupName', 'id');
        $customers      = Customer::all()->pluck('CustomerName', 'id');
        $customergroups = ProductCategory::all()->pluck('CustomerCategoryName', 'id');

        return view('specialdeals.index', compact('deals', 'categories', 'products', 'buyinggroups', 'customers', 'customergroups'));

    }

    public function create()
    {
        abort_if(Gate::denies('specialdeal_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories     = ProductCategory::all()->pluck('StockGroupName', 'id');
        //$products       = Product::all()->pluck('StockItemName', 'id');
        $products       = Product::all();
        $buyinggroups   = BuyingGroup::all()->pluck('BuyingGroupName', 'id');
        //$customers      = Customer::all()->pluck('CustomerName', 'id');
        $customers      = Customer::all();
        $customergroups = ProductCategory::all()->pluck('CustomerCategoryName', 'id');

        return view('specialdeals.create', compact('categories', 'products', 'buyinggroups', 'customers', 'customergroups'));
    }

    public function store(StoreSpecialDealsRequest $request)
    {
        $deal = SpecialDeals::create($request->all());

        return redirect()->route('deals.index');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function importExcel(Request $request)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        SpecialDeals::truncate();

        \Excel::import(new SpecialDealsImport,$request->import_file);

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
