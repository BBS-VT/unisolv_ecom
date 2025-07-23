<?php

namespace App\Providers;

use App\Helpers\Features;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $cart = Session::get('cart', []);
            $cartCount = array_sum(array_column($cart, 'quantity'));
            $cartTotal = 0;

            if (Features::publicPricesEnabled()) {
                foreach ($cart as $item) {
                    $cartTotal += $item['price'] * $item['quantity'];
                }
            }

            $view->with([
                'cartCount' => $cartCount,
                'cartTotal' => $cartTotal,
            ]);
        });

        $this->app['events']->listen('auth.login', function ($user) {
            $this->mergeGuestCartWithUserCart($user);
        });
    }
    /**
     * Merge guest cart with user cart on login
     *
     * @param $user
     */
    private function mergeGuestCartWithUserCart($user)
    {
        // This is now handled by LoadUserCart middleware
    }
}
