<?php

namespace App\Jobs;

use App\Models\KMR;
use App\Models\YMCOM;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncStockLimitsJob implements ShouldQueue
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
        $forecastData = KMR::query()
            ->selectRaw('TRIM(MPROD) AS partNumber, MRDTE AS dateRequired, SUM(MQTY) AS quantityRequired')
            ->groupBy('MPROD', 'MRDTE')
            ->get();

        $allChildren = collect();

        foreach ($forecastData as $data) {
            $children = YMCOM::getChildren(
                $data->PARTNUMBER,
                (float) $data->QUANTITYREQUIRED,
                $data->DATEREQUIRED,
            );

            $allChildren = $allChildren->merge($children);
        }

        $result = $allChildren
            ->groupBy(fn($item) => $item['part_number'] . '-' . $item['required_date'])
            ->map(function ($group) {
                return [
                    'part_number'       => $group->first()['part_number'],
                    'parent_part_number' => $group->first()['parent_part_number'],
                    'required_quantity' => $group->sum('required_quantity'),
                    'required_date'     => $group->first()['required_date'],
                ];
            })
            ->values();

        dd($result[0]);
    }
}
