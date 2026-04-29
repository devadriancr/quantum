<div class="space-y-4">

    {{-- Input de escaneo (solo visible cuando el movimiento está en proceso) --}}
    @if(!$isCompleted)
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 p-5">
        <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400 mb-2">
            Escanear Etiqueta
        </label>
        <div class="relative">
            <input
                id="scan-input"
                type="text"
                autofocus
                autocomplete="off"
                class="form-input w-full text-sm pr-10 border-2 border-violet-300 dark:border-violet-600 focus:border-violet-500 focus:ring-violet-500 rounded-lg"
            />
            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-violet-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z"/>
                </svg>
            </div>
        </div>
        <div id="scan-result" class="hidden mt-3"></div>
    </div>
    @endif

    {{-- Tabla de salidas --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 overflow-hidden">
        <header class="px-5 py-3 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 dark:text-gray-100 text-sm">Material en esta entrega</h3>
            <div class="flex items-center gap-2">
                <span class="text-sm font-bold text-violet-600 dark:text-violet-400 bg-violet-50 dark:bg-violet-500/10 px-2 py-0.5 rounded">
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
                        <th class="px-5 py-3 whitespace-nowrap"><div class="font-semibold text-center">{{ __('Estado') }}</div></th>
                        @if(!$isCompleted)
                            <th class="px-5 py-3 whitespace-nowrap w-12"></th>
                        @endif
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
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-3">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
                                    </svg>
                                    Registrado
                                </span>
                            </td>
                            @if(!$isCompleted)
                                <td class="px-5 py-3 text-center">
                                    <button
                                        type="button"
                                        class="remove-line-btn inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                                        data-line-id="{{ $scan->id }}"
                                        title="Retornar al almacén de recibo"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        {{ __('Eliminar') }}
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isCompleted ? 4 : 5 }}" class="px-5 py-10 text-center text-gray-400 dark:text-gray-500 text-sm italic">
                                Aún no se han escaneado piezas para esta entrega.
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
