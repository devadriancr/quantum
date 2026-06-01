<?php

namespace App\Jobs;

use App\Jobs\StoreStockLimitsJob;
use App\Models\ECL;
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
        $startDate = Carbon::now()->startOfMonth()->format('Ymd');
        $endDate   = Carbon::now()->endOfMonth()->format('Ymd');

        $firmData = ECL::query()
            ->selectRaw("
                TRIM(I.IPROD) AS ITEMNUMBER,
                E.LSDTE AS DATEREQUIRED,
                SUM(E.LQORD) AS QUANTITYREQUIRED
            ")
            ->from('LX834F01.ECL AS E')
            ->join('LX834F01.IIM AS I', 'E.LPROD', '=', 'I.IPROD')
            ->join('LX834F01.FRT AS F', 'E.LPROD', '=', 'F.RPROD')
            ->where('I.ICLAS', '=', 'F1')
            ->whereRaw("TRIM(I.IMPLC) != 'OBSOLETE'")
            ->where('F.RDDDT', '=', '99999999')
            ->where('E.LSDTE', '>=', $startDate)
            ->where('E.LSDTE', '<=', $endDate)
            ->groupByRaw('I.IPROD, E.LSDTE')
            ->orderByRaw('E.LSDTE, I.IPROD')
            ->get();

        $forecastData = KMR::query()
            ->selectRaw("
                TRIM(I.IPROD) AS ITEMNUMBER,
                SUBSTR(K.MRDTE, 1, 4) || SUBSTR(TRIM(K.MRCNO), 1, 4) AS DATEREQUIRED,
                SUM(K.MQTY) AS QUANTITYREQUIRED
            ")
            ->from('LX834F01.KMR AS K')
            ->join('LX834F01.IIM AS I', 'K.MPROD', '=', 'I.IPROD')
            ->join('LX834F01.FRT AS F', 'K.MPROD', '=', 'F.RPROD')
            ->where('I.ICLAS', '=', 'F1')
            ->whereRaw("TRIM(I.IMPLC) != 'OBSOLETE'")
            ->where('F.RDDDT', '=', '99999999')
            ->whereRaw("SUBSTR(K.MRDTE, 1, 4) || SUBSTR(TRIM(K.MRCNO), 1, 4) >= ?", [$startDate])
            ->whereRaw("SUBSTR(K.MRDTE, 1, 4) || SUBSTR(TRIM(K.MRCNO), 1, 4) <= ?", [$endDate])
            ->groupByRaw("TRIM(I.IPROD), SUBSTR(K.MRDTE, 1, 4) || SUBSTR(TRIM(K.MRCNO), 1, 4)")
            ->orderByRaw("DATEREQUIRED, TRIM(I.IPROD)")
            ->get();

        // 1. Indexar $firmData por clave única ITEMNUMBER + DATEREQUIRED
        $firmIndexed = $firmData->keyBy(function ($item) {
            return $item->ITEMNUMBER . '_' . $item->DATEREQUIRED;
        });

        // 2. Recorrer $forecastData y agregar solo los que NO existen en $firmData
        $forecastData->each(function ($item) use ($firmIndexed) {
            $key = $item->ITEMNUMBER . '_' . $item->DATEREQUIRED;
            if (!$firmIndexed->has($key)) {
                $firmIndexed->put($key, $item);
            }
        });

        $mergedData = $firmIndexed->values()
            ->sortBy([
                ['DATEREQUIRED', 'asc'],
                ['ITEMNUMBER', 'asc'],
            ])
            ->values();

        $allChildren = collect();

        foreach ($mergedData as $data) {
            $allChildren = $allChildren->merge(
                YMCOM::getChildren($data->ITEMNUMBER, (float) $data->QUANTITYREQUIRED, (string) $data->DATEREQUIRED, 'S1')
            );
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

        foreach ($result as $data) {
            StoreStockLimitsJob::dispatch(
                $data['part_number'],
                $data['daily_average'],
                $data['stock_min'],
                $data['stock_max']
            );
        }
    }
}
