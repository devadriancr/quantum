<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">{{ __('Editar Ubicación') }}</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('locations.show', $location) }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                    &larr; {{ __('Regresar') }}
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl">

            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ $location->name }}</h2>
            </header>

            <div class="px-5 pt-5 pb-2">
                <ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm mb-4">

                    {{-- Solo lectura: Código y Nombre --}}
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

                    {{-- Solo lectura: Estado --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
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

                    {{-- Formulario editable --}}
                    <form id="location-form" action="{{ route('locations.update', $location) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Fila y Rack --}}
                        <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Fila') }}</span>
                                <div class="flex-1">
                                    <input id="row" name="row" type="number" min="1"
                                           value="{{ old('row', $location->row) }}"
                                           placeholder="{{ __('Ej. 1') }}"
                                           class="form-input w-full @error('row') border-red-300 @enderror" />
                                    @error('row')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Rack') }}</span>
                                <div class="flex-1">
                                    <input id="rack" name="rack" type="number" min="1"
                                           value="{{ old('rack', $location->rack) }}"
                                           placeholder="{{ __('Ej. 1') }}"
                                           class="form-input w-full @error('rack') border-red-300 @enderror" />
                                    @error('rack')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </li>

                        {{-- Estante y Zona --}}
                        <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Estante') }}</span>
                                <div class="flex-1">
                                    <input id="shelf" name="shelf" type="number" min="1"
                                           value="{{ old('shelf', $location->shelf) }}"
                                           placeholder="{{ __('Ej. 1') }}"
                                           class="form-input w-full @error('shelf') border-red-300 @enderror" />
                                    @error('shelf')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Zona') }}</span>
                                <div class="flex-1">
                                    <select id="zone" name="zone"
                                            class="form-select w-full @error('zone') border-red-300 @enderror">
                                        <option value="">— {{ __('Sin zona') }} —</option>
                                        <option value="RECEIVING" @selected(old('zone', $location->zone) === 'RECEIVING')>{{ __('Recibo') }}</option>
                                        <option value="STORAGE"   @selected(old('zone', $location->zone) === 'STORAGE')>{{ __('Almacén') }}</option>
                                        <option value="SHIPPING"  @selected(old('zone', $location->zone) === 'SHIPPING')>{{ __('Embarque') }}</option>
                                    </select>
                                    @error('zone')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </li>

                    </form>

                </ul>
            </div>

            {{-- Botones --}}
            <div class="px-5 pb-5">
                <div class="flex justify-end pt-5 border-t border-gray-100 dark:border-gray-700/60 gap-3">
                    <a href="{{ route('locations.show', $location) }}"
                       class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                        {{ __('Cancelar') }}
                    </a>
                    <button type="submit" form="location-form"
                            class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                        {{ __('Actualizar') }}
                    </button>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
