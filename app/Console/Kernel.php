<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Keep the last 7 days of database sessions; drop older rows and expired cache.
        $schedule->command('sessions:prune')->daily();

        // Clear leftover file sessions, logs, compiled views, and file cache
        // left behind by the old file drivers. New sessions/cache are in the database.
        $schedule->command('storage:purge --all')->daily();
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
