<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">{{ __('Editar Especificación de Empaque') }}</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('packing-specifications.index') }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                    &larr; {{ __('Regresar') }}
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl">

            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ $packingSpecification->name }}</h2>
            </header>

            <form id="spec-form" action="{{ route('packing-specifications.update', $packingSpecification) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="px-5 pt-5 pb-2">
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">

                        <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">

                            {{-- Nombre --}}
                            <div class="flex items-start gap-3">
                                <label for="name" class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">
                                    {{ __('Nombre') }} <span class="text-red-500">*</span>
                                </label>
                                <div class="flex-1">
                                    <input id="name" name="name" type="text"
                                           value="{{ old('name', $packingSpecification->name) }}"
                                           placeholder="{{ __('Ej. Caja de 12') }}"
                                           class="form-input w-full @error('name') border-red-300 @enderror" />
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Cantidad --}}
                            <div class="flex items-start gap-3">
                                <label for="quantity" class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">
                                    {{ __('Cantidad') }}
                                </label>
                                <div class="flex-1">
                                    <input id="quantity" name="quantity" type="number" min="0"
                                           value="{{ old('quantity', $packingSpecification->quantity) }}"
                                           placeholder="{{ __('Ej. 12') }}"
                                           class="form-input w-full @error('quantity') border-red-300 @enderror" />
                                    @error('quantity')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </li>

                        <li class="py-3">
                            {{-- Estado --}}
                            <div class="flex items-center gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Activo') }}</span>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="hidden" name="status" value="0" />
                                    <input id="status" name="status" type="checkbox" value="1"
                                           {{ old('status', $packingSpecification->status) ? 'checked' : '' }}
                                           class="form-checkbox text-violet-500" />
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Habilitado') }}</span>
                                </label>
                            </div>
                        </li>

                    </ul>
                </div>

                <div class="px-5 pb-5">
                    <div class="flex justify-end pt-5 border-t border-gray-100 dark:border-gray-700/60 gap-3">
                        <a href="{{ route('packing-specifications.index') }}"
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
