<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">{{ __('Editar Socio') }}</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('partners.index', $partner) }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                    &larr; {{ __('Regresar') }}
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl">

            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ $partner->name }}</h2>
            </header>

            <div class="px-5 pt-5 pb-2">
                <ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm mb-4">

                    <form id="partner-form" action="{{ route('partners.update', $partner) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Código y Nombre --}}
                        <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Código') }} <span class="text-red-500">*</span></span>
                                <div class="flex-1">
                                    <input id="code" name="code" type="text"
                                           value="{{ old('code', $partner->code) }}"
                                           class="form-input w-full @error('code') border-red-300 @enderror" />
                                    @error('code')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Nombre') }} <span class="text-red-500">*</span></span>
                                <div class="flex-1">
                                    <input id="name" name="name" type="text"
                                           value="{{ old('name', $partner->name) }}"
                                           class="form-input w-full @error('name') border-red-300 @enderror" />
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </li>

                        {{-- Tipo y Estado --}}
                        <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Tipo') }} <span class="text-red-500">*</span></span>
                                <div class="flex-1">
                                    <select id="partner_type" name="partner_type"
                                            class="form-select w-full @error('partner_type') border-red-300 @enderror">
                                        <option value="supplier" @selected(old('partner_type', $partner->partner_type) === 'supplier')>{{ __('Proveedor') }}</option>
                                        <option value="customer" @selected(old('partner_type', $partner->partner_type) === 'customer')>{{ __('Cliente') }}</option>
                                        <option value="both"     @selected(old('partner_type', $partner->partner_type) === 'both')>{{ __('Ambos') }}</option>
                                    </select>
                                    @error('partner_type')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Estado') }} <span class="text-red-500">*</span></span>
                                <div class="flex-1">
                                    <select id="status" name="status"
                                            class="form-select w-full @error('status') border-red-300 @enderror">
                                        <option value="ACTIVE"   @selected(old('status', $partner->status) === 'ACTIVE')>{{ __('Activo') }}</option>
                                        <option value="INACTIVE" @selected(old('status', $partner->status) === 'INACTIVE')>{{ __('Inactivo') }}</option>
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </li>

                        {{-- Email y Teléfono --}}
                        <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Email') }}</span>
                                <div class="flex-1">
                                    <input id="contact_email" name="contact_email" type="email"
                                           value="{{ old('contact_email', $partner->contact_email) }}"
                                           class="form-input w-full @error('contact_email') border-red-300 @enderror" />
                                    @error('contact_email')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Teléfono') }}</span>
                                <div class="flex-1">
                                    <input id="contact_phone" name="contact_phone" type="text"
                                           value="{{ old('contact_phone', $partner->contact_phone) }}"
                                           class="form-input w-full @error('contact_phone') border-red-300 @enderror" />
                                    @error('contact_phone')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </li>

                        {{-- Dirección --}}
                        <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                            <div class="flex items-start gap-3 md:col-span-2">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Dirección') }}</span>
                                <div class="flex-1">
                                    <input id="address" name="address" type="text"
                                           value="{{ old('address', $partner->address) }}"
                                           class="form-input w-full @error('address') border-red-300 @enderror" />
                                    @error('address')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </li>

                        {{-- Ciudad y País --}}
                        <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('Ciudad') }}</span>
                                <div class="flex-1">
                                    <input id="city" name="city" type="text"
                                           value="{{ old('city', $partner->city) }}"
                                           class="form-input w-full @error('city') border-red-300 @enderror" />
                                    @error('city')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">{{ __('País') }}</span>
                                <div class="flex-1">
                                    <input id="country" name="country" type="text"
                                           value="{{ old('country', $partner->country) }}"
                                           class="form-input w-full @error('country') border-red-300 @enderror" />
                                    @error('country')
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
                    <a href="{{ route('partners.index', $partner) }}"
                       class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                        {{ __('Cancelar') }}
                    </a>
                    <button type="submit" form="partner-form"
                            class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                        {{ __('Actualizar') }}
                    </button>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
