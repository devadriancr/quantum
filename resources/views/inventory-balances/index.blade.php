<x-app-layout>
    <style>
        .select2-container { width: 100% !important; max-width: 100% !important; }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px; padding-left: 12px; color: #374151;
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .select2-container .select2-selection--single {
            height: 38px !important; border: 1px solid #e5e7eb !important; border-radius: 0.5rem !important;
        }
        .dark .select2-container .select2-selection--single { background-color: #1f2937 !important; border-color: #374151 !important; }
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered { color: #d1d5db !important; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px !important; }
        .select2-dropdown { border-radius: 0.5rem; border-color: #e5e7eb; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); font-size: 0.875rem; }
        .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: rgb(139 92 246); }
        .dark .select2-dropdown { background-color: #1f2937; border-color: #374151; }
        .dark .select2-container--default .select2-results__option { color: #d1d5db; }
        .dark .select2-container--default .select2-search--dropdown .select2-search__field { background-color: #111827; border-color: #374151; color: #d1d5db; }
    </style>

    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    {{ __('Inventario') }}
                </h1>
            </div>

            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">

                {{-- Filtro por almacén con Select2 --}}
                <form method="GET" action="{{ route('inventory-balances.index') }}" id="warehouse-form">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <select id="warehouse-select" name="warehouse" class="warehouse-select" style="min-width: 220px;">
                        <option value="">Todos los almacenes</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ $warehouse == $wh->id ? 'selected' : '' }}>
                                [{{ $wh->code }}] {{ $wh->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                {{-- Búsqueda --}}
                <form action="{{ route('inventory-balances.index') }}" method="GET" class="relative flex items-center">
                    @if($warehouse)
                        <input type="hidden" name="warehouse" value="{{ $warehouse }}">
                    @endif
                    <label for="action-search" class="sr-only">Buscar</label>
                    <input
                        id="action-search"
                        name="search"
                        class="form-input pl-9 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg"
                        type="search"
                        placeholder="N° parte, descripción, ubicación..."
                        value="{{ $search }}"
                    />
                    <button class="absolute inset-0 right-auto group" type="submit" aria-label="Buscar">
                        <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400 ml-3 mr-2" width="16" height="16" viewBox="0 0 16 16">
                            <path d="M7 14c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zM7 2C4.243 2 2 4.243 2 7s2.243 5 5 5 5-2.243 5-5-2.243-5-5-5z" />
                            <path d="M15.707 14.293L13.314 11.9a8.019 8.019 0 01-1.414 1.414l2.393 2.393a.997.997 0 001.414 0 .999.999 0 000-1.414z" />
                        </svg>
                    </button>
                    @if($search)
                        <a href="{{ route('inventory-balances.index', $warehouse ? ['warehouse' => $warehouse] : []) }}"
                           class="ml-2 text-sm text-gray-500 hover:text-violet-500 underline whitespace-nowrap">
                            Limpiar
                        </a>
                    @endif
                </form>

            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60">
            <div class="overflow-x-auto">
                <table class="table-auto w-full dark:text-gray-300">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-t border-b border-gray-100 dark:border-gray-700/60">
                        <tr>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('Número de Parte') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('Ubicación') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('Almacén') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('Último Mov.') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-right">{{ __('Cantidad Actual') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-right">{{ __('Acciones') }}</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($balances as $balance)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10">

                                {{-- Número de Parte + Descripción --}}
                                <td class="px-5 py-3">
                                    <p class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $balance->item?->code ?? '—' }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 max-w-xs truncate" title="{{ $balance->item?->description }}">
                                        {{ $balance->item?->description ?? '' }}
                                    </p>
                                </td>

                                {{-- Ubicación --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">
                                        {{ $balance->location?->code ?? '—' }}
                                    </span>
                                    @if($balance->location?->name)
                                        <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">
                                            {{ $balance->location->name }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Almacén --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    @php $wh = $balance->location?->warehouse; @endphp
                                    @if($wh)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700 dark:bg-violet-500/20 dark:text-violet-400">
                                            {{ $wh->code }}
                                        </span>
                                        <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">{{ $wh->name }}</span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- Último Movimiento --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    @if($balance->last_movement_date)
                                        <p class="text-gray-700 dark:text-gray-300 text-sm">
                                            {{ \Carbon\Carbon::parse($balance->last_movement_date)->format('d/m/Y') }}
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                            {{ \Carbon\Carbon::parse($balance->last_movement_date)->format('H:i') }}
                                        </p>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- Cantidad Actual --}}
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <span class="text-sm text-gray-800 dark:text-gray-100 text-base">
                                        {{ number_format($balance->current_quantity, 0) }}
                                    </span>
                                </td>

                                {{-- Acciones --}}
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('inventory-balances.show', $balance) }}"
                                        class="inline-flex items-center gap-1 font-medium text-violet-500 hover:text-violet-600 dark:hover:text-violet-400 text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            {{ __('Ver') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500 text-sm">
                                    @if($search || $warehouse)
                                        No se encontraron registros con los filtros aplicados.
                                    @else
                                        No hay balances de inventario registrados aún.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($balances->hasPages() || $balances->total() > 0)
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400 text-center sm:text-left">
                            {{ __('Mostrando') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $balances->firstItem() ?? 0 }}</span>
                            {{ __('a') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $balances->lastItem() ?? 0 }}</span>
                            {{ __('de') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $balances->total() }}</span>
                            {{ __('resultados') }}
                        </div>
                        <div class="flex justify-center sm:justify-end">
                            {{ $balances->appends(request()->query())->links('pagination::simple-tailwind') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#warehouse-select').select2({
                placeholder: 'Todos los almacenes',
                allowClear: true,
                width: '100%',
                language: { noResults: () => 'Sin resultados', searching: () => 'Buscando...' },
            }).on('change', function () {
                document.getElementById('warehouse-form').submit();
            });
        });
    </script>

</x-app-layout>
