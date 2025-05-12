<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Helpers\Features;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ecommerce\Update;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
class EcommerceSettingsController extends Controller
{
    public function index()
    {
        $currentCompany = auth()->user()->currentCompany();

        $settings = [
            'b2b_ecommerce_enabled' => CompanySetting::getSetting('b2b_ecommerce_enabled', $currentCompany->id),
            'ecommerce_guest_checkout' => CompanySetting::getSetting('ecommerce_guest_checkout', $currentCompany->id),
            'ecommerce_public_prices' => CompanySetting::getSetting('ecommerce_public_prices', $currentCompany->id),
            'ecommerce_show_product_images' => CompanySetting::getSetting('ecommerce_show_product_images', $currentCompany->id),
            'ecommerce_backorders' => CompanySetting::getSetting('ecommerce_backorders', $currentCompany->id),
            'ecommerce_require_approval' => CompanySetting::getSetting('ecommerce_require_approval', $currentCompany->id),
            'ecommerce_show_stock' => CompanySetting::getSetting('ecommerce_show_stock', $currentCompany->id),
            'ecommerce_min_order_amount' => CompanySetting::getSetting('ecommerce_min_order_amount', $currentCompany->id),
            'ecommerce_products_per_page' => CompanySetting::getSetting('ecommerce_products_per_page', $currentCompany->id),
            'ecommerce_new_customer_requires_approval' => CompanySetting::getSetting('ecommerce_new_customer_requires_approval', $currentCompany->id)
        ];

        return view('admin.settings.ecommerce.index', compact('settings'));
    }

    public function update(Update $request)
    {
        $currentCompany = auth()->user()->currentCompany();

        // Update settings
        $currentCompany->setSetting('b2b_ecommerce_enabled', $request->input('b2b_ecommerce_enabled'));
        $currentCompany->setSetting('ecommerce_guest_checkout',$request->input('ecommerce_guest_checkout'));
        $currentCompany->setSetting('ecommerce_public_prices', $request->input('ecommerce_public_prices'));
        $currentCompany->setSetting('ecommerce_show_product_images', $request->input('ecommerce_show_product_images'));
        $currentCompany->setSetting('ecommerce_backorders', $request->input('ecommerce_backorders'));
        $currentCompany->setSetting('ecommerce_require_approval', $request->input('ecommerce_require_approval'));
        $currentCompany->setSetting('ecommerce_show_stock', $request->input('ecommerce_show_stock'));
        $currentCompany->setSetting('ecommerce_min_order_amount', $request->input('ecommerce_min_order_amount'));
        $currentCompany->setSetting('ecommerce_products_per_page', $request->input('ecommerce_products_per_page'));
        $currentCompany->setSetting('ecommerce_new_customer_requires_approval', $request->input('ecommerce_new_customer_requires_approval'));


        foreach ($request->validated() as $key => $value) {
            $currentCompany->setSetting($key, $value);
        }

        // Clear cache after updating settings
        Features::clearCache($currentCompany->id);

        return redirect()->route('admin.settings.ecommerce.index')
            ->with('success', __('Settings updated successfully.'));
    }
}
