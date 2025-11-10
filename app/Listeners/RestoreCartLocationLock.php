<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\CartService;

class RestoreCartLocationLock
{
    public function handle(Login $event)
    {
        CartService::ensureLocationLock();
    }
}
