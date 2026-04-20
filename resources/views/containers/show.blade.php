<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Encabezado --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    {{ __('Detalle de Contenedor') }}
                </h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                @if ($container->status === 'PENDING')
                    <a href="{{ route('containers.edit', $container) }}"
                       class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                        {{ __('Editar') }}
                    </a>
                @endif
                <a href="{{ route('containers.index') }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                    &larr; {{ __('Regresar') }}
                </a>
            </div>
        </div>

        {{-- Mensajes de sesión --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm">
                {{ __(session('success')) }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
                {{ __(session('error')) }}
            </div>
        @endif

        @php
            $statusColors = [
                'PENDING'    => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400',
                'EXPECTED'   => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                'ARRIVED'    => 'bg-teal-100 text-teal-700 dark:bg-teal-500/20 dark:text-teal-400',
                'UNLOADING'  => 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400',
                'INSPECTION' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400',
                'RECEIVED'   => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                'REJECTED'   => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                'IN_TRANSIT' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-400',
            ];
            $partnerTypeMap = [
                'CUSTOMER' => ['label' => __('Cliente'),   'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400'],
                'SUPPLIER' => ['label' => __('Proveedor'), 'class' => 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400'],
                'BOTH'     => ['label' => __('Ambos'),     'class' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400'],
            ];
        @endphp

        {{-- Card: información del contenedor --}}
        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 mb-2">

            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center gap-3">
                {{-- Ícono de contenedor --}}
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-violet-100 dark:bg-violet-500/20 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-violet-600 dark:text-violet-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                </div>
                <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">{{ $container->code }}</h2>
            </header>

            <div class="py-2 px-3">
                <ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">

                    {{-- Socio y tipo de socio --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-2 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Socio') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $container->partner->name ?? '—' }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Tipo de socio') }}</span>
                            @php
                                $pType = $container->partner->partner_type ?? null;
                                $pInfo = $partnerTypeMap[$pType] ?? null;
                            @endphp
                            @if ($pInfo)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pInfo['class'] }}">
                                    {{ $pInfo['label'] }}
                                </span>
                            @else
                                <span class="font-medium text-gray-800 dark:text-gray-100">—</span>
                            @endif
                        </div>
                    </li>

                    {{-- Tipo de contenedor y Estado juntos --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-2 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Tipo de contenedor') }}</span>
                            <div class="flex items-center gap-3">
                                <span class="font-medium text-gray-800 dark:text-gray-100">
                                    {{ __(\App\Models\Container::TYPE_OPTIONS[$container->container_type] ?? $container->container_type) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Estado') }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$container->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ __(\App\Models\Container::STATUS_OPTIONS[$container->status] ?? $container->status) }}
                            </span>
                        </div>
                    </li>

                    {{-- Fecha y hora estimada de llegada --}}
                    <li class="grid grid-cols-1 md:grid-cols-2 py-2 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Fecha estimada') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">
                                {{ $container->estimated_arrival_date
                                    ? \Carbon\Carbon::parse($container->estimated_arrival_date)->format('d/m/Y')
                                    : '—' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Hora estimada') }}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100">
                                {{ $container->estimated_arrival_time
                                    ? \Carbon\Carbon::parse($container->estimated_arrival_time)->format('H:i')
                                    : '—' }}
                            </span>
                        </div>
                    </li>

                    {{-- Notas --}}
                    @if ($container->notes)
                        <li class="py-2">
                            <div class="flex items-start gap-3">
                                <span class="w-44 shrink-0 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 pt-0.5">{{ __('Notas') }}</span>
                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $container->notes }}</p>
                            </div>
                        </li>
                    @endif

                </ul>
            </div>
        </div>

        {{-- Tabla de líneas (agrupadas por documento) --}}
        @foreach ($container->shipmentDocuments as $document)
            <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 mb-6">

                <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between gap-3 flex-wrap">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-500 dark:text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">
                            {{ $document->document_number ?? '—' }}
                        </h3>
                    </div>
                    @php
                        $docStatusColors = [
                            'PENDING'     => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400',
                            'RECEIVED'    => 'bg-teal-100 text-teal-700 dark:bg-teal-500/20 dark:text-teal-400',
                            'PROCESSED'   => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                            'PARTIAL'     => 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400',
                            'COMPLETE'    => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                            'DISCREPANCY' => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $docStatusColors[$document->document_status] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ __(\App\Models\ShipmentDocument::STATUS_OPTIONS[$document->document_status] ?? $document->document_status) }}
                    </span>
                </header>

                <div class="overflow-x-auto">
                    <table class="table-auto w-full dark:text-gray-300 text-sm">
                        <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/60">
                            <tr>
                                <th class="px-5 py-3 whitespace-nowrap">
                                    <div class="font-semibold text-left">{{ __('#') }}</div>
                                </th>
                                <th class="px-5 py-3 whitespace-nowrap">
                                    <div class="font-semibold text-left">{{ __('Serial') }}</div>
                                </th>
                                <th class="px-5 py-3 whitespace-nowrap">
                                    <div class="font-semibold text-left">{{ __('Código de artículo') }}</div>
                                </th>
                                <th class="px-5 py-3 whitespace-nowrap">
                                    <div class="font-semibold text-left">{{ __('Descripción') }}</div>
                                </th>
                                <th class="px-5 py-3 whitespace-nowrap">
                                    <div class="font-semibold text-right">{{ __('Cantidad a recibir') }}</div>
                                </th>
                                <th class="px-5 py-3 whitespace-nowrap">
                                    <div class="font-semibold text-left">{{ __('Estado') }}</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                            @forelse ($document->shipmentDocumentLines as $line)
                                @php
                                    $lineStatusColors = [
                                        'PENDING'     => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400',
                                        'RECEIVED'    => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                                        'DAMAGED'     => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                                        'EXPECTED'    => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                                        'DISCREPANCY' => 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400',
                                    ];
                                @endphp
                                <tr>
                                    <td class="px-5 py-3 whitespace-nowrap text-gray-500 dark:text-gray-400">
                                        {{ $line->line_number }}
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        <span class="font-medium text-gray-800 dark:text-gray-100">
                                            {{ $line->serial_number ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        <span class="font-medium text-gray-800 dark:text-gray-100">
                                            {{ $line->item->code ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="text-gray-600 dark:text-gray-400">
                                            {{ $line->item->description ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap text-right">
                                        <span class="font-semibold text-gray-800 dark:text-gray-100">
                                            {{ number_format($line->quantity_received, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        @if ($line->status)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $lineStatusColors[$line->status] ?? 'bg-gray-100 text-gray-600' }}">
                                                {{ __(\App\Models\ShipmentDocumentLine::STATUS_OPTIONS[$line->status] ?? $line->status) }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                                        {{ __('Este documento no tiene líneas registradas.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        @if ($container->shipmentDocuments->isEmpty())
            <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 px-5 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                {{ __('Este contenedor no tiene documentos de envío registrados.') }}
            </div>
        @endif

    </div>
</x-app-layout>
