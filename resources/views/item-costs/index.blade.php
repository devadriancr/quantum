<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Historial de Costos</h1>
            </div>
            <form action="{{ route('item-costs.index') }}" method="GET" class="relative flex items-center">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M7 14c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zM7 2C4.243 2 2 4.243 2 7s2.243 5 5 5 5-2.243 5-5-2.243-5-5-5z"/>
                        <path d="M15.707 14.293L13.314 11.9a8.019 8.019 0 01-1.414 1.414l2.393 2.393a.997.997 0 001.414 0 .999.999 0 000-1.414z"/>
                    </svg>
                </div>
                <input name="search" value="{{ $search }}" type="search"
                       class="form-input pl-9 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg"
                       placeholder="N° parte, descripción...">
                @if($search)
                    <a href="{{ route('item-costs.index') }}" class="ml-2 text-sm text-gray-500 hover:text-violet-500 underline whitespace-nowrap">Limpiar</a>
                @endif
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60">
            <div class="overflow-x-auto">
                <table class="table-auto w-full dark:text-gray-300">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-t border-b border-gray-100 dark:border-gray-700/60">
                        <tr>
                            <th class="px-5 py-3 text-left">Item</th>
                            <th class="px-5 py-3 text-center">Moneda</th>
                            <th class="px-5 py-3 text-right">Costo</th>
                            <th class="px-5 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($items as $item)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10">

                                {{-- Columna Item (Código + Descripción) --}}
                                <td class="px-5 py-3">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-gray-800 dark:text-gray-100">
                                            {{ $item->code }}
                                        </span>
                                        <span class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5 max-w-sm truncate leading-tight" title="{{ $item->description }}">
                                            {{ $item->description ?? 'Sin descripción' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Columna Moneda --}}
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    @if($item->lastCost && $item->lastCost->currency)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">
                                            {{ $item->lastCost->currency->code }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- Columna Costo --}}
                                <td class="px-5 py-3 text-right whitespace-nowrap">
                                    <span class="font-mono font-medium text-gray-800 dark:text-gray-100">
                                        @if($item->lastCost)
                                            {{ $item->lastCost->currency?->symbol }}{{ number_format($item->lastCost->total_cost, 4) }}
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </span>
                                </td>

                                {{-- Columna Acciones --}}
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <a href="{{ route('item-costs.show', $item) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        Ver historial
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500 text-sm">
                                    @if($search)
                                        No se encontraron artículos con esa búsqueda.
                                    @else
                                        No hay historial de costos. Ejecuta <code class="font-mono bg-gray-100 dark:bg-gray-700 px-1 rounded">php artisan sync:vendor-item-costs</code> para sincronizar.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($items->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Mostrando <span class="font-medium text-gray-800 dark:text-gray-100">{{ $items->firstItem() }}</span>
                            a <span class="font-medium text-gray-800 dark:text-gray-100">{{ $items->lastItem() }}</span>
                            de <span class="font-medium text-gray-800 dark:text-gray-100">{{ $items->total() }}</span> resultados
                        </div>
                        <div>{{ $items->links('pagination::simple-tailwind') }}</div>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
