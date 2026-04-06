<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">{{ __('Editar Artículo') }}</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('items.index', $item) }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                    &larr; {{ __('Regresar') }}
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl">

            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ $item->code }}</h2>
            </header>

            {{-- Información de solo lectura --}}
            <div class="px-5 pt-5 pb-2">
                <ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">

                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Código') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $item->code }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Descripción') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $item->description ?? '—' }}</span>
                        </div>
                    </li>

                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Clase') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">
                                {{ $item->itemClass ? $item->itemClass->code : '—' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Tipo') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">
                                {{ $item->itemType ? $item->itemType->code : '—' }}
                            </span>
                        </div>
                    </li>

                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Unidad de Medida') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">
                                {{ $item->measurementUnit ? $item->measurementUnit->code : '—' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Esp. de Empaque') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">
                                {{ $item->packingSpecification ? $item->packingSpecification->name . ' (' . $item->packingSpecification->quantity . ')' : '—' }}
                            </span>
                        </div>
                    </li>

                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Estado') }}</span>
                            @if ($item->active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">
                                    {{ __('Activo') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-400">
                                    {{ __('Obsoleto') }}
                                </span>
                            @endif
                        </div>
                    </li>

                    {{-- Campos editables --}}
                    <form id="item-form" action="{{ route('items.update', $item) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Stock de Seguridad') }}</span>
                                <div class="flex-1">
                                    <input id="default_safety_stock" name="default_safety_stock" type="number" min="0"
                                           value="{{ old('default_safety_stock', $item->default_safety_stock) }}"
                                           placeholder="{{ __('Ej. 100') }}"
                                           class="form-input w-full @error('default_safety_stock') border-red-300 @enderror" />
                                    @error('default_safety_stock')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Último Costo Unitario') }}</span>
                                <div class="flex-1">
                                    <input id="last_unit_cost" name="last_unit_cost" type="number" step="0.01" min="0"
                                           value="{{ old('last_unit_cost', $item->last_unit_cost) }}"
                                           placeholder="{{ __('Ej. 25.50') }}"
                                           class="form-input w-full @error('last_unit_cost') border-red-300 @enderror" />
                                    @error('last_unit_cost')
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
                    <a href="{{ route('items.index', $item) }}"
                       class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                        {{ __('Cancelar') }}
                    </a>
                    <button type="submit" form="item-form"
                            class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                        {{ __('Actualizar') }}
                    </button>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
