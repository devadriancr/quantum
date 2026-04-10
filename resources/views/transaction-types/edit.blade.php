<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">{{ __('Editar Tipo de Transacción') }}</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('transaction-types.index') }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                    &larr; {{ __('Regresar') }}
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl">

            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ $transactionType->code }}</h2>
            </header>

            <div class="px-5 pt-5 pb-2">
                <ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">

                    {{-- Solo lectura: Código y Descripción --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Código') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $transactionType->code }}</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-0.5">{{ __('Descripción') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $transactionType->description ?? '—' }}</span>
                        </div>
                    </li>

                    {{-- Campos editables --}}
                    <form id="transaction-type-form" action="{{ route('transaction-types.update', $transactionType) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Categoría y Dirección --}}
                        <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">

                            <div class="flex items-start gap-3">
                                <label for="transaction_category" class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">
                                    {{ __('Categoría') }}
                                </label>
                                <div class="flex-1">
                                    <select id="transaction_category" name="transaction_category"
                                            class="form-select w-full @error('transaction_category') border-red-300 @enderror">
                                        <option value="">— {{ __('Sin categoría') }} —</option>
                                        @foreach ([
                                            'RECEIPT'           => 'Recepción',
                                            'SHIPMENT'          => 'Envío',
                                            'INTERNAL_TRANSFER' => 'Transferencia Interna',
                                            'ADJUSTMENT'        => 'Ajuste',
                                            'RETURN'            => 'Devolución',
                                            'OTHER'             => 'Otro',
                                        ] as $value => $label)
                                            <option value="{{ $value }}" {{ old('transaction_category', $transactionType->transaction_category) === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('transaction_category')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <label for="direction" class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">
                                    {{ __('Dirección') }}
                                </label>
                                <div class="flex-1">
                                    <select id="direction" name="direction"
                                            class="form-select w-full @error('direction') border-red-300 @enderror">
                                        <option value="">— {{ __('Sin dirección') }} —</option>
                                        <option value="IN"  {{ old('direction', $transactionType->direction) === 'IN'  ? 'selected' : '' }}>{{ __('Entrada') }}</option>
                                        <option value="OUT" {{ old('direction', $transactionType->direction) === 'OUT' ? 'selected' : '' }}>{{ __('Salida') }}</option>
                                    </select>
                                    @error('direction')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </li>

                        {{-- Afecta inventario y Estado --}}
                        <li class="grid grid-cols-1 md:grid-cols-2 py-3 gap-4">

                            <div class="flex items-center gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Afecta Inventario') }}</span>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="hidden" name="affects_inventory" value="0" />
                                    <input id="affects_inventory" name="affects_inventory" type="checkbox" value="1"
                                           {{ old('affects_inventory', $transactionType->affects_inventory) ? 'checked' : '' }}
                                           class="form-checkbox text-violet-500" />
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Sí afecta') }}</span>
                                </label>
                            </div>

                            <div class="flex items-start gap-3">
                                <label for="status" class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-2">
                                    {{ __('Estado') }}
                                </label>
                                <div class="flex-1">
                                    <select id="status" name="status"
                                            class="form-select w-full @error('status') border-red-300 @enderror">
                                        <option value="ACTIVE"   {{ old('status', $transactionType->status) === 'ACTIVE'   ? 'selected' : '' }}>{{ __('Activo') }}</option>
                                        <option value="INACTIVE" {{ old('status', $transactionType->status) === 'INACTIVE' ? 'selected' : '' }}>{{ __('Inactivo') }}</option>
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

            {{-- Botones --}}
            <div class="px-5 pb-5">
                <div class="flex justify-end pt-5 border-t border-gray-100 dark:border-gray-700/60 gap-3">
                    <a href="{{ route('transaction-types.index') }}"
                       class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                        {{ __('Cancelar') }}
                    </a>
                    <button type="submit" form="transaction-type-form"
                            class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                        {{ __('Actualizar') }}
                    </button>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
