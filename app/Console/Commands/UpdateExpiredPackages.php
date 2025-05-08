<?php

namespace App\Console\Commands;

use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateExpiredPackages extends Command
{
    protected $signature = 'packages:update-expired';
    protected $description = 'Update package statuses to expired if their expiration date has passed';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $today = Carbon::today();
        Package::where('expiration_date', '<', $today)
               ->where('status', '<>', 'expired')
               ->update(['status' => 'expired']);

        $this->info('Expired packages have been updated.');
    }
}
