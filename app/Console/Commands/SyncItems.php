<?php

namespace App\Console\Commands;

use App\Jobs\SyncItemJob;
use Illuminate\Console\Command;

class SyncItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicia el proceso de sincronización de productos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching Item synchronization job...');

        SyncItemJob::dispatch();

        $this->info('The process has been started in the background.');
    }
}
