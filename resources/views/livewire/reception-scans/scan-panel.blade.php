<div class="space-y-4">

    {{-- Input de escaneo (solo cuando no está completado) --}}
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
                placeholder="Escanea aquí..."
                class="form-input w-full text-sm pr-10 border-2 border-violet-300 dark:border-violet-600 focus:border-violet-500 focus:ring-violet-500 rounded-lg"
            />
            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-violet-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z"/>
                </svg>
            </div>
        </div>

        {{-- Notificación de resultado --}}
        <div id="scan-result" class="hidden mt-3"></div>
    </div>
    @endif

    {{-- Historial de escaneos --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 overflow-hidden">
        <header class="px-4 py-3 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 dark:text-gray-100 text-sm">Historial de escaneos</h3>
            <span class="text-sm font-bold text-violet-600 dark:text-violet-400 bg-violet-50 dark:bg-violet-500/10 px-2 py-0.5 rounded">
                {{ $total }} Escaneo(s)
            </span>
        </header>
        <div class="overflow-x-auto max-h-80 overflow-y-auto">
            <table class="table-auto w-full">
                <thead class="text-xs font-semibold text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/60 sticky top-0">
                    <tr>
                        <th class="px-3 py-2 text-left">Tipo</th>
                        <th class="px-3 py-2 text-left">Artículo</th>
                        <th class="px-3 py-2 text-left">Serial</th>
                        <th class="px-3 py-2 text-right">Cant.</th>
                        <th class="px-3 py-2 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @forelse($scans as $scan)
                        @php
                            $statusColors = [
                                'MATCHED'   => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                                'UNMATCHED' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400',
                                'DUPLICATE' => 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400',
                                'ERROR'     => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                            ];
                            $statusLabels = ['MATCHED' => 'OK', 'UNMATCHED' => 'Sin doc.', 'DUPLICATE' => 'Duplicado', 'ERROR' => 'Error'];
                        @endphp
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10">
                            <td class="px-3 py-2">
                                <span class="text-xs font-bold px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                    {{ $scan['consignment_type'] ?? '—' }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $scan['item_code'] ?? '—' }}</span>
                                @if($scan['item_description'])
                                    <span class="block text-xs text-gray-400">{{ $scan['item_description'] }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400 max-w-[120px] truncate">
                                {{ $scan['serial'] ?? '—' }}
                            </td>
                            <td class="px-3 py-2 text-right text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ number_format($scan['qty'], 0) }}
                            </td>
                            <td class="px-3 py-2 text-center">
                                <span class="inline-flex px-1.5 py-0.5 rounded-full text-xs font-bold {{ $statusColors[$scan['status']] ?? '' }}">
                                    {{ $statusLabels[$scan['status']] ?? $scan['status'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-gray-400 text-sm italic">
                                Aún no se han escaneado piezas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
