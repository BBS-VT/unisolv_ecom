<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Account\Update;
use Illuminate\Support\Facades\Hash;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    /**
     * Display Account Settings Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.settings.account.index');
    }

    /**
     * Update Current Authenticated User's account
     *
     * @param \App\Http\Requests\Settings\Account\Update $request
     *
     * @param \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function update(Update $request)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Update User
        $user = $request->user();
        $validated = $request->validated();
        unset($validated['password']);
        $user->update($validated);

        // If Password fields are filled
        if ($request->old_password && $request->new_password) {
            $user->password = Hash::make($request->new_password);
            $user->save();
        }

        // Upload and save avatar
        if ($request->avatar) {
            $request->validate(['avatar' => 'required|image|mimes:png,jpg|max:2048']);
            $path = $request->avatar->storeAs('avatars', 'avatar-'. $user->id .'.'.$request->avatar->getClientOriginalExtension(), 'public_dir');
            $user->setSetting('avatar', '/uploads/'.$path);
        }

        session()->flash('alert-success', __('global.account_updated'));
        return redirect()->route('settings.account');
    }

}
