<x-app-layout>
    <x-toast-notifications />

    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-start mb-8 gap-4">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    {{ __('Contenedores') }}
                </h1>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 items-start sm:items-center flex-wrap">

                {{-- Filtros --}}
                <form action="{{ route('containers.index') }}" method="GET"
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
                            class="form-input pl-9 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-blue-300 rounded-lg"
                            type="search"
                            placeholder="{{ __('Buscar por código o estado...') }}"
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

                    {{-- Botón Buscar --}}
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-800 hover:bg-gray-900 dark:bg-gray-100 dark:hover:bg-white text-white dark:text-gray-800 text-sm font-medium rounded-lg transition-colors shadow-sm">
                        {{ __('Buscar') }}
                    </button>

                    @if($search || $dateRange)
                        <a href="{{ route('containers.index') }}"
                           class="text-sm text-gray-500 hover:text-blue-500 underline whitespace-nowrap ml-2">
                            {{ __('Limpiar') }}
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60">

            {{-- Header de la tarjeta con botones --}}
            <div class="flex items-center justify-end gap-2 px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <a href="{{ route('containers.create') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-800 hover:bg-blue-900 dark:bg-blue-100 dark:hover:bg-white dark:text-blue-800 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __('Nuevo Contenedor') }}
                </a>
                <button
                    onclick="document.getElementById('modal-import').classList.remove('hidden')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-800 hover:bg-green-900 dark:bg-green-100 dark:hover:bg-white dark:text-green-800 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                    </svg>
                    {{ __('Importar Excel') }}
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="table-auto w-full dark:text-gray-300">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-t border-b border-gray-100 dark:border-gray-700/60">
                        <tr>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('Código') }}</div>
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
                                <div class="font-semibold text-right">{{ __('Acciones') }}</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse ($containers as $container)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ $container->code }}</span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        {{ $container->estimated_arrival_date
                                            ? \Carbon\Carbon::parse($container->estimated_arrival_date)->format('d-m-Y')
                                            : '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        {{ $container->estimated_arrival_time
                                            ? \Carbon\Carbon::parse($container->estimated_arrival_time)->format('H:i:s')
                                            : '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'PENDING'    => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400',
                                            'EXPECTED'   => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                                            'ARRIVED'    => 'bg-teal-100 text-teal-700 dark:bg-teal-500/20 dark:text-teal-400',
                                            'UNLOADING'  => 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400',
                                            'INSPECTION' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400',
                                            'RECEIVED'   => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                                            'REJECTED'   => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                                            'IN_TRANSIT' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-400',
                                        ];
                                        $colorClass = $statusColors[$container->status] ?? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                        {{ \App\Models\Container::STATUS_OPTIONS[$container->status] ?? $container->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <div class="flex justify-end items-center gap-2">

                                        {{-- Botón Ver --}}
                                        <a href="{{ route('containers.show', $container) }}"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium border border-blue-200 dark:border-blue-500/30 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-colors" title="{{ __('Ver Detalles') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            {{ __('Ver') }}
                                        </a>

                                        {{-- Botón Editar --}}
                                        @if ($container->status === 'PENDING')
                                            <a href="{{ route('containers.edit', $container) }}"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium border border-amber-200 dark:border-amber-500/30 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                                {{ __('Editar') }}
                                            </a>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium border border-gray-100 dark:border-gray-800 text-gray-400 dark:text-gray-600 cursor-not-allowed bg-gray-50/50 dark:bg-transparent"
                                                title="{{ __('Solo se pueden editar contenedores pendientes') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                                </svg>
                                                {{ __('Editar') }}
                                            </span>
                                        @endif

                                        {{-- Botón Eliminar --}}
                                        @php
                                            $canDelete = $container->status === 'PENDING'
                                                && $container->stock_movement_lines_count == 0;
                                        @endphp
                                        @if ($canDelete)
                                            <form action="{{ route('containers.destroy', $container) }}" method="POST" id="form-delete-{{ $container->id }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        onclick="confirmDelete({{ $container->id }}, '{{ $container->code }}')"
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                    {{ __('Eliminar') }}
                                                </button>
                                            </form>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium border border-gray-100 dark:border-gray-800 text-gray-400 dark:text-gray-600 cursor-not-allowed bg-gray-50/50 dark:bg-transparent"
                                                title="{{ __('No se puede eliminar: el documento tiene líneas registradas, estado no pendiente o tiene movimientos de inventario') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                                </svg>
                                                {{ __('Eliminar') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                                    {{ __('No se encontraron contenedores.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if ($containers->hasPages() || $containers->total() > 0)
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400 text-center sm:text-left">
                            {{ __('Mostrando') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $containers->firstItem() ?? 0 }}</span>
                            {{ __('a') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $containers->lastItem() ?? 0 }}</span>
                            {{ __('de') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $containers->total() }}</span>
                            {{ __('resultados') }}
                        </div>
                        <div class="flex justify-center sm:justify-end">
                            {{ $containers->links('pagination::simple-tailwind') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>

    {{-- ================================================ --}}
    {{-- Modal Importar Excel                             --}}
    {{-- ================================================ --}}
    <div id="modal-import" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-4 p-6">

            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                    {{ __('Importar contenedores desde Excel') }}
                </h2>
                <button
                    onclick="document.getElementById('modal-import').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('containers.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Archivo Excel') }} <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="file"
                        name="excel_file"
                        accept=".xlsx,.xls,.csv"
                        required
                        class="block w-full text-sm text-gray-700 dark:text-gray-300
                               file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                               file:text-sm file:font-semibold
                               file:bg-blue-50 file:text-blue-700
                               hover:file:bg-blue-100 dark:file:bg-blue-500/20 dark:file:text-blue-300
                               border border-gray-200 dark:border-gray-700 rounded-lg p-1 bg-white dark:bg-gray-900"
                    />
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        type="button"
                        onclick="document.getElementById('modal-import').classList.add('hidden')"
                        class="px-4 py-2 rounded-lg text-sm font-medium border border-gray-300 dark:border-gray-600
                               text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        {{ __('Cancelar') }}
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 rounded-lg text-sm font-medium bg-blue-600 hover:bg-blue-700
                               text-white shadow-sm transition-colors">
                        {{ __('Importar') }}
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id, code) {
            const isDark = document.documentElement.classList.contains('dark');

            Swal.fire({
                title: `<span class="text-lg font-bold">${'{{ __("¿Eliminar contenedor?") }}'}</span>`,
                html: `
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        ${'{{ __("Estás a punto de borrar el contenedor") }}'}
                        <strong class="text-gray-800 dark:text-gray-100">${code}</strong>.<br>
                        ${'{{ __("Esta acción es irreversible.") }}'}
                    </div>
                `,
                icon: 'warning',
                iconColor: '#ef4444', // Rojo de Tailwind (red-500)
                showCancelButton: true,
                confirmButtonText: '{{ __("Sí, eliminar") }}',
                cancelButtonText: '{{ __("Cancelar") }}',
                reverseButtons: true,

                // Estilos personalizados con clases de Tailwind
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg mx-2 transition-colors',
                    cancelButton: 'inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg mx-2 transition-colors'
                },

                // Fondo y textos según el tema
                background: isDark ? '#111827' : '#ffffff', // gray-900 o blanco
                color: isDark ? '#f3f4f6' : '#1f2937',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-delete-' + id).submit();
                }
            });
        }

        // El resto de tu script (cierre de modal) se queda igual
        document.getElementById('modal-import').addEventListener('click', function (e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
