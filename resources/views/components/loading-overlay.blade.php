@props([
    'message' => 'Procesando',
    'subtext' => 'Espere un momento',
])


<div
    wire:loading.delay.flex
    {{ $attributes->merge([
        'class' => 'fixed inset-0 z-[9999] hidden items-center justify-center bg-gray-900/50 backdrop-blur-sm',
    ]) }}
    role="status"
    aria-live="polite"
    aria-busy="true"
>
    {{-- Contenedor de la tarjeta (Card) --}}
    {{-- Se añade 'w-fit' para que solo ocupe el espacio del spin y el texto --}}
    <div class="loading-overlay-card flex w-fit items-center gap-5 rounded-2xl bg-white/95 px-8 py-6 shadow-2xl ring-1 ring-black/5 dark:bg-gray-800/95 dark:ring-white/10">

        {{-- Spinner Único --}}
        <div class="size-10 shrink-0 animate-spin rounded-full border-4 border-violet-100 border-t-violet-600 dark:border-violet-900/40 dark:border-t-violet-400"></div>

        {{-- Contenedor de Textos --}}
        <div class="flex flex-col items-start min-w-[150px]">
            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100 whitespace-nowrap">
                {{ __($message) }}
            </p>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                {{ __($subtext) }}
            </p>
        </div>
    </div>
</div>

@once
    <style>
        .loading-overlay-card {
            animation: loading-overlay-pop 0.25s ease-out;
        }

        @keyframes loading-overlay-pop {
            from { opacity: 0; transform: scale(0.92); }
            to   { opacity: 1; transform: scale(1); }
        }
    </style>
@endonce
