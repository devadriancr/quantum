<x-app-layout>
    <x-toast-notifications />

    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-start mb-8 gap-4">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    {{ __('Salida a Línea de Producción') }}
                </h1>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 items-start sm:items-center flex-wrap">

                {{-- Filtros --}}
                <form action="{{ route('material-outputs.index') }}" method="GET"
                      class="flex flex-col sm:flex-row gap-2 items-start sm:items-center flex-wrap">

                    {{-- Búsqueda texto --}}
                    <div class="relative flex items-center">
                        <label for="action-search" class="sr-only">Buscar</label>
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
                            class="form-input pl-9 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg"
                            type="search"
                            placeholder="{{ __('Folio, N° parte...') }}"
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
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="shrink-0 text-gray-400 dark:text-gray-500"
                                width="18"
                                height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                        </div>
                    </div>

                    {{-- Botón Buscar (solo icono) --}}
                    <button type="submit" class="inline-flex items-center justify-center p-2 bg-gray-800 hover:bg-gray-900 dark:bg-gray-100 dark:hover:bg-white text-white dark:text-gray-800 rounded-lg transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 15.75-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </button>

                    @if($search || $dateRange)
                        <a href="{{ route('material-outputs.index') }}"
                           class="text-sm text-gray-500 hover:text-violet-500 underline whitespace-nowrap ml-2">
                            {{ __('Limpiar') }}
                        </a>
                    @endif
                </form>

                {{-- Botón Nueva Entrega --}}
                <form action="{{ route('material-outputs.store') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-800 hover:bg-gray-900 dark:bg-gray-100 dark:hover:bg-white text-white dark:text-gray-800 text-sm font-medium rounded-lg transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        {{ __('Nueva Entrega') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60">
            <div class="overflow-x-auto">
                <table class="table-auto w-full dark:text-gray-300">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-t border-b border-gray-100 dark:border-gray-700/60">
                        <tr>
                            <th class="px-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">{{ __('Folio') }}</div></th>
                            <th class="px-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">{{ __('Fecha y Hora') }}</div></th>
                            <th class="px-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">{{ __('Estado') }}</div></th>
                            <th class="px-5 py-3 whitespace-nowrap"><div class="font-semibold text-right">{{ __('Acciones') }}</div></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($movements as $movement)
                            @php
                                $statusMap = [
                                    'PENDING'   => ['label' => 'En proceso', 'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400'],
                                    'COMPLETED' => ['label' => 'Finalizado',  'class' => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400'],
                                ];
                                $status = $statusMap[$movement->status] ?? ['label' => $movement->status, 'class' => 'bg-gray-100 text-gray-600'];
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">

                                {{-- Folio --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $movement->movement_number }}
                                    </span>
                                </td>

                                {{-- Fecha y Hora --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($movement->movement_date)->format('d-m-Y') }}
                                    </span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500 block">
                                        {{ $movement->movement_time ? \Carbon\Carbon::parse($movement->movement_time)->format('H:i:s') : '' }}
                                    </span>
                                </td>

                                {{-- Estado --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['class'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </td>

                                {{-- Acciones --}}
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <div class="flex justify-end items-center gap-2">

                                        {{-- Botón Escanear / Ver --}}
                                        <a href="{{ route('material-outputs.show', $movement) }}"
                                           class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium border transition-colors
                                               {{ $movement->status === 'PENDING'
                                                   ? 'border-violet-200 dark:border-violet-500/30 text-violet-600 dark:text-violet-400 hover:bg-violet-50 dark:hover:bg-violet-500/10'
                                                   : 'border-blue-200 dark:border-blue-500/30 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/10' }}">
                                            @if($movement->status === 'PENDING')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                                                </svg>
                                                {{ __('Escanear') }}
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                                </svg>
                                                {{ __('Ver') }}
                                            @endif
                                        </a>

                                        {{-- Botón Eliminar (solo si está vacío) --}}
                                        @if($movement->lines->count() === 0)
                                            <form method="POST" action="{{ route('material-outputs.destroy', $movement) }}"
                                                  id="form-delete-{{ $movement->id }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        onclick="confirmDelete({{ $movement->id }}, '{{ $movement->movement_number }}')"
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                    {{ __('Eliminar') }}
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500 text-sm">
                                    @if($search || $dateRange)
                                        {{ __('No se encontraron entregas con los filtros aplicados.') }}
                                    @else
                                        {{ __('No hay entregas registradas.') }}
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($movements->hasPages() || $movements->total() > 0)
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400 text-center sm:text-left">
                            {{ __('Mostrando') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $movements->firstItem() ?? 0 }}</span>
                            {{ __('a') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $movements->lastItem() ?? 0 }}</span>
                            {{ __('de') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $movements->total() }}</span>
                            {{ __('resultados') }}
                        </div>
                        <div class="flex justify-center sm:justify-end">
                            {{ $movements->links('pagination::simple-tailwind') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id, folio) {
            const isDark = document.documentElement.classList.contains('dark');

            Swal.fire({
                title: '¿Eliminar entrega?',
                html: `El folio <strong>${folio}</strong> se eliminará de forma permanente.`,
                icon: 'warning',
                iconColor: '#ef4444',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg mx-2 transition-colors',
                    cancelButton:  'inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg mx-2 transition-colors',
                },
                background: isDark ? '#111827' : '#ffffff',
                color:      isDark ? '#f3f4f6' : '#1f2937',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-delete-' + id).submit();
                }
            });
        }
    </script>
</x-app-layout>
