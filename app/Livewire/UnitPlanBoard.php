<?php

namespace App\Livewire;

use App\Imports\UnitPlansImport;
use App\Models\InventoryBalance;
use App\Models\Item;
use App\Models\UnitPlan;
use App\Models\UnitPlanLine;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
#[Title('Planeación de Unidades')]
class UnitPlanBoard extends Component
{
    use WithFileUploads;

    /* ──────────────────────────────────────────────────────────────────
       Estado público: MINÚSCULO. Esto es todo lo que viaja entre cliente
       y servidor. Sin datos pesados — todo el detalle vive solo en cache
       server-side, y la UI rica vive en Alpine.
       ────────────────────────────────────────────────────────────────── */

    public $excelFile = null;
    public ?string $cacheKey = null;
    public bool $hasData = false;
    public int $totalContainers = 0;
    public string $planName = '';

    public array $days = [
        ['value' => 1, 'label' => 'Lunes'],
        ['value' => 2, 'label' => 'Martes'],
        ['value' => 3, 'label' => 'Miércoles'],
        ['value' => 4, 'label' => 'Jueves'],
        ['value' => 5, 'label' => 'Viernes'],
        ['value' => 6, 'label' => 'Sábado'],
    ];

    /* ──────────────────────────────────────────────────────────────────
       Upload del Excel: procesa, guarda en cache server-side y EMITE un
       evento con los datos para que Alpine los reciba UNA SOLA VEZ.
       ────────────────────────────────────────────────────────────────── */

