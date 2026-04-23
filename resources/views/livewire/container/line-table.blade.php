<div>
    {{-- Buscador de artículos --}}
    <div class="relative mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Buscar artículo
        </label>
        <div class="relative">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Código o descripción..."
                autocomplete="off"
                class="form-input w-full text-sm pr-10 border-gray-200 dark:border-gray-700 rounded-lg dark:bg-gray-900 dark:text-gray-100"
            />
            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                @if(strlen($search) >= 2)
                    <svg wire:loading wire:target="search" class="size-4 text-violet-400 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                @endif
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-gray-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </div>
        </div>

        {{-- Resultados del buscador --}}
        @if(strlen($search) >= 2)
            <div class="absolute z-20 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg max-h-56 overflow-y-auto">
                @forelse($this->items as $item)
                    <button
                        type="button"
                        wire:click="addItem({{ $item->id }})"
                        class="w-full flex items-center justify-between px-4 py-2.5 text-sm hover:bg-violet-50 dark:hover:bg-violet-500/10 transition-colors text-left">
                        <div>
                            <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $item->code }}</span>
                            @if($item->description)
                                <span class="ml-2 text-gray-400 text-xs">{{ Str::limit($item->description, 50) }}</span>
                            @endif
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 text-violet-500 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                @empty
                    <p class="px-4 py-3 text-sm text-gray-400 italic text-center">Sin resultados.</p>
                @endforelse
            </div>
        @endif
    </div>

    {{-- Tabla de líneas seleccionadas --}}
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="table-auto w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-900/30 text-xs font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th class="px-3 py-2 text-left w-8">#</th>
                    <th class="px-3 py-2 text-left">Código</th>
                    <th class="px-3 py-2 text-left">Descripción</th>
                    <th class="px-3 py-2 text-right w-36">Cantidad</th>
                    <th class="px-3 py-2 w-10"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                @forelse($lines as $index => $line)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10">
                        <td class="px-3 py-2 text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-3 py-2 font-medium text-gray-800 dark:text-gray-100">
                            {{ $line['item_code'] }}
                            {{-- Hidden input para el submit del form --}}
                            <input type="hidden" name="lines[{{ $index }}][item_code]" value="{{ $line['item_code'] }}">
                        </td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400 text-xs">
                            {{ $line['item_description'] }}
                        </td>
                        <td class="px-3 py-2">
                            <input
                                type="number"
                                name="lines[{{ $index }}][quantity]"
                                wire:model="lines.{{ $index }}.quantity"
                                min="0.01"
                                step="0.01"
                                required
                                class="form-input w-full text-sm text-right border-gray-200 dark:border-gray-700 rounded-lg dark:bg-gray-900 dark:text-gray-100"
                            />
                        </td>
                        <td class="px-3 py-2 text-center">
                            <button
                                type="button"
                                wire:click="removeLine({{ $index }})"
                                class="text-red-400 hover:text-red-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-8 text-center text-gray-400 text-sm italic">
                            Sin líneas. Busca un artículo arriba para agregar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(count($lines) > 0)
        <p class="mt-2 text-xs text-gray-400">
            {{ count($lines) }} {{ count($lines) === 1 ? 'artículo' : 'artículos' }} agregados
        </p>
    @endif
</div>
