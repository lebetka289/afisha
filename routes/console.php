<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('notifications:event-reminders')->dailyAt('08:00');
Schedule::command('notifications:new-artist-events')->dailyAt('10:00');
Schedule::command('notifications:past-events')->dailyAt('01:00');
