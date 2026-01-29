<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule security scan to run daily at 3 AM
Schedule::command('security:scan')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->emailOutputOnFailure(env('ADMIN_EMAIL'));
