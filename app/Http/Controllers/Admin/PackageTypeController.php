<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PackageType;
use Illuminate\Http\Request;
use App\Http\Requests\Settings\PackageType\Store;
use App\Http\Requests\Settings\PackageType\Update;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class PackageTypeController extends Controller
{
    /**
     * Create a new Package Type
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        abort_if(Gate::denies('settings_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product_unit = new PackageType();

        // Fill model with old input
        if (!empty($request->old())) {
            $product_unit->fill($request->old());
        }

        return view('admin.settings.product.unit.create', compact('product_unit'));
    }

    /**
     * Store the Package Type in the Database
     *
     * @param \App\Http\Requests\Settings\PackageType\Store $request
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function store(Store $request)
    {
        $user = $request->user();
        $currentCompany = auth()->user()->currentCompany();

        // Create Product Unit and Store in Database
        PackageType::create([
            'PackageTypeName' => $request->name,
            'company_id'      => $currentCompany->id,
            'LastEditedBy'    => auth()->user()->id,
        ]);

        session()->flash('alert-success', __('global.package_type_added'));
        return redirect()->route('settings.product');
    }

    /**
     * Display the Form for Editing Package Type
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        abort_if(Gate::denies('settings_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product_unit = PackageType::findOrFail($request->product_unit);

        return view('admin.settings.product.unit.edit', compact('product_unit'));
    }

    /**
     * Update the Product Unit
     *
     * @param \App\Http\Requests\Settings\PackageType\Update $request
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function update(Update $request)
    {
        $product_unit = PackageType::findOrFail($request->product_unit);

        // Update Package Type in the Database
        $product_unit->update([
            'PackageTypeName' => $request->name
        ]);

        session()->flash('alert-success', __('global.product_unit_updated'));
        return redirect()->route('settings.product');
    }

    /**
     * Delete the Product Unit
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        abort_if(Gate::denies('settings_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product_unit = PackageType::findOrFail($request->product_unit);

        // Delete Package Type from the Database
        $product_unit->delete();

        session()->flash('alert-success', __('global.product_unit_deleted'));
        return redirect()->route('settings.product');
    }
}
