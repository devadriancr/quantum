<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    {{ __('Recepción de Almacén') }}
                </h1>
            </div>

            <form action="{{ route('reception.index') }}" method="GET" class="relative flex items-center">
                <input
                    name="search"
                    value="{{ $search }}"
                    class="form-input pl-9 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg"
                    type="search"
                    placeholder="Buscar documento o socio..."
                />
                <button class="absolute inset-0 right-auto group" type="submit">
                    <svg class="shrink-0 fill-current text-gray-400 ml-3 mr-2" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M7 14c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zM7 2C4.243 2 2 4.243 2 7s2.243 5 5 5 5-2.243 5-5-2.243-5-5-5z"/>
                        <path d="M15.707 14.293L13.314 11.9a8.019 8.019 0 01-1.414 1.414l2.393 2.393a.997.997 0 001.414 0 .999.999 0 000-1.414z"/>
                    </svg>
                </button>
                @if($search)
                    <a href="{{ route('reception.index') }}" class="ml-2 text-sm text-gray-500 hover:text-violet-500 underline">Limpiar</a>
                @endif
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60">
            <div class="overflow-x-auto">
                <table class="table-auto w-full dark:text-gray-300">
                    <thead class="text-sm font-semibold text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/60">
                        <tr>
                            <th class="px-5 py-3 text-left">{{ __('Documento') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Contenedor') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Socio') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Fecha') }}</th>
                            <th class="px-5 py-3 text-center">{{ __('Estado') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Acción') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($documents as $doc)
                            @php
                                $statusColors = [
                                    'PENDING' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400',
                                    'PARTIAL' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                                ];
                                $statusLabels = \App\Models\ShipmentDocument::STATUS_OPTIONS;
                            @endphp
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10">
                                <td class="px-5 py-3">
                                    <span class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $doc->document_number ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-gray-600 dark:text-gray-400">
                                    {{ $doc->container->code ?? '—' }}
                                </td>
                                <td class="px-5 py-3 text-gray-600 dark:text-gray-400">
                                    {{ $doc->partner->name ?? '—' }}
                                </td>
                                <td class="px-5 py-3 text-gray-600 dark:text-gray-400">
                                    {{ $doc->document_date ? \Carbon\Carbon::parse($doc->document_date)->format('d/m/Y') : '—' }}
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium {{ $statusColors[$doc->document_status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $statusLabels[$doc->document_status] ?? $doc->document_status }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('reception.show', $doc) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold bg-violet-500 hover:bg-violet-600 text-white transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                                        </svg>
                                        Escanear
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">
                                    No hay documentos pendientes de recepción.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($documents->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Mostrando <span class="font-medium text-gray-800 dark:text-gray-100">{{ $documents->firstItem() }}</span>
                            a <span class="font-medium text-gray-800 dark:text-gray-100">{{ $documents->lastItem() }}</span>
                            de <span class="font-medium text-gray-800 dark:text-gray-100">{{ $documents->total() }}</span>
                        </div>
                        <div>{{ $documents->links('pagination::simple-tailwind') }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
