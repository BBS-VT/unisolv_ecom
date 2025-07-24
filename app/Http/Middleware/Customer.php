<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Features;

class Customer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->IsCustomer == 1) {

            if (Features::ecommerceEnabled() && $request->has('redirect_to_shop')) {
                return redirect()->route('shop.home');
            }

            $customerId = Auth::user()->customer->id ?? null;

            return redirect()->route('customer_portal.dashboard', ['customer' => $customerId]);

        }

        return redirect('/home')->with('error', 'You must be a Customer to access this page.');
    }
}
