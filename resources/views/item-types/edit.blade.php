<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Page header --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">{{ __('Editar Tipo de Artículo') }}</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('item-types.index') }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                    &larr; {{ __('Regresar') }}
                </a>
            </div>
        </div>

        {{-- Form card --}}
        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl">
            <div class="p-5">
                <form action="{{ route('item-types.update', $itemType) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="code">
                                    {{ __('Código') }} <span class="text-red-500">*</span>
                                </label>
                                <input id="code" name="code" type="text"
                                       value="{{ old('code', $itemType->code) }}"
                                       class="form-input w-full @error('code') border-red-300 @enderror" />
                                @error('code')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="name">
                                    {{ __('Nombre') }} <span class="text-red-500">*</span>
                                </label>
                                <input id="name" name="name" type="text"
                                       value="{{ old('name', $itemType->name) }}"
                                       class="form-input w-full @error('name') border-red-300 @enderror" />
                                @error('name')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="flex items-center">
                            <label for="status" class="inline-flex items-center cursor-pointer">
                                <input id="status" name="status" type="checkbox"
                                       value="1"
                                       class="form-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                       {{ old('status', $itemType->status) ? 'checked' : '' }} />
                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('Activo') }}
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end mt-8 pt-5 border-t border-gray-100 dark:border-gray-700/60 gap-3">
                        <a href="{{ route('item-types.index') }}"
                           class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                            {{ __('Cancelar') }}
                        </a>
                        <button type="submit"
                                class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                            {{ __('Actualizar') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
