<?php

namespace App\Console\Commands;

use App\Jobs\SyncProjectJob;
use Illuminate\Console\Command;

class SyncProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:projects';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicia el proceso de sincronización de proyectos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching Project synchronization job...');

        SyncProjectJob::dispatch();

        $this->info('The process has been started in the background.');
    }
}
