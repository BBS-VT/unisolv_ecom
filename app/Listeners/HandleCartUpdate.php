<?php

namespace App\Listeners;

use App\Events\CartUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandleCartUpdate
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\CartUpdated  $event
     * @return void
     */
    public function handle(CartUpdated $event)
    {
        Log::info('Cart updated', [
            'user_id' => $event->userId,
            'action' => $event->action,
            'cart_items' => count($event->cartData),
        ]);
    }
}
