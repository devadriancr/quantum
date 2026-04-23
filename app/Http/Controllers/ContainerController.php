<?php

namespace App\Http\Controllers;

use App\Imports\ContainersImport;
use App\Models\Container;
use App\Models\Item;
use App\Models\Partner;
use App\Models\ShipmentDocumentLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ContainerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $containers = Container::with('partner')
            ->when($search, function ($query, $search) {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('containers.index', compact('containers'));
    }

    public function create()
    {
        $partners = Partner::orderBy('name')->get();

        return view('containers.create', compact('partners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'                   => 'required|string|max:255',
            'partner_id'             => 'nullable|exists:partners,id',
            'container_type'         => 'required|in:' . implode(',', array_keys(Container::TYPE_OPTIONS)),
            'estimated_arrival_date' => 'nullable|date',
            'estimated_arrival_time' => 'nullable|date_format:H:i',
            'notes'                  => 'nullable|string',
            'lines'                  => 'nullable|array',
            'lines.*.item_code'      => 'required|string|exists:items,code',
            'lines.*.quantity'       => 'required|numeric|min:0.01',
        ], [
            'lines.*.item_code.exists'   => 'El artículo ":input" no existe en el catálogo.',
            'lines.*.item_code.required' => 'El código de artículo es obligatorio.',
            'lines.*.quantity.required'  => 'La cantidad es obligatoria.',
            'lines.*.quantity.min'       => 'La cantidad debe ser mayor a cero.',
        ]);

        $container = null;

        DB::transaction(function () use ($request, &$container) {
            $container = Container::create([
                'code'                   => $request->code,
                'partner_id'             => $request->partner_id,
                'container_type'         => $request->container_type,
                'estimated_arrival_date' => $request->estimated_arrival_date,
                'estimated_arrival_time' => $request->estimated_arrival_time,
                'notes'                  => $request->notes,
                'status'                 => 'PENDING',
            ]);

            $document = $container->shipmentDocuments()->create([
                'partner_id'             => $container->partner_id,
                'document_number'        => 'DOC-' . $container->code,
                'document_date'          => $container->estimated_arrival_date ?? now()->toDateString(),
                'document_time'          => $container->estimated_arrival_time,
                'estimated_arrival_date' => $container->estimated_arrival_date,
                'estimated_arrival_time' => $container->estimated_arrival_time,
                'document_status'        => 'PENDING',
            ]);

            $lineNumber = 0;
            foreach (($request->lines ?? []) as $lineData) {
                $item = Item::where('code', $lineData['item_code'])->first();
                if (! $item) continue;

                $lineNumber++;
                $document->shipmentDocumentLines()->create([
                    'line_number'       => $lineNumber,
                    'item_id'           => $item->id,
                    'serial_number'     => null,
                    'quantity_declared' => $lineData['quantity'],
                    'status'            => 'PENDING',
                ]);
            }
        });

        return redirect()
            ->route('containers.show', $container)
            ->with('success', 'Contenedor y documento creados correctamente.');
    }

    public function show(Request $request, $id)
    {
        $search = $request->input('search_lines');

        $container = Container::with([
            'partner',
            'shipmentDocuments' => function ($query) use ($search) {
                $query->with(['shipmentDocumentLines' => function ($lineQuery) use ($search) {
                    $lineQuery->when($search, function ($q) use ($search) {
                        $q->where('serial_number', 'like', "%{$search}%")
                            ->orWhereHas('item', function ($itemQ) use ($search) {
                                $itemQ->where('code', 'like', "%{$search}%")
                                    ->orWhere('description', 'like', "%{$search}%");
                            });
                    })->with('item');
                }])->withCount('shipmentDocumentLines');
            }
        ])->findOrFail($id);

        return view('containers.show', compact('container', 'search'));
    }

    public function edit(Container $container)
    {
        if ($container->status !== 'PENDING') {
            return redirect()
                ->route('containers.show', $container)
                ->with('error', 'Solo se pueden editar contenedores en estado Pendiente.');
        }

        $partners = Partner::orderBy('name')->get();

        return view('containers.edit', compact('container', 'partners'));
    }

    public function update(Request $request, Container $container)
    {
        if ($container->status !== 'PENDING') {
            return redirect()
                ->route('containers.show', $container)
                ->with('error', 'Solo se pueden editar contenedores en estado Pendiente.');
        }

        $validated = $request->validate([
            'code'                   => 'required|string|max:255',
            'partner_id'             => 'nullable|exists:partners,id',
            'container_type'         => 'required|in:' . implode(',', array_keys(Container::TYPE_OPTIONS)),
            'estimated_arrival_date' => 'nullable|date',
            'estimated_arrival_time' => 'nullable|date_format:H:i',
            'notes'                  => 'nullable|string',
        ]);

        $container->update($validated);

        $container->shipmentDocuments()->update([
            'partner_id'             => $validated['partner_id'] ?? null,
            'document_date'          => $validated['estimated_arrival_date'] ?? null,
            'document_time'          => $validated['estimated_arrival_time'] ?? null,
            'estimated_arrival_date' => $validated['estimated_arrival_date'] ?? null,
            'estimated_arrival_time' => $validated['estimated_arrival_time'] ?? null,
        ]);

        return redirect()
            ->route('containers.show', $container)
            ->with('success', 'Contenedor y documentos actualizados correctamente.');
    }

    public function destroy(Container $container)
    {
        foreach ($container->shipmentDocuments as $document) {
            $document->shipmentDocumentLines()->delete();
            $document->delete();
        }
        $container->delete();

        return redirect()
            ->route('containers.index')
            ->with('success', 'Contenedor eliminado correctamente.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:10240',
            ],
        ], [
            'excel_file.required' => 'Debes seleccionar un archivo.',
            'excel_file.mimes'    => 'El archivo debe ser de tipo Excel (.xlsx, .xls) o CSV.',
            'excel_file.max'      => 'El archivo no debe superar los 10 MB.',
        ]);

        try {
            $import = new ContainersImport();
            Excel::import($import, $request->file('excel_file'));

            $msg = "Importación completada: {$import->created} líneas creadas.";

            if ($import->skipped > 0) {
                $msg .= " {$import->skipped} filas omitidas.";
            }

            if (! empty($import->errors)) {
                return redirect()->route('containers.index')
                    ->with('success', $msg)
                    ->with('import_warnings', $import->errors);
            }

            return redirect()->route('containers.index')->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->route('containers.index')
                ->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }
}
