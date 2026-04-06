<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">{{ __('Editar Proyecto') }}</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('projects.index', $project) }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                    &larr; {{ __('Regresar') }}
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl">

            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ $project->model }}</h2>
            </header>

            <div class="px-5 pt-5 pb-2">
                <ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm mb-4">

                    {{-- Solo lectura: Código y Modelo --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Código') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $project->code }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Modelo') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $project->model ?? '—' }}</span>
                        </div>
                    </li>

                    {{-- Solo lectura: Estado y Socio --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Estado') }}</span>
                            @php
                                $statusMap = [
                                    'ACTIVE'   => ['label' => 'Activo',   'class' => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400'],
                                    'INACTIVE' => ['label' => 'Inactivo', 'class' => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400'],
                                ];
                                $status = $statusMap[$project->status] ?? ['label' => $project->status, 'class' => 'bg-gray-100 text-gray-600'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['class'] }}">
                                {{ $status['label'] }}
                            </span>
                        </div>
                        @if($project->partner)
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Socio') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $project->partner->name }}</span>
                        </div>
                        @endif
                    </li>

                    {{-- Formulario editable --}}
                    <form id="project-form" action="{{ route('projects.update', $project) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Nombre --}}
                        <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                            <div class="flex items-start gap-3 md:col-span-2">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Nombre') }}</span>
                                <div class="flex-1">
                                    <input id="name" name="name" type="text"
                                           value="{{ old('name', $project->name) }}"
                                           placeholder="{{ __('Nombre del proyecto') }}"
                                           class="form-input w-full @error('name') border-red-300 @enderror" />
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </li>

                        {{-- Descripción --}}
                        <li class="py-3">
                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Descripción') }}</span>
                                <div class="flex-1">
                                    <textarea id="description" name="description" rows="4"
                                              placeholder="{{ __('Descripción del proyecto...') }}"
                                              class="form-textarea w-full @error('description') border-red-300 @enderror">{{ old('description', $project->description) }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </li>

                    </form>

                </ul>
            </div>

            <div class="px-5 pb-5">
                <div class="flex justify-end pt-5 border-t border-gray-100 dark:border-gray-700/60 gap-3">
                    <a href="{{ route('projects.index', $project) }}"
                       class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                        {{ __('Cancelar') }}
                    </a>
                    <button type="submit" form="project-form"
                            class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                        {{ __('Actualizar') }}
                    </button>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
