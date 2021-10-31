<?php


namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        //$home = auth()->user()->roles('Sales Rep') ? '/sales' : '/dashboard';
        $home = Auth::user()->roles('Admin') ? '/dashboard' : '/sales';

        return redirect()->intended($home);

    }
}
