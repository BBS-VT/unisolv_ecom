<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\CustomerCategory;
use Illuminate\Http\Request;
use App\Http\Requests\Settings\Customer\Update;

class CustomerController extends Controller
{
    /**
     * Display Customer Settings Page
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $currentCompany = $user->currentCompany();

        $customerCategories = CustomerCategory::findByCompany($currentCompany->id)->paginate(5);

        return view('admin.settings.customer.index', compact('currentCompany', 'customerCategories'));
    }



}
