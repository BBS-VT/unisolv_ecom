<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\ImportPromotionsCommand::class,
        \App\Console\Commands\CleanupExpiredPromotionsCommand::class,
        \App\Console\Commands\UpdatePromotionStatusCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('cleanup:temp-uploads')->daily();
        $schedule->command('carts:identify-abandoned')->dailyAt('10:00');

        // Update promotion statuses every hour
        $schedule->command('promotions:update-status')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground();

        // Cleanup old expired promotions weekly
        $schedule->command('promotions:cleanup --days=90')
            ->weekly()
            ->sundays()
            ->at('02:00')
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
