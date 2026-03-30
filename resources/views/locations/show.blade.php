<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">{{ __('Detalle de Ubicación') }}</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('locations.edit', $location) }}"
                   class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                    {{ __('Editar') }}
                </a>
                <a href="{{ route('locations.index') }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                    &larr; {{ __('Regresar') }}
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl">
            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ $location->name }}</h2>
            </header>
            <div class="p-5">
                <ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">

                    {{-- Código y Nombre --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Código') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $location->code }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Nombre') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $location->name }}</span>
                        </div>
                    </li>

                    {{-- Fila y Rack --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Fila') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $location->row ?? '—' }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Rack') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $location->rack ?? '—' }}</span>
                        </div>
                    </li>

                    {{-- Estante y Zona --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Estante') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $location->shelf ?? '—' }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Zona') }}</span>
                            @php
                                $zoneMap = [
                                    'RECEIVING' => ['label' => 'Recepción', 'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400'],
                                    'STORAGE'   => ['label' => 'Almacenaje', 'class' => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400'],
                                    'SHIPPING'  => ['label' => 'Envío',     'class' => 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400'],
                                ];
                                $zone = $location->zone ? ($zoneMap[$location->zone] ?? ['label' => $location->zone, 'class' => 'bg-gray-100 text-gray-600']) : null;
                            @endphp
                            @if($zone)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $zone['class'] }}">
                                    {{ $zone['label'] }}
                                </span>
                            @else
                                <span class="font-medium text-gray-800 dark:text-gray-100">—</span>
                            @endif
                        </div>
                    </li>

                    {{-- Capacidad y Estado --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Cap. Disponible') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">
                                {{ $location->available_capacity !== null ? number_format($location->available_capacity, 2) : '—' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Estado') }}</span>
                            @php
                                $statusMap = [
                                    'ACTIVE'       => ['label' => 'Activo',        'class' => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400'],
                                    'AVAILABLE'    => ['label' => 'Disponible',    'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400'],
                                    'OCCUPIED'     => ['label' => 'Ocupado',       'class' => 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400'],
                                    'RESERVED'     => ['label' => 'Reservado',     'class' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400'],
                                    'MAINTENANCE'  => ['label' => 'Mantenimiento', 'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400'],
                                    'BLOCKED'      => ['label' => 'Bloqueado',     'class' => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400'],
                                    'UNAVAILABLE'  => ['label' => 'No disponible', 'class' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
                                ];
                                $status = $statusMap[$location->status] ?? ['label' => $location->status, 'class' => 'bg-gray-100 text-gray-600'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['class'] }}">
                                {{ $status['label'] }}
                            </span>
                        </div>
                    </li>

                    {{-- Almacén --}}
                    @if($location->warehouse)
                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Almacén') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $location->warehouse->name }}</span>
                        </div>
                    </li>
                    @endif

                    {{-- Fechas --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4 text-gray-600 dark:text-gray-400">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Creado') }}</span>
                            <span>{{ $location->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Actualizado') }}</span>
                            <span>{{ $location->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </li>

                </ul>
            </div>
        </div>

    </div>
</x-app-layout>
