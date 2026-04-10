<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">{{ __('Editar Prefijo') }}</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('project-prefixes.index') }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                    &larr; {{ __('Regresar') }}
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl">

            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ $projectPrefix->code }}</h2>
            </header>

            <div class="px-5 pt-5 pb-2">
                <ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm mb-4">

                    <form id="prefix-form" action="{{ route('project-prefixes.update', $projectPrefix) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Código y Proyecto --}}
                        <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Código') }} <span class="text-red-500">*</span></span>
                                <div class="flex-1">
                                    <input id="code" name="code" type="text"
                                           value="{{ old('code', $projectPrefix->code) }}"
                                           class="form-input w-full @error('code') border-red-300 @enderror" />
                                    @error('code')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Proyecto') }} <span class="text-red-500">*</span></span>
                                <div class="flex-1">
                                    <select id="project_id" name="project_id"
                                            class="form-select w-full @error('project_id') border-red-300 @enderror">
                                        <option value="">— {{ __('Seleccionar') }} —</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}"
                                                    @selected(old('project_id', $projectPrefix->project_id) == $project->id)>
                                                {{ $project->model }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_id')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </li>

                        {{-- Descripción y Estado --}}
                        <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Descripción') }}</span>
                                <div class="flex-1">
                                    <input id="description" name="description" type="text"
                                           value="{{ old('description', $projectPrefix->description) }}"
                                           class="form-input w-full @error('description') border-red-300 @enderror" />
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Estado') }} <span class="text-red-500">*</span></span>
                                <div class="flex-1">
                                    <select id="status" name="status"
                                            class="form-select w-full @error('status') border-red-300 @enderror">
                                        <option value="ACTIVE"   @selected(old('status', $projectPrefix->status) === 'ACTIVE')>{{ __('Activo') }}</option>
                                        <option value="INACTIVE" @selected(old('status', $projectPrefix->status) === 'INACTIVE')>{{ __('Inactivo') }}</option>
                                    </select>
                                    @error('status')
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
                    <a href="{{ route('project-prefixes.index') }}"
                       class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                        {{ __('Cancelar') }}
                    </a>
                    <button type="submit" form="prefix-form"
                            class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                        {{ __('Actualizar') }}
                    </button>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
