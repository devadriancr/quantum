<?php

namespace App\Console\Commands;

use App\Jobs\SyncTransactionTypeJob;
use Illuminate\Console\Command;

class SyncTransactionType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:transaction-type';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicia el proceso de sincronización de tipo de transacción';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching Transaction Types synchronization job...');

        SyncTransactionTypeJob::dispatch();

        $this->info('The process has been started in the background.');
    }
}
