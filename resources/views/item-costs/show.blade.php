<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Encabezado --}}
        <div class="sm:flex sm:justify-between sm:items-start mb-8 gap-4">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    Historial de Precios
                </h1>
            </div>
            <div class="flex gap-2 items-center">
                <a href="{{ route('item-costs.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                    </svg>
                    Regresar
                </a>
            </div>
        </div>

        {{-- Tabla de historial --}}
        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-end">
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $costs->total() }} registros</span>
            </div>
            <div class="overflow-x-auto">
                <table class="table-auto w-full dark:text-gray-300">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/60">
                        <tr>
                            <th class="px-5 py-3 text-left">Fecha Inicio</th>
                            <th class="px-5 py-3 text-left">Fecha Fin</th>
                            <th class="px-5 py-3 text-center">Moneda</th>
                            <th class="px-5 py-3 text-center">Costo</th>
                            <th class="px-5 py-3 text-center">Proveedor</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($costs as $cost)
                            @php $isLatest = $loop->first; @endphp
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10 {{ $isLatest ? 'bg-violet-50/40 dark:bg-violet-500/5' : '' }}">

                                {{-- Fecha Inicio --}}
                                <td class="px-5 py-3 text-left whitespace-nowrap">
                                    <span class="text-gray-700 dark:text-gray-300">
                                        {{ $cost->start_date?->format('d/m/Y') ?? '—' }}
                                    </span>
                                </td>

                                {{-- Fecha Fin --}}
                                <td class="px-5 py-3 text-left whitespace-nowrap">
                                    @if($cost->end_date && $cost->end_date->year < 9999)
                                        <span class="text-gray-700 dark:text-gray-300">{{ $cost->end_date->format('d/m/Y') }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">
                                            Sin vencimiento
                                        </span>
                                    @endif
                                </td>

                                {{-- Moneda --}}
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <span class="font-mono font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $cost->currency->code ?? '—' }}
                                    </span>
                                </td>

                                {{-- Costo --}}
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <span class="font-mono font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $cost->currency?->symbol }} {{ number_format($cost->total_cost, 4) }}
                                    </span>
                                </td>

                                {{-- Proveedor --}}
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <span class="font-mono text-xs text-gray-600 dark:text-gray-400">
                                        {{ $cost->vendor_number ?? '—' }}
                                    </span>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500 text-sm">
                                    No hay registros de costo para este artículo.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($costs->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Mostrando <span class="font-medium text-gray-800 dark:text-gray-100">{{ $costs->firstItem() }}</span>
                            a <span class="font-medium text-gray-800 dark:text-gray-100">{{ $costs->lastItem() }}</span>
                            de <span class="font-medium text-gray-800 dark:text-gray-100">{{ $costs->total() }}</span> resultados
                        </div>
                        <div>{{ $costs->links('pagination::simple-tailwind') }}</div>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
