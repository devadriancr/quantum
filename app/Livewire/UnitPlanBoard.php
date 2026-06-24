<?php

namespace App\Livewire;

use App\Exports\UnitPlanExport;
use App\Imports\UnitPlansImport;
use App\Models\Container;
use App\Models\InventoryBalance;
use App\Models\Item;
use App\Models\ShipmentDocument;
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
        $unitCostByCode   = [];

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
                    if (!isset($unitCostByCode[$item['item_code']])) {
                        $unitCostByCode[$item['item_code']] = (float) ($item['unit_cost'] ?? 0);
                    }
                }
                $pool[$g['date']]['containers'][$c['code']] = (float) $c['total_cost'];
                $itemsByContainer[$c['code']]               = $itemsDict;
            }
        }

        // Inventario actual: 1 query agregada
        $stockByCode  = [];
        $limitsByCode = [];
        if (!empty($itemIdByCode)) {
            // SQL Server limita a 2100 parámetros por consulta: troceamos los ids.
            $itemIds = array_values($itemIdByCode);

            // union (no flatMap): las claves son item_id enteras y collapse
            // las reindexaría, rompiendo la asociación id => total.
            $stocks = collect($itemIds)
                ->chunk(2000)
                ->reduce(fn($carry, $chunk) => $carry->union(
                    InventoryBalance::whereIn('item_id', $chunk->values())
                        ->select('item_id', DB::raw('SUM(current_quantity) as total'))
                        ->groupBy('item_id')
                        ->pluck('total', 'item_id')
                ), collect());
            foreach ($itemIdByCode as $code => $id) {
                $stockByCode[$code] = (float) ($stocks[$id] ?? 0);
            }

            // Límites de stock por item (mín, máx, consumo diario promedio).
            // Se agregan por item_id porque el inventario también se suma
            // entre ubicaciones. El consumo diario se redondea hacia arriba.
            $limits = collect($itemIds)
                ->chunk(2000)
                ->flatMap(fn($chunk) => \App\Models\StockLimit::where('active', true)
                    ->whereIn('item_id', $chunk->values())
                    ->select(
                        'item_id',
                        DB::raw('SUM(minimum_quantity) as min_qty'),
                        DB::raw('SUM(maximum_quantity) as max_qty'),
                        DB::raw('SUM(daily_average) as daily_avg'),
                    )
                    ->groupBy('item_id')
                    ->get())
                ->keyBy('item_id');

            foreach ($itemIdByCode as $code => $id) {
                $lim = $limits->get($id);
                $limitsByCode[$code] = [
                    'min'   => $lim ? (float) $lim->min_qty : null,
                    'max'   => $lim ? (float) $lim->max_qty : null,
                    'daily' => $lim ? (int) ceil((float) $lim->daily_avg) : 0,
                ];
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
            'unit_cost_by_code'  => $unitCostByCode,
            'stock_by_code'      => $stockByCode,
            'limits_by_code'     => $limitsByCode,
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

    public function savePlan(array $assignments, array $slotTimes, string $planName = '')
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

        // Cada contenedor se registra con el horario de su fila: toda fila que
        // tenga al menos un contenedor asignado DEBE tener una hora definida.
        $missingTimeSlots = collect($assignments)
            ->pluck('slot')
            ->unique()
            ->filter(fn($slot) => empty($slotTimes[$slot] ?? null));

        if ($missingTimeSlots->isNotEmpty()) {
            $this->dispatch('toast', type: 'error', title: 'Horario faltante',
                body: 'Cada fila con contenedores debe tener una hora asignada.');
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

        // Fechas del tablero = días (Lunes-Sábado) de la SEMANA SIGUIENTE.
        // Esa fecha es la que se registra como llegada estimada del contenedor.
        $weekStart = now()->addWeek()->startOfWeek();           // lunes de la próxima semana
        $dayLabels = collect($this->days)->pluck('label', 'value');

        try {
            $allItemCodes = collect($assignments)->keys()
                ->flatMap(fn($code) => array_keys($itemsByContainer[$code] ?? []))
                ->unique();

            // SQL Server limita a 2100 parámetros por consulta: troceamos.
            $items = $allItemCodes
                ->chunk(2000)
                ->flatMap(fn($chunk) => Item::whereIn('code', $chunk->values())
                    ->with(['lastCost.currency'])
                    ->get())
                ->keyBy('code');

            $exportRows  = [];
            $savedCount  = 0;

            DB::transaction(function () use (
                $items, $itemsByContainer, $dateByCode, $assignments, $slotTimes,
                $weekStart, $dayLabels, &$exportRows, &$savedCount
            ) {
                foreach ($assignments as $code => $pos) {
                    $day  = (int) $pos['day'];
                    $slot = (int) $pos['slot'];

                    $arrivalDate = $weekStart->copy()->addDays($day - 1)->toDateString();
                    $arrivalTime = \Carbon\Carbon::parse($slotTimes[$slot])->format('H:i:s');
                    $customsDate = $dateByCode[$code] ?? null;

                    // Reutilizar el contenedor si ya existe con mismo código,
                    // fecha y hora de llegada; si no, crearlo.
                    $container = Container::where('code', $code)
                        ->where('estimated_arrival_date', $arrivalDate)
                        ->where('estimated_arrival_time', $arrivalTime)
                        ->first();

                    if (!$container) {
                        $container = Container::create([
                            'code'                   => $code,
                            'partner_id'             => null,
                            'container_type'         => 'CONTAINER',
                            'estimated_arrival_date' => $arrivalDate,
                            'estimated_arrival_time' => $arrivalTime,
                            'status'                 => 'PENDING',
                        ]);
                    }

                    // Documento del contenedor (uno por contenedor).
                    $document = ShipmentDocument::firstOrCreate(
                        ['container_id' => $container->id],
                        [
                            'document_number'        => $code,
                            'partner_id'             => null,
                            'document_date'          => $arrivalDate,
                            'document_time'          => $arrivalTime,
                            'estimated_arrival_date' => $arrivalDate,
                            'estimated_arrival_time' => $arrivalTime,
                            'document_status'        => 'PENDING',
                        ]
                    );

                    $lineNumber = (int) $document->shipmentDocumentLines()->max('line_number');

                    foreach (($itemsByContainer[$code] ?? []) as $itemCode => $quantity) {
                        $model    = $items->get($itemCode);
                        $unitCost = $model?->lastCost?->total_cost ?? 0;

                        $lineNumber++;
                        $document->shipmentDocumentLines()->create([
                            'line_number'       => $lineNumber,
                            'item_id'           => $model?->id,
                            'serial_number'     => null,
                            'quantity_declared' => $quantity,
                            'unit_cost'         => $unitCost,
                            'status'            => 'PENDING',
                        ]);

                        $exportRows[] = [
                            $dayLabels[$day] ?? $day,
                            \Carbon\Carbon::parse($arrivalDate)->format('d/m/Y'),
                            substr($arrivalTime, 0, 5),
                            $customsDate ? \Carbon\Carbon::parse($customsDate)->format('d/m/Y') : '—',
                            $code,
                            $itemCode,
                            (float) $quantity,
                            (float) $unitCost,
                            round(((float) $quantity) * (float) $unitCost, 4),
                            $model?->lastCost?->currency?->code ?? '—',
                        ];
                    }

                    $savedCount++;
                }
            });

            $this->dispatch('toast', type: 'success', title: '¡Planeación guardada!',
                body: "{$savedCount} contenedor(es) registrado(s) correctamente.");

            // Avisar al cliente que el guardado fue exitoso para recargar la
            // página (nueva planeación) una vez disparada la descarga del Excel.
            $this->dispatch('plan-saved');

            $fileName = 'planeacion-' . now()->format('Ymd-His') . '.xlsx';

            return Excel::download(new UnitPlanExport($exportRows), $fileName);
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', title: 'Error al guardar', body: $e->getMessage());
        }
    }

    public function render()
    {
        // Fechas de la semana siguiente para cada día (Lunes-Sábado). Estas son
        // las que se registran como llegada estimada al guardar.
        $weekStart = now()->addWeek()->startOfWeek();
        $dayDates  = collect($this->days)->mapWithKeys(fn($d) => [
            $d['value'] => $weekStart->copy()->addDays($d['value'] - 1)->format('d/m/Y'),
        ]);

        return view('livewire.unit-plan-board', ['dayDates' => $dayDates]);
    }
}
