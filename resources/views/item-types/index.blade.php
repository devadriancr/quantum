<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">{{ __('Tipo de Articulos') }}</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                @can('create item-types')
                <a href="{{ route('item-types.create') }}"
                   class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                    {{ __('Agregar Tipo de Artículo') }}
                </a>
                @endcan
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl">
            <div class="p-3">
                <div class="overflow-x-auto">
                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <th class="p-2 text-left">
                                    <div class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Código') }}</div>
                                </th>
                                <th class="p-2 text-left">
                                    <div class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Nombre') }}</div>
                                </th>
                                <th class="p-2 text-left">
                                    <div class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Estado') }}</div>
                                </th>
                                <th class="p-2 text-right">
                                    <div class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">{{ __('Acciones') }}  </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                            @forelse ($itemTypes as $itemType)
                                <tr>
                                    <td class="p-2 whitespace-nowrap">
                                        <span class="font-medium text-gray-800 dark:text-gray-100">{{ $itemType->code }}</span>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <span class="text-gray-600 dark:text-gray-400">{{ $itemType->name }}</span>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        @if ($itemType->status)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400">
                                                {{ __('Activo') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400">
                                                {{ __('Inactivo') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-2 whitespace-nowrap text-right">
                                        <a href="{{ route('item-types.show', $itemType) }}"
                                           class="inline-flex items-center gap-1 font-medium text-violet-500 hover:text-violet-600 dark:hover:text-violet-400 text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            {{ __('Ver') }}
                                        </a>
                                        @can('edit item-types')
                                        <a href="{{ route('item-types.edit', $itemType) }}"
                                           class="inline-flex items-center gap-1 font-medium text-blue-500 hover:text-blue-600 dark:hover:text-blue-400 text-sm ml-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            {{ __('Editar') }}
                                        </a>
                                        @endcan
                                        @can('delete item-types')
                                        <form action="{{ route('item-types.destroy', $itemType) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 font-medium text-red-500 hover:text-red-600 dark:hover:text-red-400 text-sm ml-3"
                                                    onclick="return confirm('Are you sure you want to delete this item type?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                                {{ __('Eliminar') }}
                                            </button>
                                        </form>
                                        @endcan

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-gray-400 dark:text-gray-500 text-sm">
                                        No se han encontrado tipos de artículos.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
