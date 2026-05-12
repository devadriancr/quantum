<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Usuarios</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gestiona los usuarios del sistema y sus roles asignados.</p>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">

                {{-- Búsqueda --}}
                <form action="{{ route('users.index') }}" method="GET" class="relative flex items-center">
                    <label for="user-search" class="sr-only">Buscar</label>
                    <input id="user-search" name="search"
                           class="form-input pl-9 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg"
                           type="search" placeholder="Nombre, email, código..." value="{{ $search }}">
                    <button class="absolute inset-0 right-auto group" type="submit" aria-label="Buscar">
                        <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500 group-hover:text-gray-500 ml-3 mr-2" width="16" height="16" viewBox="0 0 16 16">
                            <path d="M7 14c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zM7 2C4.243 2 2 4.243 2 7s2.243 5 5 5 5-2.243 5-5-2.243-5-5-5z" />
                            <path d="M15.707 14.293L13.314 11.9a8.019 8.019 0 01-1.414 1.414l2.393 2.393a.997.997 0 001.414 0 .999.999 0 000-1.414z" />
                        </svg>
                    </button>
                    @if($search)
                        <a href="{{ route('users.index') }}" class="ml-2 text-sm text-gray-500 hover:text-violet-500 underline whitespace-nowrap">Limpiar</a>
                    @endif
                </form>

                @can('create users')
                <a href="{{ route('users.create') }}"
                   class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nuevo Usuario
                </a>
                @endcan

            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60">
            <div class="overflow-x-auto">
                <table class="table-auto w-full dark:text-gray-300">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-t border-b border-gray-100 dark:border-gray-700/60">
                        <tr>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Usuario</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Nickname / Código</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Email</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Rol</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-center">Estado</div>
                            </th>
                            <th class="px-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-right">Acciones</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10">

                                {{-- Nombre + avatar inicial --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="size-8 rounded-full bg-violet-100 dark:bg-violet-500/20 flex items-center justify-center shrink-0">
                                            <span class="text-xs font-bold text-violet-600 dark:text-violet-400 uppercase">
                                                {{ substr($user->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <span class="font-medium text-gray-800 dark:text-gray-100">
                                            {{ $user->name }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Nickname / Código empleado --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <div class="flex flex-col gap-0.5">
                                        @if($user->nickname)
                                            <span class="text-gray-700 dark:text-gray-300 font-medium text-xs">
                                                {{ $user->nickname }}
                                            </span>
                                        @endif
                                        @if($user->employee_code)
                                            <span class="text-gray-400 dark:text-gray-500 text-xs font-mono">
                                                {{ $user->employee_code }}
                                            </span>
                                        @endif
                                        @if(!$user->nickname && !$user->employee_code)
                                            <span class="text-gray-300 dark:text-gray-600">—</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Email --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="text-gray-600 dark:text-gray-400">{{ $user->email }}</span>
                                </td>

                                {{-- Rol --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    @if($user->role)
                                        @php
                                            $palette = [
                                                'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                                                'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400',
                                                'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400',
                                                'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400',
                                                'bg-lime-100 text-lime-700 dark:bg-lime-500/20 dark:text-lime-400',
                                                'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                                                'bg-teal-100 text-teal-700 dark:bg-teal-500/20 dark:text-teal-400',
                                                'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-400',
                                                'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                                                'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-400',
                                                'bg-violet-100 text-violet-700 dark:bg-violet-500/20 dark:text-violet-400',
                                                'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400',
                                                'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-500/20 dark:text-fuchsia-400',
                                                'bg-pink-100 text-pink-700 dark:bg-pink-500/20 dark:text-pink-400',
                                                'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400',
                                            ];
                                            $badge = $palette[abs(crc32($user->role->name)) % count($palette)];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                            {{ $user->role->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-500">
                                            Sin rol
                                        </span>
                                    @endif
                                </td>

                                {{-- Estado (email verificado) --}}
                                <td class="px-5 py-3 whitespace-nowrap text-center">
                                    @if($user->email_verified_at)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-3">
                                                <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm3.844-8.791a.75.75 0 0 0-1.188-.918l-3.7 4.79-1.764-1.764a.75.75 0 1 0-1.061 1.06l2.5 2.5a.75.75 0 0 0 1.137-.089l4.076-5.579Z" clip-rule="evenodd" />
                                            </svg>
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-3">
                                                <path fill-rule="evenodd" d="M6.701 2.25c.577-1 2.02-1 2.598 0l5.196 9a1.5 1.5 0 0 1-1.299 2.25H2.804a1.5 1.5 0 0 1-1.3-2.25l5.197-9ZM8 4a.75.75 0 0 1 .75.75v3a.75.75 0 1 1-1.5 0v-3A.75.75 0 0 1 8 4Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                                            </svg>
                                            Pendiente
                                        </span>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <div class="flex justify-end gap-2">
                                        @can('edit users')
                                        <a href="{{ route('users.edit', $user) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 border border-blue-300 dark:border-blue-600 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" />
                                            </svg>
                                            Editar
                                        </a>
                                        @endcan
                                        @can('delete users')
                                        @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user) }}"
                                              onsubmit="return confirm('¿Eliminar al usuario «{{ $user->name }}»? Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 border border-red-300 dark:border-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                                Eliminar
                                            </button>
                                        </form>
                                        @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500 text-sm">
                                    @if($search)
                                        No se encontraron usuarios que coincidan con la búsqueda.
                                    @else
                                        No hay usuarios registrados.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages() || $users->total() > 0)
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Mostrando
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $users->firstItem() ?? 0 }}</span>
                            a
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $users->lastItem() ?? 0 }}</span>
                            de
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $users->total() }}</span>
                            resultados
                        </div>
                        <div>{{ $users->links('pagination::simple-tailwind') }}</div>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
