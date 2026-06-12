<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     */
    protected $commands = [
    \App\Console\Commands\UpdateShiprocketStatus::class,
];

protected function schedule(Schedule $schedule)
{
    /* Run every 6 hours — tracks shipments + checks COD remittance */
    $schedule->command('shiprocket:update-status')
             ->everySixHours()
             ->withoutOverlapping()
             ->appendOutputTo(storage_path('logs/shiprocket-cron.log'));
 
    /* Alternative schedules (pick whichever fits your needs):
     *
     * Every hour:
     *   $schedule->command('shiprocket:update-status')->hourly();
     *
     * Twice a day (9 AM + 9 PM):
     *   $schedule->command('shiprocket:update-status')->twiceDaily(9, 21);
     *
     * Once a day at 2 AM:
     *   $schedule->command('shiprocket:update-status')->dailyAt('02:00');
     */
}
}
