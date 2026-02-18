<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule payment reminders
Schedule::command('payments:send-reminders')->dailyAt('09:00');

// Generate session occurrences for next 30 days
Schedule::command('sessions:generate --days=30')->dailyAt('00:00');
