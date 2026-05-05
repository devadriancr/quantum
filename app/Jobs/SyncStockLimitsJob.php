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
        $startDate = Carbon::now()->subWeek()->startOfWeek()->format('Ymd');
        $endDate   = Carbon::now()->endOfWeek()->format('Ymd');

        $forecastData = KMR::query()
            ->selectRaw('TRIM(MPROD) AS partNumber, MRDTE AS dateRequired, SUM(MQTY) AS quantityRequired')
            ->where('MRDTE', '>=', $startDate)
            ->where('MRDTE', '<=', $endDate)
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
            ->groupBy('part_number')
            ->map(function ($partGroup, $partNumber) {

                $byDate = $partGroup
                    ->groupBy('required_date')
                    ->map(fn($dateGroup) => $dateGroup->sum('required_quantity'));

                $totalDays     = $byDate->count();
                $totalQuantity = $byDate->sum();

                $dailyAverage = $totalDays > 0
                    ? round($totalQuantity / $totalDays, 4)
                    : 0;

                return [
                    'part_number'        => $partNumber,
                    'parent_part_number' => $partGroup->first()['parent_part_number'],
                    'total_quantity'     => $totalQuantity,
                    'total_days'         => $totalDays,
                    'daily_average'      => $dailyAverage,
                    'stock_min'          => ceil($dailyAverage * 2),
                    'stock_max'          => ceil($dailyAverage * 5),
                ];
            })
            ->values();

        foreach ($result as $data){
            StoreStockLimitsJob::dispatch(
                $data['part_number'],
                $data['stock_min'],
                $data['stock_max']
            );
        }
    }
}
