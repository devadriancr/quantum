<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    {{ __('Salida a Línea de Producción') }}
                </h1>
            </div>

            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <form action="{{ route('material-outputs.index') }}" method="GET" class="relative flex items-center">
                    <label for="action-search" class="sr-only">Buscar</label>
                    <input
                        id="action-search"
                        name="search"
                        class="form-input pl-9 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg"
                        type="search"
                        placeholder="Folio, N° parte..."
                        value="{{ $search }}"
                    />
                    <button class="absolute inset-0 right-auto group" type="submit" aria-label="Buscar">
                        <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500 group-hover:text-gray-500 ml-3 mr-2" width="16" height="16" viewBox="0 0 16 16">
                            <path d="M7 14c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zM7 2C4.243 2 2 4.243 2 7s2.243 5 5 5 5-2.243 5-5-2.243-5-5-5z" />
                            <path d="M15.707 14.293L13.314 11.9a8.019 8.019 0 01-1.414 1.414l2.393 2.393a.997.997 0 001.414 0 .999.999 0 000-1.414z" />
                        </svg>
                    </button>
                    @if($search)
                        <a href="{{ route('material-outputs.index') }}"
                           class="ml-2 text-sm text-gray-500 hover:text-violet-500 underline whitespace-nowrap">
                            Limpiar
                        </a>
                    @endif
                </form>

                <form action="{{ route('material-outputs.store') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 mr-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Nueva Entrega
                    </button>
                </form>
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
                            <th class="px-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">{{ __('Folio') }}</div></th>
                            <th class="px-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">{{ __('Fecha y Hora') }}</div></th>
                            <th class="px-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">{{ __('Origen → Destino') }}</div></th>
                            <th class="px-5 py-3 whitespace-nowrap"><div class="font-semibold text-right">{{ __('Piezas') }}</div></th>
                            <th class="px-5 py-3 whitespace-nowrap"><div class="font-semibold text-center">{{ __('Estado') }}</div></th>
                            <th class="px-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">{{ __('Creado por') }}</div></th>
                            <th class="px-5 py-3 whitespace-nowrap"><div class="font-semibold text-right">{{ __('Acción') }}</div></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($movements as $movement)
                            @php
                                $statusMap = [
                                    'PENDING'   => ['label' => 'En proceso', 'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400'],
                                    'COMPLETED' => ['label' => 'Finalizado', 'class' => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400'],
                                ];
                                $status = $statusMap[$movement->status] ?? ['label' => $movement->status, 'class' => 'bg-gray-100 text-gray-600'];
                            @endphp
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10">
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="font-medium text-sm text-gray-800 dark:text-gray-100">
                                        {{ $movement->movement_number }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <p class="text-sm text-gray-800 dark:text-gray-300">
                                        {{ \Carbon\Carbon::parse($movement->movement_date)->format('d-m-Y') }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $movement->movement_time ? \Carbon\Carbon::parse($movement->movement_time)->format('H:i:s') : '' }}
                                    </p>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">
                                            {{ $movement->locationFrom?->code ?? '—' }}
                                        </span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3 text-gray-400">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                        </svg>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">
                                            {{ $movement->locationTo?->code ?? '—' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <span class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $movement->lines->count() }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['class'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $movement->createdBy?->name ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">

                                        <a href="{{ route('material-outputs.show', $movement) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold
                                                  {{ $movement->status === 'PENDING'
                                                      ? 'bg-violet-500 hover:bg-violet-600 text-white'
                                                      : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300' }}
                                                  transition-colors">
                                            @if($movement->status === 'PENDING')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75L17.25 9m0 0L21 12.75M17.25 9v12" />
                                                </svg>
                                                Escanear
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                                </svg>
                                                Ver
                                            @endif
                                        </a>

                                        @if($movement->lines->count() === 0)
                                            <form method="POST" action="{{ route('material-outputs.destroy', $movement) }}"
                                                  onsubmit="return confirm('¿Eliminar este movimiento vacío?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        title="Eliminar movimiento vacío"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold bg-red-500 hover:bg-red-600 text-white transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                    Eliminar
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500 text-sm">
                                    @if($search)
                                        No se encontraron entregas que coincidan con la búsqueda.
                                    @else
                                        No hay entregas registradas.
                                        <form action="{{ route('material-outputs.store') }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-violet-500 hover:underline ml-1">Crear la primera</button>.
                                        </form>
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
</x-app-layout>
