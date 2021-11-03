<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Requests\Order\Store;
use App\Http\Requests\Order\Update;
use App\Models\Product;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Customer;
use App\Models\SpecialDeals;
use DB;
use Gate;
use PDF;
use Symfony\Component\HttpFoundation\Response;
use Mpociot\VatCalculator\Facades\VatCalculator;
use Carbon\Carbon;
use Spatie\ArrayToXml\ArrayToXml;
use Session;

class OrdersController extends Controller
{
    /**
     * Display Orders Page
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = $request->user();
        $currentCompany = $user->currentCompany();

        // Query Invoices by Company and Tab
        if($request->tab == 'all') {
            $query = Order::findByCompany($currentCompany->id)->orderBy('OrderDate', 'desc');
            $tab = 'all';
        } else if($request->tab == 'due') {
            $query = Order::findByCompany($currentCompany->id)->active()->orderBy('OrderDate', 'desc');
            $tab = 'processed';
        } else {
            $query = Order::findByCompany($currentCompany->id)->new()->orderBy('OrderNumber', 'desc');
            $tab = 'new';
        }

        // Apply Filters and Paginate
        $orders = QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::partial('OrderNumber'),
                AllowedFilter::scope('from'),
                AllowedFilter::scope('to'),
            ])
            ->paginate()
            ->appends(request()->query());

        //echo "<pre>"; print_r($orders); die;
        return view ('orders.index', compact('orders', 'tab'));

    }
}
