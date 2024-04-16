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
        $schedule->command('inventory:begin')->timezone('Asia/Manila')->everyMinute()->withoutOverlapping();
        $schedule->command('inventory:merchandise')->timezone('Asia/Manila')->everyMinute()->withoutOverlapping();
        $schedule->command('inventory:supplies')->timezone('Asia/Manila')->everyMinute()->withoutOverlapping();
        $schedule->command('check:levels')->timezone('Asia/Manila')->twiceDaily(7, 13)->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
