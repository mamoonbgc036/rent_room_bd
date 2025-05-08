<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;

class Scheduler
{
    public function schedule(Schedule $schedule): void
    {
        // Run auto-renewal check hourly
        $schedule->command('bookings:process-renewals')
            ->hourly()
            ->withoutOverlapping()
            ->emailOutputOnFailure(config('mail.admin_email'));
    }
}
