<?php

namespace App\Http\Middleware;

use App\Models\UserCart;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoadUserCart
{
    /**
     * Handle the request to load the user's cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Don't restore cart if we just came from a successful checkout
        if ($request->session()->has('order_just_completed')) {
            $request->session()->forget('order_just_completed');
            return $next($request);
        }

        if (Auth::check()) {
            $user = Auth::user();
            $userCart = UserCart::where('user_id', $user->id)->first();

            if ($userCart) {
                if (Session::has('cart')) {
                    $sessionCart = Session::get('cart', []);
                    $dbCart = $userCart->cart_data;

                    $mergedCart = [];
                    $productIds = [];

                    foreach ($sessionCart as $item) {
                        $mergedCart[] = $item;
                        $productIds[] = $item['product_id'];
                    }

                    foreach ($dbCart as $item) {
                        if (!in_array($item['product_id'], $productIds)) {
                            $mergedCart[] = $item;
                        }
                    }

                    Session::put('cart', $mergedCart);

                    $userCart->update(['cart_data' => $mergedCart]);

                } else {
                    Session::put('cart', $userCart->cart_data);
                }
            } else if (Session::has('cart')) {

                UserCart::create([
                    'user_id' => $user->id,
                    'cart_data' => Session::get('cart', []),
                ]);
            }
        }
        return $next($request);
    }
}
