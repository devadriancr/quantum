<x-app-layout>
    <x-toast-notifications />

    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Encabezado --}}
        <div class="sm:flex sm:justify-between sm:items-start mb-8 gap-4">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    {{ __('Recepción de Material') }}
                </h1>
            </div>

            {{-- Filtros --}}
            <form action="{{ route('reception.index') }}" method="GET"
                  class="flex flex-col sm:flex-row gap-2 items-start sm:items-center flex-wrap">

                {{-- Búsqueda texto --}}
                <div class="relative flex items-center">
                    <label for="action-search" class="sr-only">Buscar</label>
                    {{-- Ícono de lupa decorativo --}}
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500" width="16" height="16" viewBox="0 0 16 16">
                            <path d="M7 14c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zM7 2C4.243 2 2 4.243 2 7s2.243 5 5 5 5-2.243 5-5-2.243-5-5-5z"/>
                            <path d="M15.707 14.293L13.314 11.9a8.019 8.019 0 01-1.414 1.414l2.393 2.393a.997.997 0 001.414 0 .999.999 0 000-1.414z"/>
                        </svg>
                    </div>
                    <input
                        id="action-search"
                        name="search"
                        value="{{ $search ?? '' }}"
                        class="form-input pl-9 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-blue-300 rounded-lg"
                        type="search"
                        placeholder="{{ __('Documento, contenedor, socio...') }}"
                    />
                </div>

                {{-- Datepicker rango de fechas --}}
                <div class="relative flex items-center">
                    <input
                        name="date_range"
                        class="datepicker form-input pl-10 dark:bg-gray-800 text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100 font-medium w-[15.5rem]"
                        placeholder="{{ __('Rango de fechas') }}"
                        data-class="flatpickr-right"
                        data-no-default
                        data-selected="{{ $dateRange ?? '' }}"
                    />
                    {{-- El icono va DESPUÉS del input para que quede arriba --}}
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none" {{-- Cambiado de fill-current a none --}}
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor" {{-- Usamos stroke para iconos de líneas --}}
                            class="shrink-0 text-gray-400 dark:text-gray-500"
                            width="18"
                            height="18">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                    </div>
                </div>

                {{-- Botón Buscar Explícito --}}
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-800 hover:bg-gray-900 dark:bg-gray-100 dark:hover:bg-white text-white dark:text-gray-800 text-sm font-medium rounded-lg transition-colors shadow-sm">
                    {{ __('Buscar') }}
                </button>

                @if($search || $dateRange)
                    <a href="{{ route('reception.index') }}"
                       class="text-sm text-gray-500 hover:text-blue-500 underline whitespace-nowrap ml-2">
                        {{ __('Limpiar') }}
                    </a>
                @endif
            </form>
        </div>

        {{-- Filtro de fecha activo --}}
        @if($dateRange)
            <div class="flex items-center gap-2 mb-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                    {{ $dateRange }}
                    <a href="{{ route('reception.index', array_filter(['search' => $search])) }}"
                       class="ml-1 hover:text-blue-900 dark:hover:text-blue-100">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </a>
                </span>
            </div>
        @endif

        {{-- Tabla --}}
        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60">
            <div class="overflow-x-auto">
                <table class="table-auto w-full dark:text-gray-300">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-t border-b border-gray-100 dark:border-gray-700/60">
                        <tr>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('Contenedor') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('Fecha') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('Hora') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('Estado') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-right">{{ __('Acción') }}</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($documents as $doc)
                            @php
                                $statusColors = [
                                    'PENDING' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400',
                                    'PARTIAL' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                                ];
                                $statusLabels = \App\Models\ShipmentDocument::STATUS_OPTIONS;
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        {{ $doc->container->code ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        {{ $doc->document_date
                                            ? \Carbon\Carbon::parse($doc->document_date)->format('d/m/Y')
                                            : '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        {{ $doc->document_time
                                            ? \Carbon\Carbon::parse($doc->document_time)->format('H:i')
                                            : '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$doc->document_status] ?? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                        {{ $statusLabels[$doc->document_status] ?? $doc->document_status }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-right">

                                    {{-- Botón Escanear Modificado --}}
                                    <a href="{{ route('reception.show', $doc) }}"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium border bg-gray-800 hover:bg-gray-900 dark:bg-gray-100 dark:hover:bg-white text-white dark:text-gray-800 text-sm font-medium rounded-lg transition-colors shadow-sm">
                                       <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                                        </svg>
                                        {{ __('Escanear') }}
                                    </a>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                                    {{ __('No hay documentos pendientes de recepción.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if($documents->hasPages() || $documents->total() > 0)
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400 text-center sm:text-left">
                            {{ __('Mostrando') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $documents->firstItem() ?? 0 }}</span>
                            {{ __('a') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $documents->lastItem() ?? 0 }}</span>
                            {{ __('de') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $documents->total() }}</span>
                            {{ __('resultados') }}
                        </div>
                        <div class="flex justify-center sm:justify-end">
                            {{ $documents->links('pagination::simple-tailwind') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
