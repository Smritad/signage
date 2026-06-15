<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 | Keep shipment/courier statuses fresh in the background so the order lists
 | (Prepaid + COD) show up-to-date status on refresh without per-page API calls.
 | Requires a server cron running every minute:  php artisan schedule:run
 */
Schedule::command('shiprocket:update-status')
    ->hourly()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/shiprocket-cron.log'));
