<?php


namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $redirect = config('fortify.home');

        $authUser = auth()->user();

        if($authUser->IsSalesperson == '1') {
            $redirect = route('sales.dashboard');
        } elseif($authUser->IsCustomer == '1') {
            $redirect = route('customers.dashboard');
        }

        return redirect($redirect);

        //return $request->wantsJson()
        //    ? response()->json(['two_factor' => false])
        //    : redirect()->intended(config('fortify.home'));

    }
}
