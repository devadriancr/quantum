<x-app-layout>
    <style>
        .select2-container {
            width: 100% !important;
            max-width: 100% !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 12px;
            color: #374151;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
        }
        .dark .select2-container .select2-selection--single {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #d1d5db !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
    </style>

    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    {{ __('Editar Límite de Stock') }}
                </h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('stock-limits.index') }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                    &larr; {{ __('Regresar') }}
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl">
            <form id="stock-limit-form" action="{{ route('stock-limits.update', $stockLimit) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="px-5 pt-5 pb-2">
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">

                        {{-- Fila 1: Número de parte + Ubicación (solo lectura) --}}
                        <li class="grid grid-cols-1 md:grid-cols-2 py-4 gap-6">
                            <div class="flex items-center gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">
                                    {{ __('Número de Parte') }}
                                </span>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $stockLimit->item?->code }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $stockLimit->item?->description }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">
                                    {{ __('Ubicación') }}
                                </span>
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">
                                        {{ $stockLimit->location?->code }}
                                    </span>
                                    <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $stockLimit->location?->warehouse?->name }}
                                    </span>
                                </div>
                            </div>
                        </li>

                        {{-- Fila 2: Mínimo + Máximo --}}
                        <li class="grid grid-cols-1 md:grid-cols-2 py-4 gap-6">

                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">
                                    {{ __('Cantidad Mínima') }} <span class="text-red-400">*</span>
                                </span>
                                <div class="flex-1">
                                    <input name="minimum_quantity" type="number" step="0.01" min="0"
                                           value="{{ old('minimum_quantity', $stockLimit->minimum_quantity) }}"
                                           class="form-input w-full @error('minimum_quantity') border-red-300 @enderror" />
                                    @error('minimum_quantity')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">
                                    {{ __('Cantidad Máxima') }} <span class="text-red-400">*</span>
                                </span>
                                <div class="flex-1">
                                    <input name="maximum_quantity" type="number" step="0.01" min="0"
                                           value="{{ old('maximum_quantity', $stockLimit->maximum_quantity) }}"
                                           class="form-input w-full @error('maximum_quantity') border-red-300 @enderror" />
                                    @error('maximum_quantity')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                        </li>

                        {{-- Fila 3: Punto de Reorden + Cantidad a Pedir --}}
                        <li class="grid grid-cols-1 md:grid-cols-2 py-4 gap-6">

                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">
                                    {{ __('Punto de Reorden') }} <span class="text-red-400">*</span>
                                </span>
                                <div class="flex-1">
                                    <input name="reorder_point" type="number" step="0.01" min="0"
                                           value="{{ old('reorder_point', $stockLimit->reorder_point) }}"
                                           class="form-input w-full @error('reorder_point') border-red-300 @enderror" />
                                    @error('reorder_point')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">
                                    {{ __('Cantidad a Pedir') }} <span class="text-red-400">*</span>
                                </span>
                                <div class="flex-1">
                                    <input name="reorder_quantity" type="number" step="0.01" min="0"
                                           value="{{ old('reorder_quantity', $stockLimit->reorder_quantity) }}"
                                           class="form-input w-full @error('reorder_quantity') border-red-300 @enderror" />
                                    @error('reorder_quantity')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                        </li>

                        {{-- Activo --}}
                        <li class="py-4">
                            <div class="flex items-center gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">
                                    {{ __('Activo') }}
                                </span>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="active" value="1"
                                           {{ old('active', $stockLimit->active) ? 'checked' : '' }}
                                           class="form-checkbox text-violet-500" />
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Habilitado</span>
                                </label>
                            </div>
                        </li>

                    </ul>
                </div>

                <div class="px-5 pb-5">
                    <div class="flex justify-end pt-5 border-t border-gray-100 dark:border-gray-700/60 gap-3">
                        <a href="{{ route('stock-limits.index') }}"
                           class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                            {{ __('Cancelar') }}
                        </a>
                        <button type="submit"
                                class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                            {{ __('Actualizar') }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>
