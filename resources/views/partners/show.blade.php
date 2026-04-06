<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">{{ __('Detalle de Socio') }}</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('partners.edit', $partner) }}"
                   class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                    {{ __('Editar') }}
                </a>
                <a href="{{ route('partners.index') }}"
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
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ $partner->name }}</h2>
            </header>
            <div class="p-5">
                <ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">

                    {{-- Código y Nombre --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Código') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $partner->code }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Nombre') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $partner->name }}</span>
                        </div>
                    </li>

                    {{-- Tipo y Estado --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Tipo') }}</span>
                            @php
                                $typeMap = [
                                    'supplier' => ['label' => 'Proveedor', 'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400'],
                                    'customer' => ['label' => 'Cliente',   'class' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400'],
                                    'both'     => ['label' => 'Ambos',     'class' => 'bg-teal-100 text-teal-700 dark:bg-teal-500/20 dark:text-teal-400'],
                                ];
                                $type = $typeMap[$partner->partner_type] ?? ['label' => $partner->partner_type, 'class' => 'bg-gray-100 text-gray-600'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $type['class'] }}">
                                {{ $type['label'] }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Estado') }}</span>
                            @php
                                $statusMap = [
                                    'ACTIVE'   => ['label' => 'Activo',   'class' => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400'],
                                    'INACTIVE' => ['label' => 'Inactivo', 'class' => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400'],
                                ];
                                $status = $statusMap[$partner->status] ?? ['label' => $partner->status, 'class' => 'bg-gray-100 text-gray-600'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['class'] }}">
                                {{ $status['label'] }}
                            </span>
                        </div>
                    </li>

                    {{-- Email y Teléfono --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Email') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $partner->contact_email ?? '—' }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Teléfono') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $partner->contact_phone ?? '—' }}</span>
                        </div>
                    </li>

                    {{-- Dirección --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Dirección') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $partner->address ?? '—' }}</span>
                        </div>
                    </li>

                    {{-- Ciudad y País --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Ciudad') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $partner->city ?? '—' }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('País') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $partner->country ?? '—' }}</span>
                        </div>
                    </li>

                    {{-- Fechas --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4 text-gray-600 dark:text-gray-400">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Creado') }}</span>
                            <span>{{ $partner->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Actualizado') }}</span>
                            <span>{{ $partner->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </li>

                </ul>
            </div>

            {{-- Botón eliminar --}}
            <div class="px-5 pb-5">
                <div class="flex justify-end pt-5 border-t border-gray-100 dark:border-gray-700/60">
                    <form action="{{ route('partners.destroy', $partner) }}" method="POST"
                          onsubmit="return confirm('{{ __('¿Estás seguro de que deseas eliminar este socio?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="btn bg-red-500 hover:bg-red-600 text-white">
                            {{ __('Eliminar') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
