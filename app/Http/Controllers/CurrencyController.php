<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $currencies = Currency::when($search, fn($q) => $q
                ->where('code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
            )
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return view('currencies.index', compact('currencies', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('currencies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'          => 'required|string|max:10|uppercase|unique:currencies,code',
            'name'          => 'required|string|max:100',
            'symbol'        => 'required|string|max:10',
            'units_per_usd' => 'required|numeric|min:0.000001',
            'active'        => 'boolean',
        ], [
            'code.required'          => 'El código de moneda es obligatorio.',
            'code.unique'            => 'Ya existe una moneda con ese código.',
            'name.required'          => 'El nombre es obligatorio.',
            'symbol.required'        => 'El símbolo es obligatorio.',
            'units_per_usd.required' => 'El tipo de cambio es obligatorio.',
            'units_per_usd.min'      => 'El tipo de cambio debe ser mayor a cero.',
        ]);

        $validated['active'] = $request->boolean('active', true);

        Currency::create($validated);

        return redirect()->route('currencies.index')
            ->with('success', "Moneda {$validated['code']} creada correctamente.");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Currency $currency)
    {
        return view('currencies.edit', compact('currency'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'symbol'        => 'required|string|max:10',
            'units_per_usd' => 'required|numeric|min:0.000001',
            'active'        => 'boolean',
        ], [
            'name.required'          => 'El nombre es obligatorio.',
            'symbol.required'        => 'El símbolo es obligatorio.',
            'units_per_usd.required' => 'El tipo de cambio es obligatorio.',
            'units_per_usd.min'      => 'El tipo de cambio debe ser mayor a cero.',
        ]);

        $validated['active'] = $request->boolean('active');

        $currency->update($validated);

        return redirect()->route('currencies.index')
            ->with('success', "Moneda {$currency->code} actualizada correctamente.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Currency $currency)
    {
        if ($currency->itemCosts()->exists()) {
            return redirect()->route('currencies.index')
                ->with('error', "No se puede eliminar {$currency->code}: tiene historial de costos asociado.");
        }

        $currency->delete();

        return redirect()->route('currencies.index')
            ->with('success', "Moneda {$currency->code} eliminada.");
    }

    /**
     * Refresh exchange rates from external API.
     */
    public function refreshRates(Request $request)
    {
        try {
            $response = Http::timeout(10)->get('https://api.fxratesapi.com/latest');

            if (! $response->successful()) {
                return back()->with('error', 'No se pudo conectar con la API de tasas de cambio.');
            }

            $rates   = $response->json('rates', []);
            $updated = 0;

            Currency::where('active', true)->each(function ($currency) use ($rates, &$updated) {
                if (isset($rates[$currency->code])) {
                    $currency->update(['units_per_usd' => $rates[$currency->code]]);
                    $updated++;
                }
            });

            return redirect()->route('currencies.index')
                ->with('success', "Tasas actualizadas para {$updated} monedas.");
        } catch (\Throwable) {
            return back()->with('error', 'Error al conectar con la API de tasas de cambio.');
        }
    }
}
