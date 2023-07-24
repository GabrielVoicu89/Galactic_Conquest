<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected $commands = [
        Commands\UpdateOreResources::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('update:ore')->everyTenSeconds();
        // $schedule->call(function () {
        //     Artisan::call('update:ore');
        //     Log::info('Scheduled task executed at: ' . now());
        // })->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
