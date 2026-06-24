<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="mb-8">
            <div class="flex items-center gap-3">
                <a href="{{ route('roles.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Editar Rol: {{ $role->name }}</h1>
            </div>
        </div>

        <form method="POST" action="{{ route('roles.update', $role) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Datos básicos --}}
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 p-6">
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Información del Rol</h2>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $role->name) }}"
                                   class="form-input w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg"
                                   placeholder="Ej. Supervisor">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Etiqueta
                            </label>
                            <input type="text" name="label" value="{{ old('label', $role->label) }}"
                                   class="form-input w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg"
                                   placeholder="Nombre descriptivo (opcional)">
                            @error('label')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Matriz de permisos --}}
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Permisos</h2>
                            <div class="flex gap-3 text-xs">
                                <button type="button" onclick="toggleAll(true)"
                                        class="text-violet-600 dark:text-violet-400 hover:underline font-medium">
                                    Seleccionar todo
                                </button>
                                <span class="text-gray-300 dark:text-gray-600">|</span>
                                <button type="button" onclick="toggleAll(false)"
                                        class="text-gray-500 dark:text-gray-400 hover:underline font-medium">
                                    Quitar todo
                                </button>
                            </div>
                        </div>

                        @php
                            $modules = [
                                'items' => 'Artículos',
                                'item-types' => 'Tipos de Artículo',
                                'item-classes' => 'Clases de Artículo',
                                'measurement-units' => 'Unidades de Medida',
                                'warehouses' => 'Almacenes',
                                'locations' => 'Ubicaciones',
                                'partners' => 'Socios',
                                'projects' => 'Proyectos',
                                'project-prefixes' => 'Prefijos de Proyecto',
                                'packing-specifications' => 'Esp. de Empaque',
                                'transaction-types' => 'Tipos de Transacción',
                                'containers' => 'Contenedores',
                                'reception' => 'Recepción de Material',
                                'stock-movements' => 'Histórico de Movimientos',
                                'inventory-balances' => 'Inventario',
                                'material-outputs' => 'Salidas de Material',
                                'stock-limits' => 'Límites de Stock',
                                'currencies' => 'Monedas',
                                'item-costs' => 'Historial de Costos',
                                'inventory-adjustments' => 'Ajustes de Inventario',
                                'unit-plans' => 'Planeación de Unidades',
                                'roles' => 'Roles',
                                'permissions' => 'Permisos',
                                'users' => 'Usuarios',
                            ];
                            $actions = ['create' => 'Crear', 'view' => 'Ver', 'edit' => 'Editar', 'delete' => 'Eliminar'];
                            $currentPermIds = old('permissions', $assignedIds);
                        @endphp

                        <div class="overflow-x-auto">
                            <table class="table-auto w-full text-sm">
                                <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/60">
                                    <tr>
                                        <th class="px-5 py-3 text-left">Módulo</th>
                                        @foreach($actions as $actionLabel)
                                            <th class="px-4 py-3 text-center">{{ $actionLabel }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                                    @foreach($modules as $module => $moduleLabel)
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10">
                                            <td class="px-5 py-3 font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                                {{ $moduleLabel }}
                                            </td>
                                            @foreach(array_keys($actions) as $action)
                                                @php
                                                    $perm = $permissions->flatten()->firstWhere('name', "{$action} {$module}");
                                                @endphp
                                                <td class="px-4 py-3 text-center">
                                                    @if($perm)
                                                        <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                                               class="perm-checkbox rounded border-gray-300 dark:border-gray-600 text-violet-500 focus:ring-violet-400"
                                                               {{ in_array($perm->id, $currentPermIds) ? 'checked' : '' }}>
                                                    @else
                                                        <span class="text-gray-300 dark:text-gray-600 text-xs">—</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @php
                            // Permisos que no siguen el patrón "{acción} {módulo}" de la matriz
                            $standardNames = collect($modules)->keys()
                                ->crossJoin(array_keys($actions))
                                ->map(fn ($p) => "{$p[1]} {$p[0]}")
                                ->all();
                            $specialPerms = $permissions->flatten()
                                ->reject(fn ($p) => in_array($p->name, $standardNames));
                        @endphp
                        @if($specialPerms->isNotEmpty())
                            <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60">
                                <h3 class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-3">Permisos Especiales</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($specialPerms as $perm)
                                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                                   class="perm-checkbox rounded border-gray-300 dark:border-gray-600 text-violet-500 focus:ring-violet-400"
                                                   {{ in_array($perm->id, $currentPermIds) ? 'checked' : '' }}>
                                            {{ $perm->label ?: $perm->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @error('permissions')
                            <p class="text-red-500 text-xs px-5 py-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            {{-- Botones --}}
            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('roles.index') }}"
                   class="btn border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancelar
                </a>
                <button type="submit"
                        class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                    Guardar Cambios
                </button>
            </div>
        </form>

    </div>

    <script>
        function toggleAll(check) {
            document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = check);
        }
    </script>
</x-app-layout>
