<div>
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
            {{ __('Contenedores disponibles') }}
        </h2>
        @if ($hasData)
            <span class="text-xs text-gray-500">
                {{ $totalAvailable }} {{ __('sin asignar') }}
            </span>
        @endif
    </div>

    @if (!$hasData)
        <p class="text-sm text-gray-500 italic">
            {{ __('Carga un archivo Excel para ver los contenedores agrupados.') }}
        </p>
    @endif

    <div class="space-y-4 overflow-y-auto max-h-[500px] pr-2">
        @foreach ($poolByDate as $date => $bucket)
            <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl border border-gray-200 dark:border-gray-700/60 p-4"
                 wire:key="group-{{ $date }}">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-700 dark:text-gray-200">
                        {{ __('Fecha:') }} {{ $bucket['label'] }}
                    </h3>
                    <span class="text-xs text-gray-500">
                        {{ $bucket['count'] }} {{ __('contenedor(es) sin asignar') }}
                    </span>
                </div>

                {{-- Grid más anchito: 2 cols móvil, 3 cols sm, 4 cols md+ --}}
                <div class="kanban-pool grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 min-h-20"
                     data-pool-date="{{ $date }}">
                    @foreach ($bucket['available'] as $code => $price)
                        <div class="kanban-card bg-violet-100 dark:bg-violet-900/40 border border-violet-400 text-violet-800 dark:text-violet-200 rounded-md px-3 py-3 text-center cursor-move"
                             data-container-code="{{ $code }}"
                             wire:key="pool-{{ $date }}-{{ $code }}">
                            <p class="font-bold text-sm truncate">{{ $code }}</p>
                            <p class="text-xs opacity-75 mt-1">${{ number_format((float) $price, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
