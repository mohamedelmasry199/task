<?php

namespace App\Console;

use App\Console\Commands\ExpirationTask;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('user:expiration-task')->everyMinute();
        $schedule->command('auto:birthdaywith')->everyMinute();


    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        ExpirationTask::class;


        require base_path('routes/console.php');
    }
}
