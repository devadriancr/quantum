<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    Detalle de Recepción
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-mono">
                    {{ $stockMovement->movement_number }}
                </p>
            </div>
            <a href="{{ route('stock-movements.index') }}"
               class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 text-gray-600 dark:text-gray-300">
                &larr; Regresar
            </a>
        </div>

        {{-- Cabecera del movimiento --}}
        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 mb-6">
            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">Información del movimiento</h2>
            </header>
            <div class="py-4 px-5">
                @php
                    $sc = [
                        'PENDING'   => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400',
                        'COMPLETED' => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                        'CANCELED'  => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                        'RECEIVED'  => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                        'VERIFIED'  => 'bg-teal-100 text-teal-700 dark:bg-teal-500/20 dark:text-teal-400',
                        'RECORDED'  => 'bg-violet-100 text-violet-700 dark:bg-violet-500/20 dark:text-violet-400',
                        'REJECTED'  => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                    ];
                @endphp
                <ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">
                    {{-- Fila 1: Fecha, Tipo, Transacción, Estado --}}
                    <li class="grid grid-cols-2 md:grid-cols-4 py-3 gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Fecha</p>
                            <p class="font-medium text-gray-800 dark:text-gray-100 mt-0.5">
                                {{ \Carbon\Carbon::parse($stockMovement->movement_date)->format('d/m/Y') }}
                                <span class="text-gray-400 ml-1">{{ substr($stockMovement->movement_time, 0, 5) }}</span>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Tipo</p>
                            <p class="font-medium text-gray-800 dark:text-gray-100 mt-0.5">
                                {{ \App\Models\StockMovement::MOVEMENT_TYPES[$stockMovement->movement_type] ?? $stockMovement->movement_type }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Tipo de transacción</p>
                            <p class="font-medium text-gray-800 dark:text-gray-100 mt-0.5">
                                {{ $stockMovement->transactionType?->code ?? '—' }}
                                @if($stockMovement->transactionType?->name)
                                    <span class="text-gray-400 text-xs ml-1">{{ $stockMovement->transactionType->name }}</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Estado</p>
                            <span class="inline-flex mt-0.5 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $sc[$stockMovement->status] ?? '' }}">
                                {{ \App\Models\StockMovement::STATUS_OPTIONS[$stockMovement->status] ?? $stockMovement->status }}
                            </span>
                        </div>
                    </li>
                    {{-- Fila 2: Contenedor, Ubicación destino, Socio, Documento --}}
                    <li class="grid grid-cols-2 md:grid-cols-4 py-3 gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Contenedor</p>
                            <p class="font-medium text-gray-800 dark:text-gray-100 mt-0.5">
                                {{ $stockMovement->container?->code ?? '—' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Ubicación destino</p>
                            <p class="font-medium text-violet-600 dark:text-violet-400 mt-0.5">
                                {{ $stockMovement->locationTo?->code ?? '—' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Socio</p>
                            <p class="font-medium text-gray-800 dark:text-gray-100 mt-0.5">
                                {{ $stockMovement->partner?->name ?? '—' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Documento</p>
                            <p class="font-medium text-gray-800 dark:text-gray-100 mt-0.5">
                                {{ $stockMovement->shipmentDocument?->document_number ?? '—' }}
                            </p>
                        </div>
                    </li>
                    {{-- Fila 3: Totales y notas --}}
                    <li class="grid grid-cols-2 md:grid-cols-4 py-3 gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Total piezas</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-0.5">
                                {{ number_format($totalQty, 0) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Costo total</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-0.5">
                                ${{ number_format($totalCost, 2) }}
                            </p>
                        </div>
                        @if($stockMovement->notes)
                        <div class="col-span-2">
                            <p class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Notas</p>
                            <p class="text-gray-700 dark:text-gray-300 mt-0.5">{{ $stockMovement->notes }}</p>
                        </div>
                        @endif
                    </li>
                </ul>
            </div>
        </div>

        {{-- Líneas del movimiento --}}
        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 overflow-hidden">
            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">Números de parte recibidos</h2>
                <span class="text-xs font-bold text-violet-600 bg-violet-50 dark:bg-violet-500/10 px-2 py-0.5 rounded">
                    {{ $stockMovement->lines->count() }} {{ $stockMovement->lines->count() === 1 ? 'línea' : 'líneas' }}
                </span>
            </header>
            <div class="overflow-x-auto">
                <table class="table-auto w-full dark:text-gray-300 text-sm">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/60">
                        <tr>
                            <th class="px-5 py-3 text-left">#</th>
                            <th class="px-5 py-3 text-left">N° Parte</th>
                            <th class="px-5 py-3 text-left">Descripción</th>
                            <th class="px-5 py-3 text-left">Serial / Lote</th>
                            <th class="px-5 py-3 text-right">Cantidad</th>
                            <th class="px-5 py-3 text-right">Costo unit.</th>
                            <th class="px-5 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($stockMovement->lines as $i => $line)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10">
                                <td class="px-5 py-3 text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-5 py-3 font-mono font-semibold text-gray-800 dark:text-gray-100 text-xs">
                                    {{ $line->item?->code ?? '—' }}
                                </td>
                                <td class="px-5 py-3 text-gray-600 dark:text-gray-400">
                                    {{ $line->item?->description ?? '—' }}
                                </td>
                                <td class="px-5 py-3 text-xs">
                                    @if($line->serial_batch_number)
                                        <span class="font-mono bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-0.5 rounded">
                                            {{ $line->serial_batch_number }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right font-medium text-gray-800 dark:text-gray-100">
                                    {{ number_format($line->quantity_received, 0) }}
                                </td>
                                <td class="px-5 py-3 text-right text-gray-600 dark:text-gray-400">
                                    ${{ number_format($line->unit_cost ?? 0, 4) }}
                                </td>
                                <td class="px-5 py-3 text-right font-semibold text-gray-800 dark:text-gray-100">
                                    ${{ number_format(($line->quantity_received ?? 0) * ($line->unit_cost ?? 0), 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-gray-400 text-sm">
                                    Este movimiento no tiene líneas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($stockMovement->lines->isNotEmpty())
                        <tfoot class="border-t-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/20">
                            <tr>
                                <td colspan="4" class="px-5 py-3 text-right text-xs font-semibold uppercase text-gray-500">Totales</td>
                                <td class="px-5 py-3 text-right font-bold text-gray-800 dark:text-gray-100">{{ number_format($totalQty, 0) }}</td>
                                <td></td>
                                <td class="px-5 py-3 text-right font-bold text-gray-800 dark:text-gray-100">${{ number_format($totalCost, 2) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
