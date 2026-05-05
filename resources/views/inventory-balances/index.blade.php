<x-app-layout>
    <style>
        /* Row status colors */
        tr.row-danger  { background-color: #fef2f2; border-left: 4px solid #f87171; }
        tr.row-warning { background-color: #fefce8; border-left: 4px solid #facc15; }
        tr.row-success { background-color: #f0fdf4; border-left: 4px solid #4ade80; }
        .dark tr.row-danger  { background-color: rgba(239,68,68,0.12); border-left-color: #f87171; }
        .dark tr.row-warning { background-color: rgba(234,179,8,0.12);  border-left-color: #facc15; }
        .dark tr.row-success { background-color: rgba(74,222,128,0.10); border-left-color: #4ade80; }
        tr.row-danger:hover, tr.row-warning:hover, tr.row-success:hover { filter: brightness(0.97); }
    </style>

    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Header --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    {{ __('Inventario') }}
                </h1>
            </div>

            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">

                {{-- Búsqueda --}}
                <form action="{{ route('inventory-balances.index') }}" method="GET" class="relative flex items-center">
                    @if($warehouse)
                        <input type="hidden" name="warehouse" value="{{ $warehouse }}">
                    @endif
                    <label for="action-search" class="sr-only">Buscar</label>
                    <input
                        id="action-search"
                        name="search"
                        class="form-input pl-9 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg"
                        type="search"
                        placeholder="N° parte, descripción, ubicación..."
                        value="{{ $search }}"
                    />
                    <button class="absolute inset-0 right-auto group" type="submit" aria-label="Buscar">
                        <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400 ml-3 mr-2" width="16" height="16" viewBox="0 0 16 16">
                            <path d="M7 14c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zM7 2C4.243 2 2 4.243 2 7s2.243 5 5 5 5-2.243 5-5-2.243-5-5-5z" />
                            <path d="M15.707 14.293L13.314 11.9a8.019 8.019 0 01-1.414 1.414l2.393 2.393a.997.997 0 001.414 0 .999.999 0 000-1.414z" />
                        </svg>
                    </button>
                    @if($search)
                        <a href="{{ route('inventory-balances.index', $warehouse ? ['warehouse' => $warehouse] : []) }}"
                           class="ml-2 text-sm text-gray-500 hover:text-violet-500 underline whitespace-nowrap">
                            Limpiar
                        </a>
                    @endif
                </form>

                {{-- Exportar Excel --}}
                <a href="{{ route('inventory-balances.export', array_filter(['search' => $search, 'warehouse' => $warehouse])) }}"
                   class="btn bg-green-600 hover:bg-green-800 text-white flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Excel
                </a>

            </div>
        </div>

        {{-- Tabla --}}
        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60">
            <div class="overflow-x-auto">
                <table class="table-auto w-full dark:text-gray-300">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-t border-b border-gray-100 dark:border-gray-700/60">
                        <tr>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">N° Parte</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Últ. Entrada</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-center">Cant. Consumido</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-center">Cant. Actual</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-center">DOH</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Ubicación / Almacén</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-right">Acciones</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($balances as $balance)
                            @php
                                $key        = $balance->item_id . '-' . $balance->location_id;
                                $stockLimit = $stockLimitsMap[$key] ?? null;
                                $qty        = $balance->current_quantity;
                                $lastIn     = $lastInbounds[$key] ?? null;
                                $outbound = isset($outboundQtys[$key]) ? (float) $outboundQtys[$key]->total : 0;
                                $returns  = isset($returnQtys[$key])   ? (float) $returnQtys[$key]->total   : 0;
                                $consumed = max(0, $outbound - $returns);
                                $doh      = $consumed > 0 ? round($qty / $consumed) : null;

                                $rowClass = '';
                                if ($stockLimit) {
                                    if ($qty <= $stockLimit->minimum_quantity) {
                                        $rowClass = 'row-danger';
                                    } elseif ($qty >= $stockLimit->maximum_quantity) {
                                        $rowClass = 'row-warning';
                                    } else {
                                        $rowClass = 'row-success';
                                    }
                                }
                            @endphp
                            <tr class="{{ $rowClass }}">

                                {{-- N° Parte + Descripción --}}
                                <td class="px-5 py-3">
                                    <p class="text-xs text-gray-700 dark:text-gray-200">
                                        {{ $balance->item?->code ?? '—' }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 max-w-xs truncate" title="{{ $balance->item?->description }}">
                                        {{ $balance->item?->description ?? '' }}
                                    </p>
                                </td>

                                {{-- Último Movimiento de Entrada --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    @if($lastIn)
                                        <p class="text-xs text-gray-700 dark:text-gray-200">
                                            {{ \Carbon\Carbon::parse($lastIn->movement_date)->format('d-m-Y') }}
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                            {{ $lastIn->movement_time ? substr($lastIn->movement_time, 0, 5) : '' }}
                                        </p>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>

                                {{-- Cantidad Consumida --}}
                                <td class="px-5 py-3 whitespace-nowrap text-center">
                                    <span class="text-xs text-gray-700 dark:text-gray-200">
                                        {{ $consumed > 0 ? number_format($consumed, 0, '.', '') : '—' }}
                                    </span>
                                </td>

                                {{-- Cantidad Actual --}}
                                <td class="px-5 py-3 whitespace-nowrap text-center">
                                    @php
                                        $qtyClass = 'text-gray-700 dark:text-gray-200';
                                        if ($stockLimit) {
                                            if ($qty <= $stockLimit->minimum_quantity) {
                                                $qtyClass = 'text-red-600 dark:text-red-400';
                                            } elseif ($qty >= $stockLimit->maximum_quantity) {
                                                $qtyClass = 'text-yellow-600 dark:text-yellow-400';
                                            } else {
                                                $qtyClass = 'text-green-700 dark:text-green-400';
                                            }
                                        }
                                    @endphp
                                    <span class="text-xs {{ $qtyClass }}">
                                        {{ number_format($qty, 0, '.', '') }}
                                    </span>
                                    @if($stockLimit)
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                            Mín: {{ number_format($stockLimit->minimum_quantity, 0, '.', '') }}
                                            &nbsp;·&nbsp;
                                            Máx: {{ number_format($stockLimit->maximum_quantity, 0, '.', '') }}
                                        </p>
                                    @endif
                                </td>

                                {{-- DOH --}}
                                <td class="px-5 py-3 whitespace-nowrap text-center">
                                    @if($doh !== null)
                                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-lg text-xs
                                            @if($rowClass === 'row-danger') bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300
                                            @elseif($rowClass === 'row-warning') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300
                                            @elseif($rowClass === 'row-success') bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300
                                            @else bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300
                                            @endif
                                        ">
                                            {{ number_format($doh, 0, '.', '') }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>

                                {{-- Ubicación + Almacén --}}
                                <td class="px-5 py-3">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">
                                            {{ $balance->location?->code ?? '—' }}
                                        </span>
                                        @if($balance->location?->name)
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $balance->location->name }}
                                            </span>
                                        @endif
                                    </div>
                                    @php $wh = $balance->location?->warehouse; @endphp
                                    @if($wh)
                                        <div class="flex items-center gap-1.5 mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700 dark:bg-violet-500/20 dark:text-violet-400">
                                                {{ $wh->code }}
                                            </span>
                                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $wh->name }}</span>
                                        </div>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <a href="{{ route('inventory-balances.show', $balance) }}"
                                       class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium border border-blue-200 dark:border-blue-500/30 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500 text-sm">
                                    @if($search || $warehouse)
                                        No se encontraron registros con los filtros aplicados.
                                    @else
                                        No hay balances de inventario registrados aún.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($balances->hasPages() || $balances->total() > 0)
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400 text-center sm:text-left">
                            Mostrando
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $balances->firstItem() ?? 0 }}</span>
                            a
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $balances->lastItem() ?? 0 }}</span>
                            de
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $balances->total() }}</span>
                            resultados
                        </div>
                        <div class="flex justify-center sm:justify-end">
                            {{ $balances->appends(request()->query())->links('pagination::simple-tailwind') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
