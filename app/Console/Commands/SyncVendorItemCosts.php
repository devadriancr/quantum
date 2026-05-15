<?php

namespace App\Console\Commands;

use App\Jobs\SyncVendorItemCostsJob;
use Illuminate\Console\Command;

class SyncVendorItemCosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:vendor-item-costs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts the vendor item costs synchronization process';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching Vendor Item Costs synchronization job...');

        SyncVendorItemCostsJob::dispatch();

        $this->info('The process has been started in the background.');
    }
}
