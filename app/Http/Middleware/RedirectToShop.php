<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Features;
class RedirectToShop
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('redirect_to_shop')) {
            session()->put('redirect_to_shop', true);
        }

        return $next($request);
    }

}
