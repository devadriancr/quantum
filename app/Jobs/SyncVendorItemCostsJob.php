<?php

namespace App\Jobs;

use App\Models\YCS2;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncVendorItemCostsJob implements ShouldQueue
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
        $vendorCostRecords = YCS2::query()
            ->from('LX834FU01.YCS2 as Y2')
            ->join('LX834F01.IIM as I', 'Y2.Y2ITEM', '=', 'I.IPROD')
            ->selectRaw("
                TRIM(I.IPROD) as item_number,
                I.ICLAS as item_class,
                Y2.Y2VEND as vendor_number,
                Y2.Y2CURR as currency_code,
                Y2.Y2SDTE as starting_date,
                Y2.Y2EDTE as end_date,
                Y2.Y2CSTT as total_cost,
                TRIM(I.IMPLC) as lifecycle
            ")
            // ->where('Y2.Y2EDTE', '99999999')
            ->whereRaw("TRIM(I.IMPLC) != 'OBSOLETE'")
            // ->whereRaw("TRIM(I.ICLAS) = 'S1'")
            ->orderByDesc('Y2.Y2SDTE')
            ->get();

        $uniqueRecords = $vendorCostRecords->unique(
            fn($r) => trim($r->ITEM_NUMBER) . '|' . trim($r->VENDOR_NUMBER) . '|' . trim($r->STARTING_DATE)
        );

        foreach ($uniqueRecords as $record) {
            StoreVendorItemCostsJob::dispatch(
                trim($record->ITEM_NUMBER),
                trim($record->VENDOR_NUMBER),
                trim($record->CURRENCY_CODE),
                trim($record->STARTING_DATE),
                trim($record->END_DATE),
                trim($record->TOTAL_COST),
            );
        }
    }
}
