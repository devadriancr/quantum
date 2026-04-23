<x-app-layout>
    <div class="px-2 sm:px-6 lg:px-8 py-4 w-full max-w-9xl mx-auto">

        {{-- Encabezado --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    Recepción de Material
                </h1>
            </div>
            <div class="flex gap-2 mt-4 sm:mt-0">
                <button id="btn-complete"
                    class="btn bg-green-600 hover:bg-green-700 text-white inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    Completar Recepción
                </button>
                <a href="{{ route('reception.index') }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 text-gray-600 dark:text-gray-300">
                    &larr; Regresar
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">

            {{-- Coincidentes --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 p-4 flex items-center gap-4">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-green-100 dark:bg-green-500/20 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-green-600 dark:text-green-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-400 dark:text-gray-500">Coincidentes</p>
                    <p id="stat-matched" class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['matched'] }}</p>
                </div>
            </div>

            {{-- Sin coincidencia --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 p-4 flex items-center gap-4">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-yellow-100 dark:bg-yellow-500/20 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-yellow-600 dark:text-yellow-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-400 dark:text-gray-500">Sin coincidencia</p>
                    <p id="stat-unmatched" class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['unmatched'] }}</p>
                </div>
            </div>

            {{-- Duplicados --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 p-4 flex items-center gap-4">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-500/20 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-orange-600 dark:text-orange-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-400 dark:text-gray-500">Duplicados</p>
                    <p id="stat-duplicate" class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['duplicate'] }}</p>
                </div>
            </div>

            {{-- Errores --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 p-4 flex items-center gap-4">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-red-100 dark:bg-red-500/20 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-red-600 dark:text-red-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-400 dark:text-gray-500">Errores</p>
                    <p id="stat-error" class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['error'] }}</p>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- ── Panel de escaneo ── --}}
            <div class="xl:col-span-2 space-y-4">

                {{-- Input de escaneo --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 p-5">
                    <label class="block text-sm font-semibold text-gray-500 capitalize dark:text-gray-400 mb-2">
                        Escanear Etiqueta
                    </label>
                    <div class="relative">
                        <input
                            id="scan-input"
                            type="text"
                            autofocus
                            autocomplete="off"
                            placeholder="Escanea aquí..."
                            class="form-input w-full text-sm pr-10 border-2 border-violet-300 dark:border-violet-600 focus:border-violet-500 focus:ring-violet-500 rounded-lg"
                        />
                        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-violet-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z"/>
                            </svg>
                        </div>
                    </div>
                    <div id="scan-result" class="hidden mt-3 px-3 py-2 rounded-lg text-sm"></div>
                </div>

                {{-- Tabla de escaneos recientes --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 overflow-hidden">
                    <header class="px-4 py-3 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100 text-sm capitalize">Historial de escaneos</h3>
                        <span id="total-count" class="text-sm font-bold text-violet-600 dark:text-violet-400 bg-violet-50 dark:bg-violet-500/10 px-2 py-0.5 rounded">
                            {{ $stats['total'] }} Escaneo(s)
                        </span>
                    </header>
                    <div class="overflow-x-auto max-h-80 overflow-y-auto">
                        <table class="table-auto w-full">
                            <thead class="text-xs font-semibold text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/60 sticky top-0">
                                <tr>
                                    <th class="px-3 py-2 text-left">Tipo</th>
                                    <th class="px-3 py-2 text-left">Artículo</th>
                                    <th class="px-3 py-2 text-left">Serial</th>
                                    <th class="px-3 py-2 text-right">Cant.</th>
                                    <th class="px-3 py-2 text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody id="scans-table" class="divide-y divide-gray-100 dark:divide-gray-700/60">
                                @forelse($scans as $scan)
                                    @php
                                        $scanColors = [
                                            'MATCHED'   => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                                            'UNMATCHED' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400',
                                            'DUPLICATE' => 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400',
                                            'ERROR'     => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                                        ];
                                        $scanLabels = ['MATCHED'=>'OK','UNMATCHED'=>'Sin doc.','DUPLICATE'=>'Duplicado','ERROR'=>'Error'];
                                    @endphp
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10">
                                        <td class="px-3 py-2">
                                            <span class="text-xs font-bold px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                                {{ $scan->consignment_type }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2">
                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $scan->parsed_item_code }}</span>
                                            @if($scan->matchedDocumentLine?->item?->description)
                                                <span class="block text-xs text-gray-400">{{ $scan->matchedDocumentLine->item->description }}</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400 max-w-[120px] truncate">
                                            {{ $scan->parsed_serial ?? '—' }}
                                        </td>
                                        <td class="px-3 py-2 text-right text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ number_format($scan->parsed_quantity, 0) }}
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <span class="inline-flex px-1.5 py-0.5 rounded-full text-xs font-bold {{ $scanColors[$scan->scan_status] ?? '' }}">
                                                {{ $scanLabels[$scan->scan_status] ?? $scan->scan_status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="empty-row">
                                        <td colspan="5" class="px-5 py-8 text-center text-gray-400 text-sm italic">
                                            Aún no se han escaneado piezas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ── Panel de progreso por artículo ── --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 overflow-hidden self-start">
                <header class="px-4 py-3 border-b border-gray-100 dark:border-gray-700/60">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 text-sm capitalize">Progreso del documento</h3>
                </header>
                <div class="divide-y divide-gray-100 dark:divide-gray-700/60 max-h-[560px] overflow-y-auto" id="progress-panel">
                    @forelse($documentLines as $line)
                        @php
                            $received = $line->quantity_received_computed ?? 0;
                            $declared = (float) ($line->quantity_declared ?? 0);
                            $percent  = $declared > 0 ? min(100, round(($received / $declared) * 100)) : 0;
                            $lineStatusColors = [
                                'PENDING'     => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400',
                                'RECEIVED'    => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                                'DAMAGED'     => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                                'EXPECTED'    => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                                'DISCREPANCY' => 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400',
                            ];
                        @endphp
                        <div class="px-4 py-3" id="progress-line-{{ $line->id }}">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-200 truncate max-w-[120px]">
                                    {{ $line->item->code ?? '—' }}
                                </span>
                                <span id="status-badge-{{ $line->id }}"
                                    class="inline-flex px-1.5 py-0.5 rounded text-xs font-bold {{ $lineStatusColors[$line->status] ?? '' }}">
                                    {{ \App\Models\ShipmentDocumentLine::STATUS_OPTIONS[$line->status] ?? $line->status }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mb-2 truncate">{{ $line->item->description ?? '' }}</p>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                    <div id="progress-bar-{{ $line->id }}"
                                        class="bg-violet-500 h-1.5 rounded-full transition-all duration-500"
                                        style="width: {{ $percent }}%">
                                    </div>
                                </div>
                                <span id="progress-text-{{ $line->id }}"
                                    class="text-xs font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                    {{ number_format($received, 0) }} / {{ number_format($declared, 0) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="px-5 py-6 text-sm text-gray-400 text-center italic">Sin líneas en este documento.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const SCAN_URL     = "{{ route('reception.scan', $shipmentDocument) }}";
        const COMPLETE_URL = "{{ route('reception.complete', $shipmentDocument) }}";
        const MOVEMENT_ID  = {{ $movement->id }};
        const CSRF         = "{{ csrf_token() }}";

        const scanInput  = document.getElementById('scan-input');
        const scanResult = document.getElementById('scan-result');
        const scansTable = document.getElementById('scans-table');
        let   emptyRow   = document.getElementById('empty-row');

        // ── Auto-foco constante ─────────────────────────
        scanInput.focus();
        document.addEventListener('click',   () => setTimeout(() => scanInput.focus(), 0));
        document.addEventListener('keydown', (e) => {
            if (!e.target.closest('button') && !e.target.closest('.swal2-container')) {
                scanInput.focus();
            }
        });

        // ── Procesar escaneo al presionar Enter ─────────
        scanInput.addEventListener('keydown', async (e) => {
            if (e.key !== 'Enter') return;
            e.preventDefault();

            const content = scanInput.value.trim();
            if (!content) return;

            scanInput.disabled = true;

            try {
                const res  = await fetch(SCAN_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ scan_content: content, stock_movement_id: MOVEMENT_ID }),
                });
                const data = await res.json();

                showResult(data);
                updateStats(data.status);

                if (data.status !== 'error') {
                    prependScanRow(data);
                }

                if (data.doc_line_id) {
                    updateProgressPanel(data);
                }

            } catch (err) {
                showResultRaw('error', 'Error de red. Intenta de nuevo.');
            } finally {
                scanInput.value    = '';
                scanInput.disabled = false;
                scanInput.focus();
            }
        });

        function showResult(data) {
            const colorMap = {
                matched:   'bg-green-50 border border-green-200 text-green-800 dark:bg-green-500/10 dark:text-green-300',
                unmatched: 'bg-yellow-50 border border-yellow-200 text-yellow-800 dark:bg-yellow-500/10 dark:text-yellow-300',
                duplicate: 'bg-orange-50 border border-orange-200 text-orange-800 dark:bg-orange-500/10 dark:text-orange-300',
                error:     'bg-red-50 border border-red-200 text-red-800 dark:bg-red-500/10 dark:text-red-300',
            };
            const icons = { matched: '✓', unmatched: '⚠', duplicate: '⊘', error: '✗' };

            const color = colorMap[data.status] || colorMap.error;
            const icon  = icons[data.status]    || '?';

            let html = `<span class="font-bold">${icon} ${data.message}</span>`;
            if (data.item_code)        html += ` &nbsp;·&nbsp;<span>${data.item_code}</span>`;
            if (data.serial)           html += ` &nbsp;·&nbsp;Serial: <span>${data.serial}</span>`;
            if (data.qty)              html += ` &nbsp;·&nbsp;Cant: <strong>${data.qty}</strong>`;
            if (data.consignment_type) html += ` &nbsp;·&nbsp;<span class="font-bold px-1 rounded bg-white/50">${data.consignment_type}</span>`;

            scanResult.className = `mt-3 px-3 py-2 rounded-lg text-sm ${color}`;
            scanResult.innerHTML = html;
            scanResult.classList.remove('hidden');
        }

        function showResultRaw(status, msg) {
            showResult({ status, message: msg });
        }

        function updateStats(status) {
            const map = {
                matched:   'stat-matched',
                unmatched: 'stat-unmatched',
                duplicate: 'stat-duplicate',
                error:     'stat-error',
            };
            const el = document.getElementById(map[status]);
            if (el) el.textContent = parseInt(el.textContent || '0') + 1;

            const totalEl = document.getElementById('total-count');
            if (totalEl) {
                const cur = parseInt(totalEl.textContent) || 0;
                totalEl.textContent = (cur + 1) + ' escaneos';
            }
        }

        function prependScanRow(data) {
            if (emptyRow) { emptyRow.remove(); emptyRow = null; }

            const statusConfig = {
                matched:   { label: 'OK',        css: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' },
                unmatched: { label: 'Sin doc.',   css: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400' },
                duplicate: { label: 'Duplicado',  css: 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400' },
                error:     { label: 'Error',      css: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400' },
            };
            const cfg = statusConfig[data.status] || statusConfig.error;

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50/50 dark:hover:bg-gray-900/10';
            tr.innerHTML = `
                <td class="px-3 py-2">
                    <span class="text-xs font-bold px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                        ${data.consignment_type ?? '—'}
                    </span>
                </td>
                <td class="px-3 py-2">
                    <span class="text-sm font-medium text-gray-800 dark:text-gray-100">${data.item_code ?? '—'}</span>
                    ${data.item_description ? `<span class="block text-xs text-gray-400">${data.item_description}</span>` : ''}
                </td>
                <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400 max-w-[120px] truncate">${data.serial ?? '—'}</td>
                <td class="px-3 py-2 text-right text-sm font-medium text-gray-700 dark:text-gray-300">${data.qty ?? 0}</td>
                <td class="px-3 py-2 text-center">
                    <span class="inline-flex px-1.5 py-0.5 rounded-full text-xs font-bold ${cfg.css}">${cfg.label}</span>
                </td>
            `;
            scansTable.insertBefore(tr, scansTable.firstChild);
        }

        // ── Actualiza la barra de progreso del panel derecho ──
        function updateProgressPanel(data) {
            const lineId  = data.doc_line_id;
            const received = parseFloat(data.total_received)    || 0;
            const declared = parseFloat(data.quantity_declared)  || 0;
            const percent  = declared > 0 ? Math.min(100, Math.round((received / declared) * 100)) : 0;

            const textEl = document.getElementById(`progress-text-${lineId}`);
            if (textEl) textEl.textContent = `${received.toLocaleString('es-MX')} / ${declared.toLocaleString('es-MX')}`;

            const barEl = document.getElementById(`progress-bar-${lineId}`);
            if (barEl) barEl.style.width = `${percent}%`;

            if (received >= declared && declared > 0) {
                const badgeEl = document.getElementById(`status-badge-${lineId}`);
                if (badgeEl) {
                    badgeEl.textContent = 'Recibido';
                    badgeEl.className = 'inline-flex px-1.5 py-0.5 rounded text-xs font-bold bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400';
                }
                if (barEl) barEl.classList.replace('bg-violet-500', 'bg-green-500');
            }
        }

        // ── Completar recepción ──────────────────────────
        document.getElementById('btn-complete').addEventListener('click', () => {
            Swal.fire({
                title: '¿Completar recepción?',
                text: 'Se cerrará el movimiento y se actualizará el estado del documento.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, completar',
                cancelButtonText: 'Cancelar',
                background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                color:      document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937',
            }).then(async (result) => {
                if (!result.isConfirmed) return;

                const res  = await fetch(COMPLETE_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ stock_movement_id: MOVEMENT_ID }),
                });
                const data = await res.json();

                if (data.status === 'completed') {
                    Swal.fire({
                        title: '¡Recepción completada!',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                        color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937',
                    }).then(() => window.location.href = "{{ route('reception.index') }}");
                }
            });
        });
    </script>
</x-app-layout>
