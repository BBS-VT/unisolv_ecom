<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\PackageType;
use Illuminate\Http\Request;
use App\Http\Requests\Settings\Product\Update;

class ProductController extends Controller
{
    /**
     * Display Product Settings Page
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $currentCompany = $user->currentCompany();

        // Get Product Units by Company
        $product_units = PackageType::findByCompany($currentCompany->id)->paginate(5);
        $locations = Location::orderBy('SortOrder')
            ->orderBy('LocationCode')
            ->get();

        return view('admin.settings.product.index', compact('product_units', 'currentCompany', 'locations'));
    }

    /**
     * Update the Product Settings
     *
     * @param \App\Http\Requests\Settings\Product\Update $request
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

        session()->flash('alert-success', __('global.product_settings_updated'));
        return redirect()->route('settings.product');
    }
}
