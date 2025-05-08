<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


// Schedule the command
Artisan::command('schedule:run', function () {
    // Replace this with your preferred scheduling logic
    $this->call('packages:update-expired');
})->hourly();
