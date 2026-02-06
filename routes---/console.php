<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule Meta leads fetching every 30 minutes
Schedule::command('meta:fetch-leads')->everyThirtyMinutes();

// Schedule Meta leads pushing every hour
Schedule::command('meta:push-leads')->hourly();
