<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <div class="flex items-center gap-3">
                    <a href="{{ route('roles.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>
                    </a>
                    <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">{{ $role->name }}</h1>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 ml-8">Permisos asignados a este rol</p>
            </div>
            @can('edit roles')
            <a href="{{ route('roles.edit', $role) }}"
               class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" />
                </svg>
                Editar permisos
            </a>
            @endcan
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 overflow-x-auto">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Matriz de Permisos</h2>
            </div>
            <table class="table-auto w-full text-sm dark:text-gray-300">
                <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/60">
                    <tr>
                        <th class="px-5 py-3 text-left">Módulo</th>
                        <th class="px-4 py-3 text-center">Crear</th>
                        <th class="px-4 py-3 text-center">Ver</th>
                        <th class="px-4 py-3 text-center">Editar</th>
                        <th class="px-4 py-3 text-center">Eliminar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @php
                        $labels = [
                            'items' => 'Artículos',
                            'item-types' => 'Tipos de Artículo',
                            'item-classes' => 'Clases de Artículo',
                            'measurement-units' => 'Unidades de Medida',
                            'warehouses' => 'Almacenes',
                            'locations' => 'Ubicaciones',
                            'partners' => 'Socios',
                            'projects' => 'Proyectos',
                            'project-prefixes' => 'Prefijos de Proyecto',
                            'packing-specifications' => 'Especificaciones de Empaque',
                            'transaction-types' => 'Tipos de Transacción',
                            'containers' => 'Contenedores',
                            'reception' => 'Recepción de Material',
                            'stock-movements' => 'Movimientos de Stock',
                            'inventory-balances' => 'Inventario',
                            'material-outputs' => 'Salidas de Material',
                            'stock-limits' => 'Límites de Stock',
                            'roles' => 'Roles',
                            'permissions' => 'Permisos',
                            'users' => 'Usuarios',
                        ];
                    @endphp
                    @foreach($modules as $module)
                        @php $label = $labels[$module] ?? ucfirst(str_replace('-', ' ', $module)); @endphp
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10">
                            <td class="px-5 py-3 font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                {{ $label }}
                            </td>
                            @foreach(['create', 'view', 'edit', 'delete'] as $action)
                                <td class="px-4 py-3 text-center">
                                    @if(in_array("{$action} {$module}", $assigned))
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 text-green-500 mx-auto">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 text-gray-200 dark:text-gray-600 mx-auto">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm-1.965-9.371a.75.75 0 1 0-1.07 1.05l1.965 1.998L5.965 13.68a.75.75 0 1 0 1.07 1.05L9 12.73l1.965 2a.75.75 0 1 0 1.07-1.05L10.07 11.68l1.965-1.998a.75.75 0 1 0-1.07-1.05L9 10.62 7.035 8.629Z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
