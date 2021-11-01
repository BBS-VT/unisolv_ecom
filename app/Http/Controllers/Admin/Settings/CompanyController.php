<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Company\Update;
use App\Models\CompanySetting;
use Gate;
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

        return view('admin.settings.company.index');
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
        abort_if(Gate::denies('settings_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = $request->user();
        $currentCompany = $user->currentCompany();

        // Update Company
        $currentCompany->update($request->validated());

        // Update Company Address
        // $address = $request->input('billing');
        // $address['name'] = $currentCompany->name;
        // $currentCompany->updateAddress('billing', $address);

        // Update Company Logo
        if ($request->avatar) {
            $request->validate(['avatar' => 'required|image|mimes:png,jpg|max:2048']);
            $path = $request->avatar->storeAs('company_logos', 'logo-'. $currentCompany->id .'.'.$request->avatar->getClientOriginalExtension(), 'public_dir');
            CompanySetting::setSetting('avatar', '/uploads/'.$path, $currentCompany->id);
        }

        session()->flash('alert-success', __('global.company_updated'));
        return redirect()->route('settings.company');
    }
}
