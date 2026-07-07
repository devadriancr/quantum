<div x-data="unitPlanBoard" class="px-4 sm:px-6 lg:px-8 py-4 w-full max-w-9xl mx-auto">

    {{-- ─────────────────── Header ─────────────────── --}}
    <div class="sm:flex sm:justify-between sm:items-center mb-4">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                {{ __('Planeación de Unidades') }}
            </h1>
        </div>

        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <input type="file" wire:model="excelFile" x-ref="fileInput" accept=".xlsx,.xls,.xlsb,.xlsm,.csv" class="hidden" />

            {{-- Cargar Documento: verde outline + icono arrow-up-tray de Heroicons --}}
            <button
                type="button"
                @click="$refs.fileInput.click()"
                wire:loading.attr="disabled"
                class="btn border border-green-600 bg-green-100 text-green-700 hover:bg-green-200 disabled:opacity-50 dark:bg-green-900/30 dark:text-green-300 dark:border-green-500 dark:hover:bg-green-900/50 transition-colors"
            >
                {{-- Heroicons: arrow-up-tray (outline) --}}
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                </svg>
                {{ __('Cargar Documento') }}
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
                class="pointer-events-auto rounded-lg shadow-lg border px-4 py-3 flex items-start gap-3 cursor-default"
                :class="{
                    'bg-green-50 border-green-300 text-green-800 dark:bg-green-900/40 dark:border-green-700 dark:text-green-200': t.type === 'success',
                    'bg-red-50 border-red-300 text-red-800 dark:bg-red-900/40 dark:border-red-700 dark:text-red-200': t.type === 'error',
                    'bg-amber-50 border-amber-300 text-amber-800 dark:bg-amber-900/40 dark:border-amber-700 dark:text-amber-200': t.type === 'warning',
                }"
                @mouseenter="pauseToast(t.id)"
                @mouseleave="resumeToast(t.id)">
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

    {{-- ─────────────────── Modal: detalle de contenedor ─────────────────── --}}
    <div x-show="detailModal.open" x-cloak
         class="fixed inset-0 z-[60] flex items-center justify-center p-4"
         @keydown.escape.window="closeContainerItems()">
        {{-- Fondo --}}
        <div x-show="detailModal.open"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-gray-900/50"
             @click="closeContainerItems()"></div>

        {{-- Panel --}}
        <div x-show="detailModal.open"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-2xl max-h-[75vh] flex flex-col">

            <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white truncate" x-text="detailModal.code"></h3>
                <span class="shrink-0 text-lg font-bold text-gray-900 dark:text-white" x-text="formatMoney(lookupPrice(detailModal.code))"></span>
            </div>

            <div class="overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/60 sticky top-0">
                        <tr>
                            <th class="px-6 py-2.5 text-left">{{ __('N° de Parte') }}</th>
                            <th class="px-6 py-2.5 text-right">{{ __('Cantidad') }}</th>
                            <th class="px-6 py-2.5 text-right">{{ __('Precio') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in modalItems" :key="row.code">
                            <tr class="border-b border-gray-100 dark:border-gray-700/60 hover:bg-gray-50/60 dark:hover:bg-gray-900/20">
                                <td class="px-6 py-2 text-gray-900 dark:text-white" x-text="row.code"></td>
                                <td class="px-6 py-2 text-right tabular-nums font-semibold text-gray-900 dark:text-white" x-text="formatQty(row.qty)"></td>
                                <td class="px-6 py-2 text-right tabular-nums font-semibold text-gray-900 dark:text-white" x-text="formatMoney(row.price)"></td>
                            </tr>
                        </template>
                        <template x-if="modalItems.length === 0">
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500 italic">
                                    {{ __('Sin números de parte para este contenedor.') }}
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-700/60 flex justify-end">
                <button type="button" @click="closeContainerItems()"
                        class="btn-sm border border-gray-300 bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                    {{ __('Cerrar') }}
                </button>
            </div>
        </div>
    </div>

    {{-- ─────────────────── Tablero de horarios ─────────────────── --}}
    <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 overflow-x-auto">
        <table class="w-full table-fixed dark:text-gray-300">
            <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/60">
                <tr>
                    <th class="px-3 py-3 text-center text-[10px]">{{ __('Hora') }}</th>
                    <template x-for="day in days" :key="day.value">
                        <th class="px-3 py-3 text-center border-l border-gray-100 dark:border-gray-700/60">
                            <div x-text="day.label"></div>
                            <div class="text-[10px] font-normal normal-case text-gray-400 mt-0.5" x-text="dayDates[day.value]"></div>
                        </th>
                    </template>
                    <th class="w-14 border-l border-gray-100 dark:border-gray-700/60"></th>
                </tr>
            </thead>
            <tbody class="text-sm">
                <template x-for="(slotIdx, idx) in slots" :key="slotIdx">
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
                                <div class="kanban-slot min-h-12 rounded-md border-2 border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/30 p-1 flex items-center justify-center"
                                     :data-day="day.value"
                                     :data-slot="slotIdx">
                                    <template x-if="getCell(day.value, slotIdx)">
                                        <div class="kanban-card relative w-full bg-violet-100 dark:bg-violet-900/40 border border-violet-400 text-violet-800 dark:text-violet-200 rounded px-2 py-2 text-center cursor-move shadow-sm"
                                             :data-container-code="getCell(day.value, slotIdx).code">
                                            <button type="button"
                                                    class="no-drag absolute top-1 left-1 p-0.5 rounded text-red-500 hover:text-red-700 hover:bg-red-100 dark:text-red-300 dark:hover:bg-red-900/50"
                                                    title="{{ __('Quitar contenedor') }}"
                                                    @click.stop="unassignContainer(getCell(day.value, slotIdx).code)">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                            <button type="button"
                                                    class="no-drag absolute top-1 right-1 p-0.5 rounded text-violet-500 hover:text-violet-800 hover:bg-violet-200/70 dark:text-violet-300 dark:hover:bg-violet-700/60"
                                                    title="{{ __('Ver números de parte') }}"
                                                    @click.stop="showContainerItems(getCell(day.value, slotIdx).code)">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                            </button>
                                            <p class="font-bold text-xs truncate px-4" x-text="getCell(day.value, slotIdx).code"></p>
                                            <p class="font-bold text-xs opacity-75 mt-0.5" x-text="formatMoney(getCell(day.value, slotIdx).price)"></p>
                                        </div>
                                    </template>
                                </div>
                            </td>
                        </template>
                        <td class="border-l border-gray-100 dark:border-gray-700/60 align-middle p-1">
                            <div x-show="idx === slots.length - 1" class="flex items-center justify-center gap-1">
                                <button type="button" @click="addSlot()"
                                        title="{{ __('Agregar línea') }}"
                                        class="p-1 rounded text-violet-600 hover:text-violet-800 hover:bg-violet-100 dark:text-violet-300 dark:hover:bg-violet-900/40">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </button>
                                <button type="button" x-show="slots.length > 4" @click="removeSlot(slotIdx)"
                                        title="{{ __('Eliminar línea') }}"
                                        class="p-1 rounded text-red-500 hover:text-red-700 hover:bg-red-100 dark:text-red-300 dark:hover:bg-red-900/40">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>

                {{-- Fila de totales --}}
                <tr class="bg-gray-50 dark:bg-gray-900/30">
                    <td class="px-2 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 text-center">$</td>
                    <template x-for="day in days" :key="'total-' + day.value">
                        <td class="px-2 py-3 text-center border-l border-gray-100 dark:border-gray-700/60 font-bold text-green-700 dark:text-green-400 text-sm"
                            x-text="formatMoney(getDayTotal(day.value))"></td>
                    </template>
                    <td class="border-l border-gray-100 dark:border-gray-700/60"></td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- ─────────────────── Inventario + Pool ─────────────────── --}}
    <div class="mt-5 grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Proyección de Inventario (siempre visible con headers + empty state) --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                    {{ __('Proyección de Inventario') }}
                </h2>
                <span class="text-xs text-gray-500" x-show="itemsProjectionBase.length > 0">
                    <span x-text="filteredProjectionBase.length"></span> / <span x-text="itemsProjectionBase.length"></span> {{ __('item(s)') }}
                </span>
            </div>

            {{-- Buscador por número de parte --}}
            <div class="relative mb-3" x-show="itemsProjectionBase.length > 0">
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

            <div x-show="itemsProjectionBase.length === 0"
                 class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-dashed border-gray-300 dark:border-gray-700/60 p-12 text-center">
                <div class="flex flex-col items-center gap-3 text-gray-400 dark:text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-12 opacity-50">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                    </svg>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Sin datos para mostrar') }}</p>
                    <p class="text-xs text-gray-400">{{ __('Arrastra un contenedor al tablero para ver la proyección.') }}</p>
                </div>
            </div>

            {{-- Cada columna (día) tiene su propio ranking ascendente: el item
                 más crítico (más negativo / menor stock) de ESE día queda
                 arriba, sin importar en qué posición esté en los otros días. --}}
            <div x-show="itemsProjectionBase.length > 0" class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 overflow-auto max-h-[500px]">
                <table class="w-full dark:text-gray-300">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 border-b-2 border-gray-300 dark:border-gray-600 sticky top-0 z-10">
                        <tr>
                            <th class="w-7 px-1 py-3 text-center text-xs bg-gray-50 dark:bg-gray-900/20">{{ __('#') }}</th>
                            <template x-for="(day, dayIdx) in days" :key="'ph-' + day.value">
                                <th class="px-3 py-3 text-center border-l-2 border-gray-300 dark:border-gray-600 min-w-[130px]"
                                    :class="dayIdx % 2 === 0 ? 'bg-slate-50 dark:bg-slate-800/50' : 'bg-gray-50 dark:bg-gray-900/20'">
                                    <div x-text="day.label"></div>
                                    <div class="text-[10px] font-normal normal-case text-gray-400 mt-0.5" x-text="dayDates[day.value]"></div>
                                </th>
                            </template>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        {{-- Sin coincidencias en búsqueda --}}
                        <template x-if="itemsProjectionBase.length > 0 && projectionRows.length === 0">
                            <tr>
                                <td :colspan="days.length + 1" class="px-3 py-8 text-center text-sm text-gray-500 italic">
                                    {{ __('No se encontraron items con ese número de parte.') }}
                                </td>
                            </tr>
                        </template>

                        {{-- Filas con datos: cada fila es una posición de ranking,
                             no un item fijo. --}}
                        <template x-for="(row, rowIdx) in projectionRows" :key="'prow-' + rowIdx">
                            <tr class="border-b-2 border-gray-300 dark:border-gray-600 hover:brightness-95 dark:hover:brightness-110">
                                <td class="px-1 py-2 text-center align-middle text-xs font-semibold text-gray-500 dark:text-gray-400" x-text="rowIdx + 1"></td>
                                <template x-for="(cell, dIdx) in row" :key="'pd-' + rowIdx + '-' + dIdx">
                                    <td class="px-2 py-3 text-center border-l-2 border-gray-300 dark:border-gray-600 align-top"
                                        :class="cellBgClass(cell.value, cell.min, cell.max, dIdx)">

                                        {{-- N° Parte + indicador de estado --}}
                                        <div class="flex items-center justify-center gap-1 mb-1.5">
                                            <span class="shrink-0 w-1.5 h-1.5 rounded-full"
                                                  :class="{
                                                      'bg-red-500':     cell.min != null && cell.value <= cell.min,
                                                      'bg-yellow-400':  cell.min != null && cell.max != null && cell.value > cell.min && cell.value >= cell.max,
                                                      'bg-emerald-500': cell.min != null && cell.max != null && cell.value > cell.min && cell.value < cell.max,
                                                      'bg-gray-300 dark:bg-gray-600': cell.min == null
                                                  }"></span>
                                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300 truncate leading-none"
                                                  x-text="cell.code"></span>
                                        </div>

                                        {{-- Valor de consumo puro (stock - d×daily) --}}
                                        <div class="text-sm font-black tabular-nums leading-none"
                                             :class="cellValueClass(cell.value, cell.min, cell.max)"
                                             x-text="formatInt(cell.value)"></div>

                                        {{-- INV / PICS con separador --}}
                                        <div class="mt-2 pt-1.5 border-t border-gray-300 dark:border-gray-500 space-y-0.5">
                                            <div class="flex items-center justify-between gap-1">
                                                <span class="text-[10px] font-semibold uppercase text-gray-400 dark:text-gray-500">INV</span>
                                                <span class="text-xs font-semibold tabular-nums text-gray-800 dark:text-gray-200"
                                                      x-text="formatInt(cell.inv)"></span>
                                            </div>
                                            <div x-show="cell.hasPics"
                                                 class="flex items-center justify-between gap-1">
                                                <span class="text-[10px] font-semibold uppercase text-gray-400 dark:text-gray-500">PICS</span>
                                                <span class="text-xs font-semibold tabular-nums text-gray-800 dark:text-gray-200"
                                                      x-text="formatInt(cell.pics)"></span>
                                            </div>
                                        </div>

                                    </td>
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
                        <div class="relative flex items-center justify-center mb-3">
                            <h3 class="font-semibold text-gray-700 dark:text-gray-200 text-sm text-center" x-text="bucket.label"></h3>
                            <span class="absolute right-0 text-xs text-gray-500">
                                <span x-text="bucket.count"></span> {{ __('disponibles') }}
                            </span>
                        </div>

                        <div class="kanban-pool grid content-start grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 min-h-20"
                             :data-pool-date="date">
                            <template x-for="code in Object.keys(bucket.containers)" :key="code">
                                <div class="kanban-card relative flex flex-col justify-center bg-violet-100 dark:bg-violet-900/40 border border-violet-400 text-violet-800 dark:text-violet-200 rounded-lg px-3 py-2 text-center cursor-move hover:shadow-md transition-shadow"
                                     :data-container-code="code">
                                    <button type="button"
                                            class="no-drag absolute top-1 right-1 p-0.5 rounded text-violet-500 hover:text-violet-800 hover:bg-violet-200/70 dark:text-violet-300 dark:hover:bg-violet-700/60"
                                            title="{{ __('Ver números de parte') }}"
                                            @click.stop="showContainerItems(code)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </button>
                                    <p class="font-bold text-xs truncate pr-5" x-text="code"></p>
                                    <p class="font-bold text-xs opacity-75 mt-0.5" x-text="formatMoney(bucket.containers[code])"></p>
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
        [x-cloak] { display: none !important; }
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
        slots: [1, 2, 3, 4],
        slotTimes: { 1: '02:00', 2: '08:30', 3: '14:30', 4: '21:00' },
        planName: '',
        days: @js(collect($days)->values()),
        dayDates: @js($dayDates),
        toasts: [],
        toastSeq: 0,
        toastTimers: {},

        // ─── Búsquedas ───
        containerSearch: '',
        itemSearch: '',
        projectionSearch: '',

        // ─── Modal de detalle de contenedor ───
        detailModal: { open: false, code: null },

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
                    duration: data.type === 'error' || data.type === 'warning' ? 20000 : 12000,
                });
            });

            // Tras guardar con éxito: dejar que la descarga del Excel arranque y
            // el toast de éxito sea visible un momento, luego recargar la página
            // para iniciar una nueva planeación desde cero.
            this.$wire.on('plan-saved', () => {
                setTimeout(() => window.location.reload(), 1500);
            });

            this.$watch('assignments', () => {
                this.$nextTick(() => this.initSortables());
            });

            this.$watch('slots', () => {
                this.$nextTick(() => this.initSortables());
            });

            this.$watch('containerSearch', () => {
                this.$nextTick(() => this.initSortables());
            });
            this.$watch('itemSearch', () => {
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

        formatQty(value) {
            const n = parseFloat(value || 0);
            return n.toLocaleString('en-US', { maximumFractionDigits: 2 });
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

        // Datos base por item, sin ordenar.
        // Cada entrada incluye: byDay (acumulado con recibos y consumo),
        // invByDay (inventario inicial descontando consumo día a día, sin recibos),
        // picsByDay (cantidad PICS descontando consumo día a día).
        // invByDay y picsByDay no descuentan consumo en el día 1 (d=0); el
        // descuento empieza a partir del día 2 (d≥1, restando d*daily).
        get itemsProjectionBase() {
            if (!this.excelData || Object.keys(this.assignments).length === 0) return [];

            const itemsByContainer = this.excelData.items_by_container;
            const stockByCode      = this.excelData.stock_by_code;
            const limitsByCode     = this.excelData.limits_by_code || {};
            const picsByCode       = this.excelData.pics_by_code   || {};

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
                const start   = stockByCode[code] || 0;
                const limits  = limitsByCode[code] || { min: null, max: null, daily: 0 };
                const daily   = limits.daily || 0;
                const picsQty = picsByCode[code]   || 0;

                const cumulative = [];
                const invByDay   = [];
                const picsByDay  = [];
                let containers = 0;

                for (let d = 0; d < 6; d++) {
                    // Acumular piezas de contenedores asignados para este día.
                    containers += byDay[d];

                    // INV: stock al inicio del día, incluyendo contenedores ya recibidos.
                    // Sin contenedores: día 1 = start (sin descuento), día 2 = start - daily, etc.
                    invByDay.push(start + containers - d * daily);

                    // Valor principal: stock al final del día = INV menos un consumo diario.
                    cumulative.push(start + containers - (d + 1) * daily);

                    // PICS: no se afecta por contenedores, solo baja por consumo.
                    picsByDay.push(picsQty - d * daily);
                }

                rows.push({
                    code,
                    start,
                    byDay: cumulative,
                    invByDay,
                    picsByDay,
                    picsQty,
                    min: limits.min,
                    max: limits.max,
                });
            }
            return rows;
        },

        get filteredProjectionBase() {
            const term = this.projectionSearch.trim().toLowerCase();
            if (!term) return this.itemsProjectionBase;
            return this.itemsProjectionBase.filter(row => row.code.toLowerCase().includes(term));
        },

        // Matriz fila x día para la tabla: cada columna (día) se ordena de
        // forma independiente, de menor a mayor (lo más negativo/crítico
        // primero). El item que cae en una posición puede variar de un día a
        // otro, por eso cada celda lleva su propio código de item.
        // Cada celda incluye value (consumo), inv (INV del día) y pics (PICS del día).
        get projectionRows() {
            const base = this.filteredProjectionBase;
            if (base.length === 0) return [];

            const columns = [];
            for (let d = 0; d < 6; d++) {
                const list = base.map(row => ({
                    code:    row.code,
                    value:   row.byDay[d],
                    inv:     row.invByDay  ? row.invByDay[d]  : 0,
                    pics:    row.picsByDay ? row.picsByDay[d] : 0,
                    hasPics: (row.picsQty || 0) > 0,
                    min:     row.min,
                    max:     row.max,
                }));
                list.sort((a, b) => a.value - b.value);
                columns.push(list);
            }

            const rows = [];
            for (let i = 0; i < base.length; i++) {
                rows.push(columns.map(col => col[i]));
            }
            return rows;
        },

        // Fondo de la celda: color tenue de estado cuando hay límites configurados;
        // fondo alterno de columna (slate/white) cuando no hay límites.
        cellBgClass(value, min, max, dIdx) {
            if (min == null || max == null) {
                return (dIdx ?? 0) % 2 === 0
                    ? 'bg-slate-50 dark:bg-slate-800/30'
                    : 'bg-white dark:bg-gray-800';
            }
            if (value <= min) return 'bg-red-50 dark:bg-red-900/25';
            if (value >= max) return 'bg-amber-50 dark:bg-amber-900/25';
            return 'bg-emerald-50 dark:bg-emerald-900/20';
        },

        // Color del valor de consumo: hereda el color temático de la celda.
        cellValueClass(value, min, max) {
            if (min == null || max == null) return 'text-gray-700 dark:text-gray-200';
            if (value <= min) return 'text-red-600 dark:text-red-400';
            if (value >= max) return 'text-yellow-600 dark:text-yellow-500';
            return 'text-green-700 dark:text-green-400';
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
            for (const s of this.slots) {
                if (s !== slot && this.slotTimes[s] === t) return true;
            }
            return false;
        },

        // ─── Mutaciones ───
        addSlot() {
            const next = this.slots.length ? Math.max(...this.slots) + 1 : 1;
            this.slots.push(next);
            this.slotTimes[next] = '';
        },

        // Solo aplica a líneas agregadas (más allá de las 4 fijas). Libera
        // también los contenedores que estuvieran asignados a esa fila.
        removeSlot(slot) {
            this.slots = this.slots.filter(s => s !== slot);
            delete this.slotTimes[slot];

            const next = { ...this.assignments };
            for (const c in next) {
                if (next[c].slot === slot) delete next[c];
            }
            this.assignments = next;
        },

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

        // ─── Modal: números de parte de un contenedor ───
        get modalItems() {
            if (!this.excelData || !this.detailModal.code) return [];
            const items = this.excelData.items_by_container[this.detailModal.code] || {};
            const costs = this.excelData.unit_cost_by_code || {};
            return Object.entries(items)
                .map(([code, qty]) => ({ code, qty, price: qty * (costs[code] || 0) }))
                .sort((a, b) => a.code.localeCompare(b.code));
        },

        showContainerItems(code) {
            this.detailModal = { open: true, code };
        },

        closeContainerItems() {
            this.detailModal = { open: false, code: null };
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
                filter: '.no-drag',  // botones dentro de la card no inician arrastre
                preventOnFilter: false,

                // Recordamos de dónde salió la card para poder devolverla a mano.
                // Alpine es la única fuente de verdad del DOM; Sortable solo es el
                // mecanismo de arrastre.
                onStart(evt) {
                    evt.item._homeParent = evt.from;
                    evt.item._homeNext   = evt.item.nextElementSibling;

                    // forceFallback:true clona el nodo DOM y lo agrega a <body>
                    // ANTES de disparar onStart (síncronamente). Alpine detecta
                    // el clon vía MutationObserver (microtask), que corre DESPUÉS
                    // de que el stack síncrono vacíe. Marcando x-ignore aquí —
                    // síncronamente — el MO verá el atributo y no intentará
                    // evaluar x-text="code" fuera del x-for donde 'code' no existe.
                    const ghost = document.querySelector('body > .sortable-drag');
                    if (ghost) {
                        ghost.setAttribute('x-ignore', '');
                        ghost.removeAttribute('data-has-alpine-state');
                    }
                },

                onEnd(evt) {
                    const code = evt.item.dataset.containerCode;
                    const dest = evt.to;

                    if (dest && dest.classList.contains('kanban-slot')) {
                        // Drop válido en una celda: quitamos el nodo que movió
                        // Sortable y dejamos que Alpine pinte la card en la celda.
                        evt.item.remove();
                        const day  = parseInt(dest.dataset.day, 10);
                        const slot = parseInt(dest.dataset.slot, 10);
                        self.assignContainer(code, day, slot);
                    } else if (dest && dest.classList.contains('kanban-pool') && (code in self.assignments)) {
                        // Regresó al pool una card que estaba asignada: desasignar.
                        evt.item.remove();
                        self.unassignContainer(code);
                    } else {
                        // Drop SIN cambio de estado: fuera de la tabla, en un hueco
                        // entre celdas, o de vuelta al mismo pool sin asignar. Sortable
                        // ya movió/sacó el nodo, pero Alpine NO recrea nodos con la
                        // misma key, así que la card "desaparece". Lo arreglamos
                        // devolviendo el nodo a su posición original a mano.
                        const home = evt.item._homeParent;
                        if (home) {
                            home.insertBefore(evt.item, evt.item._homeNext || null);
                        }
                    }

                    evt.item._homeParent = null;
                    evt.item._homeNext   = null;
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
        showToast({ type = 'success', title = '', body = '', details = [], duration = 12000 }) {
            const id = ++this.toastSeq;
            this.toasts.push({ id, type, title, body, details });
            if (duration > 0) {
                this.toastTimers[id] = {
                    remaining: duration,
                    startedAt: Date.now(),
                    handle: setTimeout(() => this.dismissToast(id), duration),
                };
            }
        },

        pauseToast(id) {
            const t = this.toastTimers[id];
            if (!t || t.handle === null) return;
            clearTimeout(t.handle);
            t.handle    = null;
            t.remaining = Math.max(0, t.remaining - (Date.now() - t.startedAt));
        },

        resumeToast(id) {
            const t = this.toastTimers[id];
            if (!t || t.handle !== null) return;
            t.startedAt = Date.now();
            t.handle    = setTimeout(() => this.dismissToast(id), t.remaining);
        },

        dismissToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
            delete this.toastTimers[id];
        },

        // ─── Save ───
        async save() {
            for (const s of this.slots) {
                if (this.hasSlotTimeConflict(s)) {
                    this.showToast({ type: 'error', title: 'Horarios repetidos', body: 'Los horarios deben ser distintos.' });
                    return;
                }
            }
            if (Object.keys(this.assignments).length === 0) {
                this.showToast({ type: 'error', title: 'Sin contenedores', body: 'Asigna al menos un contenedor.' });
                return;
            }
            // Cada fila con contenedores debe tener una hora: con ese horario se
            // registra el contenedor.
            const usedSlots = new Set(Object.values(this.assignments).map(p => p.slot));
            for (const slot of usedSlots) {
                if (!this.slotTimes[slot]) {
                    this.showToast({ type: 'error', title: 'Horario faltante', body: 'Cada fila con contenedores debe tener una hora asignada.' });
                    return;
                }
            }
            await this.$wire.savePlan(this.assignments, this.slotTimes, this.planName);
        },
    }));
</script>
@endscript
