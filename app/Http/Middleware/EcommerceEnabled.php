<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\Features;
class EcommerceEnabled
{
    public function handle(Request $request, Closure $next)
    {
        if (!Features::ecommerceEnabled()) {
            abort(404, 'E-commerce feature is not enabled.');
        }

        return $next($request);
    }
}
