<?php

namespace App\Jobs;

use App\Models\Warehouse;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class StoreWarehouseJob implements ShouldQueue
{
    use Queueable;

    public $status;
    public $code;
    public $name;

    /**
     * Create a new job instance.
     */
    public function __construct($status, $code, $name)
    {
        $this->status = $status;
        $this->code = $code;
        $this->name = $name;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (str_starts_with($this->status, 'WZ')) {
            $status = 'INACTIVE';
        } else {
            $status = 'OPERATIONAL';
        }

        Warehouse::updateOrCreate(
            ['code' => $this->code],
            [
                'name' => $this->name,
                'status' => $status
            ]
        );
    }
}
