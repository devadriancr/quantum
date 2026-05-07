<?php

namespace App\Jobs;

use App\Jobs\StoreStockLimitsJob;
use App\Models\KMR;
use App\Models\YMCOM;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncStockLimitsJob implements ShouldQueue
{
    use Queueable;

    public function __construct() {}

    public function handle(): void
    {
        $startDate = Carbon::now()->startOfWeek()->format('Ymd');
        $endDate   = Carbon::now()->addWeek()->endOfWeek()->format('Ymd');

        $forecastData = KMR::query()
            ->selectRaw('TRIM(K.MPROD) AS PARTNUMBER, I.ICLAS AS ITEMCLASS, K.MRDTE AS DATEREQUIRED, SUM(K.MQTY) AS QUANTITYREQUIRED')
            ->from('LX834F01.KMR AS K')
            ->join('LX834F01.IIM AS I', 'K.MPROD', '=', 'I.IPROD')
            ->where('I.ICLAS', 'F1')
            ->where('K.MRDTE', '>=', $startDate)
            ->where('K.MRDTE', '<=', $endDate)
            ->groupBy('K.MPROD', 'K.MRDTE', 'I.ICLAS')
            ->get();

        $allChildren = collect();

        foreach ($forecastData as $data) {
            $allChildren = $allChildren->merge(
                YMCOM::getChildren($data->PARTNUMBER, (float) $data->QUANTITYREQUIRED, (string) $data->DATEREQUIRED, 'S1')
            );
        }

        $result = $allChildren
            ->groupBy('part_number')
            ->map(function ($partGroup, $partNumber) {
                $byDate = $partGroup
                    ->groupBy('required_date')
                    ->map(fn ($dateGroup) => $dateGroup->sum('required_quantity'));

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

        foreach ($result as $data) {
            StoreStockLimitsJob::dispatch(
                $data['part_number'],
                $data['stock_min'],
                $data['stock_max']
            );
        }
    }
}
