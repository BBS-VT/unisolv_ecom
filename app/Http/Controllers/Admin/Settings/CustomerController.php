<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\BuyingGroup;
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
        //$buyingGroups = BuyingGroup::findByCompany($currentCompany->id)->get();
        $buyingGroups = BuyingGroup::paginate();

        return view('admin.settings.customer.index', compact('currentCompany', 'customerCategories', 'buyingGroups'));
    }

    /**
     * Update the Product Settings
     *
     * @param \App\Http\Requests\Settings\Customer\Update $request
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function update(Update $request)
    {
        $user = $request->user();
        $currentCompany = $user->currentCompany();

        // Update each setting in the database
        foreach ($request->validated() as $key => $value) {
            $currentCompany->setSetting($key, $value);
        }

        session()->flash('alert-success', __('global.customer_settings_updated'));
        return redirect()->route('settings.customer');
    }



}
