<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">{{ __('Detalle de Artículo') }}</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('items.edit', $item) }}"
                   class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                    {{ __('Editar') }}
                </a>
                <a href="{{ route('items.index') }}"
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
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ $item->code }}</h2>
            </header>
            <div class="p-5">
                <ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">

                    {{-- Código y Descripción --}}
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

                    {{-- Clase y Tipo --}}
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

                    {{-- Unidad de medida y Especificación de empaque --}}
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

                    {{-- Stock de seguridad y Último costo --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Stock de Seguridad') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">
                                {{ $item->default_safety_stock !== null ? number_format($item->default_safety_stock) : '—' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Último Costo Unitario') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">
                                {{ $item->last_unit_cost !== null ? '$' . number_format($item->last_unit_cost, 2) : '—' }}
                            </span>
                        </div>
                    </li>

                    {{-- Estado --}}
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

                    {{-- Fechas --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4 text-gray-600 dark:text-gray-400">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Creado') }}</span>
                            <span>{{ $item->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Actualizado') }}</span>
                            <span>{{ $item->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </li>

                </ul>
            </div>
        </div>

    </div>
</x-app-layout>
