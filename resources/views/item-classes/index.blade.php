<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Page header --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">{{ __('Clases de Artículos') }}</h1>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60">
            <div class="overflow-x-auto">
                <table class="table-auto w-full dark:text-gray-300">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-t border-b border-gray-100 dark:border-gray-700/60">
                        <tr>
                            <th class="px-5 py-3 first:pl-5 last:pr-5 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('Código') }}</div>
                            </th>
                            <th class="px-5 py-3 first:pl-5 last:pr-5 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('Nombre') }}</div>
                            </th>
                            <th class="px-5 py-3 first:pl-5 last:pr-5 whitespace-nowrap">
                                <div class="font-semibold text-left">{{ __('Estado') }}</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($itemClasses as $item)
                        <tr>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <div class="font-medium text-gray-800 dark:text-gray-100">{{ $item->code }}</div>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <div>{{ $item->name }}</div>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                @if($item->status)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">
                                        {{ __('Activo') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                        {{ __('Inactivo') }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-5 py-10 text-center text-gray-500 dark:text-gray-400">
                                {{ __('No se encontraron clases de artículos.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($itemClasses->total() > 0)
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400 text-center sm:text-left">
                            {{ __('Mostrando') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $itemClasses->firstItem() }}</span>
                            {{ __('a') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $itemClasses->lastItem() }}</span>
                            {{ __('de') }}
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $itemClasses->total() }}</span>
                            {{ __('resultados') }}
                        </div>

                        <div class="flex justify-center sm:justify-end">
                            {{ $itemClasses->links('pagination::simple-tailwind') }}
                        </div>

                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
