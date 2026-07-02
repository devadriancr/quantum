<x-app-layout>
    <x-toast-notifications />

    <div class="px-2 sm:px-6 lg:px-8 py-4 w-full max-w-9xl mx-auto">

        {{-- Encabezado --}}
        <div class="sm:flex sm:justify-between sm:items-start mb-6 gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    Salida a Almacén Externo
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $movement->movement_number }}
                </p>
            </div>
            <div class="flex items-center gap-3 mt-4 sm:mt-0 flex-wrap">

                @if($movement->status === 'PENDING')
                    <button id="btn-dispatch"
                        class="inline-flex items-center gap-2 px-4 py-2 border-2 border-blue-500 dark:border-blue-500 bg-blue-50 dark:bg-blue-500/10 hover:bg-blue-100 dark:hover:bg-blue-500/20 text-blue-700 dark:text-blue-400 text-sm font-medium rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                            <path d="M3.105 2.288a.75.75 0 0 0-.826.95l1.414 4.926A1.5 1.5 0 0 0 5.135 9.25h6.115a.75.75 0 0 1 0 1.5H5.135a1.5 1.5 0 0 0-1.442 1.086l-1.414 4.926a.75.75 0 0 0 .826.95 28.897 28.897 0 0 0 15.293-7.154.75.75 0 0 0 0-1.115A28.897 28.897 0 0 0 3.105 2.288Z"/>
                        </svg>
                        Enviar Viaje
                    </button>
                @endif

                @if($movement->status === 'IN_TRANSIT')
                    <a href="{{ route('external-warehouse.return.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 border-2 border-amber-500 dark:border-amber-500 bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 text-amber-700 dark:text-amber-400 text-sm font-medium rounded-lg transition-colors"
                       title="Los retornos se gestionan en el módulo de Retornos, sin estar atados a un viaje específico">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                            <path fill-rule="evenodd" d="M7.793 2.232a.75.75 0 0 1-.025 1.06L3.622 7.25h10.003a5.375 5.375 0 0 1 0 10.75H10.75a.75.75 0 0 1 0-1.5h2.875a3.875 3.875 0 0 0 0-7.75H3.622l4.146 3.957a.75.75 0 0 1-1.036 1.085l-5.5-5.25a.75.75 0 0 1 0-1.085l5.5-5.25a.75.75 0 0 1 1.06.025Z" clip-rule="evenodd"/>
                        </svg>
                        Ir a Retornos
                    </a>
                @endif

                <a href="{{ route('external-warehouse.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 border-2 border-gray-400 dark:border-gray-500 bg-gray-50 dark:bg-gray-700/40 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                    &larr; Regresar
                </a>
            </div>
        </div>

        {{-- Documentos del viaje --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 p-4">
                <p class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 mb-1">Factura</p>
                <p class="text-base font-bold text-gray-800 dark:text-gray-100 font-mono">{{ $movement->invoice_number ?? '—' }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 p-4">
                <p class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 mb-1">Carta Porte</p>
                <p class="text-base font-bold text-gray-800 dark:text-gray-100 font-mono">{{ $movement->carta_porte ?? '—' }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 p-4">
                <p class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 mb-1">Estado</p>
                @php
                    $statusMap = [
                        'PENDING'    => ['label' => 'En Carga',    'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400'],
                        'IN_TRANSIT' => ['label' => 'En Tránsito', 'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400'],
                        'COMPLETED'  => ['label' => 'Completado',  'class' => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400'],
                    ];
                    $statusInfo = $statusMap[$movement->status] ?? ['label' => $movement->status, 'class' => 'bg-gray-100 text-gray-600'];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $statusInfo['class'] }}">
                    {{ $statusInfo['label'] }}
                </span>
            </div>
        </div>

        {{-- Componente Livewire: input + tabla de escaneo --}}
        @livewire('external-warehouse.scan-panel', [
            'movementId'   => $movement->id,
            'isCompleted'  => $movement->status !== 'PENDING',
            'locationFrom' => $movement->locationFrom?->code ?? 'L60',
            'locationTo'   => $movement->locationTo?->code ?? 'L61',
        ])

        {{-- Aviso: los retornos se gestionan desde el módulo independiente --}}
        @if($movement->status === 'IN_TRANSIT')
            <div class="mt-6 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-xl p-4 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 text-amber-500 shrink-0">
                    <path fill-rule="evenodd" d="M7.793 2.232a.75.75 0 0 1-.025 1.06L3.622 7.25h10.003a5.375 5.375 0 0 1 0 10.75H10.75a.75.75 0 0 1 0-1.5h2.875a3.875 3.875 0 0 0 0-7.75H3.622l4.146 3.957a.75.75 0 0 1-1.036 1.085l-5.5-5.25a.75.75 0 0 1 0-1.085l5.5-5.25a.75.75 0 0 1 1.06.025Z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-amber-700 dark:text-amber-300">Viaje enviado a almacén externo.</p>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">
                        Cuando regrese material (parcial o total, de este u otros viajes), regístralo desde
                        <a href="{{ route('external-warehouse.return.index') }}" class="underline font-medium">Retornos de Almacén Externo</a>.
                    </p>
                </div>
            </div>
        @endif

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const SCAN_URL     = "{{ route('external-warehouse.scan', $movement) }}";
        const DISPATCH_URL = "{{ route('external-warehouse.dispatch', $movement) }}";
        const REMOVE_URL   = "{{ route('external-warehouse.remove-line', [$movement, '__LINE__']) }}";
        const CSRF         = "{{ csrf_token() }}";
        const IS_PENDING   = {{ $movement->status === 'PENDING' ? 'true' : 'false' }};

        const scanInput  = document.getElementById('scan-input');
        const scanResult = document.getElementById('scan-result');

        if (scanInput && IS_PENDING) {
            scanInput.focus();
            document.addEventListener('click', (e) => {
                if (!e.target.closest('button') && !e.target.closest('.swal2-container') && !e.target.closest('nav') && !e.target.closest('form')) {
                    setTimeout(() => scanInput.focus(), 0);
                }
            });
        }

        const ICONS = {
            success:   `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 shrink-0"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>`,
            duplicate: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 shrink-0"><path d="M7 3.5A1.5 1.5 0 0 1 8.5 2h3.879a1.5 1.5 0 0 1 1.06.44l3.122 3.12A1.5 1.5 0 0 1 17 6.622V12.5a1.5 1.5 0 0 1-1.5 1.5h-1v-3.379a3 3 0 0 0-.879-2.121L10.5 5.379A3 3 0 0 0 8.379 4.5H7v-1Z"/><path d="M4.5 6A1.5 1.5 0 0 0 3 7.5v9A1.5 1.5 0 0 0 4.5 18h7a1.5 1.5 0 0 0 1.5-1.5v-5.879a1.5 1.5 0 0 0-.44-1.06L9.44 6.439A1.5 1.5 0 0 0 8.378 6H4.5Z"/></svg>`,
            error:     `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 shrink-0"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>`,
        };

        const NOTIFICATION = {
            success:   { cls: 'bg-green-50  border-green-200  text-green-800  dark:bg-green-500/10  dark:border-green-500/20  dark:text-green-300'  },
            duplicate: { cls: 'bg-orange-50 border-orange-200 text-orange-800 dark:bg-orange-500/10 dark:border-orange-500/20 dark:text-orange-300' },
            error:     { cls: 'bg-red-50    border-red-200    text-red-800    dark:bg-red-500/10    dark:border-red-500/20    dark:text-red-300'    },
        };

        function showResult(status, message) {
            const cfg  = NOTIFICATION[status] || NOTIFICATION.error;
            const icon = ICONS[status]        || ICONS.error;
            scanResult.className = `flex items-center gap-2 mt-3 px-3 py-2 rounded-lg text-sm border ${cfg.cls}`;
            scanResult.innerHTML = `${icon}<span class="font-semibold">${message}</span>`;
            scanResult.classList.remove('hidden');
        }

        function swalBase(extra = {}) {
            const isDark = document.documentElement.classList.contains('dark');
            return {
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg mx-2 transition-colors',
                    cancelButton:  'inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg mx-2 transition-colors',
                },
                background: isDark ? '#111827' : '#ffffff',
                color:      isDark ? '#f3f4f6' : '#1f2937',
                ...extra,
            };
        }

        function swalConfirm(extra = {}) {
            return swalBase({
                ...extra,
                customClass: {
                    confirmButton: 'inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg mx-2 transition-colors',
                    cancelButton:  'inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg mx-2 transition-colors',
                },
            });
        }

        // ── Escaneo ──────────────────────────────────────────────────────────
        if (scanInput) {
            scanInput.addEventListener('keydown', async (e) => {
                if (e.key !== 'Enter') return;
                e.preventDefault();
                const content = scanInput.value.trim();
                if (!content) return;
                scanInput.disabled = true;
                try {
                    const res  = await fetch(SCAN_URL, {
                        method:  'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                        body:    JSON.stringify({ scan_content: content }),
                    });
                    const data = await res.json();
                    showResult(data.status, data.message);
                    if (data.status === 'success') {
                        Livewire.dispatch('scan-ext-completed');
                    }
                } catch {
                    showResult('error', 'Error de conexión. Intenta de nuevo.');
                } finally {
                    scanInput.value    = '';
                    scanInput.disabled = false;
                    if (IS_PENDING) scanInput.focus();
                }
            });
        }

        // ── Quitar línea ─────────────────────────────────────────────────────
        document.addEventListener('click', async (e) => {
            const btn = e.target.closest('.remove-line-btn');
            if (!btn) return;
            const lineId = btn.dataset.lineId;
            const result = await Swal.fire(swalBase({
                title:             '¿Quitar material?',
                text:              'El material regresará al inventario de L60.',
                icon:              'warning',
                iconColor:         '#ef4444',
                showCancelButton:  true,
                confirmButtonText: 'Sí, quitar',
                cancelButtonText:  'Cancelar',
                reverseButtons:    true,
            }));
            if (!result.isConfirmed) return;
            try {
                const url = REMOVE_URL.replace('__LINE__', lineId);
                const res = await fetch(url, {
                    method:  'DELETE',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                });
                const data = await res.json();
                if (data.status === 'success') {
                    Livewire.dispatch('line-ext-removed');
                    Swal.fire(swalBase({ title: 'Removido', icon: 'success', iconColor: '#16a34a', timer: 1200, showConfirmButton: false }));
                } else {
                    Swal.fire(swalBase({ title: 'Error', text: data.message, icon: 'error', iconColor: '#ef4444' }));
                }
            } catch {
                Swal.fire(swalBase({ title: 'Error', text: 'Error de conexión.', icon: 'error', iconColor: '#ef4444' }));
            }
        });

        // ── Enviar viaje ─────────────────────────────────────────────────────
        const btnDispatch = document.getElementById('btn-dispatch');
        if (btnDispatch) {
            btnDispatch.addEventListener('click', async () => {
                const result = await Swal.fire(swalConfirm({
                    title:             '¿Enviar viaje?',
                    text:              'El material pasará a estado "En Tránsito" y se asignará a L61. Ya no podrás agregar ni quitar piezas.',
                    icon:              'question',
                    showCancelButton:  true,
                    confirmButtonText: 'Sí, enviar',
                    cancelButtonText:  'Cancelar',
                    reverseButtons:    true,
                }));
                if (!result.isConfirmed) return;
                try {
                    const res  = await fetch(DISPATCH_URL, {
                        method:  'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                        body:    JSON.stringify({}),
                    });
                    const data = await res.json();
                    if (data.status === 'dispatched') {
                        Livewire.dispatch('movement-ext-dispatched');
                        btnDispatch.remove();
                        await Swal.fire(swalBase({
                            title:             '¡Viaje enviado!',
                            text:              'Material en tránsito hacia L61.',
                            icon:              'success',
                            iconColor:         '#2563eb',
                            timer:             1800,
                            showConfirmButton: false,
                        }));
                        window.location.href = "{{ route('external-warehouse.index') }}";
                    } else {
                        Swal.fire(swalBase({ title: 'Error', text: data.message, icon: 'error', iconColor: '#ef4444' }));
                    }
                } catch {
                    Swal.fire(swalBase({ title: 'Error', text: 'No se pudo enviar el viaje.', icon: 'error', iconColor: '#ef4444' }));
                }
            });
        }
    </script>
</x-app-layout>
