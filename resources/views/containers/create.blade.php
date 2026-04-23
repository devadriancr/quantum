<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Encabezado --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    Nuevo Contenedor
                </h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('containers.index') }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                    &larr; {{ __('Regresar') }}
                </a>
            </div>
        </div>

        {{-- Errores de validación --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
                <p class="font-semibold mb-1">{{ __('Por favor corrige los siguientes errores:') }}</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="container-form" action="{{ route('containers.store') }}" method="POST">
            @csrf

            {{-- ── Datos del Contenedor ─────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl mb-6">

                <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ __('Datos del contenedor') }}</h2>
                </header>

                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="code">
                                {{ __('Código') }} <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="code"
                                type="text"
                                name="code"
                                value="{{ old('code') }}"
                                required
                                placeholder="Ej. CT-2026-001"
                                class="form-input w-full @error('code') border-red-300 @enderror"
                            />
                            @error('code')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="container_type">
                                {{ __('Tipo de contenedor') }} <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="container_type"
                                name="container_type"
                                required
                                class="form-select w-full @error('container_type') border-red-300 @enderror">
                                <option value="">— {{ __('Seleccionar') }} —</option>
                                @foreach (\App\Models\Container::TYPE_OPTIONS as $key => $label)
                                    <option value="{{ $key }}" {{ old('container_type') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('container_type')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="partner_id">
                                {{ __('Socio') }}
                            </label>
                            <select
                                id="partner_id"
                                name="partner_id"
                                class="form-select w-full">
                                <option value="">{{ __('Sin socio') }}</option>
                                @foreach ($partners as $partner)
                                    <option value="{{ $partner->id }}" {{ old('partner_id') == $partner->id ? 'selected' : '' }}>
                                        {{ $partner->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="estimated_arrival_date">
                                {{ __('Fecha estimada de llegada') }}
                            </label>
                            <input
                                id="estimated_arrival_date"
                                type="date"
                                name="estimated_arrival_date"
                                value="{{ old('estimated_arrival_date') }}"
                                class="form-input w-full"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="estimated_arrival_time">
                                {{ __('Hora estimada de llegada') }}
                            </label>
                            <input
                                id="estimated_arrival_time"
                                type="time"
                                name="estimated_arrival_time"
                                value="{{ old('estimated_arrival_time') }}"
                                class="form-input w-full"
                            />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="notes">
                                {{ __('Notas') }}
                            </label>
                            <textarea
                                id="notes"
                                name="notes"
                                rows="2"
                                placeholder="{{ __('Observaciones opcionales...') }}"
                                class="form-textarea w-full">{{ old('notes') }}</textarea>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ── Líneas del Documento ─────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl mb-6">

                <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100">
                        {{ __('Líneas del documento') }}
                    </h2>
                </header>

                <div class="p-5">
                    <livewire:container.line-table />
                </div>
            </div>

            {{-- Acciones --}}
            <div class="flex justify-end pt-2 gap-3">
                <a href="{{ route('containers.index') }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                    {{ __('Cancelar') }}
                </a>
                <button type="submit"
                        class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                    {{ __('Crear contenedor') }}
                </button>
            </div>

        </form>
    </div>
</x-app-layout>
