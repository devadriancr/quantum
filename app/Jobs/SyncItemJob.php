<?php

namespace App\Jobs;

use App\Models\IIM;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncItemJob implements ShouldQueue
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
        $items = IIM::query()
            ->select('IPROD AS partNumber', 'IDESC AS partName', 'IUMS AS measurementUnit','IITYP AS itemType', 'ICLAS AS itemClass', 'IREF04 AS project', 'IMPLC AS isObsolete', 'IMSPKT AS standardPack', 'IMBOXQ AS quantityStandardPack')
            // ->where('IMPLC', 'LIKE', 'OBSOLETE  ')
            ->get();

        foreach ($items as $item) {
            StoreItemJob::dispatch(
                preg_replace('/[^a-zA-Z0-9\/\-\s]/', '', trim($item->partNumber)),
                preg_replace('/[^a-zA-Z0-9\/\-\s]/', '', trim($item->partName)),
                trim($item->measurementUnit),
                trim($item->itemType),
                trim($item->itemClass),
                trim($item->project),
                trim($item->isObsolete),
                trim($item->standardPack),
                trim($item->quantityStandardPack)
            );
        }
    }
}
