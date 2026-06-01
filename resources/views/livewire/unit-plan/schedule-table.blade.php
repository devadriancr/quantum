<div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 overflow-x-auto">
    <table class="w-full table-fixed dark:text-gray-300">
        <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/60">
            <tr>
                <th class="px-3 py-3 text-center text-[10px]">{{ __('Hora') }}</th>
                @foreach ($days as $day)
                    <th class="px-3 py-3 text-center border-l border-gray-100 dark:border-gray-700/60">
                        {{ $day['label'] }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="text-sm">
            @for ($slotIdx = 1; $slotIdx <= 4; $slotIdx++)
                <tr class="border-b border-gray-100 dark:border-gray-700/60">
                    <td class="px-2 py-2 align-middle">
                        <input
                            type="time"
                            wire:model.live="$parent.slotTimes.{{ $slotIdx }}"
                            class="form-input w-full text-xs {{ $this->hasSlotTimeConflict($slotIdx) ? 'border-red-500' : '' }}"
                        />
                    </td>
                    @foreach ($days as $day)
                        @php $cell = $schedule[$day['value']][$slotIdx] ?? null; @endphp
                        <td class="border-l border-gray-100 dark:border-gray-700/60 align-top p-2">
                            <div class="kanban-slot min-h-16 rounded-md border-2 border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/30 p-1 flex items-center justify-center"
                                 data-day="{{ $day['value'] }}"
                                 data-slot="{{ $slotIdx }}">
                                @if ($cell)
                                    <div class="kanban-card w-full bg-violet-100 dark:bg-violet-900/40 border border-violet-400 text-violet-800 dark:text-violet-200 rounded px-2 py-2 text-center cursor-move"
                                         data-container-code="{{ $cell['code'] }}"
                                         wire:key="slot-{{ $day['value'] }}-{{ $slotIdx }}-{{ $cell['code'] }}">
                                        <p class="font-bold text-xs truncate">{{ $cell['code'] }}</p>
                                        <p class="text-[10px] opacity-75">${{ number_format((float) $cell['price'], 2) }}</p>
                                    </div>
                                @endif
                            </div>
                        </td>
                    @endforeach
                </tr>
            @endfor

            <tr class="bg-gray-50 dark:bg-gray-900/30">
                <td class="px-2 py-3 text-xs font-semibold text-gray-600 text-center">$</td>
                @foreach ($days as $day)
                    <td class="px-2 py-3 text-center border-l border-gray-100 dark:border-gray-700/60 font-bold text-green-700 dark:text-green-400 text-sm">
                        ${{ number_format($dayTotals[$day['value']], 2) }}
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>
</div>
