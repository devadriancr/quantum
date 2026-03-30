<?php

namespace App\Jobs;

use App\Models\ILM;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncLocationJob implements ShouldQueue
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
        $locations = ILM::query()
            ->select('WID AS status', 'WWHS AS warehouse', 'WLOC AS code', 'WDESC AS name')
            ->orderBy('WWHS', 'ASC')
            ->orderBy('WLOC', 'ASC')
            ->get();

        foreach ($locations as $location) {
            StoreLocationJob::dispatch($location->status, $location->warehouse, $location->code, $location->name);
        }
    }
}
