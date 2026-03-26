<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">{{ __('Editar Almacén') }}</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('warehouses.show', $warehouse) }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                    &larr; {{ __('Regresar') }}
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl">

            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ $warehouse->name }}</h2>
            </header>

            {{-- Información de solo lectura --}}
            <div class="px-5 pt-5 pb-2">
                <ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm mb-4">

                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Código') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $warehouse->code }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Nombre') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $warehouse->name }}</span>
                        </div>
                    </li>

                    {{-- Capacidad Total (editable) + Estado (solo lectura) en el mismo renglón --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">

                        {{-- Input de Capacidad Total --}}
                        <form id="capacity-form" action="{{ route('warehouses.update', $warehouse) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Capacidad Total') }}</span>
                                <div class="flex-1">
                                    <input id="total_capacity" name="total_capacity" type="number" step="0.01" min="0"
                                           value="{{ old('total_capacity', $warehouse->total_capacity) }}"
                                           placeholder="{{ __('Ej. 1000.00') }}"
                                           class="form-input w-full @error('total_capacity') border-red-300 @enderror" />
                                            @error('total_capacity')
                                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                            @enderror
                                </div>
                            </div>
                        </form>

                        {{-- Badge de Estado --}}
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Estado') }}</span>
                            @php
                                $statusMap = [
                                    'OPERATIONAL' => ['label' => 'Operativo',   'class' => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400'],
                                    'MAINTENANCE' => ['label' => 'Mantenimiento', 'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400'],
                                    'CLOSED'      => ['label' => 'Cerrado',       'class' => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400'],
                                    'INACTIVE'    => ['label' => 'Inactivo',      'class' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
                                ];
                                $status = $statusMap[$warehouse->status] ?? ['label' => $warehouse->status, 'class' => 'bg-gray-100 text-gray-600'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['class'] }}">
                                {{ __($status['label']) }}
                            </span>
                        </div>

                    </li>

                </ul>
            </div>

            {{-- Botones del formulario --}}
            <div class="px-5 pb-5">
                <div class="flex justify-end pt-5 border-t border-gray-100 dark:border-gray-700/60 gap-3">
                    <a href="{{ route('warehouses.show', $warehouse) }}"
                       class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                        {{ __('Cancelar') }}
                    </a>
                    <button type="submit" form="capacity-form"
                            class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                        {{ __('Actualizar') }}
                    </button>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
