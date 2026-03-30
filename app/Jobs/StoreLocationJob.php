<?php

namespace App\Jobs;

use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class StoreLocationJob implements ShouldQueue
{
    use Queueable;

    public $status;
    public $warehouseCode;
    public $code;
    public $name;

    /**
     * Create a new job instance.
     */
    public function __construct($status, $warehouseCode, $code, $name)
    {
        $this->status = $status;
        $this->warehouseCode = $warehouseCode;
        $this->code = $code;
        $this->name = $name;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (str_starts_with($this->status, 'WZ')) {
            $status = 'UNAVAILABLE';
        } else {
            $status = 'AVAILABLE';
        }
        $warehouse = Warehouse::query()->where('code', $this->warehouseCode)->first();

        Location::updateOrCreate(
            ['code' => $this->code],
            [
                'name' => $this->name,
                'status' => $status,
                'warehouse_id' => $warehouse ? $warehouse->id : null
            ]
        );
    }
}
