<?php


namespace App\Http\Responses;

use App\Helpers\Features;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $redirect = config('fortify.home');
        $authUser = auth()->user();

        if ($authUser->IsCustomer == "1"  && Features::ecommerceEnabled() && $request->has('redirect_to_shop')) {
            return redirect()->route('shop.home');
        }

        if($authUser->IsSalesperson == '1' && $authUser->hasRole('Sales Rep')) {
            $redirect = route('sales.dashboard');
        } elseif($authUser->IsCustomer == '1') {
            $redirect = route('customers.dashboard');
        }

        return redirect($redirect);
        //return redirect()->route('dashboard');

    }
}