    public function updatedExcelFile(): void
    {
        $this->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'excelFile.required' => 'Debes seleccionar un archivo.',
            'excelFile.mimes'    => 'El archivo debe ser Excel (.xlsx, .xls) o CSV.',
            'excelFile.max'      => 'El archivo no debe superar los 10 MB.',
        ]);

        try {
            $import = new UnitPlansImport();
            Excel::import($import, $this->excelFile->getRealPath());

            $payload = $this->buildPayload($import->groups);

            $body = "{$import->rows} filas leídas, {$import->skipped} omitidas.";
            if ($import->itemsSkipped > 0) {
                $body .= "\n{$import->itemsSkipped} item(s) descartados (no registrados).";
            }
            if ($import->containersSkipped > 0) {
                $body .= "\n{$import->containersSkipped} contenedor(es) descartados (sin items válidos).";
            }

            $this->dispatch('toast', type: 'success', title: 'Excel procesado', body: $body);

            if (!empty($import->errors)) {
                $this->dispatch('toast',
                    type: 'warning',
                    title: 'Avisos durante la importación',
                    details: array_slice($import->errors, 0, 50),
                );
            }

            // Mandar todos los datos a Alpine via event — UNA SOLA VEZ
            $this->dispatch('excel-loaded', data: $payload);
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', title: 'Error al procesar', body: $e->getMessage());
        } finally {
            $this->excelFile = null;
        }
    }

    /**
     * Construye el payload completo, lo cachea server-side (para save) y
     * devuelve la estructura que Alpine va a usar para renderizar todo.
     */
    private function buildPayload(array $groups): array
    {
        $pool             = [];
        $itemsByContainer = [];
        $itemIdByCode     = [];

        foreach ($groups as $g) {
            $pool[$g['date']] = [
                'label'      => $g['date_label'],
                'containers' => [],
            ];

            foreach ($g['containers'] as $c) {
                $itemsDict = [];
                foreach ($c['items'] as $item) {
                    $itemsDict[$item['item_code']] = (float) $item['quantity'];
                    if (!empty($item['item_id']) && !isset($itemIdByCode[$item['item_code']])) {
                        $itemIdByCode[$item['item_code']] = $item['item_id'];
                    }
                }
                $pool[$g['date']]['containers'][$c['code']] = (float) $c['total_cost'];
                $itemsByContainer[$c['code']]               = $itemsDict;
            }
        }

        // Inventario actual: 1 query agregada
        $stockByCode = [];
        if (!empty($itemIdByCode)) {
            $stocks = InventoryBalance::whereIn('item_id', array_values($itemIdByCode))
                ->select('item_id', DB::raw('SUM(current_quantity) as total'))
                ->groupBy('item_id')
                ->pluck('total', 'item_id');
            foreach ($itemIdByCode as $code => $id) {
                $stockByCode[$code] = (float) ($stocks[$id] ?? 0);
            }
        }

        // Cache server-side (solo para que save() pueda persistir sin re-procesar el Excel)
        if ($this->cacheKey) {
            Cache::forget($this->cacheKey);
        }
        $cacheKey = 'unit_plan_' . uniqid('', true);
        Cache::put($cacheKey, [
            'items_by_container' => $itemsByContainer,
            'item_ids'           => $itemIdByCode,
            'date_by_code'       => $this->buildDateByCode($pool),
        ], now()->addHours(4));

        $this->cacheKey        = $cacheKey;
        $this->hasData         = true;
        $this->totalContainers = count($itemsByContainer);

        // Lo que mandamos al cliente (Alpine) — UNA SOLA VEZ
        return [
            'pool'               => $pool,
            'items_by_container' => $itemsByContainer,
            'stock_by_code'      => $stockByCode,
            'total_containers'   => $this->totalContainers,
        ];
    }

    private function buildDateByCode(array $pool): array
    {
        $out = [];
        foreach ($pool as $date => $bucket) {
            foreach ($bucket['containers'] as $code => $_) {
                $out[$code] = $date;
            }
        }
        return $out;
    }

    /* ──────────────────────────────────────────────────────────────────
       Save: Alpine manda assignments + slotTimes. Server lee cache para
       obtener items detallados y persiste el plan.
       ────────────────────────────────────────────────────────────────── */

    public function savePlan(array $assignments, array $slotTimes, string $planName = ''): void
    {
        $times = array_filter($slotTimes);
        if (count($times) !== count(array_unique($times))) {
            $this->dispatch('toast', type: 'error', title: 'Horarios repetidos',
                body: 'Las 4 horas deben ser distintas.');
            return;
        }

        if (empty($assignments)) {
            $this->dispatch('toast', type: 'error', title: 'Sin contenedores',
                body: 'Asigna al menos un contenedor antes de guardar.');
            return;
        }

        $cached = $this->cacheKey ? Cache::get($this->cacheKey) : null;
        if (!$cached) {
            $this->dispatch('toast', type: 'error', title: 'Sesión expirada',
                body: 'Los datos del Excel se perdieron. Por favor, cárgalo de nuevo.');
            return;
        }

        $itemsByContainer = $cached['items_by_container'];
        $dateByCode       = $cached['date_by_code'];

        try {
            $allItemCodes = collect($assignments)->keys()
                ->flatMap(fn($code) => array_keys($itemsByContainer[$code] ?? []))
                ->unique();

            $items = Item::whereIn('code', $allItemCodes)
                ->with(['lastCost.currency'])
                ->get()
                ->keyBy('code');

            $plan = DB::transaction(function () use ($items, $itemsByContainer, $dateByCode, $assignments, $slotTimes, $planName) {
                $plan = UnitPlan::create([
                    'name'            => $planName !== '' ? $planName : 'Planeación ' . now()->format('Y-m-d H:i'),
                    'week_start_date' => now()->startOfWeek()->toDateString(),
                    'total_cost'      => 0,
                ]);

                $planTotal = 0;

                foreach ($assignments as $code => $pos) {
                    $containerItems = $itemsByContainer[$code] ?? [];
                    $time           = $slotTimes[$pos['slot']] ?? null;
                    $customsDate    = $dateByCode[$code] ?? null;

                    foreach ($containerItems as $itemCode => $quantity) {
                        $model     = $items->get($itemCode);
                        $unitCost  = $model?->lastCost?->total_cost ?? 0;
                        $lineTotal = round(((float) $quantity) * (float) $unitCost, 4);
                        $planTotal += $lineTotal;

                        UnitPlanLine::create([
                            'unit_plan_id'   => $plan->id,
                            'container_code' => $code,
                            'customs_date'   => $customsDate,
                            'item_id'        => $model?->id,
                            'item_code'      => $itemCode,
                            'quantity'       => $quantity,
                            'day_of_week'    => (int) $pos['day'],
                            'slot_index'     => (int) $pos['slot'],
                            'schedule_time'  => $time,
                            'unit_cost'      => $unitCost,
                            'line_total'     => $lineTotal,
                            'currency_id'    => $model?->lastCost?->currency_id,
                        ]);
                    }
                }

                $plan->update(['total_cost' => $planTotal]);
                return $plan;
            });

            $this->dispatch('toast', type: 'success', title: '¡Planeación guardada!',
                body: "Plan #{$plan->id} guardado correctamente.");
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', title: 'Error al guardar', body: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.unit-plan-board');
    }
}
