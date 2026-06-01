<?php

namespace App\Livewire\UnitPlan;

use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class InventoryProjection extends Component
{
    /** Llave del cache donde viven items_by_container + stock_by_code. */
    #[Reactive]
    public ?string $cacheKey = null;

    #[Reactive]
    public array $assignments = [];

    #[Reactive]
    public array $days = [];

    public function render()
    {
        $projection = $this->buildProjection();

        return view('livewire.unit-plan.inventory-projection', [
            'projection' => $projection,
        ]);
    }

    /**
     * Construye la proyección de inventario acumulada por día.
     * Todo server-side — al cliente solo va el HTML resultante.
     */
    private function buildProjection(): array
    {
        if (empty($this->assignments) || !$this->cacheKey) {
            return [];
        }

        $cached = Cache::get($this->cacheKey);
        if (!$cached) return [];

        $itemsByContainer = $cached['items_by_container'] ?? [];
        $stockByCode      = $cached['stock_by_code'] ?? [];

        // Sumar cantidades por (item_code, día) desde contenedores asignados
        $perItemDay = [];
        foreach ($this->assignments as $code => $pos) {
            $items = $itemsByContainer[$code] ?? [];
            foreach ($items as $itemCode => $qty) {
                $perItemDay[$itemCode][$pos['day']] = ($perItemDay[$itemCode][$pos['day']] ?? 0) + (float) $qty;
            }
        }

        if (empty($perItemDay)) return [];

        $rows = [];
        foreach ($perItemDay as $code => $byDay) {
            $start   = (float) ($stockByCode[$code] ?? 0);
            $running = $start;
            $perDay  = [];
            for ($d = 1; $d <= 6; $d++) {
                $running   += $byDay[$d] ?? 0;
                $perDay[$d] = $running;
            }
            $rows[] = [
                'code'   => $code,
                'start'  => $start,
                'by_day' => $perDay,
            ];
        }
        usort($rows, fn($a, $b) => strcmp($a['code'], $b['code']));

        return $rows;
    }
}
