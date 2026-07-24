<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Generate default task setiap hari jam 08:00
Schedule::command('tasks:generate-daily')->dailyAt('08:00');

// Mark pending tasks as not done setiap hari jam 21:00
Schedule::command('tasks:mark-not-done')->dailyAt('21:00');