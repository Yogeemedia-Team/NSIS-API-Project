<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
            // Monthly fee calculation
        $schedule->call(function () {
            Artisan::call('fees:monthly-calculation');
        })->monthlyOn(31, '00:00');

        // Invoice generation
        $schedule->call(function () {
            Artisan::call('fees:invoice-generate');
        })->monthlyOn(31, '00:05');
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
