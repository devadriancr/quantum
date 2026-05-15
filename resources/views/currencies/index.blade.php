<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Monedas</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end items-center gap-2">

                {{-- Búsqueda --}}
                <form action="{{ route('currencies.index') }}" method="GET" class="relative flex items-center">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500" width="16" height="16" viewBox="0 0 16 16">
                            <path d="M7 14c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zM7 2C4.243 2 2 4.243 2 7s2.243 5 5 5 5-2.243 5-5-2.243-5-5-5z"/>
                            <path d="M15.707 14.293L13.314 11.9a8.019 8.019 0 01-1.414 1.414l2.393 2.393a.997.997 0 001.414 0 .999.999 0 000-1.414z"/>
                        </svg>
                    </div>
                    <input name="search" value="{{ $search }}" type="search"
                           class="form-input pl-9 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg text-sm"
                           placeholder="Código o nombre...">
                    @if($search)
                        <a href="{{ route('currencies.index') }}" class="ml-2 text-sm text-gray-500 hover:text-violet-500 underline whitespace-nowrap">Limpiar</a>
                    @endif
                </form>

                {{-- Actualizar tasas --}}
                <form action="{{ route('currencies.refresh-rates') }}" method="POST" class="flex items-center">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-violet-600 dark:text-violet-400 border border-violet-300 dark:border-violet-600 rounded-lg hover:bg-violet-50 dark:hover:bg-violet-900/20 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Actualizar tasas
                    </button>
                </form>

                {{-- Nueva --}}
                <a href="{{ route('currencies.create') }}"
                   class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white inline-flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nueva Moneda
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">{{ session('error') }}</div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60">
            <div class="overflow-x-auto">
                <table class="table-auto w-full dark:text-gray-300">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-t border-b border-gray-100 dark:border-gray-700/60">
                        <tr>
                            <th class="px-5 py-3 text-left">Código</th>
                            <th class="px-5 py-3 text-left">Nombre</th>
                            <th class="px-5 py-3 text-center">Símbolo</th>
                            <th class="px-5 py-3 text-center">Estado</th>
                            <th class="px-5 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($currencies as $currency)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10">
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="font-mono font-semibold text-gray-800 dark:text-gray-100">{{ $currency->code }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="text-gray-700 dark:text-gray-300">{{ $currency->name }}</span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        {{ $currency->symbol }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if($currency->active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">Activa</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Inactiva</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('currencies.edit', $currency) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 border border-blue-300 dark:border-blue-600 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                            </svg>
                                            Editar
                                        </a>

                                        {{-- Formulario modificado con data-code para personalizar la alerta --}}
                                        <form method="POST" action="{{ route('currencies.destroy', $currency) }}" data-code="{{ $currency->code }}">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete(this)"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 border border-red-300 dark:border-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500 text-sm">
                                    No hay monedas registradas.
                                    <a href="{{ route('currencies.create') }}" class="text-violet-500 hover:underline ml-1">Agregar la primera</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($currencies->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Mostrando <span class="font-medium text-gray-800 dark:text-gray-100">{{ $currencies->firstItem() }}</span>
                            a <span class="font-medium text-gray-800 dark:text-gray-100">{{ $currencies->lastItem() }}</span>
                            de <span class="font-medium text-gray-800 dark:text-gray-100">{{ $currencies->total() }}</span> resultados
                        </div>
                        <div>{{ $currencies->links('pagination::simple-tailwind') }}</div>
                    </div>
                </div>
            @endif
        </div>

    </div>

    {{-- Script de SweetAlert2 con soporte adaptativo para Modo Oscuro --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(button) {
            const form = button.closest('form');
            const currencyCode = form.getAttribute('data-code');
            const isDark = document.documentElement.classList.contains('dark');

            Swal.fire({
                title: '¿Eliminar moneda?',
                text: `¿Estás seguro de que deseas eliminar la moneda ${currencyCode}? Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: isDark ? '#f3f4f6' : '#111827', // Invierte los tonos para que contrasten bien
                cancelButtonColor: '#ef4444',                       // Rojo de peligro elegante
                background: isDark ? '#1f2937' : '#ffffff',         // Se adapta al fondo de tus tarjetas
                color: isDark ? '#f3f4f6' : '#1f2937',               // Color del texto adaptativo
                customClass: {
                    popup: 'rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg font-sans',
                    confirmButton: 'font-medium rounded-lg text-sm px-4 py-2 text-gray-900 dark:text-gray-900',
                    cancelButton: 'font-medium rounded-lg text-sm px-4 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>
