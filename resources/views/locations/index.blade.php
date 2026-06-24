<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    {{ __('Ubicaciones') }}
                </h1>
            </div>

            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <form action="{{ route('locations.index') }}" method="GET" class="relative flex items-center">
                    <label for="action-search" class="sr-only">Search</label>
                    <input
                        id="action-search"
                        name="search"
                        class="form-input pl-9 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg"
                        type="search"
                        placeholder="{{ __('Buscar por nombre o código...') }}"
                        value="{{ request('search') }}"
                    />
                    <button class="absolute inset-0 right-auto group" type="submit" aria-label="Search">
                        <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400 ml-3 mr-2" width="16" height="16" viewBox="0 0 16 16">
                            <path d="M7 14c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zM7 2C4.243 2 2 4.243 2 7s2.243 5 5 5 5-2.243 5-5-2.243-5-5-5z" />
                            <path d="M15.707 14.293L13.314 11.9a8.019 8.019 0 01-1.414 1.414l2.393 2.393a.997.997 0 001.414 0 .999.999 0 000-1.414z" />
                        </svg>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('locations.index') }}" class="ml-2 text-sm text-gray-500 hover:text-violet-500 underline whitespace-nowrap">
                            {{ __('Limpiar') }}
                        </a>
                    @endif
                </form>
            </div>
        </div>

        @if (session('success'))
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
                                <div class="font-semibold text-left">{{ __('Código') }}</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('Nombre') }}</div>
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
                        @forelse ($locations as $location)
                            <tr>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ $location->code }}</span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="text-gray-600 dark:text-gray-400">{{ $location->name }}</span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    @php
                                        $statusMap = [
                                            'ACTIVE'      => ['label' => 'Activo',        'class' => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400'],
                                            'AVAILABLE'   => ['label' => 'Disponible',    'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400'],
                                            'OCCUPIED'    => ['label' => 'Ocupado',       'class' => 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400'],
                                            'RESERVED'    => ['label' => 'Reservado',     'class' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400'],
                                            'MAINTENANCE' => ['label' => 'Mantenimiento', 'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400'],
                                            'BLOCKED'     => ['label' => 'Bloqueado',     'class' => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400'],
                                            'UNAVAILABLE' => ['label' => 'No disponible', 'class' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
                                        ];
                                        $status = $statusMap[$location->status] ?? ['label' => $location->status, 'class' => 'bg-gray-100 text-gray-600'];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['class'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('locations.show', $location) }}"
                                           class="inline-flex items-center gap-1 font-medium text-violet-500 hover:text-violet-600 dark:hover:text-violet-400 text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            {{ __('Ver') }}
                                        </a>
                                        @can('edit locations')
                                        <a href="{{ route('locations.edit', $location) }}"
                                           class="inline-flex items-center gap-1 font-medium text-blue-500 hover:text-blue-600 dark:hover:text-blue-400 text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            {{ __('Editar') }}
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                                    {{ __('No se han encontrado ubicaciones que coincidan con tu búsqueda.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($locations->hasPages() || $locations->total() > 0)
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400 text-center sm:text-left">
                            {{ __('Mostrando') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $locations->firstItem() ?? 0 }}</span>
                            {{ __('a') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $locations->lastItem() ?? 0 }}</span>
                            {{ __('de') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $locations->total() }}</span>
                            {{ __('resultados') }}
                        </div>
                        <div class="flex justify-center sm:justify-end">
                            {{ $locations->links('pagination::simple-tailwind') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
