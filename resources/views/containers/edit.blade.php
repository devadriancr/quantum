<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Encabezado --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    {{ __('Editar Contenedor') }}
                </h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('containers.index') }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                    &larr; {{ __('Regresar') }}
                </a>
            </div>
        </div>

        {{-- Aviso de estado no editable (defensa adicional en la vista) --}}
        @if ($container->status !== 'PENDING')
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded-lg mb-6 text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                {{ __('Este contenedor no se puede editar porque su estado actual es') }}
                <strong>{{ \App\Models\Container::STATUS_OPTIONS[$container->status] ?? $container->status }}</strong>.
                {{ __('Solo los contenedores en estado Pendiente son editables.') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60">

            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ $container->code }}</h2>
            </header>

            <div class="px-5 pt-5 pb-2">
                <ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">

                    <form id="container-form" action="{{ route('containers.update', $container) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Código del contenedor y Tipo --}}
                        <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">

                            <div class="flex items-start gap-3">
                                <label for="code" class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">
                                    {{ __('Código') }}
                                </label>
                                <div class="flex-1">
                                    <input
                                        id="code"
                                        name="code"
                                        type="text"
                                        value="{{ old('code', $container->code) }}"
                                        class="form-input w-full @error('code') border-red-300 @enderror"
                                        placeholder="{{ __('Número / código del contenedor') }}"
                                        {{ $container->status !== 'PENDING' ? 'disabled' : '' }}
                                    />
                                    @error('code')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <label for="container_type" class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">
                                    {{ __('Tipo') }}
                                </label>
                                <div class="flex-1">
                                    <select
                                        id="container_type"
                                        name="container_type"
                                        class="form-select w-full @error('container_type') border-red-300 @enderror"
                                        {{ $container->status !== 'PENDING' ? 'disabled' : '' }}
                                    >
                                        @foreach (\App\Models\Container::TYPE_OPTIONS as $value => $label)
                                            <option value="{{ $value }}" {{ old('container_type', $container->container_type) === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('container_type')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </li>

                        {{-- Socio --}}
                        <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                            <div class="flex items-start gap-3">
                                <label for="partner_id" class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">
                                    {{ __('Socio') }}
                                </label>
                                <div class="flex-1">
                                    <select
                                        id="partner_id"
                                        name="partner_id"
                                        class="form-select w-full @error('partner_id') border-red-300 @enderror"
                                        {{ $container->status !== 'PENDING' ? 'disabled' : '' }}
                                    >
                                        <option value="">— {{ __('Seleccionar socio') }} —</option>
                                        @foreach ($partners as $partner)
                                            <option value="{{ $partner->id }}" {{ old('partner_id', $container->partner_id) == $partner->id ? 'selected' : '' }}>
                                                {{ $partner->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('partner_id')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </li>

                        {{-- Fecha y hora estimada de llegada --}}
                        <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">

                            <div class="flex items-start gap-3">
                                <label for="estimated_arrival_date" class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">
                                    {{ __('Fecha estimada') }}
                                </label>
                                <div class="flex-1">
                                    <input
                                        id="estimated_arrival_date"
                                        name="estimated_arrival_date"
                                        type="date"
                                        value="{{ old('estimated_arrival_date', $container->estimated_arrival_date) }}"
                                        class="form-input w-full @error('estimated_arrival_date') border-red-300 @enderror"
                                        {{ $container->status !== 'PENDING' ? 'disabled' : '' }}
                                    />
                                    @error('estimated_arrival_date')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <label for="estimated_arrival_time" class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">
                                    {{ __('Hora estimada') }}
                                </label>
                                <div class="flex-1">
                                    <input
                                        id="estimated_arrival_time"
                                        name="estimated_arrival_time"
                                        type="time"
                                        value="{{ old('estimated_arrival_time', $container->estimated_arrival_time ? \Carbon\Carbon::parse($container->estimated_arrival_time)->format('H:i') : '') }}"
                                        class="form-input w-full @error('estimated_arrival_time') border-red-300 @enderror"
                                        {{ $container->status !== 'PENDING' ? 'disabled' : '' }}
                                    />
                                    @error('estimated_arrival_time')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </li>

                        {{-- Notas --}}
                        <li class="py-3">
                            <div class="flex items-start gap-3">
                                <label for="notes" class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">
                                    {{ __('Notas') }}
                                </label>
                                <div class="flex-1">
                                    <textarea
                                        id="notes"
                                        name="notes"
                                        rows="3"
                                        class="form-textarea w-full @error('notes') border-red-300 @enderror"
                                        placeholder="{{ __('Observaciones o comentarios...') }}"
                                        {{ $container->status !== 'PENDING' ? 'disabled' : '' }}
                                    >{{ old('notes', $container->notes) }}</textarea>
                                    @error('notes')
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
                    <a href="{{ route('containers.index') }}"
                       class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                        {{ __('Cancelar') }}
                    </a>
                    @if ($container->status === 'PENDING')
                        <button type="submit" form="container-form"
                                class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                            {{ __('Actualizar') }}
                        </button>
                    @endif
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
