<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Requests\Settings\Order\Update;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class OrderController
{
    public function index()
    {
        abort_if(Gate::denies('settings_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = Auth::user();
        $currentCompany = $user->currentCompany();

        return view('admin.settings.order.index', compact('currentCompany'));
    }

    /**
     * Update the Preferences
     *
     * @param \App\Http\Requests\Settings\Order\Update $request
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function update(Update $request)
    {
        $user = $request->user();
        $currentCompany = $user->currentCompany();

        $currentCompany->setSetting('fulfillment_mailbox', $request->input('fulfillment_mailbox'));

        // Update each setting in the database
        foreach ($request->validated() as $key => $value) {
            $currentCompany->setSetting($key, $value);
        }

        session()->flash('alert-success', __('global.preferences_updated'));
        return redirect()->route('settings.order');
    }

}
