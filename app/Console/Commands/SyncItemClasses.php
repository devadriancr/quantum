<?php

namespace App\Console\Commands;

use App\Jobs\SyncItemClassesJob;
use Illuminate\Console\Command;

class SyncItemClasses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:item-classes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicia el proceso de sincronización de clases de artículos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Despachando el job de sincronización...');
        SyncItemClassesJob::dispatch();
        $this->info('Proceso iniciado en segundo plano.');
    }
}
