<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Header --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    {{ $inventoryBalance->item?->code }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $inventoryBalance->item?->description }}
                </p>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('inventory-balances.index') }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                    &larr; {{ __('Regresar') }}
                </a>
            </div>
        </div>

        {{-- Info del balance --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-xs px-5 py-4">
                <div class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 mb-1">Ubicación</div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">
                    {{ $inventoryBalance->location?->code ?? '—' }}
                </span>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-xs px-5 py-4">
                <div class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 mb-1">Almacén</div>
                <div class="font-medium text-gray-800 dark:text-gray-100 text-sm">
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
                <div class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 mb-1">Último Movimiento</div>
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    {{ $inventoryBalance->last_movement_date
                        ? \Carbon\Carbon::parse($inventoryBalance->last_movement_date)->format('d/m/Y H:i')
                        : '—' }}
                </div>
            </div>
        </div>

        {{-- Historial de movimientos --}}
        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">Historial de movimientos</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="table-auto w-full dark:text-gray-300">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/60">
                        <tr>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('Fecha y Hora') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('Serial / Lote') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-center">{{ __('Estado') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('Ubicación') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-right">{{ __('Cantidad') }}</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($movements as $line)
                            @php
                                $mov      = $line->stockMovement;
                                $isEntry  = $mov?->location_id_to == $inventoryBalance->location_id;
                                $location = $isEntry ? $mov?->locationTo : $mov?->locationFrom;
                            @endphp
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10">

                                {{-- Fecha y hora --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <p class="text-gray-700 dark:text-gray-300">
                                        {{ \Carbon\Carbon::parse($line->created_at)->format('d/m/Y') }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ \Carbon\Carbon::parse($line->created_at)->format('H:i:s') }}
                                    </p>
                                </td>

                                {{-- Serial --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="font-mono text-gray-700 dark:text-gray-300 text-xs">
                                        {{ $line->serial_batch_number ?? '—' }}
                                    </span>
                                </td>

                                {{-- Estado: Entrada / Salida --}}
                                <td class="px-5 py-3 whitespace-nowrap text-center">
                                    @if($isEntry)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                            </svg>
                                            Entrada
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                            </svg>
                                            Salida
                                        </span>
                                    @endif
                                </td>

                                {{-- Ubicación --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">
                                        {{ $location?->code ?? '—' }}
                                    </span>
                                </td>

                                {{-- Cantidad --}}
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <span class="font-medium {{ $isEntry ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                                        {{ $isEntry ? '+' : '-' }}{{ number_format($line->quantity_received, 0) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500 text-sm">
                                    No hay movimientos registrados para este artículo en esta ubicación.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($movements->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Mostrando
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $movements->firstItem() }}</span>
                            a
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $movements->lastItem() }}</span>
                            de
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $movements->total() }}</span>
                        </div>
                        <div>{{ $movements->links('pagination::simple-tailwind') }}</div>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
