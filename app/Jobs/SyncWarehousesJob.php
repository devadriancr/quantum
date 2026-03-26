<?php

namespace App\Jobs;

use App\Models\IWM;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncWarehousesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $warehouses = IWM::query()
            ->select('LID AS status', 'LWHS AS code', 'LDESC AS name')
            ->orderBy('LWHS', 'ASC')
            ->get();

        foreach ($warehouses as $warehouse) {
            StoreWarehouseJob::dispatch(
                trim($warehouse->status),
                trim($warehouse->code),
                trim($warehouse->name)
            );
        }
    }
}
