<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Header --}}
        <div class="sm:flex sm:justify-between sm:items-start mb-8 gap-4">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    {{ $inventoryBalance->item?->code ?? '—' }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $inventoryBalance->item?->description }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('inventory-balances.index') }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300 text-sm">
                    &larr; Regresar
                </a>
            </div>
        </div>

        {{-- Tarjetas de resumen --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-xs px-5 py-4">
                <div class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 mb-1">Ubicación</div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">
                    {{ $inventoryBalance->location?->code ?? '—' }}
                </span>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-xs px-5 py-4">
                <div class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 mb-1">Almacén</div>
                <div class="text-sm text-gray-800 dark:text-gray-100">
                    {{ $inventoryBalance->location?->warehouse?->name ?? '—' }}
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-xs px-5 py-4">
                <div class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 mb-1">Cantidad Actual</div>
                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                    {{ number_format($inventoryBalance->current_quantity, 0) }}
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-xs px-5 py-4">
                <div class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 mb-1">Movimientos</div>
                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                    {{ $movements->total() }}
                </div>
            </div>
        </div>

        {{-- Tabla de movimientos --}}
        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60">

            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">Seriales en almacén L60</h2>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Entradas, devoluciones y salidas — ordenado por fecha descendente</p>
            </div>

            <div class="overflow-x-auto">
                <table class="table-auto w-full dark:text-gray-300">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/60">
                        <tr>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Fecha y Hora</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">N° Parte</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Serial</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-center">Tipo</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-right">Cantidad</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Referencia</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($movements as $line)
                            @php
                                $mov     = $line->stockMovement;
                                $movType = $mov?->movement_type;
                                $isEntry = in_array($movType, ['INBOUND', 'RETURN']);

                                $typeColors = [
                                    'INBOUND'    => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                                    'OUTBOUND'   => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                                    'ADJUSTMENT' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400',
                                    'TRANSFER'   => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                                    'RETURN'     => 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400',
                                ];
                                $typeLabels = [
                                    'INBOUND'    => 'Entrada',
                                    'OUTBOUND'   => 'Salida',
                                    'ADJUSTMENT' => 'Ajuste',
                                    'TRANSFER'   => 'Traspaso',
                                    'RETURN'     => 'Devolución',
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10">

                                {{-- Fecha y Hora --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <p class="text-xs text-gray-800 dark:text-gray-100">
                                        {{ $mov?->movement_date ? \Carbon\Carbon::parse($mov->movement_date)->format('d-m-Y') : '—' }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                        {{ $mov?->movement_time ? \Carbon\Carbon::parse($mov->movement_time)->format('H:i:s') : '' }}
                                    </p>
                                </td>

                                {{-- N° Parte --}}
                                <td class="px-5 py-3">
                                    <p class="text-xs text-gray-800 dark:text-gray-100">
                                        {{ $line->item?->code ?? $inventoryBalance->item?->code ?? '—' }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 max-w-xs truncate"
                                       title="{{ $line->item?->description ?? $inventoryBalance->item?->description }}">
                                        {{ $line->item?->description ?? $inventoryBalance->item?->description ?? '' }}
                                    </p>
                                </td>

                                {{-- Serial --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="font-mono text-xs text-gray-700 dark:text-gray-200">
                                        {{ $line->serial_batch_number ?? '—' }}
                                    </span>
                                </td>

                                {{-- Tipo de movimiento --}}
                                <td class="px-5 py-3 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$movType] ?? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                        {{ $typeLabels[$movType] ?? $movType ?? '—' }}
                                    </span>
                                </td>

                                {{-- Cantidad --}}
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <span class="text-xs {{ $isEntry ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                                        {{ $isEntry ? '+' : '−' }}{{ number_format($line->quantity_received, 0) }}
                                    </span>
                                </td>

                                {{-- Referencia (contenedor / camión) --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    @if($mov?->container)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                            {{ $mov->container->code }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500 text-sm">
                                    No hay movimientos registrados para este artículo en esta ubicación.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($movements->hasPages() || $movements->total() > 0)
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Mostrando
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $movements->firstItem() ?? 0 }}</span>
                            a
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $movements->lastItem() ?? 0 }}</span>
                            de
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $movements->total() }}</span>
                            resultados
                        </div>
                        <div>{{ $movements->links('pagination::simple-tailwind') }}</div>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
