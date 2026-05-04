{{-- Toast Container --}}
<div id="toast-container" class="fixed top-5 right-5 z-[100] flex flex-col gap-3 pointer-events-none w-full max-w-sm"></div>

<script>
(function () {
    // Configuración estética refinada
    const theme = {
        success: {
            bg: 'bg-white/80 dark:bg-emerald-900/90',
            border: 'border-emerald-500/20',
            icon: 'text-emerald-500',
            progress: 'bg-emerald-500',
            title: 'Éxito',
            svg: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" /></svg>'
        },
        error: {
            bg: 'bg-white/80 dark:bg-red-900/90',
            border: 'border-red-500/20',
            icon: 'text-red-500',
            progress: 'bg-red-500',
            title: 'Error',
            svg: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" /></svg>'
        },
        warning: {
            bg: 'bg-white/80 dark:bg-amber-900/90',
            border: 'border-amber-500/20',
            icon: 'text-amber-500',
            progress: 'bg-amber-500',
            title: 'Advertencia',
            svg: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.401 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" /></svg>'
        },
        info: {
            bg: 'bg-white/80 dark:bg-blue-900/90',
            border: 'border-blue-500/20',
            icon: 'text-blue-500',
            progress: 'bg-blue-500',
            title: 'Información',
            svg: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" /></svg>'
        }
    };

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    window.showToast = function (message, type = 'info', duration = 5000) {
        const config = theme[type] || theme.info;
        const container = document.getElementById('toast-container');

        const el = document.createElement('div');
        el.className = `pointer-events-auto relative group overflow-hidden flex items-start gap-3 p-4 rounded-2xl shadow-2xl backdrop-blur-md border ${config.border} ${config.bg} transform translate-x-full opacity-0 transition-all duration-500 ease-out`;

        el.innerHTML = `
            <div class="flex-shrink-0 ${config.icon}">${config.svg}</div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-900 dark:text-white">${config.title}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">${escapeHtml(message)}</p>
            </div>
            <button onclick="window.dismissToast(this.closest('.relative'))" class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
            <div class="absolute bottom-0 left-0 h-1 ${config.progress} transition-all duration-100 ease-linear" style="width: 100%"></div>
        `;

        container.appendChild(el);

        // Animación de entrada
        setTimeout(() => el.classList.remove('translate-x-full', 'opacity-0'), 10);

        // Lógica de temporizador y barra de progreso
        let remaining = duration;
        let start = Date.now();
        let timer;
        const progressBar = el.querySelector('.absolute.bottom-0');

        const updateProgress = () => {
            const perc = (remaining / duration) * 100;
            progressBar.style.width = `${perc}%`;
        };

        const startTimer = () => {
            start = Date.now();
            timer = setTimeout(() => window.dismissToast(el), remaining);
        };

        el.onmouseenter = () => {
            clearTimeout(timer);
            remaining -= Date.now() - start;
        };

        el.onmouseleave = () => {
            if (remaining > 0) startTimer();
        };

        const progressInterval = setInterval(() => {
            if (el.parentNode && !el.matches(':hover')) {
                const currentRemaining = remaining - (Date.now() - start);
                const perc = Math.max(0, (currentRemaining / duration) * 100);
                progressBar.style.width = `${perc}%`;
            }
            if (!el.parentNode) clearInterval(progressInterval);
        }, 50);

        startTimer();
    };

    window.dismissToast = function (el) {
        if (!el) return;
        el.classList.add('translate-x-full', 'opacity-0', 'scale-95');
        setTimeout(() => el.remove(), 500);
    };

    // Auto-ejecución desde Laravel Session
    document.addEventListener('DOMContentLoaded', () => {
        @if (session('success')) window.showToast(@json(session('success')), 'success'); @endif
        @if (session('error')) window.showToast(@json(session('error')), 'error', 7000); @endif
    });
})();
</script>
