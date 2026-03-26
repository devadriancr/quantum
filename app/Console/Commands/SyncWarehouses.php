<?php

namespace App\Console\Commands;

use App\Jobs\SyncWarehousesJob;
use Illuminate\Console\Command;

class SyncWarehouses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:warehouses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicia el proceso de sincronización de almacenes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching Warehouses synchronization job...');

        SyncWarehousesJob::dispatch();

        $this->info('The process has been started in the background.');
    }
}
