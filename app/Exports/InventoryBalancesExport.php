<?php

namespace App\Exports;

use App\Models\InventoryBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryBalancesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public function __construct(
        private ?string $search,
        private ?string $warehouse
    ) {}

    public function collection()
    {
        $balances = InventoryBalance::with(['item', 'location.warehouse'])
            ->whereHas('location', fn($q) => $q->where('code', 'like', 'L60%'))
            ->when($this->search, function ($q) {
                $s = $this->search;
                $q->whereHas('item', fn($i) => $i->where('code', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%"))
                    ->orWhereHas('location', fn($l) => $l->where('code', 'like', "%{$s}%")->orWhere('name', 'like', "%{$s}%"));
            })
            ->when($this->warehouse, fn($q) => $q->whereHas('location.warehouse', fn($w) => $w->where('id', $this->warehouse)))
            ->orderByDesc('updated_at')
            ->get();

        if ($balances->isEmpty()) {
            return collect();
        }

        $itemIds     = $balances->pluck('item_id')->unique()->values();
        $locationIds = $balances->pluck('location_id')->unique()->values();

        $lastInbounds = DB::table('stock_movements as sm')
            ->join('stock_movement_lines as sml', 'sm.id', '=', 'sml.stock_movement_id')
            ->select('sml.item_id', 'sm.location_id_to as location_id', 'sm.movement_date', 'sm.movement_time')
            ->where('sm.movement_type', 'INBOUND')
            ->whereIn('sml.item_id', $itemIds)
            ->whereIn('sm.location_id_to', $locationIds)
            ->orderByDesc('sm.movement_date')
            ->orderByDesc('sm.movement_time')
            ->get()
            ->groupBy(fn($r) => $r->item_id . '-' . $r->location_id)
            ->map(fn($g) => $g->first());

        $outboundQtys = DB::table('stock_movements as sm')
            ->join('stock_movement_lines as sml', 'sm.id', '=', 'sml.stock_movement_id')
            ->select('sml.item_id', 'sm.location_id_from as location_id', DB::raw('SUM(sml.quantity_received) as total'))
            ->where('sm.movement_type', 'OUTBOUND')
            ->whereIn('sml.item_id', $itemIds)
            ->whereIn('sm.location_id_from', $locationIds)
            ->groupBy('sml.item_id', 'sm.location_id_from')
            ->get()
            ->keyBy(fn($r) => $r->item_id . '-' . $r->location_id);

        $returnQtys = DB::table('stock_movements as sm')
            ->join('stock_movement_lines as sml', 'sm.id', '=', 'sml.stock_movement_id')
            ->select('sml.item_id', 'sm.location_id_to as location_id', DB::raw('SUM(sml.quantity_received) as total'))
            ->where('sm.movement_type', 'RETURN')
            ->whereIn('sml.item_id', $itemIds)
            ->whereIn('sm.location_id_to', $locationIds)
            ->groupBy('sml.item_id', 'sm.location_id_to')
            ->get()
            ->keyBy(fn($r) => $r->item_id . '-' . $r->location_id);

        return $balances->map(function ($balance) use ($lastInbounds, $outboundQtys, $returnQtys) {
            $key      = $balance->item_id . '-' . $balance->location_id;
            $qty      = (float) $balance->current_quantity;
            $lastIn   = $lastInbounds[$key] ?? null;
            $outbound = isset($outboundQtys[$key]) ? (float) $outboundQtys[$key]->total : 0;
            $returns  = isset($returnQtys[$key])   ? (float) $returnQtys[$key]->total   : 0;
            $consumed = max(0, $outbound - $returns);
            $doh      = $consumed > 0 ? (int) round($qty / $consumed) : null;

            $lastInStr = '—';
            if ($lastIn) {
                $date      = Carbon::parse($lastIn->movement_date)->format('d/m/Y');
                $time      = $lastIn->movement_time ? substr($lastIn->movement_time, 0, 8) : '';
                $lastInStr = $time ? "{$date} {$time}" : $date;
            }

            $locationStr = $balance->location?->code ?? '—';
            if ($balance->location?->name) {
                $locationStr .= ' - ' . $balance->location->name;
            }

            $wh = $balance->location?->warehouse;
            $warehouseStr = $wh ? "{$wh->code} - {$wh->name}" : '—';

            return [
                $lastInStr,
                $balance->item?->code ?? '—',
                $consumed > 0 ? (int) $consumed : '—',
                (int) $qty,
                $doh ?? '—',
                $locationStr,
                $warehouseStr,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ÚLTIMA ENTREGA',
            'NÚMERO PARTE',
            'CANT. CONSUMIDO',
            'CANT. ACTUAL',
            'DOH',
            'UBICACIÓN',
            'ALMACÉN',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('B')
              ->getNumberFormat()
              ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
