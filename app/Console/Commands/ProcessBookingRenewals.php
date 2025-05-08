<?php

namespace App\Console\Commands;

use App\Services\AutoRenewalService;
use Illuminate\Console\Command;

class ProcessBookingRenewals extends Command
{
    protected $signature = 'bookings:process-renewals';
    protected $description = 'Process auto-renewals for eligible bookings';

    public function handle(AutoRenewalService $renewalService): int
    {
        $this->info('Starting auto-renewal processing...');

        try {
            $renewalService->checkAndProcessRenewals();
            $this->info('Auto-renewal processing completed successfully.');
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error processing auto-renewals: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
