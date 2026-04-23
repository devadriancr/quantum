<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    {{ __('Historial de Recepciones') }}
                </h1>
            </div>

            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">

                {{-- Datepicker de rango --}}
                <form method="GET" action="{{ route('stock-movements.index') }}" id="filter-form">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @foreach((array) request('movement_type', []) as $mt)
                        <input type="hidden" name="movement_type[]" value="{{ $mt }}">
                    @endforeach
                    @foreach((array) request('status', []) as $st)
                        <input type="hidden" name="status[]" value="{{ $st }}">
                    @endforeach

                    <div class="relative">
                        <input
                            id="date-range-input"
                            class="datepicker form-input pl-9 dark:bg-gray-800 text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100 font-medium w-[15.5rem]"
                            placeholder="Seleccionar fechas"
                            data-class="flatpickr-right"
                            autocomplete="off"
                            readonly
                        />
                        <input type="hidden" name="date_from" id="date_from" value="{{ request('date_from') }}">
                        <input type="hidden" name="date_to"   id="date_to"   value="{{ request('date_to') }}">
                        <div class="absolute inset-0 right-auto flex items-center pointer-events-none">
                            <svg class="fill-current text-gray-400 dark:text-gray-500 ml-3" width="16" height="16" viewBox="0 0 16 16">
                                <path d="M5 4a1 1 0 0 0 0 2h6a1 1 0 1 0 0-2H5Z"/>
                                <path d="M4 0a4 4 0 0 0-4 4v8a4 4 0 0 0 4 4h8a4 4 0 0 0 4-4V4a4 4 0 0 0-4-4H4ZM2 4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V4Z"/>
                            </svg>
                        </div>
                    </div>
                </form>

                {{-- Filtro dropdown --}}
                <div class="relative inline-flex" x-data="{ open: false }">
                    <button
                        class="btn px-2.5 bg-white dark:bg-gray-800 border-gray-200 hover:border-gray-300 dark:border-gray-700/60 dark:hover:border-gray-600 text-gray-400 dark:text-gray-500"
                        aria-haspopup="true"
                        @click.prevent="open = !open"
                        :aria-expanded="open"
                    >
                        <span class="sr-only">Filtros</span>
                        <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16">
                            <path d="M0 3a1 1 0 0 1 1-1h14a1 1 0 1 1 0 2H1a1 1 0 0 1-1-1ZM3 8a1 1 0 0 1 1-1h8a1 1 0 1 1 0 2H4a1 1 0 0 1-1-1ZM7 12a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2H7Z"/>
                        </svg>
                    </button>
                    <div
                        class="origin-top-right z-10 absolute top-full right-0 min-w-56 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 pt-1.5 rounded-lg shadow-lg overflow-hidden mt-1"
                        @click.outside="open = false"
                        @keydown.escape.window="open = false"
                        x-show="open"
                        x-transition:enter="transition ease-out duration-200 transform"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-out duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        x-cloak
                    >
                        <form method="GET" action="{{ route('stock-movements.index') }}" id="dropdown-filter-form">
                            @if(request('date_from'))
                                <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                            @endif
                            @if(request('date_to'))
                                <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                            @endif
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif

                            <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase pt-1.5 pb-2 px-3">
                                Tipo de movimiento
                            </div>
                            <ul class="mb-2">
                                @foreach(\App\Models\StockMovement::MOVEMENT_TYPES as $val => $label)
                                    <li class="py-1 px-3">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="movement_type[]" value="{{ $val }}" class="form-checkbox"
                                                {{ in_array($val, (array) request('movement_type', [])) ? 'checked' : '' }}>
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase pb-2 px-3 border-t border-gray-100 dark:border-gray-700/60 pt-2">
                                Estado
                            </div>
                            <ul class="mb-2">
                                @foreach(\App\Models\StockMovement::STATUS_OPTIONS as $val => $label)
                                    <li class="py-1 px-3">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="status[]" value="{{ $val }}" class="form-checkbox"
                                                {{ in_array($val, (array) request('status', [])) ? 'checked' : '' }}>
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="py-2 px-3 border-t border-gray-200 dark:border-gray-700/60 bg-gray-50 dark:bg-gray-700/20">
                                <ul class="flex items-center justify-between">
                                    <li>
                                        <a href="{{ route('stock-movements.index') }}"
                                           class="btn-xs bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-red-500">
                                            Limpiar
                                        </a>
                                    </li>
                                    <li>
                                        <button type="submit" @click="open = false"
                                            class="btn-xs bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-300">
                                            Aplicar
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Búsqueda --}}
                <form action="{{ route('stock-movements.index') }}" method="GET" class="relative flex items-center">
                    @foreach((array) request('movement_type', []) as $mt)
                        <input type="hidden" name="movement_type[]" value="{{ $mt }}">
                    @endforeach
                    @foreach((array) request('status', []) as $st)
                        <input type="hidden" name="status[]" value="{{ $st }}">
                    @endforeach
                    @if(request('date_from'))
                        <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    @endif
                    @if(request('date_to'))
                        <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                    @endif

                    <label for="action-search" class="sr-only">Search</label>
                    <input
                        id="action-search"
                        name="search"
                        class="form-input pl-9 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg"
                        type="search"
                        placeholder="{{ __('N° parte, descripción, contenedor, serial...') }}"
                        value="{{ request('search') }}"
                    />
                    <button class="absolute inset-0 right-auto group" type="submit" aria-label="Search">
                        <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400 ml-3 mr-2" width="16" height="16" viewBox="0 0 16 16">
                            <path d="M7 14c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zM7 2C4.243 2 2 4.243 2 7s2.243 5 5 5 5-2.243 5-5-2.243-5-5-5z" />
                            <path d="M15.707 14.293L13.314 11.9a8.019 8.019 0 01-1.414 1.414l2.393 2.393a.997.997 0 001.414 0 .999.999 0 000-1.414z" />
                        </svg>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('stock-movements.index', request()->except('search')) }}"
                           class="ml-2 text-sm text-gray-500 hover:text-violet-500 underline whitespace-nowrap">
                            {{ __('Limpiar') }}
                        </a>
                    @endif
                </form>

            </div>
        </div>

        {{-- Chips de filtros activos --}}
        @if(request()->hasAny(['date_from','date_to','movement_type','status']))
            <div class="flex flex-wrap gap-2 mb-4">
                @if(request('date_from') || request('date_to'))
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">
                        Fechas: {{ request('date_from') }} → {{ request('date_to') }}
                        <a href="{{ request()->fullUrlWithQuery(['date_from' => null, 'date_to' => null]) }}" class="hover:text-blue-900 ml-1">✕</a>
                    </span>
                @endif
                @foreach((array) request('movement_type', []) as $mt)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300">
                        Tipo: {{ \App\Models\StockMovement::MOVEMENT_TYPES[$mt] ?? $mt }}
                    </span>
                @endforeach
                @foreach((array) request('status', []) as $st)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                        Estado: {{ \App\Models\StockMovement::STATUS_OPTIONS[$st] ?? $st }}
                    </span>
                @endforeach
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60">
            <div class="overflow-x-auto">
                <table class="table-auto w-full dark:text-gray-300">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-t border-b border-gray-100 dark:border-gray-700/60">
                        <tr>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('N° Parte') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('Descripción') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('Serial / Lote') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-right">{{ __('Cantidad') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('Tipo') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('Fecha') }}</div>
                            </th>
                            {{-- <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-right">{{ __('Acciones') }}</div>
                            </th> --}}
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($lines as $line)
                            <tr>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $line->item?->code ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        {{ $line->item?->description ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        {{ $line->serial_batch_number ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <span class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ number_format($line->quantity_received, 0) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    @php
                                        $typeColors = [
                                            'INBOUND'    => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                                            'OUTBOUND'   => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                                            'ADJUSTMENT' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400',
                                            'TRANSFER'   => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                                            'RETURN'     => 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400',
                                        ];
                                        $movType = $line->stockMovement?->movement_type;
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$movType] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ \App\Models\StockMovement::MOVEMENT_TYPES[$movType] ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        {{ $line->stockMovement?->movement_date
                                            ? \Carbon\Carbon::parse($line->stockMovement->movement_date)->format('d/m/Y')
                                            : '—' }}
                                    </span>
                                </td>
                                {{-- <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('stock-movements.show', $line->stock_movement_id) }}"
                                           class="inline-flex items-center gap-1 font-medium text-violet-500 hover:text-violet-600 dark:hover:text-violet-400 text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            {{ __('Ver') }}
                                        </a>
                                    </div>
                                </td> --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                                    {{ __('No se encontraron recepciones que coincidan con tu búsqueda.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($lines->hasPages() || $lines->total() > 0)
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400 text-center sm:text-left">
                            {{ __('Mostrando') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $lines->firstItem() ?? 0 }}</span>
                            {{ __('a') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $lines->lastItem() ?? 0 }}</span>
                            {{ __('de') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $lines->total() }}</span>
                            {{ __('resultados') }}
                        </div>
                        <div class="flex justify-center sm:justify-end">
                            {{ $lines->links('pagination::simple-tailwind') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>

    {{-- Flatpickr --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script>
        const currentDateFrom = document.getElementById('date_from').value;
        const currentDateTo   = document.getElementById('date_to').value;

        const fpConfig = {
            mode: 'range',
            dateFormat: 'Y-m-d',
            locale: 'es',
            onChange: function(selectedDates) {
                if (selectedDates.length === 2) {
                    document.getElementById('date_from').value = selectedDates[0].toISOString().split('T')[0];
                    document.getElementById('date_to').value   = selectedDates[1].toISOString().split('T')[0];
                    document.getElementById('filter-form').submit();
                }
            },
        };

        if (currentDateFrom && currentDateTo) {
            fpConfig.defaultDate = [currentDateFrom, currentDateTo];
        }

        flatpickr('#date-range-input', fpConfig);
    </script>
</x-app-layout>
