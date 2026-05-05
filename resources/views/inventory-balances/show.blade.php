<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Header --}}
        <div class="sm:flex sm:justify-between sm:items-start mb-6 gap-4">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    {{ $inventoryBalance->item?->code ?? '—' }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $inventoryBalance->item?->description }}
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 items-start sm:items-center flex-wrap">

                {{-- Filtros --}}
                <form action="{{ route('inventory-balances.show', $inventoryBalance) }}" method="GET"
                      class="flex flex-col sm:flex-row gap-2 items-start sm:items-center flex-wrap">

                    {{-- Búsqueda texto --}}
                    <div class="relative flex items-center">
                        <label for="action-search" class="sr-only">Buscar</label>
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500" width="16" height="16" viewBox="0 0 16 16">
                                <path d="M7 14c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zM7 2C4.243 2 2 4.243 2 7s2.243 5 5 5 5-2.243 5-5-2.243-5-5-5z"/>
                                <path d="M15.707 14.293L13.314 11.9a8.019 8.019 0 01-1.414 1.414l2.393 2.393a.997.997 0 001.414 0 .999.999 0 000-1.414z"/>
                            </svg>
                        </div>
                        <input
                            id="action-search"
                            name="search"
                            value="{{ $search ?? '' }}"
                            class="form-input pl-9 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg"
                            type="search"
                            placeholder="Folio, contenedor..."
                        />
                    </div>

                    {{-- Datepicker rango de fechas --}}
                    <div class="relative flex items-center">
                        <input
                            name="date_range"
                            class="datepicker form-input pl-10 dark:bg-gray-800 text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100 font-medium w-[15.5rem]"
                            placeholder="Rango de fechas"
                            data-class="flatpickr-right"
                            data-no-default
                            data-selected="{{ $dateRange ?? '' }}"
                        />
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="shrink-0 text-gray-400 dark:text-gray-500"
                                width="18"
                                height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                        </div>
                    </div>

                    {{-- Botón Buscar --}}
                    <button type="submit" class="inline-flex items-center justify-center p-2 bg-gray-800 hover:bg-gray-900 dark:bg-gray-100 dark:hover:bg-white text-white dark:text-gray-800 rounded-lg transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 15.75-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </button>

                    @if($search || $dateRange)
                        <a href="{{ route('inventory-balances.show', $inventoryBalance) }}"
                           class="text-sm text-gray-500 hover:text-violet-500 underline whitespace-nowrap ml-2">
                            Limpiar
                        </a>
                    @endif
                </form>

                <a href="{{ route('inventory-balances.index') }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300 text-sm">
                    &larr; Regresar
                </a>
            </div>
        </div>

        {{-- Tabla de movimientos --}}
        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60">

            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">Movimientos</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="table-auto w-full dark:text-gray-300">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/60">
                        <tr>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Fecha y Hora</div>
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
                                <div class="font-semibold text-left">Origen</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Destino</div>
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

                                $locFrom = $mov?->locationFrom;
                                $locTo   = $mov?->locationTo;
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

                                {{-- Origen --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    @if($locFrom)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">
                                            {{ $locFrom->code }}
                                        </span>
                                        @if($locFrom->warehouse)
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $locFrom->warehouse->name }}</p>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>

                                {{-- Destino --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    @if($locTo)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-violet-100 text-violet-700 dark:bg-violet-500/20 dark:text-violet-400">
                                            {{ $locTo->code }}
                                        </span>
                                        @if($locTo->warehouse)
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $locTo->warehouse->name }}</p>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>

                                {{-- Referencia (contenedor) --}}
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
                                <td colspan="7" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500 text-sm">
                                    @if($search || $dateRange)
                                        No se encontraron movimientos con los filtros aplicados.
                                    @else
                                        No hay movimientos registrados para este artículo en esta ubicación.
                                    @endif
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
