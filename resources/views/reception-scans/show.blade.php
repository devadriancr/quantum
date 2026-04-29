<x-app-layout>
    <div class="px-2 sm:px-6 lg:px-8 py-4 w-full max-w-9xl mx-auto">

        {{-- Encabezado --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    Recepción de Material
                </h1>
            </div>
            <div class="flex gap-2 mt-4 sm:mt-0 items-center">
                @if($movement->status === 'COMPLETED')
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400 border border-green-200 dark:border-green-500/30">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
                        </svg>
                        Recepción Completada
                    </span>
                @else
                    <button id="btn-complete"
                        class="btn bg-green-600 hover:bg-green-700 text-white inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
                        </svg>
                        Completar Recepción
                    </button>
                @endif
                <a href="{{ route('reception.index') }}"
                   class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 text-gray-600 dark:text-gray-300">
                    &larr; Regresar
                </a>
            </div>
        </div>

        {{-- Estadísticas --}}
        <div class="mb-6">
            @livewire('reception-scans.stats', [
                'movementId' => $movement->id,
                'stats'      => $stats,
            ])
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- Panel de escaneo --}}
            <div class="xl:col-span-2">
                @livewire('reception-scans.scan-panel', [
                    'movementId'   => $movement->id,
                    'initialScans' => $scans,
                    'isCompleted'  => $movement->status === 'COMPLETED',
                ])
            </div>

            {{-- Progreso del documento --}}
            @livewire('reception-scans.document-progress', [
                'shipmentDocumentId' => $shipmentDocument->id,
                'documentLines'      => $documentLines,
            ])

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

        // ── Auto-foco constante (solo si el input existe, es decir, no está completado) ──
        if (scanInput) {
            scanInput.focus();
            document.addEventListener('click', () => setTimeout(() => scanInput.focus(), 0));
            document.addEventListener('keydown', (e) => {
                if (!e.target.closest('button') && !e.target.closest('.swal2-container')) {
                    scanInput.focus();
                }
            });
        }

        // ── Iconos HeroIcons para notificaciones ────────
        const ICONS = {
            matched: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 shrink-0">
                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
            </svg>`,
            unmatched: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 shrink-0">
                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/>
            </svg>`,
            duplicate: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 shrink-0">
                <path d="M7 3.5A1.5 1.5 0 0 1 8.5 2h3.879a1.5 1.5 0 0 1 1.06.44l3.122 3.12A1.5 1.5 0 0 1 17 6.622V12.5a1.5 1.5 0 0 1-1.5 1.5h-1v-3.379a3 3 0 0 0-.879-2.121L10.5 5.379A3 3 0 0 0 8.379 4.5H7v-1Z"/>
                <path d="M4.5 6A1.5 1.5 0 0 0 3 7.5v9A1.5 1.5 0 0 0 4.5 18h7a1.5 1.5 0 0 0 1.5-1.5v-5.879a1.5 1.5 0 0 0-.44-1.06L9.44 6.439A1.5 1.5 0 0 0 8.378 6H4.5Z"/>
            </svg>`,
            error: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 shrink-0">
                <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/>
            </svg>`,
        };

        const NOTIFICATION = {
            matched:   { cls: 'bg-green-50 border-green-200 text-green-800 dark:bg-green-500/10 dark:border-green-500/20 dark:text-green-300',  msg: 'Escaneado correctamente' },
            unmatched: { cls: 'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-500/10 dark:border-yellow-500/20 dark:text-yellow-300', msg: 'Sin coincidencia en documento' },
            duplicate: { cls: 'bg-orange-50 border-orange-200 text-orange-800 dark:bg-orange-500/10 dark:border-orange-500/20 dark:text-orange-300', msg: 'Serial ya escaneado' },
            error:     { cls: 'bg-red-50 border-red-200 text-red-800 dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-300',       msg: 'Error de escaneo' },
        };

        function showResult(status) {
            const cfg  = NOTIFICATION[status] || NOTIFICATION.error;
            const icon = ICONS[status]        || ICONS.error;
            scanResult.className = `flex items-center gap-2 mt-3 px-3 py-2 rounded-lg text-sm border ${cfg.cls}`;
            scanResult.innerHTML = `${icon}<span class="font-semibold">${cfg.msg}</span>`;
            scanResult.classList.remove('hidden');
        }

        // ── Procesar escaneo al presionar Enter ─────────
        scanInput?.addEventListener('keydown', async (e) => {
            if (e.key !== 'Enter') return;
            e.preventDefault();

            const content = scanInput.value.trim();
            if (!content) return;

            scanInput.disabled = true;

            try {
                const res  = await fetch(SCAN_URL, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body:    JSON.stringify({ scan_content: content, stock_movement_id: MOVEMENT_ID }),
                });
                const data = await res.json();

                showResult(data.status);

                Livewire.dispatch('scan-completed', { payload: data });

            } catch {
                showResult('error');
            } finally {
                scanInput.value    = '';
                scanInput.disabled = false;
                scanInput.focus();
            }
        });

        // ── Completar recepción ──────────────────────────
        document.getElementById('btn-complete')?.addEventListener('click', () => {
            const isDark = document.documentElement.classList.contains('dark');

            Swal.fire({
                title: `<span class="text-lg font-bold">${'Completar recepción'}</span>`,
                html: `
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Se cerrará el movimiento y no podrá escanearse más material.
                    </div>
                `,
                icon: 'question',
                iconColor: '#16a34a',
                showCancelButton: true,
                confirmButtonText: 'Sí, completar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg mx-2 transition-colors',
                    cancelButton:  'inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg mx-2 transition-colors',
                },
                background: isDark ? '#111827' : '#ffffff',
                color:      isDark ? '#f3f4f6' : '#1f2937',
            }).then(async (result) => {
                if (!result.isConfirmed) return;

                const res  = await fetch(COMPLETE_URL, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body:    JSON.stringify({ stock_movement_id: MOVEMENT_ID }),
                });
                const data = await res.json();

                if (data.status === 'completed') {
                    Swal.fire({
                        title: '¡Recepción completada!',
                        text: 'El movimiento fue cerrado correctamente.',
                        icon: 'success',
                        iconColor: '#16a34a',
                        timer: 1800,
                        showConfirmButton: false,
                        buttonsStyling: false,
                        background: isDark ? '#111827' : '#ffffff',
                        color:      isDark ? '#f3f4f6' : '#1f2937',
                    }).then(() => window.location.href = "{{ route('reception.index') }}");
                }
            });
        });
    </script>
</x-app-layout>
