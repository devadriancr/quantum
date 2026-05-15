<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Nueva Moneda</h1>
            </div>
            <a href="{{ route('currencies.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Regresar
            </a>
        </div>

        <form method="POST" action="{{ route('currencies.store') }}">
            @csrf

            <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 p-6 mb-6">
                <!-- Sistema de rejilla de 12 columnas para control simétrico -->
                <div class="grid grid-cols-12 gap-x-6 gap-y-5">

                    <!-- Nombre -->
                    <div class="col-span-12 md:col-span-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-input w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg @error('name') border-red-300 @enderror"
                               placeholder="Dólar Americano, Peso Mexicano...">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Código ISO  -->
                    <div class="col-span-12 md:col-span-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Código ISO <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="code" value="{{ old('code') }}" maxlength="10"
                               class="form-input w-full font-mono uppercase bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg @error('code') border-red-300 @enderror"
                               placeholder="USD, MXN, JPY...">
                        @error('code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Símbolo -->
                    <div class="col-span-12 md:col-span-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Símbolo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="symbol" value="{{ old('symbol') }}" maxlength="10"
                               class="form-input w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg @error('symbol') border-red-300 @enderror"
                               placeholder="$, €, ¥...">
                        @error('symbol')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Unidades por USD -->
                    <div class="col-span-12 md:col-span-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Unidades por USD <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="units_per_usd" value="{{ old('units_per_usd', 1) }}"
                               step="0.000001" min="0.000001"
                               class="form-input w-full font-mono bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg @error('units_per_usd') border-red-300 @enderror"
                               placeholder="17.500000">
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">¿Cuántas unidades equivalen a 1 USD?</p>
                        @error('units_per_usd')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Moneda activa -->
                    <div class="col-span-12 md:col-span-4 md:pt-6">
                        <div class="flex items-center h-[42px]">
                            <label class="flex items-center gap-2 cursor-pointer bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-700/60 rounded-lg px-4 py-2.5 w-full hover:bg-gray-100 dark:hover:bg-gray-700/50 transition">
                                <input type="checkbox" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}
                                       class="form-checkbox text-violet-500 rounded border-gray-300 dark:border-gray-700 focus:ring-violet-500 w-4 h-4">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Moneda activa</span>
                            </label>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Botones de acción --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('currencies.index') }}"
                   class="btn border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancelar
                </a>
                <button type="submit"
                        class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                    Guardar Moneda
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
