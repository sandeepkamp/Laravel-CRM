<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('leads:reminder')->everyMinute();
        $schedule->command('deals:reminder')->everyMinute();
        $schedule->command('send:expiry-reminders')->hourly();
        $schedule->command('notify:expiry')->daily();
        $schedule->command('seo:send-renewal-reminders')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}


