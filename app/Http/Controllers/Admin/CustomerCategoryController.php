<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerCategory;
use App\Http\Requests\MassDestroyCustomerCategoryRequest;
use App\Http\Requests\StoreCustomerCategoryRequest;
use App\Http\Requests\UpdateCustomerCategoryRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerCategoryController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('customer_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $customerCategories = CustomerCategory::all();

        return view('admin.customerCategory.index', compact('customerCategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('customer_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.customerCategory.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomerCategoryRequest $request)
    {
        $customerCategory = CustomerCategory::create($request->all());

        return redirect()->route('admin.customer-category.index');
    }
}
