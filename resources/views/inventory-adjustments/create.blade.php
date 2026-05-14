<x-app-layout>
    <style>
        /* ── Select2 base ─────────────────────────────────────────────── */
        .select2-container { width: 100% !important; max-width: 100% !important; }

        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            background-color: #fff !important;
        }
        .dark .select2-container .select2-selection--single {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 12px;
            color: #374151;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #d1d5db !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }

        /* ── Dropdown: evitar que la página crezca ────────────────────── */
        .select2-dropdown {
            border: 1px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 8px 16px -4px rgba(0,0,0,.12);
            z-index: 9999;
        }
        .dark .select2-dropdown {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }
        .select2-results__options {
            max-height: 220px;
            overflow-y: auto;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #7c3aed !important;
        }
        .dark .select2-results__option {
            color: #d1d5db;
        }
        .dark .select2-search--dropdown .select2-search__field {
            background-color: #111827;
            border-color: #374151;
            color: #d1d5db;
        }
    </style>

    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Encabezado --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    {{ __('Nuevo Ajuste de Inventario') }}
                </h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('inventory-adjustments.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                    </svg>
                    Regresar
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="adjustment-form" action="{{ route('inventory-adjustments.store') }}" method="POST">
            @csrf

            {{-- Fecha: oculta, siempre hoy --}}
            <input type="hidden" name="adjustment_date" value="{{ today()->toDateString() }}">

            {{-- Información General --}}
            <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 p-6 mb-6">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-5">
                    {{ __('Información General') }}
                </h2>

                <div class="grid grid-cols-1 gap-4">

                    {{-- Tipo — renglon completo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Tipo de ajuste <span class="text-red-500">*</span>
                        </label>
                        <select id="adjustment_type" name="adjustment_type"
                                class="w-full @error('adjustment_type') border-red-300 @enderror">
                            <option value="">— Seleccionar tipo —</option>
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}" {{ old('adjustment_type') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('adjustment_type')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Motivo + Notas en el mismo renglón --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Motivo <span class="text-red-500">*</span>
                            </label>
                            <textarea name="reason" rows="3"
                                      class="form-textarea w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg @error('reason') border-red-300 @enderror"
                                      placeholder="Describe el motivo del ajuste...">{{ old('reason') }}</textarea>
                            @error('reason')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Notas adicionales
                            </label>
                            <textarea name="notes" rows="3"
                                      class="form-textarea w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:border-violet-300 rounded-lg"
                                      placeholder="Observaciones opcionales...">{{ old('notes') }}</textarea>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Líneas de ajuste --}}
            <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 mb-6">
                <div class="px-5 pt-5 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                            {{ __('Líneas de Ajuste') }}
                        </h2>
                        <button type="button" id="add-line"
                                class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Agregar línea
                        </button>
                    </div>

                    @error('lines')
                        <p class="mb-3 text-sm text-red-500">{{ $message }}</p>
                    @enderror

                    <div class="overflow-x-auto">
                        <table class="table-auto w-full text-sm" id="lines-table">
                            <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-t border-b border-gray-100 dark:border-gray-700/60">
                                <tr>
                                    <th class="px-3 py-2 text-left w-10">#</th>
                                    <th class="px-3 py-2 text-left min-w-56">N° Parte</th>
                                    <th class="px-3 py-2 text-left min-w-48">Ubicación</th>
                                    <th class="px-3 py-2 text-right w-36">Cant. Sistema</th>
                                    <th class="px-3 py-2 text-right w-36">Ajuste (±)</th>
                                    <th class="px-3 py-2 text-right w-36">Cant. Final</th>
                                    <th class="px-3 py-2 text-left min-w-40">Notas</th>
                                    <th class="px-3 py-2 w-10"></th>
                                </tr>
                            </thead>
                            <tbody id="lines-body" class="divide-y divide-gray-100 dark:divide-gray-700/60">
                            </tbody>
                        </table>
                    </div>

                    <p id="no-lines-msg" class="py-8 text-center text-gray-400 dark:text-gray-500 text-sm">
                        No hay líneas aún. Haz clic en "Agregar línea" para comenzar.
                    </p>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('inventory-adjustments.index') }}"
                   class="btn border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    {{ __('Cancelar') }}
                </a>
                <button type="submit"
                        class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                    {{ __('Aplicar Ajuste') }}
                </button>
            </div>
        </form>
    </div>

    {{-- Datos para JS --}}
    @php
        $itemsForJs     = $items->map(fn($i) => ['id' => $i->id, 'code' => $i->code, 'description' => $i->description]);
        $locationsForJs = $locations->map(fn($l) => ['id' => $l->id, 'code' => $l->code, 'name' => $l->name, 'warehouse' => $l->warehouse?->name]);
    @endphp
    <script>
        const ITEMS        = @json($itemsForJs);
        const LOCATIONS    = @json($locationsForJs);
        const BALANCES_URL = '{{ url("/api/inventory-balance") }}';
    </script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        let lineIndex = 0;

        const select2Config = {
            width: '100%',
            dropdownAutoWidth: false,
            language: {
                noResults:  () => 'Sin resultados',
                searching:  () => 'Buscando...',
            },
        };

        // ── Select2 para el tipo de ajuste ──────────────────────────────
        $(document).ready(function () {
            $('#adjustment_type').select2({
                ...select2Config,
                placeholder: '— Seleccionar tipo —',
                minimumResultsForSearch: Infinity, // oculta el buscador (pocos items)
            });

            toggleNoLinesMsg();
            $('#lines-table').hide();

            $('#add-line').on('click', () => addLine());
        });

        // ── Opciones dinámicas para líneas ──────────────────────────────
        function buildItemOptions(selectedId) {
            return ITEMS.map(i =>
                `<option value="${i.id}" ${selectedId == i.id ? 'selected' : ''}>[${i.code}] ${i.description}</option>`
            ).join('');
        }

        function buildLocationOptions(selectedId) {
            return LOCATIONS.map(l =>
                `<option value="${l.id}" ${selectedId == l.id ? 'selected' : ''}>[${l.code}] ${l.name}${l.warehouse ? ' — ' + l.warehouse : ''}</option>`
            ).join('');
        }

        // ── Cant. Final = sistema + ajuste ──────────────────────────────
        function updateFinal(row) {
            const sys   = parseFloat(row.find('.system-qty').text()) || 0;
            const delta = parseFloat(row.find('.adjusted-qty-input').val()) || 0;
            const final = sys + delta;
            const cell  = row.find('.variance-cell');
            cell.text(final.toFixed(2));
            cell.removeClass('text-green-600 text-red-600 text-gray-500 dark:text-gray-400');
            if      (final > 0) cell.addClass('text-green-600');
            else if (final < 0) cell.addClass('text-red-600');
            else                cell.addClass('text-gray-500 dark:text-gray-400');
        }

        // ── Consultar cantidad actual en el sistema ──────────────────────
        function fetchSystemQty(row, itemId, locationId) {
            const sysCell = row.find('.system-qty');
            if (!itemId || !locationId) { sysCell.text('—'); return; }

            $.get(`/api/inventory-balance?item_id=${itemId}&location_id=${locationId}`, function (data) {
                sysCell.text(parseFloat(data.current_quantity ?? 0).toFixed(2));
                updateFinal(row);
            }).fail(function () {
                sysCell.text('0.00');
                updateFinal(row);
            });
        }

        // ── Agregar línea ────────────────────────────────────────────────
        function addLine(itemId = '', locationId = '') {
            const idx = lineIndex++;
            const row = $(`
                <tr class="line-row" data-idx="${idx}">
                    <td class="px-3 py-2 text-gray-500 dark:text-gray-400 text-xs">${idx + 1}</td>
                    <td class="px-3 py-2">
                        <select name="lines[${idx}][item_id]" class="item-select w-full">
                            <option value="">Seleccionar...</option>
                            ${buildItemOptions(itemId)}
                        </select>
                    </td>
                    <td class="px-3 py-2">
                        <select name="lines[${idx}][location_id]" class="location-select w-full">
                            <option value="">Seleccionar...</option>
                            ${buildLocationOptions(locationId)}
                        </select>
                    </td>
                    <td class="px-3 py-2 text-right">
                        <span class="system-qty font-mono text-xs text-gray-600 dark:text-gray-400">—</span>
                    </td>
                    <td class="px-3 py-2">
                        <input type="number" name="lines[${idx}][adjusted_quantity]" step="0.01"
                               class="adjusted-qty-input form-input w-full text-right"
                               placeholder="0.00" />
                    </td>
                    <td class="px-3 py-2 text-right">
                        <span class="variance-cell font-mono font-semibold text-xs text-gray-400">—</span>
                    </td>
                    <td class="px-3 py-2">
                        <input type="text" name="lines[${idx}][notes]"
                               class="form-input w-full text-xs"
                               placeholder="Observación..." />
                    </td>
                    <td class="px-3 py-2 text-center">
                        <button type="button" class="remove-line text-red-400 hover:text-red-600 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                    </td>
                </tr>
            `);

            $('#lines-body').append(row);

            row.find('.item-select').select2({ ...select2Config, placeholder: 'Buscar número de parte...' });
            row.find('.location-select').select2({ ...select2Config, placeholder: 'Buscar ubicación...' });

            row.find('.item-select, .location-select').on('change', function () {
                const itemId     = row.find('.item-select').val();
                const locationId = row.find('.location-select').val();
                fetchSystemQty(row, itemId, locationId);
            });

            row.find('.adjusted-qty-input').on('input', function () {
                updateFinal(row);
            });

            row.find('.remove-line').on('click', function () {
                row.remove();
                toggleNoLinesMsg();
                renumberLines();
            });

            toggleNoLinesMsg();

            if (itemId && locationId) {
                fetchSystemQty(row, itemId, locationId);
            }
        }

        function toggleNoLinesMsg() {
            const count = $('#lines-body tr').length;
            $('#no-lines-msg').toggle(count === 0);
            $('#lines-table').toggle(count > 0);
        }

        function renumberLines() {
            $('#lines-body tr').each(function (i) {
                $(this).find('td:first').text(i + 1);
            });
        }
    </script>
</x-app-layout>
