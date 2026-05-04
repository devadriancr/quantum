<x-app-layout>
    <x-toast-notifications />

    <div class="px-2 sm:px-6 lg:px-8 py-4 w-full max-w-9xl mx-auto">

        {{-- Encabezado --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    Entrega a Producción
                </h1>
            </div>
            <div class="flex items-center gap-3 mt-4 sm:mt-0">
                @if($movement->status === 'PENDING')
                    <button id="btn-complete"
                        class="btn bg-green-600 hover:bg-green-700 text-white inline-flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
                        </svg>
                        Finalizar Escaneo
                    </button>
                @endif
                <a href="{{ route('material-outputs.index') }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                    &larr; Regresar
                </a>
            </div>
        </div>

        {{-- Componente Livewire: input + tabla --}}
        @livewire('material-outputs.scan-panel', [
            'movementId'   => $movement->id,
            'isCompleted'  => $movement->status === 'COMPLETED',
            'locationFrom' => $movement->locationFrom?->code ?? 'L60',
            'locationTo'   => $movement->locationTo?->code ?? 'L12',
        ])

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const SCAN_URL     = "{{ route('material-outputs.scan', $movement) }}";
        const COMPLETE_URL = "{{ route('material-outputs.complete', $movement) }}";
        const REMOVE_URL   = "{{ route('material-outputs.remove-line', [$movement, '__LINE__']) }}";
        const CSRF         = "{{ csrf_token() }}";
        const IS_COMPLETED = {{ $movement->status === 'COMPLETED' ? 'true' : 'false' }};

        const scanInput  = document.getElementById('scan-input');
        const scanResult = document.getElementById('scan-result');

        // ── Auto-foco (solo si no está finalizado) ──────────────────────
        if (scanInput && !IS_COMPLETED) {
            scanInput.focus();
            document.addEventListener('click', (e) => {
                if (!e.target.closest('button') && !e.target.closest('.swal2-container') && !e.target.closest('nav')) {
                    setTimeout(() => scanInput.focus(), 0);
                }
            });
        }

        // ── Iconos por estado ───────────────────────────────────────────
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

        // ── Procesar escaneo ────────────────────────────────────────────
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
                        Livewire.dispatch('scan-output-completed');
                    }
                } catch {
                    showResult('error', 'Error de conexión. Intenta de nuevo.');
                } finally {
                    scanInput.value    = '';
                    scanInput.disabled = false;
                    if (!IS_COMPLETED) scanInput.focus();
                }
            });
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
                    confirmButton: 'inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg mx-2 transition-colors',
                    cancelButton:  'inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg mx-2 transition-colors',
                },
            });
        }

        // ── Eliminar línea (retorno al almacén) ────────────────────────
        document.addEventListener('click', async (e) => {
            const btn = e.target.closest('.remove-line-btn');
            if (!btn) return;

            const lineId = btn.dataset.lineId;

            const result = await Swal.fire(swalBase({
                title:             '¿Eliminar pieza?',
                text:              'El material regresará al almacén (L60).',
                icon:              'warning',
                iconColor:         '#ef4444',
                showCancelButton:  true,
                confirmButtonText: 'Sí, eliminar',
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
                    Livewire.dispatch('line-removed');
                    Swal.fire(swalBase({
                        title:             'Material retornado',
                        text:              'La pieza volvió al almacén de recibo.',
                        icon:              'success',
                        iconColor:         '#16a34a',
                        timer:             1500,
                        showConfirmButton: false,
                    }));
                } else {
                    Swal.fire(swalBase({ title: 'Error', text: data.message, icon: 'error', iconColor: '#ef4444' }));
                }
            } catch {
                Swal.fire(swalBase({ title: 'Error', text: 'Error de conexión.', icon: 'error', iconColor: '#ef4444' }));
            }
        });

        // ── Finalizar escaneo ───────────────────────────────────────────
        const btnComplete = document.getElementById('btn-complete');
        if (btnComplete) {
            btnComplete.addEventListener('click', async () => {
                const result = await Swal.fire(swalConfirm({
                    title:             '¿Finalizar entrega?',
                    text:              'Ya no podrás agregar ni quitar piezas.',
                    icon:              'question',
                    showCancelButton:  true,
                    confirmButtonText: 'Sí, finalizar',
                    cancelButtonText:  'Cancelar',
                    reverseButtons:    true,
                }));

                if (!result.isConfirmed) return;

                try {
                    const res  = await fetch(COMPLETE_URL, {
                        method:  'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                        body:    JSON.stringify({}),
                    });
                    const data = await res.json();

                    if (data.status === 'completed') {
                        Livewire.dispatch('movement-completed');
                        btnComplete.remove();
                        await Swal.fire(swalBase({
                            title:             '¡Entrega finalizada!',
                            icon:              'success',
                            iconColor:         '#16a34a',
                            timer:             1500,
                            showConfirmButton: false,
                        }));
                        window.location.href = "{{ route('material-outputs.index') }}";
                    }
                } catch {
                    Swal.fire(swalBase({ title: 'Error', text: 'No se pudo finalizar.', icon: 'error', iconColor: '#ef4444' }));
                }
            });
        }
    </script>
</x-app-layout>
