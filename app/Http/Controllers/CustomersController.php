<?php

namespace App\Http\Controllers;

use App\Models\BuyingGroup;
use App\Models\Customer;
use App\Models\CustomerCategory;
use App\Models\CustomerStatus;
use App\Models\User;
use App\Models\Order;
use App\Models\Country;
use App\Imports\CustomerMasterImport;
use App\Http\Requests\MassDestroyCustomerRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Gate;
use DateTime;
use DB;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('customer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $customers = Customer::all();

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('customer_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salesreps = User::where('IsSalesperson', 1)->pluck('PreferredName', 'id')->prepend(trans('global.pleaseSelect'), '');
        $billingCustomers = Customer::all()->pluck('CustomerName', 'id')->prepend(trans('global.pleaseSelect'), '');
        $customerCategories = CustomerCategory::all()->pluck('AccountType', 'id')->prepend(trans('global.pleaseSelect'), '');
        $buyingGroups = BuyingGroup::all()->pluck('BuyingGroupName', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('customers.create', compact('salesreps', 'billingCustomers', 'customerCategories', 'buyingGroups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create($request->all());

        $customer->billingCustomer()->sync($request->input('customer[BillToCustomerID]', []));
        $customer->customerCategory()->sync($request->input('CustomerCategoryID', []));
        $customer->buyingGroup()->sync($request->input('BuyingGroupID', []));
        $customer->salesrep()->sync($request->input('SalesRepID', []));

        return redirect()->route('customers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        abort_if(Gate::denies('customer_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salesreps = User::where('IsSalesperson', 1)->pluck('FullName', 'id');
        $billingCustomers = Customer::all()->pluck('CustomerName', 'id');
        $customerCategories = CustomerCategory::all()->pluck('AccountType', 'id');
        $buyingGroups = BuyingGroup::all()->pluck('BuyingGroupName', 'id');
        $customerOrders = Order::where('CustomerID', $customer->acc_main)->get();

        $balance_bf = ($customer->customerBalance->AgedBalance2 ?? '0.00') + ($customer->customerBalance->AgedBalance3 ?? '0.00') +
            ($customer->customerBalance->AgedBalance4 ?? '0.00') + ($customer->customerBalance->AgedBalance5 ?? '0.00') + ($customer->customerBalance->AgedBalance6 ?? '0.00');

        $overdue_balance = ($customer->customerBalance->AgedBalance3 ?? '0.00') +
            ($customer->customerBalance->AgedBalance4 ?? '0.00') + ($customer->customerBalance->AgedBalance5 ?? '0.00') + ($customer->customerBalance->AgedBalance6 ?? '0.00');

        $balance_total = ($customer->customerBalance->AgedBalance1 ?? '0.00') + ($customer->customerBalance->AgedBalance2 ?? '0.00') + ($customer->customerBalance->AgedBalance3 ?? '0.00') +
            ($customer->customerBalance->AgedBalance4 ?? '0.00') + ($customer->customerBalance->AgedBalance5 ?? '0.00') + ($customer->customerBalance->AgedBalance6 ?? '0.00');

        $customer->load('customerBalance', 'lastedited');

        return view('customers.show', compact('customer', 'customerOrders', 'salesreps',
            'billingCustomers', 'customerCategories', 'buyingGroups', 'balance_bf', 'overdue_balance', 'balance_total'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        abort_if(Gate::denies('customer_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salesreps = User::where('IsSalesperson', 1)->pluck('PreferredName', 'id');
        $billingCustomers = Customer::all()->pluck('CustomerName', 'id');
        $customerCategories = CustomerCategory::all()->pluck('AccountType', 'id');
        $buyingGroups = BuyingGroup::all()->pluck('BuyingGroupName', 'id');

        $customer->load('salesrep', 'billingCustomer', 'customerCategory', 'buyingGroup');

        return view('customers.edit', compact( 'customer', 'salesreps', 'billingCustomers', 'customerCategories', 'buyingGroups'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->all());

        return redirect()->route('customers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        abort_if(Gate::denies('customer_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $customer->delete();

        return back();
    }

    public function massDestroy(MassDestroyCustomerRequest $request)
    {
        Customer::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function updateCustomerStatus(Request $request)
    {
        if($request->ajax()) {
            $data = $request->all();

            if($data['status'] == "Active"){
                $status = 0;
            } else {
                $status = 1;
            }

            Customer::where('id',$data['customer_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'customer_id'=>$data['customer_id']]);
        }
    }

    public function lookup()
    {
        abort_if(Gate::denies('customer_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $salesreps = User::where('IsSalesperson', 1)->pluck('FullName', 'id')->prepend(trans('global.pleaseSelect'), '');
        $billingCustomers = Customer::all()->pluck('CustomerName', 'id')->prepend(trans('global.pleaseSelect'), '');
        $customerCategories = CustomerCategory::all()->pluck('CustomerCategoryName', 'id')->prepend(trans('global.pleaseSelect'), '');
        $buyingGroups = BuyingGroup::all()->pluck('BuyingGroupName', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('customers.lookup', compact('salesreps', 'billingCustomers', 'customerCategories', 'buyingGroups'));
    }

    function generateStoreEan()
    {
        $date = new DateTime();
        $time = $date->getTimestamp();

        $code = '20' . str_pad($time, 10, '0');
        $weightflag = true;
        $sum = 0;

        for ($i = strlen($code) - 1; $i >= 0; $i--) {
            $sum += (int)$code[$i] * ($weightflag ? 3 : 1);
            $weightflag = !$weightflag;
        }
        $code .= (10 - ($sum % 10)) % 10;
        //return $code;
        return response()->json($code, 200);

    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function importExcel(Request $request)
    {
        abort_if(Gate::denies('customer_import'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Customer::truncate();

        \Excel::import(new CustomerMasterImport,$request->import_file);

        //DB::statement('UPDATE products SET Barcode = TRIM(Barcode)');

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        \Session::put('success', 'File imported successfully');

        return back();
    }
}
