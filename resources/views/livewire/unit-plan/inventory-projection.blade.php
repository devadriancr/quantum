<div>
    @if (!empty($projection))
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                {{ __('Proyección de inventario por día') }}
            </h2>
            <span class="text-xs text-gray-500">
                {{ count($projection) }} {{ __('item(s)') }}
            </span>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 overflow-auto max-h-[500px]">
            <table class="w-full table-fixed dark:text-gray-300">
                <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/60 sticky top-0 z-10">
                    <tr>
                        <th class="px-3 py-3 text-center text-[10px]">{{ __('N° Parte') }}</th>
                        @foreach ($days as $day)
                            <th class="px-3 py-3 text-center border-l border-gray-100 dark:border-gray-700/60">
                                {{ $day['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach ($projection as $row)
                        <tr class="border-b border-gray-100 dark:border-gray-700/60"
                            wire:key="proj-{{ $row['code'] }}">
                            <td class="px-2 py-2 text-center align-middle">
                                <div class="font-mono text-xs font-semibold text-gray-700 dark:text-gray-200">
                                    {{ $row['code'] }}
                                </div>
                                <div class="text-[10px] text-gray-400">
                                    {{ __('inicial:') }} {{ number_format($row['start'], 0) }}
                                </div>
                            </td>
                            @foreach ($days as $day)
                                @php
                                    $value = $row['by_day'][$day['value']] ?? 0;
                                    $prev  = $day['value'] === 1 ? $row['start'] : ($row['by_day'][$day['value'] - 1] ?? 0);
                                    $up    = $value > $prev;
                                @endphp
                                <td class="px-3 py-3 text-center border-l border-gray-100 dark:border-gray-700/60 text-sm
                                           {{ $up ? 'font-bold text-green-700 dark:text-green-400 bg-green-50/40 dark:bg-green-900/10' : 'text-gray-500 dark:text-gray-400' }}">
                                    {{ number_format($value, 0) }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
