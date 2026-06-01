<div x-data="unitPlanBoard" class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

    {{-- ─────────────────── Header ─────────────────── --}}
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                {{ __('Planeación de Unidades') }}
            </h1>
            <p class="text-xs text-gray-500 mt-1" x-show="excelData">
                <span x-text="totalContainersInPool.toLocaleString('en-US')"></span>
                {{ __('contenedor(es) cargado(s)') }}
            </p>
        </div>

        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <input type="file" wire:model="excelFile" x-ref="fileInput" accept=".xlsx,.xls,.csv" class="hidden" />

            {{-- Cargar Contenedores: verde outline + icono table-cells de Heroicons --}}
            <button
                type="button"
                @click="$refs.fileInput.click()"
                wire:loading.attr="disabled"
                class="btn border border-green-600 bg-green-100 text-green-700 hover:bg-green-200 disabled:opacity-50 dark:bg-green-900/30 dark:text-green-300 dark:border-green-500 dark:hover:bg-green-900/50 transition-colors"
            >
                {{-- Heroicons: table-cells (outline) --}}
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5" />
                </svg>
                {{ __('Cargar Contenedores') }}
            </button>

            {{-- Limpiar Tabla: neutro outline + icono arrow-path de Heroicons --}}
            <button
                type="button"
                @click="clearAssignments()"
                :disabled="Object.keys(assignments).length === 0"
                class="btn border border-gray-500 bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:opacity-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-600 transition-colors"
            >
                {{-- Heroicons: arrow-path (outline) --}}
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                {{ __('Limpiar Tabla') }}
            </button>

            {{-- Guardar: violeta outline + icono bookmark-square de Heroicons --}}
            <button
                type="button"
                @click="save()"
                wire:loading.attr="disabled"
                wire:target="savePlan"
                :disabled="Object.keys(assignments).length === 0"
                class="btn border border-violet-600 bg-violet-100 text-violet-700 hover:bg-violet-200 disabled:opacity-50 dark:bg-violet-900/30 dark:text-violet-300 dark:border-violet-500 dark:hover:bg-violet-900/50 transition-colors"
            >
                {{-- Heroicons: bookmark-square (outline) --}}
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6A2.25 2.25 0 0 1 6 3.75h1.5m9 0h-9" />
                </svg>
                {{ __('Guardar Planeación') }}
            </button>
        </div>
    </div>

    {{-- ─────────────────── Overlay de carga ─────────────────── --}}
    <x-loading-overlay message="En proceso" subtext="Espere un momento" />

    {{-- ─────────────────── Toasts ─────────────────── --}}
    <div class="fixed top-4 right-4 z-50 flex flex-col gap-2 w-80 pointer-events-none">
        <template x-for="t in toasts" :key="t.id">
            <div
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-4"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-4"
                class="pointer-events-auto rounded-lg shadow-lg border px-4 py-3 flex items-start gap-3"
                :class="{
                    'bg-green-50 border-green-300 text-green-800 dark:bg-green-900/40 dark:border-green-700 dark:text-green-200': t.type === 'success',
                    'bg-red-50 border-red-300 text-red-800 dark:bg-red-900/40 dark:border-red-700 dark:text-red-200': t.type === 'error',
                    'bg-amber-50 border-amber-300 text-amber-800 dark:bg-amber-900/40 dark:border-amber-700 dark:text-amber-200': t.type === 'warning',
                }">
                <div class="shrink-0 mt-0.5">
                    <svg x-show="t.type === 'success'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <svg x-show="t.type === 'error'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <svg x-show="t.type === 'warning'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.008v.008H12v-.008Z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold" x-text="t.title"></p>
                    <p x-show="t.body" class="text-xs mt-0.5 opacity-90 whitespace-pre-line" x-text="t.body"></p>
                    <div x-show="t.details && t.details.length > 0" class="mt-1 max-h-24 overflow-y-auto">
                        <ul class="text-[11px] list-disc list-inside opacity-80">
                            <template x-for="(d, i) in (t.details || [])" :key="i">
                                <li x-text="d"></li>
                            </template>
                        </ul>
                    </div>
                </div>
                <button type="button" class="shrink-0 opacity-60 hover:opacity-100" @click="dismissToast(t.id)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    </div>

    {{-- ─────────────────── Tablero de horarios ─────────────────── --}}
    <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 overflow-x-auto">
        <table class="w-full table-fixed dark:text-gray-300">
            <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/60">
                <tr>
                    <th class="px-3 py-3 text-center text-[10px]">{{ __('Hora') }}</th>
                    <template x-for="day in days" :key="day.value">
                        <th class="px-3 py-3 text-center border-l border-gray-100 dark:border-gray-700/60" x-text="day.label"></th>
                    </template>
                </tr>
            </thead>
            <tbody class="text-sm">
                <template x-for="slotIdx in [1,2,3,4]" :key="slotIdx">
                    <tr class="border-b border-gray-100 dark:border-gray-700/60">
                        <td class="px-2 py-2 align-middle">
                            <input
                                type="time"
                                x-model="slotTimes[slotIdx]"
                                class="form-input w-full text-xs"
                                :class="hasSlotTimeConflict(slotIdx) ? 'border-red-500' : ''"
                            />
                        </td>
                        <template x-for="day in days" :key="day.value + '-' + slotIdx">
                            <td class="border-l border-gray-100 dark:border-gray-700/60 align-top p-2">
                                <div class="kanban-slot min-h-16 rounded-md border-2 border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/30 p-1 flex items-center justify-center"
                                     :data-day="day.value"
                                     :data-slot="slotIdx">
                                    <template x-if="getCell(day.value, slotIdx)">
                                        <div class="kanban-card w-full bg-violet-100 dark:bg-violet-900/40 border border-violet-400 text-violet-800 dark:text-violet-200 rounded px-2 py-2 text-center cursor-move shadow-sm"
                                             :data-container-code="getCell(day.value, slotIdx).code">
                                            <p class="font-bold text-xs truncate" x-text="getCell(day.value, slotIdx).code"></p>
                                            <p class="font-bold text-xs opacity-75 mt-0.5" x-text="formatMoney(getCell(day.value, slotIdx).price)"></p>
                                        </div>
                                    </template>
                                </div>
                            </td>
                        </template>
                    </tr>
                </template>

                {{-- Fila de totales --}}
                <tr class="bg-gray-50 dark:bg-gray-900/30">
                    <td class="px-2 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 text-center">$</td>
                    <template x-for="day in days" :key="'total-' + day.value">
                        <td class="px-2 py-3 text-center border-l border-gray-100 dark:border-gray-700/60 font-bold text-green-700 dark:text-green-400 text-sm"
                            x-text="formatMoney(getDayTotal(day.value))"></td>
                    </template>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- ─────────────────── Inventario + Pool ─────────────────── --}}
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Proyección de Inventario (siempre visible con headers + empty state) --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                    {{ __('Proyección de Inventario') }}
                </h2>
                <span class="text-xs text-gray-500" x-show="projection.length > 0">
                    <span x-text="filteredProjection.length"></span> / <span x-text="projection.length"></span> {{ __('item(s)') }}
                </span>
            </div>

            {{-- Buscador por número de parte --}}
            <div class="relative mb-3" x-show="projection.length > 0">
                <input
                    type="search"
                    x-model="projectionSearch"
                    placeholder="{{ __('Buscar por n° de parte...') }}"
                    class="form-input w-full text-sm pl-9 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg"
                />
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400 pointer-events-none">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <button x-show="projectionSearch" @click="projectionSearch = ''" type="button"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 overflow-auto max-h-[500px]">
                <table class="w-full table-fixed dark:text-gray-300">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/60 sticky top-0 z-10">
                        <tr>
                            <th class="px-3 py-3 text-center text-[10px]">{{ __('N° Parte') }}</th>
                            <template x-for="day in days" :key="'ph-' + day.value">
                                <th class="px-3 py-3 text-center border-l border-gray-100 dark:border-gray-700/60" x-text="day.label"></th>
                            </template>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        {{-- Estado vacío (sin asignaciones) --}}
                        <template x-if="projection.length === 0">
                            <tr>
                                <td :colspan="days.length + 1" class="px-3 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2 text-gray-400 dark:text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10 opacity-50">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                                        </svg>
                                        <p class="text-sm font-medium">{{ __('Sin datos para mostrar') }}</p>
                                        <p class="text-xs text-gray-400">{{ __('Arrastra un contenedor al tablero para ver la proyección.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        {{-- Sin coincidencias en búsqueda --}}
                        <template x-if="projection.length > 0 && filteredProjection.length === 0">
                            <tr>
                                <td :colspan="days.length + 1" class="px-3 py-8 text-center text-sm text-gray-500 italic">
                                    {{ __('No se encontraron items con ese número de parte.') }}
                                </td>
                            </tr>
                        </template>

                        {{-- Filas con datos --}}
                        <template x-for="row in filteredProjection" :key="row.code">
                            <tr class="border-b border-gray-100 dark:border-gray-700/60 hover:bg-gray-50/60 dark:hover:bg-gray-900/20">
                                <td class="px-2 py-2 text-center align-middle">
                                    <div class="font-mono text-xs font-semibold text-gray-700 dark:text-gray-200" x-text="row.code"></div>
                                    <div class="text-[10px] text-gray-400">
                                        {{ __('inicial:') }} <span x-text="formatInt(row.start)"></span>
                                    </div>
                                </td>
                                <template x-for="(value, i) in row.byDay" :key="'pd-' + row.code + '-' + i">
                                    <td class="px-3 py-3 text-right border-l border-gray-100 dark:border-gray-700/60 text-sm tabular-nums"
                                        :class="(i === 0 ? value > row.start : value > row.byDay[i-1])
                                            ? 'font-bold text-green-700 dark:text-green-400 bg-green-50/40 dark:bg-green-900/10'
                                            : 'text-gray-500 dark:text-gray-400'"
                                        x-text="formatInt(value)"></td>
                                </template>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Contenedores Disponibles --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                    {{ __('Contenedores Disponibles') }}
                </h2>
                <span class="text-xs text-gray-500" x-show="excelData">
                    <span x-text="filteredAvailable.toLocaleString('en-US')"></span> / <span x-text="totalAvailable.toLocaleString('en-US')"></span> {{ __('sin asignar') }}
                </span>
            </div>

            {{-- Buscadores: contenedor + n° de parte --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3" x-show="excelData">
                <div class="relative">
                    <input
                        type="search"
                        x-model="containerSearch"
                        placeholder="{{ __('Buscar contenedor...') }}"
                        class="form-input w-full text-sm pl-9 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg"
                    />
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400 pointer-events-none">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <button x-show="containerSearch" @click="containerSearch = ''" type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="relative">
                    <input
                        type="search"
                        x-model="itemSearch"
                        placeholder="{{ __('Buscar n° de parte...') }}"
                        class="form-input w-full text-sm pl-9 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg"
                    />
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400 pointer-events-none">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <button x-show="itemSearch" @click="itemSearch = ''" type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Empty state cuando no hay Excel cargado --}}
            <div x-show="!excelData"
                 class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-dashed border-gray-300 dark:border-gray-700/60 p-12 text-center">
                <div class="flex flex-col items-center gap-3 text-gray-400 dark:text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-12 opacity-50">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('Aún no se ha cargado un documento de contenedores') }}
                    </p>
                    <p class="text-xs text-gray-400">
                        {{ __('Sube un archivo Excel desde el botón "Cargar Contenedores".') }}
                    </p>
                </div>
            </div>

            {{-- Sin resultados (búsqueda activa pero no hay match) --}}
            <div x-show="excelData && Object.keys(poolByDate).length === 0 && (containerSearch || itemSearch)"
                 class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 p-8 text-center text-sm text-gray-500 italic">
                {{ __('No se encontraron contenedores con esos criterios.') }}
            </div>

            {{-- Pool con datos --}}
            <div x-show="excelData" class="space-y-4 overflow-y-auto max-h-[500px] pr-2">
                <template x-for="(bucket, date) in poolByDate" :key="'g-' + date">
                    <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-gray-700 dark:text-gray-200 text-sm">
                                {{ __('Fecha:') }} <span x-text="bucket.label"></span>
                            </h3>
                            <span class="text-xs text-gray-500">
                                <span x-text="bucket.count"></span> {{ __('disponibles') }}
                            </span>
                        </div>

                        {{-- Grid con más columnas: las cards quedan del mismo tamaño que las del schedule --}}
                        <div class="kanban-pool grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 xl:grid-cols-6 gap-2 min-h-20"
                             :data-pool-date="date">
                            <template x-for="(price, code) in bucket.containers" :key="code">
                                <div class="kanban-card bg-violet-100 dark:bg-violet-900/40 border border-violet-400 text-violet-800 dark:text-violet-200 rounded px-2 py-2 text-center cursor-move hover:shadow-md transition-shadow"
                                     :data-container-code="code">
                                    <p class="font-bold text-xs truncate" x-text="code"></p>
                                    <p class="font-bold text-xs opacity-75 mt-0.5" x-text="formatMoney(price)"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ─────────────────── Estilos ─────────────────── --}}
    <style>
        .kanban-card, .kanban-slot, .kanban-pool {
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }
        .kanban-card button, .kanban-card a, .kanban-card input {
            user-select: auto; -webkit-user-select: auto;
        }
        .kanban-ghost { opacity: 0.3; }
        .kanban-chosen { cursor: grabbing !important; }
        .kanban-slot.sortable-over,
        .kanban-pool.sortable-over { background-color: rgba(139, 92, 246, 0.1); border-color: rgb(139, 92, 246); }
        .sortable-fallback { opacity: 0.9 !important; transform: rotate(2deg); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2); }

        /* Tabular nums para alinear números */
        .tabular-nums { font-variant-numeric: tabular-nums; }
    </style>
