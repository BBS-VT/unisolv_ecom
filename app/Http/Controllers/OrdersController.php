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
            $query = Order::findByCompany($currentCompany->id)->orderBy('created_at', 'desc');
            $tab = 'all';
        } else if($request->tab == 'processed') {
            $query = Order::findByCompany($currentCompany->id)->active()->orderBy('created_at', 'desc');
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

    /**
     * Display form to create new Order
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = $request->user();
        $currentCompany = $user->currentCompany();

        // Get next Order number if auto generation option is enabled
        $next_order_number = Order::getNextOrderNumber();

        // Get customers based on Sales Rep
        $salesrep = auth()->user();

        if ($salesrep->IsSalesperson == '1'){
            $customers = DB::table('customers')->where('SalesRepID', auth()->user()->RepCode)->pluck('CustomerName', 'acc_main')->prepend(trans('global.pleaseSelect'), '');
        } else {
            $customers = Customer::all()->pluck('CustomerName', 'acc_main')->prepend(trans('global.pleaseSelect'), '');
        }

        // Create new number model and set order_number and company_id
        // so that we can use it in the form
        $order = new Order();
        $order->order_number = $next_order_number;
        $order->company_id = $currentCompany->id;

        $products = Product::all();
        $tax_per_item = (boolean) $currentCompany->getSetting('tax_per_item');
        $discount_per_item = (boolean) $currentCompany->getSetting('discount_per_item');

        return view('orders.create', compact('order', 'customers', 'products', 'tax_per_item', 'discount_per_item'));
    }

    /**
     * Store the Order in the Database
     *
     * @param \App\Http\Requests\Order\Store $request
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function store(Store $request)
    {
        $user = $request->user();
        $currentCompany = $user->currentCompany();

        // Get company based settings
        $tax_per_item = (boolean) $currentCompany->getSetting('tax_per_item');
        $discount_per_item = (boolean) $currentCompany->getSetting('discount_per_item');

        // Save Order to Database
        $order = Order::create([
            'OrderDate'         => $request->order_date,
            'OrderNumber'       => $request->order_number,
            'CustomerPurchaseOrderNumber'   => $request->reference_number,
            'CustomerID'        => $request->customer_id,
            'company_id'        => $currentCompany->id,
            'SalesPersonID'     => $request->salesperson_id,
            'LastEditedBy'      => $user,
            'OrderStatusID'     => '1',
            'Authorisation'     => '0',
            'sub_total'         => $request->sub_total,
            'discount_type'     => 'percent',
            'discount_val'      => $request->total_discount ?? 0,
            'total'             => $request->grand_total,
            'Comments'          => $request->notes,
            'InternalComments'  => $request->private_notes,
            'tax_per_item'      => $tax_per_item,
            'discount_per_item' => $discount_per_item,
        ]);

        // Arrays of data for Order Items
        $products   = $request->product;
        $quantities = $request->quantity;
        $taxes      = $request->taxes;
        $prices     = $request->price;
        $totals     = $request->total;
        $discounts  = $request->discount;

        // Add products (order items)
        for ($i=0; $i < count($products); $i++) {
            $product = Product::first(
                ['id' => $products[$i], 'company_id' => $currentCompany->id],
                ['name' => $products[$i], 'price' => $prices[$i], 'status' => 1]
            );

            $item = $order->items()->create([
                'StockItem' => $product->id,
                'company_id' => $currentCompany->id,
                'Quantity'   => $quantities[$i],
                'discount_type' => 'percent',
                'discount_val'  => $discounts[$i] ?? 0,
                'UnitPrice'     => $prices[$i],
                'total'         => $totals[$i],
            ]);

            // Add taxes for Order Item if it is given
            if ($taxes && array_key_exists($i, $taxes)) {
                foreach ($taxes[$i] as $tax) {
                    $item->taxes()->create([
                        'tax_type_id' => $tax
                    ]);
                }
            }
        }

        // If Order based taxes are given
        if ($request->has('total_taxes')) {
            foreach ($request->total_taxes as $tax) {
                $order->taxes()-create([
                    'tax_type_id' => $tax
                ]);
            }
        }

        session()->flash('alert-success', __('global.order_added'));
        return redirect()->route('orders');
    }
}
