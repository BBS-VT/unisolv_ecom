<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Preference\Update;
use Illuminate\Support\Facades\Auth;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class PreferenceController extends Controller
{
    /**
     * Display Preferences Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('settings_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = Auth::user();
        $currentCompany = $user->currentCompany();

        return view('admin.settings.preference.index', compact('currentCompany'));
    }

    /**
     * Update the Preferences
     *
     * @param \App\Http\Requests\Settings\Preference\Update $request
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

        session()->flash('alert-success', __('global.preferences_updated'));
        return redirect()->route('settings.preferences');
    }
}
