<?php

namespace App\Console\Commands;

use App\Jobs\SyncStockLimitsJob;
use Illuminate\Console\Command;

class SyncStockLimits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:stock-limits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicia el proceso de sincronización de límites de stock';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching Stock Limits synchronization job...');

        SyncStockLimitsJob::dispatch();

        $this->info('The process has been started in the background.');
    }
}
