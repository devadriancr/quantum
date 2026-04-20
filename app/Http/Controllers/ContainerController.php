<?php

namespace App\Http\Controllers;

use App\Imports\ContainersImport;
use App\Models\Container;
use App\Models\Partner;
use App\Models\ShipmentDocumentLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ContainerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $search = $request->input('search_lines');

        $container = Container::with([
            'partner',
            'shipmentDocuments' => function ($query) use ($search) {
                // Cargamos las líneas filtradas
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

    /**
     * Show the form for editing the specified resource.
     */
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

    /**
     * Update the specified resource in storage.
     */
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

        // Sincronizar los documentos asociados al contenedor
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

    /**
     * Remove the specified resource from storage.
     */
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
