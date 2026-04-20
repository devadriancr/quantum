<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Models\Partner;
use App\Models\ShipmentDocumentLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    public function show($id)
    {
        $container = Container::with([
            'partner',
            'shipmentDocuments.shipmentDocumentLines',
            'shipmentDocuments.shipmentDocumentLines.item'
        ])->findOrFail($id);

        return view('containers.show', compact('container'));
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
            'partner_id'             => 'required|exists:partners,id',
            'container_type'         => 'required|in:' . implode(',', array_keys(Container::TYPE_OPTIONS)),
            'estimated_arrival_date' => 'nullable|date',
            'estimated_arrival_time' => 'nullable|date_format:H:i',
            'notes'                  => 'nullable|string',
        ]);

        $container->update($validated);

        return redirect()
            ->route('containers.show', $container)
            ->with('success', 'Contenedor actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Container $container)
    {
        foreach ($container->shippingDocuments as $document) {
            $document->lines()->delete();
            $document->delete();
        }

        $container->delete();

        return redirect()
            ->route('containers.index')
            ->with('success', 'Contenedor eliminado correctamente.');
    }
}
