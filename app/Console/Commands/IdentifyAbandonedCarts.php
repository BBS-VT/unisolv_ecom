<?php

namespace App\Console\Commands;

use App\Models\UserCart;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class IdentifyAbandonedCarts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carts:identify-abandoned';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Identify abandoned carts and send reminders';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Find carts that were last updated between 24-48 hours ago
        $abandonedCarts = UserCart::where('updated_at', '<=', Carbon::now()->subHours(24))
            ->where('updated_at', '>=', Carbon::now()->subHours(48))
            ->get();

        $this->info("Found {$abandonedCarts->count()} abandoned carts");

        foreach ($abandonedCarts as $cart) {
            // Check if cart has items
            if (empty($cart->cart_data)) {
                continue;
            }

            $user = User::find($cart->user_id);

            if ($user) {
                // Send an email reminder
                // TODO: implement sending email to user
                $this->info("Sending reminder to {$user->email}");

                // TODO:
                // Mail::to($user)->send(new AbandonedCartReminder($user, $cart));
            }
        }

        return Command::SUCCESS;
    }
}
