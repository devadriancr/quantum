<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Encabezado --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    {{ $inventoryAdjustment->adjustment_number }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Ajuste de Inventario · {{ $inventoryAdjustment->adjustment_date->format('d/m/Y') }}
                </p>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('inventory-adjustments.index') }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                    &larr; {{ __('Regresar') }}
                </a>
            </div>
        </div>

        {{-- Información General --}}
        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 mb-6">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="text-sm font-semibold uppercase text-gray-500 dark:text-gray-400">
                    {{ __('Información General') }}
                </h2>
            </div>
            <div class="px-5 py-4">
                <ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">

                    <li class="grid grid-cols-1 md:grid-cols-3 py-3 gap-4">
                        <div>
                            <span class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">N° Ajuste</span>
                            <p class="font-mono font-medium text-gray-800 dark:text-gray-100 mt-1">
                                {{ $inventoryAdjustment->adjustment_number }}
                            </p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Fecha</span>
                            <p class="text-gray-700 dark:text-gray-300 mt-1">
                                {{ $inventoryAdjustment->adjustment_date->format('d/m/Y') }}
                            </p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Tipo</span>
                            <p class="mt-1">
                                @php
                                    $typeColors = [
                                        'VARIANCE'   => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400',
                                        'DAMAGE'     => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                                        'LOSS'       => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                                        'FOUND'      => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                                        'CORRECTION' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                                    ];
                                    $color = $typeColors[$inventoryAdjustment->adjustment_type] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                    {{ \App\Models\InventoryAdjustment::ADJUSTMENT_TYPES[$inventoryAdjustment->adjustment_type] ?? $inventoryAdjustment->adjustment_type }}
                                </span>
                            </p>
                        </div>
                    </li>

                    <li class="py-3">
                        <span class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Motivo</span>
                        <p class="text-gray-700 dark:text-gray-300 mt-1">{{ $inventoryAdjustment->reason }}</p>
                    </li>

                    @if($inventoryAdjustment->notes)
                        <li class="py-3">
                            <span class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Notas</span>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $inventoryAdjustment->notes }}</p>
                        </li>
                    @endif

                    <li class="py-3">
                        <span class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Registrado por</span>
                        <p class="text-gray-700 dark:text-gray-300 mt-1">
                            {{ $inventoryAdjustment->createdBy?->name ?? '—' }}
                            <span class="text-gray-400 dark:text-gray-500 ml-2 text-xs">
                                {{ $inventoryAdjustment->created_at->format('d/m/Y H:i') }}
                            </span>
                        </p>
                    </li>

                </ul>
            </div>
        </div>

        {{-- Líneas --}}
        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                <h2 class="text-sm font-semibold uppercase text-gray-500 dark:text-gray-400">
                    {{ __('Líneas de Ajuste') }}
                </h2>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $inventoryAdjustment->lines->count() }} {{ $inventoryAdjustment->lines->count() === 1 ? 'línea' : 'líneas' }}
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="table-auto w-full dark:text-gray-300">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/60">
                        <tr>
                            <th class="px-5 py-3 text-left">#</th>
                            <th class="px-5 py-3 text-left">N° Parte</th>
                            <th class="px-5 py-3 text-left">Descripción</th>
                            <th class="px-5 py-3 text-left">Ubicación</th>
                            <th class="px-5 py-3 text-right">Cant. Anterior</th>
                            <th class="px-5 py-3 text-right">Ajuste (±)</th>
                            <th class="px-5 py-3 text-right">Cant. Final</th>
                            <th class="px-5 py-3 text-left">Notas</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($inventoryAdjustment->lines as $line)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10">
                                <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $line->line_number }}</td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $line->item?->code ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 max-w-xs">
                                    <span class="text-gray-600 dark:text-gray-400 truncate block" title="{{ $line->item?->description }}">
                                        {{ $line->item?->description ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">
                                        {{ $line->location?->code ?? '—' }}
                                    </span>
                                    @if($line->location?->warehouse)
                                        <span class="ml-1 text-xs text-gray-400 dark:text-gray-500">
                                            {{ $line->location->warehouse->name }}
                                        </span>
                                    @endif
                                </td>
                                {{-- Cant. Anterior --}}
                                <td class="px-5 py-3 text-right font-mono text-gray-600 dark:text-gray-400">
                                    {{ number_format($line->system_quantity, 2) }}
                                </td>
                                {{-- Ajuste (±) = delta aplicado --}}
                                <td class="px-5 py-3 text-right font-mono font-semibold">
                                    @php $v = (float) $line->variance; @endphp
                                    <span class="{{ $v > 0 ? 'text-green-600 dark:text-green-400' : ($v < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-400') }}">
                                        {{ $v >= 0 ? '+' : '' }}{{ number_format($v, 2) }}
                                    </span>
                                </td>
                                {{-- Cant. Final = system + delta --}}
                                <td class="px-5 py-3 text-right font-mono font-medium text-gray-800 dark:text-gray-100">
                                    {{ number_format($line->adjusted_quantity, 2) }}
                                </td>
                                <td class="px-5 py-3 text-gray-500 dark:text-gray-400">
                                    {{ $line->notes ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-gray-400 dark:text-gray-500 text-sm">
                                    Sin líneas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
