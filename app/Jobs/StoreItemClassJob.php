<?php

namespace App\Jobs;

use App\Models\ItemClass;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class StoreItemClassJob implements ShouldQueue
{
    use Queueable;

    public $code;
    public $name;

    /**
     * Create a new job instance.
     */
    public function __construct($code, $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ItemClass::updateOrCreate(
            ['code' => $this->code],
            ['name' => $this->name]
        );
    }
}
