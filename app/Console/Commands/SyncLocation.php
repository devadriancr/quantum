<?php

namespace App\Console\Commands;

use App\Jobs\SyncLocationJob;
use Illuminate\Console\Command;

class SyncLocation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicia el proceso de sincronización de ubicaciones';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching Location synchronization job...');

        SyncLocationJob::dispatch();

        $this->info('The process has been started in the background.');
    }
}
