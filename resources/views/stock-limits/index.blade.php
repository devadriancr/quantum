<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    {{ __('Límites de Stock') }}
                </h1>
            </div>

            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">

                {{-- Búsqueda --}}
                <form action="{{ route('stock-limits.index') }}" method="GET" class="relative flex items-center">
                    <label for="action-search" class="sr-only">Buscar</label>
                    <input
                        id="action-search"
                        name="search"
                        class="form-input pl-9 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg"
                        type="search"
                        placeholder="N° parte, descripción..."
                        value="{{ $search }}"
                    />
                    <button class="absolute inset-0 right-auto group" type="submit" aria-label="Buscar">
                        <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400 ml-3 mr-2" width="16" height="16" viewBox="0 0 16 16">
                            <path d="M7 14c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zM7 2C4.243 2 2 4.243 2 7s2.243 5 5 5 5-2.243 5-5-2.243-5-5-5z" />
                            <path d="M15.707 14.293L13.314 11.9a8.019 8.019 0 01-1.414 1.414l2.393 2.393a.997.997 0 001.414 0 .999.999 0 000-1.414z" />
                        </svg>
                    </button>
                    @if($search)
                        <a href="{{ route('stock-limits.index') }}"
                           class="ml-2 text-sm text-gray-500 hover:text-violet-500 underline whitespace-nowrap">
                            Limpiar
                        </a>
                    @endif
                </form>

                {{-- Nuevo --}}
                <a href="{{ route('stock-limits.create') }}"
                   class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __('Nuevo') }}
                </a>

            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm">
                {{ session('success') }}
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
                                <div class="font-semibold text-left">{{ __('Ubicación') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-right">{{ __('Promedio') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-right">{{ __('Mínimo') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-right">{{ __('Máximo') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-right">{{ __('Acciones') }}</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($limits as $limit)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10">

                                {{-- N° Parte --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $limit->item?->code ?? '—' }}
                                    </span>
                                </td>

                                {{-- Descripción --}}
                                <td class="px-5 py-3 max-w-xs">
                                    <span class="text-gray-600 dark:text-gray-400 truncate block" title="{{ $limit->item?->description }}">
                                        {{ $limit->item?->description ?? '—' }}
                                    </span>
                                </td>

                                {{-- Ubicación --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">
                                        {{ $limit->location?->code ?? '—' }}
                                    </span>
                                </td>

                                {{-- Promedio --}}
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <span class="text-gray-700 dark:text-gray-300">
                                        {{ number_format($limit->daily_average, 2) }}
                                    </span>
                                </td>

                                {{-- Mínimo --}}
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <span class="text-gray-700 dark:text-gray-300">
                                        {{ number_format($limit->minimum_quantity, 0) }}
                                    </span>
                                </td>

                                {{-- Máximo --}}
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <span class="text-gray-700 dark:text-gray-300">
                                        {{ number_format($limit->maximum_quantity, 0) }}
                                    </span>
                                </td>

                                {{-- Acciones --}}
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('stock-limits.edit', $limit) }}"
                                           class="inline-flex items-center gap-1 font-medium text-blue-500 hover:text-blue-600 dark:hover:text-blue-400 text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('stock-limits.destroy', $limit) }}"
                                              onsubmit="return confirm('¿Eliminar este límite de stock?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 font-medium text-red-500 hover:text-red-600 dark:hover:text-red-400 text-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
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
                                <td colspan="7" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500 text-sm">
                                    @if($search)
                                        No se encontraron límites que coincidan con la búsqueda.
                                    @else
                                        No hay límites de stock configurados.
                                        <a href="{{ route('stock-limits.create') }}" class="text-violet-500 hover:underline ml-1">Crear el primero</a>.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($limits->hasPages() || $limits->total() > 0)
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400 text-center sm:text-left">
                            {{ __('Mostrando') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $limits->firstItem() ?? 0 }}</span>
                            {{ __('a') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $limits->lastItem() ?? 0 }}</span>
                            {{ __('de') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $limits->total() }}</span>
                            {{ __('resultados') }}
                        </div>
                        <div class="flex justify-center sm:justify-end">
                            {{ $limits->links('pagination::simple-tailwind') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
