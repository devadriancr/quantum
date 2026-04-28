<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 overflow-hidden self-start">
    <header class="px-4 py-3 border-b border-gray-100 dark:border-gray-700/60">
        <h3 class="font-semibold text-gray-800 dark:text-gray-100 text-sm">Progreso del documento</h3>
    </header>
    <div class="divide-y divide-gray-100 dark:divide-gray-700/60 max-h-[560px] overflow-y-auto">
        @forelse($lines as $line)
            @php
                $percent = $line['declared'] > 0
                    ? min(100, round(($line['received'] / $line['declared']) * 100))
                    : 0;

                $statusColors = [
                    'PENDING'     => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400',
                    'RECEIVED'    => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                    'DAMAGED'     => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                    'EXPECTED'    => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                    'DISCREPANCY' => 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400',
                ];
                $statusLabels = [
                    'PENDING'     => 'Pendiente',
                    'RECEIVED'    => 'Recibido',
                    'DAMAGED'     => 'Dañado',
                    'EXPECTED'    => 'Esperado',
                    'DISCREPANCY' => 'Discrepancia',
                ];
                $barColor = $line['status'] === 'RECEIVED' ? 'bg-green-500' : 'bg-violet-500';
            @endphp
            <div class="px-4 py-3">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-200 truncate max-w-[120px]">
                        {{ $line['item_code'] }}
                    </span>
                    <span class="inline-flex px-1.5 py-0.5 rounded text-xs font-bold {{ $statusColors[$line['status']] ?? '' }}">
                        {{ $statusLabels[$line['status']] ?? $line['status'] }}
                    </span>
                </div>
                <p class="text-xs text-gray-400 mb-2 truncate">{{ $line['item_description'] }}</p>
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                        <div class="{{ $barColor }} h-1.5 rounded-full transition-all duration-500"
                             style="width: {{ $percent }}%">
                        </div>
                    </div>
                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap">
                        {{ number_format($line['received'], 0) }} / {{ number_format($line['declared'], 0) }}
                    </span>
                </div>
            </div>
        @empty
            <p class="px-5 py-6 text-sm text-gray-400 text-center italic">Sin líneas en este documento.</p>
        @endforelse
    </div>
</div>
