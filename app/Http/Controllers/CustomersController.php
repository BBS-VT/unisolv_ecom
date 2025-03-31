<?php

namespace App\Http\Controllers;

use App\Jobs\ImportCustomersJob;
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
use Log;
use Storage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('customer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = $request->user();
        $currentCompany = $user->currentCompany();
        $display_subaccount = (boolean) $currentCompany->getSetting('display_subaccount');

        if ($request->ajax()) {
            // Start with a query builder instead of loading all customers
            $query = Customer::query()
                ->where('company_id', $currentCompany->id)
                ->select([
                    'id',
                    'acc_main',
                    'acc_sub',
                    'CustomerName',
                    'DeliveryAddressLine1',
                    'DeliveryCity',
                    'PrimaryContactPersonID',
                    'GeneralEmailAddress',
                    'PhoneNumber',
                    'VatNr',
                    'CustomerStatus',
                    'IsOnCreditHold',
                ]);

            return DataTables::of($query)
                ->addColumn('account_code', function ($customer) use ($display_subaccount) {
                    if ($display_subaccount) {
                        return $customer->acc_main . ' ' . $customer->acc_sub;
                    } else {
                        return $customer->acc_main;
                    }
                })
                ->addColumn('name_with_address', function ($customer) {
                    $html = '<div class="d-flex align-items-center">';
                    $html .= '<div class="d-flex align-items-center">';
                    $html .= '<i class="dripicons-card mr-1"></i> <a href="' . route('customers.show', $customer->id) . '">';
                    $html .= '&nbsp;' . ($customer->CustomerName ?? '') . '</a>';
                    $html .= '</div></div>';
                    $html .= '<div class="d-flex align-items-center mt-1">';
                    $html .= '<small class="text-muted">';
                    $html .= '<i class="dripicons-location"></i> ';
                    $html .= ($customer->DeliveryAddressLine1 ?? '') . ' ' . ($customer->DeliveryCity ?? '');
                    $html .= '</small></div>';

                    return $html;
                })
                ->addColumn('contact_info', function ($customer) {
                    $html = '<div class="d-flex align-items-center">';
                    $html .= '<div class="d-flex align-items-center">';
                    $html .= '<i class="dripicons-user mr-1 text-muted"></i>';
                    $html .= '<p class="text-muted mb-0">' . ($customer->PrimaryContactID ?? '') . '</p>';
                    $html .= '</div></div>';
                    $html .= '<div class="d-flex align-items-center">';
                    $html .= '<small class="text-muted">';
                    $html .= '<i class="dripicons-mail mr-1"></i>';
                    $html .= ($customer->GeneralEmailAddress ?? '');
                    $html .= '</small></div>';

                    return $html;
                })
                ->addColumn('status', function ($customer) {
                    $html = '';
                    if ($customer->CustomerStatus == 1) {
                        $html .= '<a class="updateCustomerStatus" id="customer-' . $customer->id . '" customer_id="' . $customer->id . '"';
                        $html .= ' href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="Click to De-activate Customer">';
                        $html .= '<span class="badge badge-success">' . trans('global.active') . '</span></a>';
                    } else {
                        $html .= '<a class="updateCustomerStatus" id="customer-' . $customer->id . '" customer_id="' . $customer->id . '"';
                        $html .= ' href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="Click to Activate Customer">';
                        $html .= '<span class="badge badge-danger">' . trans('global.inactive') . '</span></a>';
                    }

                    if ($customer->IsOnCreditHold == 1) {
                        $html .= ' <span class="badge badge-warning">' . trans('global.credit_hold') . '</span>';
                    }

                    return $html;
                })
                ->addColumn('action', function ($customer) {
                    $viewButton = '';
                    $editButton = '';
                    $deleteButton = '';

                    if (Gate::allows('customer_show')) {
                        $viewButton = '<a href="' . route('customers.show', $customer->id) . '" data-toggle="tooltip"';
                        $viewButton .= ' title="' . trans('global.view') . ' ' . trans('cruds.customer.title_singular') . '"';
                        $viewButton .= ' data-placement="top">';
                        $viewButton .= '<i class="las dripicons-preview text-info font-18"></i></a>';
                    }

                    if (Gate::allows('customer_edit')) {
                        $editButton = '<a href="' . route('customers.edit', $customer->id) . '" data-toggle="tooltip"';
                        $editButton .= ' title="' . trans('global.edit') . ' ' . trans('cruds.customer.title_singular') . '"';
                        $editButton .= ' data-placement="top">';
                        $editButton .= '<i class="las dripicons-document-edit text-info font-18"></i></a>';
                    }

                    if (Gate::allows('customer_delete')) {
                        $deleteButton = '<form action="' . route('customers.destroy', $customer->id) . '" method="POST"';
                        $deleteButton .= ' onsubmit="return confirm(\'' . trans('global.areYouSure') . '\');" style="display: inline-block;">';
                        $deleteButton .= '<input type="hidden" name="_method" value="DELETE">';
                        $deleteButton .= '<input type="hidden" name="_token" value="' . csrf_token() . '">';
                        $deleteButton .= '<button aria-expanded="false" class="text-danger font-18" style="border:none; background: none;" type="submit"';
                        $deleteButton .= ' data-toggle="tooltip" data-placement="top"';
                        $deleteButton .= ' title="' . trans('global.delete') . ' ' . trans('cruds.customer.title_singular') . '">';
                        $deleteButton .= '<i class="dripicons-trash"></i></button></form>';
                    }

                    return $viewButton . ' ' . $editButton . ' ' . $deleteButton;
                })
                ->filterColumn('account_code', function($query, $keyword) use ($display_subaccount) {
                    if ($display_subaccount) {
                        $query->where(function($q) use ($keyword) {
                            $q->where('acc_main', 'like', "%{$keyword}%")
                                ->orWhere('acc_sub', 'like', "%{$keyword}%");
                        });
                    } else {
                        $query->where('acc_main', 'like', "%{$keyword}%");
                    }
                })
                ->filterColumn('name_with_address', function($query, $keyword) {
                    $query->where(function($q) use ($keyword) {
                        $q->where('CustomerName', 'like', "%{$keyword}%")
                            ->orWhere('DeliveryAddressLine1', 'like', "%{$keyword}%")
                            ->orWhere('DeliveryCity', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('contact_info', function($query, $keyword) {
                    $query->where(function($q) use ($keyword) {
                        $q->where('PrimaryContactPersonID', 'like', "%{$keyword}%")
                            ->orWhere('GeneralEmailAddress', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['name_with_address', 'contact_info', 'status', 'action'])
                ->make(true);
        }


        return view('customers.index', compact('display_subaccount'));
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

        $salesreps = User::where('IsSalesperson', 1)->get();
        $billingCustomers = Customer::all()->pluck('CustomerName', 'id');
        $customerCategories = CustomerCategory::all()->pluck('AccountType', 'id');
        $buyingGroups = BuyingGroup::all()->pluck('BuyingGroupName', 'id');
        $customerOrders = Order::where('CustomerID', $customer->acc_main)->get();
        $contacts = $customer->contacts;

        $displaySubAccount = $customer->company->getSetting('display_subaccount');

        if ($displaySubAccount == '1') {
            $balance_bf = ($customer->customerSubBalance->AgedBalance2 ?? '0.00') + ($customer->customerSubBalance->AgedBalance3 ?? '0.00') +
                ($customer->customerSubBalance->AgedBalance4 ?? '0.00') + ($customer->customerSubBalance->AgedBalance5 ?? '0.00') + ($customer->customerSubBalance->AgedBalance6 ?? '0.00');

            $overdue_balance = ($customer->customerSubBalance->AgedBalance3 ?? '0.00') +
                ($customer->customerSubBalance->AgedBalance4 ?? '0.00') + ($customer->customerSubBalance->AgedBalance5 ?? '0.00') + ($customer->customerSubBalance->AgedBalance6 ?? '0.00');

            $balance_total = ($customer->customerSubBalance->AgedBalance1 ?? '0.00') + ($customer->customerSubBalance->AgedBalance2 ?? '0.00') + ($customer->customerSubBalance->AgedBalance3 ?? '0.00') +
                ($customer->customerSubBalance->AgedBalance4 ?? '0.00') + ($customer->customerSubBalance->AgedBalance5 ?? '0.00') + ($customer->customerSubBalance->AgedBalance6 ?? '0.00');

            $customer->load('customerSubBalance', 'lastedited');

        } else {

            $balance_bf = ($customer->customerBalance->AgedBalance2 ?? '0.00') + ($customer->customerBalance->AgedBalance3 ?? '0.00') +
                ($customer->customerBalance->AgedBalance4 ?? '0.00') + ($customer->customerBalance->AgedBalance5 ?? '0.00') + ($customer->customerBalance->AgedBalance6 ?? '0.00');

            $overdue_balance = ($customer->customerBalance->AgedBalance3 ?? '0.00') +
                ($customer->customerBalance->AgedBalance4 ?? '0.00') + ($customer->customerBalance->AgedBalance5 ?? '0.00') + ($customer->customerBalance->AgedBalance6 ?? '0.00');

            $balance_total = ($customer->customerBalance->AgedBalance1 ?? '0.00') + ($customer->customerBalance->AgedBalance2 ?? '0.00') + ($customer->customerBalance->AgedBalance3 ?? '0.00') +
                ($customer->customerBalance->AgedBalance4 ?? '0.00') + ($customer->customerBalance->AgedBalance5 ?? '0.00') + ($customer->customerBalance->AgedBalance6 ?? '0.00');

            $customer->load('customerBalance', 'lastedited');
        }

        //echo "<pre>"; print_r($customer); die;
        return view('customers.show', compact('customer', 'customerOrders', 'salesreps','contacts',
            'billingCustomers', 'customerCategories', 'buyingGroups', 'balance_bf', 'overdue_balance', 'balance_total', 'displaySubAccount'));
        //return response()->json($customer->customerBalance());
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

        $salesreps = User::where('IsSalesperson', 1)->pluck('PreferredName', 'Repcode');
        $billingCustomers = Customer::all()->pluck('CustomerName', 'id');
        $customerCategories = CustomerCategory::all()->pluck('AccountType', 'id');
        $buyingGroups = BuyingGroup::all()->pluck('BuyingGroupName', 'id');

        $customer->load('salesrep', 'billingCustomer', 'customerCategory', 'buyingGroup');

        //echo "<pre>"; print_r($customer); die;
        return view('customers.edit', compact( 'customer', 'salesreps', 'billingCustomers','customerCategories', 'buyingGroups'));
        //return response()->json($salesreps);
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
        //echo "<pre>"; print_r($customer); die;
        $customer->update($request->all());
        $customer->billingCustomer()->sync($request->input('billingCustomer', []));
        $customer->customerCategory()->sync($request->input('customerCategory', []));
        $customer->buyingGroups()->sync($request->input('buyingGroups', []));
        $customer->salesrep()->sync($request->input('salesrep', []));

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


    /*public function importExcel(Request $request)
    {
        // Check user permission
        abort_if(Gate::denies('customer_import'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $filePath = $request->file_path;
        if (!$filePath || !Storage::exists($filePath)) {
            return back()->withErrors(['error' => 'Uploaded file not found.']);
        }

        ImportCustomersJob::dispatch($filePath);

        return response()->json(['message' => 'Import started successfully'], 200);
    }*/

    public function importExcel(Request $request)
    {
        // Check user permission
        abort_if(Gate::denies('customer_import'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {

            // Validate that the file exists
            Log::info("Import started");
            $filePath = $request->file_path;
            if (!$filePath || !Storage::exists($filePath)) {
                return back()->withErrors(['error' => 'Uploaded file not found.']);
            }
            Log::info("File exists at path: {$filePath}");

            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            Customer::truncate();

            // Import the file
            \Excel::import(new CustomerMasterImport, storage_path('app/'.$filePath));
            Log::info("Import completed");

            // Perform post-import SQL updates
            DB::statement('UPDATE customers SET acc_main = TRIM(acc_main)');
            DB::statement('UPDATE customers SET acc_main = LPAD(acc_main, 6, "0")');
            DB::statement('UPDATE customers SET acc_sub = "000" where acc_sub = "0"');
            DB::statement('UPDATE customers SET acc_code = CONCAT(acc_main, "-", acc_sub)');
            DB::statement('UPDATE customers SET BillToCustomerID = "9999" where BillToCustomerID is NULL');
            DB::statement('UPDATE customers SET BuyingGroupID = NULL where BuyingGroupID  = ""');
            //        DB::statement('UPDATE customers SET BuyingGroupID = "9999" where BuyingGroupID is NULL');
            DB::statement('UPDATE customers SET SalesRepID = "9999" where SalesRepID is NULL');

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            Storage::delete($filePath);

            return response()->json(['message' => 'File imported successfully'], 200);

        } catch (\Exception $e) {
            // Set error message
            //\Session::put('error', 'Error importing file: ' . $e->getMessage());
            Log::error("Import failed: " . $e->getMessage());

            return response()->json(['message' => 'Error importing file: ' . $e->getMessage()], 500);
        }

        //return back();
    }
}