</div>

@assets
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
@endassets

@script
<script>
    Alpine.data('unitPlanBoard', () => ({
        // ─── State ───
        excelData: null,
        assignments: {},
        slotTimes: { 1: '', 2: '', 3: '', 4: '' },
        planName: '',
        days: @js(collect($days)->values()),
        toasts: [],
        toastSeq: 0,

        // ─── Búsquedas ───
        containerSearch: '',
        itemSearch: '',
        projectionSearch: '',

        init() {
            this.$wire.on('excel-loaded', (event) => {
                const payload = Array.isArray(event) ? event[0] : event;
                this.excelData = payload.data || payload;
                this.assignments = {};
                this.$nextTick(() => this.initSortables());
            });

            this.$wire.on('toast', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                this.showToast({
                    type:     data.type     || 'success',
                    title:    data.title    || '',
                    body:     data.body     || '',
                    details:  data.details  || [],
                    duration: data.type === 'error' || data.type === 'warning' ? 8000 : 5000,
                });
            });

            this.$watch('assignments', () => {
                this.$nextTick(() => this.initSortables());
            });
        },

        // ─── Helpers de formato contable ───
        formatMoney(value) {
            const n = parseFloat(value || 0);
            return '$' + n.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        },

        formatInt(value) {
            const n = Math.round(parseFloat(value || 0));
            return n.toLocaleString('en-US');
        },

        // ─── Computed ───
        get totalContainersInPool() {
            if (!this.excelData) return 0;
            let n = 0;
            for (const bucket of Object.values(this.excelData.pool)) {
                n += Object.keys(bucket.containers).length;
            }
            return n;
        },

        get totalAvailable() {
            return this.totalContainersInPool - Object.keys(this.assignments).length;
        },

        get poolByDate() {
            if (!this.excelData) return {};
            const result = {};
            const cTerm = this.containerSearch.trim().toLowerCase();
            const iTerm = this.itemSearch.trim().toLowerCase();
            const itemsByContainer = this.excelData.items_by_container;

            for (const [date, bucket] of Object.entries(this.excelData.pool)) {
                const available = {};
                for (const [code, price] of Object.entries(bucket.containers)) {
                    if (code in this.assignments) continue;

                    // Filtro por código de contenedor
                    if (cTerm && !code.toLowerCase().includes(cTerm)) continue;

                    // Filtro por número de parte (contenedor debe incluir ese item)
                    if (iTerm) {
                        const items = itemsByContainer[code] || {};
                        const match = Object.keys(items).some(it => it.toLowerCase().includes(iTerm));
                        if (!match) continue;
                    }

                    available[code] = price;
                }
                // Solo incluir fechas con resultados
                if (Object.keys(available).length > 0) {
                    result[date] = {
                        label: bucket.label,
                        count: Object.keys(available).length,
                        containers: available,
                    };
                }
            }
            return result;
        },

        get filteredAvailable() {
            let count = 0;
            for (const bucket of Object.values(this.poolByDate)) {
                count += bucket.count;
            }
            return count;
        },

        get filteredProjection() {
            const term = this.projectionSearch.trim().toLowerCase();
            if (!term) return this.projection;
            return this.projection.filter(row => row.code.toLowerCase().includes(term));
        },

        get projection() {
            if (!this.excelData || Object.keys(this.assignments).length === 0) return [];

            const itemsByContainer = this.excelData.items_by_container;
            const stockByCode      = this.excelData.stock_by_code;

            const perItemDay = {};
            for (const [code, pos] of Object.entries(this.assignments)) {
                const items = itemsByContainer[code] || {};
                for (const [itemCode, qty] of Object.entries(items)) {
                    if (!perItemDay[itemCode]) perItemDay[itemCode] = [0, 0, 0, 0, 0, 0];
                    perItemDay[itemCode][pos.day - 1] += qty;
                }
            }

            const rows = [];
            for (const [code, byDay] of Object.entries(perItemDay)) {
                const start = stockByCode[code] || 0;
                let running = start;
                const cumulative = [];
                for (let d = 0; d < 6; d++) {
                    running += byDay[d];
                    cumulative.push(running);
                }
                rows.push({ code, start, byDay: cumulative });
            }
            rows.sort((a, b) => a.code.localeCompare(b.code));
            return rows;
        },

        // ─── Helpers del tablero ───
        getCell(day, slot) {
            for (const [code, pos] of Object.entries(this.assignments)) {
                if (pos.day === day && pos.slot === slot) {
                    const price = this.lookupPrice(code);
                    if (price === null) continue;
                    return { code, price };
                }
            }
            return null;
        },

        getDayTotal(day) {
            let total = 0;
            for (const [code, pos] of Object.entries(this.assignments)) {
                if (pos.day === day) {
                    const price = this.lookupPrice(code);
                    if (price !== null) total += price;
                }
            }
            return total;
        },

        lookupPrice(code) {
            if (!this.excelData) return null;
            for (const bucket of Object.values(this.excelData.pool)) {
                if (code in bucket.containers) return bucket.containers[code];
            }
            return null;
        },

        hasSlotTimeConflict(slot) {
            const t = this.slotTimes[slot];
            if (!t) return false;
            for (const s of [1, 2, 3, 4]) {
                if (s !== slot && this.slotTimes[s] === t) return true;
            }
            return false;
        },

        // ─── Mutaciones ───
        clearAssignments() {
            this.assignments = {};
            // No tocar el pool ni recargar la vista: solo vaciar las asignaciones.
            // Los contenedores reaparecen en el pool gracias a la reactividad de Alpine.
        },

        assignContainer(code, day, slot) {
            const next = { ...this.assignments };
            for (const c in next) {
                if (c !== code && next[c].day === day && next[c].slot === slot) {
                    delete next[c];
                }
            }
            next[code] = { day, slot };
            this.assignments = next;
        },

        unassignContainer(code) {
            const next = { ...this.assignments };
            delete next[code];
            this.assignments = next;
        },

        // ─── Drag & drop ───
        initSortables() {
            document.querySelectorAll('.kanban-slot, .kanban-pool').forEach(el => {
                if (el._sortable) {
                    el._sortable.destroy();
                    el._sortable = null;
                }
            });

            const self = this;
            const opts = {
                group: 'containers',
                animation: 150,
                scroll: true,
                scrollSensitivity: 100,
                scrollSpeed: 25,
                bubbleScroll: true,
                forceAutoScrollFallback: true,
                forceFallback: true,
                fallbackOnBody: true,
                fallbackTolerance: 5,
                ghostClass: 'kanban-ghost',
                chosenClass: 'kanban-chosen',
                revertOnSpill: true, // si se suelta fuera, regresa a su origen
                onEnd(evt) {
                    const code = evt.item.dataset.containerCode;
                    const dest = evt.to;

                    // Siempre quitar el nodo movido por Sortable; Alpine pintará el correcto
                    evt.item.remove();

                    let stateChanged = false;

                    if (dest && dest.classList.contains('kanban-slot')) {
                        const day  = parseInt(dest.dataset.day, 10);
                        const slot = parseInt(dest.dataset.slot, 10);
                        self.assignContainer(code, day, slot);
                        stateChanged = true;
                    } else if (dest && dest.classList.contains('kanban-pool')) {
                        if (code in self.assignments) {
                            self.unassignContainer(code);
                            stateChanged = true;
                        }
                    }

                    // Si no hubo cambio real (drop fuera, mismo pool, etc.),
                    // forzar re-render para que la card vuelva a su lugar original
                    if (!stateChanged) {
                        self.assignments = { ...self.assignments };
                    }
                },
            };

            document.querySelectorAll('.kanban-slot').forEach(el => {
                el._sortable = new Sortable(el, opts);
            });
            document.querySelectorAll('.kanban-pool').forEach(el => {
                el._sortable = new Sortable(el, opts);
            });
        },

        // ─── Toasts ───
        showToast({ type = 'success', title = '', body = '', details = [], duration = 5000 }) {
            const id = ++this.toastSeq;
            this.toasts.push({ id, type, title, body, details });
            if (duration > 0) setTimeout(() => this.dismissToast(id), duration);
        },

        dismissToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        },

        // ─── Save ───
        async save() {
            for (const s of [1, 2, 3, 4]) {
                if (this.hasSlotTimeConflict(s)) {
                    this.showToast({ type: 'error', title: 'Horarios repetidos', body: 'Las 4 horas deben ser distintas.' });
                    return;
                }
            }
            if (Object.keys(this.assignments).length === 0) {
                this.showToast({ type: 'error', title: 'Sin contenedores', body: 'Asigna al menos un contenedor.' });
                return;
            }
            await this.$wire.savePlan(this.assignments, this.slotTimes, this.planName);
        },
    }));
</script>
@endscript
