<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Company\Update;
use App\Models\Company;
use App\Models\CompanySetting;
use Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends Controller
{
    /**
     * Display Company Settings Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('settings_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = Auth::user();
        $currentCompany = $user->currentCompany();

        return view('admin.settings.company.index', compact('currentCompany'));
    }

    /**
     * Update the Company
     *
     * @param \App\Http\Requests\Application\Settings\Company\Update $request
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function update(Update $request)
    {
        abort_if(Gate::denies('settings_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = $request->user();
        $currentCompany = $user->currentCompany();

        // Update Company
        $currentCompany->update($request->validated());

        // Update Company Address
        $address = $request->input('billing');
        $address['name'] = $currentCompany->name;
        $currentCompany->updateAddress('billing', $address);

        // Update Company Logo
        if ($request->avatar) {
            $request->validate(['avatar' => 'required|image|mimes:png,jpg|max:2048']);
            $path = $request->avatar->storeAs('company_logos', 'logo-'. $currentCompany->id .'.'.$request->avatar->getClientOriginalExtension(), 'public_dir');
            CompanySetting::setSetting('avatar', '/uploads/'.$path, $currentCompany->id);
        }

        session()->flash('alert-success', __('global.company_updated'));
        return redirect()->route('settings.company');
    }

    public function updateCollectionAddress(Request $request)
    {
        $user = Auth::user();
        $currentCompany = $user->currentCompany();
        $company = Company::find($currentCompany->id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address_1' => 'required|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'country_id' => 'required|exists:countries,id',
        ]);

        $company->updateAddress('collection', $validated);

        return redirect()->back()->with('success', 'Collection address updated successfully.');

    }
}
