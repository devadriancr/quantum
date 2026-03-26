<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">{{ __('Detalle de Unidad de Medida') }}</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('measurement-units.edit', $measurementUnit) }}"
                   class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                    {{ __('Editar') }}
                </a>
                <a href="{{ route('measurement-units.index') }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                    &larr; {{ __('Regresar') }}
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl">
            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ $measurementUnit->name }}</h2>
            </header>
            <div class="p-5">
                <ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">

                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                        <div class="flex items-center">
                            <span class="w-40 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 shrink-0">{{ __('Código') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $measurementUnit->code }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-40 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 shrink-0">{{ __('Nombre') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $measurementUnit->name }}</span>
                        </div>
                    </li>

                    <li class="flex items-center py-3">
                        <span class="w-40 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 shrink-0">{{ __('Estado') }}</span>
                        @if ($measurementUnit->status)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">
                                {{ __('Activo') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                {{ __('Inactivo') }}
                            </span>
                        @endif
                    </li>

                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4 text-gray-600 dark:text-gray-400">
                        <div class="flex items-center">
                            <span class="w-40 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 shrink-0">{{ __('Creado') }}</span>
                            <span>{{ $measurementUnit->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-40 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 shrink-0">{{ __('Actualizado') }}</span>
                            <span>{{ $measurementUnit->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </li>

                </ul>
            </div>
        </div>

    </div>
</x-app-layout>
