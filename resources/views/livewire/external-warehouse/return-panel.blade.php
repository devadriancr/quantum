<div class="space-y-4">

    @if(!$isCompleted)
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 p-5">
        <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400 mb-2">
            Escanear Etiqueta — Retorno de {{ $locationFrom }} a {{ $locationTo }}
        </label>
        <div class="relative">
            <input
                id="scan-input"
                type="text"
                autofocus
                autocomplete="off"
                class="form-input w-full text-sm pr-10 border-2 border-amber-300 dark:border-amber-600 focus:border-amber-500 focus:ring-amber-500 rounded-lg"
                placeholder="Escanea el material que regresa de L61..."
            />
            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-amber-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z"/>
                </svg>
            </div>
        </div>
        <div id="scan-result" class="hidden mt-3"></div>
    </div>
    @else
    <div class="bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-xl p-4 flex items-center gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 text-green-500 shrink-0">
            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
        </svg>
        <span class="text-sm font-semibold text-green-700 dark:text-green-300">
            Retorno completado — material disponible en {{ $locationTo }} para salida a producción.
        </span>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 overflow-hidden">
        <header class="px-5 py-3 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 dark:text-gray-100 text-sm">Material retornado</h3>
            <div class="flex items-center gap-2">
                <span class="text-sm font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 px-2 py-0.5 rounded">
                    {{ $total }} Pieza(s)
                </span>
            </div>
        </header>

        <div class="overflow-x-auto">
            <table class="table-auto w-full dark:text-gray-300">
                <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/60">
                    <tr>
                        <th class="px-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">{{ __('N° Parte') }}</div></th>
                        <th class="px-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">{{ __('Serial / Lote') }}</div></th>
                        <th class="px-5 py-3 whitespace-nowrap"><div class="font-semibold text-right">{{ __('Cantidad') }}</div></th>
                        <th class="px-5 py-3 whitespace-nowrap"><div class="font-semibold text-center">{{ __('Destino') }}</div></th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                    @forelse($scans as $scan)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-800 dark:text-gray-100">{{ $scan->item?->code ?? '—' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 max-w-xs truncate">{{ $scan->item?->description ?? '' }}</p>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="font-mono text-xs text-gray-600 dark:text-gray-400">
                                    {{ $scan->serial_batch_number ?? '—' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-right">
                                <span class="font-medium text-gray-800 dark:text-gray-100">
                                    {{ number_format($scan->quantity_received, 0) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400">
                                    {{ $locationTo }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-gray-400 dark:text-gray-500 text-sm italic">
                                Aún no se han escaneado piezas para este retorno.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($scans->hasPages())
            <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Mostrando
                        <span class="font-medium text-gray-800 dark:text-gray-100">{{ $scans->firstItem() }}</span>
                        a
                        <span class="font-medium text-gray-800 dark:text-gray-100">{{ $scans->lastItem() }}</span>
                        de
                        <span class="font-medium text-gray-800 dark:text-gray-100">{{ $scans->total() }}</span>
                    </div>
                    <div>{{ $scans->links('pagination::simple-tailwind') }}</div>
                </div>
            </div>
        @endif
    </div>

</div>
