<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerCategory;
use Illuminate\Http\Request;
use App\Http\Requests\Settings\Customer\Store;
use App\Http\Requests\Settings\Customer\Update;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Gate;

class CustomerCategoryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*public function index()
    {
        abort_if(Gate::denies('customer_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $customerCategories = CustomerCategory::all();

        return view('admin.customerCategory.index', compact('customerCategories'));
    }*/

    /**
     * Create a new Customer Category.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('customer_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $customerCategory = new CustomerCategory();

        if (!empty($request->old())) {
            $customerCategory->fill($request->old());
        }
        return view('admin.settings.customer.category.create', compact('customerCategory'));
    }

    /**
     * Store the Customer Category in the Database.
     *
     * @param  \App\Http\Requests\Settings\Customer\Store  $request
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function store(Store $request)
    {
        $user = $request->user();
        $currentCompany = auth()->user()->currentCompany();

        //echo "<pre>"; print_r($request); die;
        $customerCategory = CustomerCategory::create([
            'company_id'           => $currentCompany->id,
            'AccountType'          => $request->accountType,
            'CustomerCategoryName' => $request->customerCategoryname,
            'LastEditedBy'         => auth()->user()->id,
            'created_at'           => Carbon::now(),
        ]);

        session()->flash('alert-success', __('global.customer_category_added'));
        return redirect()->route('settings.customer');
    }
}
