{{-- Toast Container --}}
<div id="toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none w-full max-w-sm"></div>

<script>
(function () {
    var borderColors = { success: 'border-green-500', error: 'border-red-500', warning: 'border-yellow-500', info: 'border-blue-500' };
    var iconColors   = { success: 'text-green-500',  error: 'text-red-500',  warning: 'text-yellow-500',  info: 'text-blue-500' };
    var titles       = { success: 'Éxito',            error: 'Error',         warning: 'Advertencia',       info: 'Información' };

    var icons = {
        success: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>',
        error:   '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>',
        warning: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>',
        info:    '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>',
    };

    var closeBtn = '<button class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors mt-0.5" onclick="window.dismissToast(this.closest(\'[data-toast]\'))">'
        + '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>'
        + '</button>';

    function escapeHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function buildToast(type, bodyHtml) {
        var el = document.createElement('div');
        el.setAttribute('data-toast', '');
        el.className = [
            'pointer-events-auto flex items-start gap-3 p-4 rounded-xl shadow-lg',
            'border border-gray-100 dark:border-gray-700 border-l-4',
            borderColors[type] || borderColors.info,
            'bg-white dark:bg-gray-800 w-full',
            'transform translate-x-full transition-all duration-300 ease-out opacity-0'
        ].join(' ');
        el.innerHTML =
            '<div class="flex-shrink-0 mt-0.5 ' + (iconColors[type] || iconColors.info) + '">' + (icons[type] || icons.info) + '</div>' +
            '<div class="flex-1 min-w-0">' + bodyHtml + '</div>' +
            closeBtn;
        return el;
    }

    function attachTimer(el, duration) {
        el._toastTimer = setTimeout(function () { window.dismissToast(el); }, duration);
    }

    function show(el, container) {
        container.appendChild(el);
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                el.classList.remove('translate-x-full', 'opacity-0');
            });
        });
    }

    window.showToast = function (message, type, duration) {
        duration = duration || 5000;
        var container = document.getElementById('toast-container');
        var bodyHtml =
            '<p class="text-sm font-semibold text-gray-800 dark:text-gray-100">' + (titles[type] || 'Notificación') + '</p>' +
            '<p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5 break-words">' + escapeHtml(message) + '</p>';
        var el = buildToast(type, bodyHtml);
        attachTimer(el, duration);
        show(el, container);
    };

    window.showToastList = function (title, items, type, duration) {
        duration = duration || 9000;
        var container = document.getElementById('toast-container');
        var listHtml = '<ul class="mt-1.5 space-y-0.5 max-h-36 overflow-y-auto pr-1">';
        items.forEach(function (item) {
            listHtml += '<li class="text-xs text-gray-500 dark:text-gray-400 flex gap-1.5"><span class="' + (iconColors[type] || iconColors.warning) + ' flex-shrink-0 mt-px">•</span><span>' + escapeHtml(item) + '</span></li>';
        });
        listHtml += '</ul>';
        var bodyHtml =
            '<p class="text-sm font-semibold text-gray-800 dark:text-gray-100">' + escapeHtml(title) + ' <span class="font-normal text-gray-500">(' + items.length + ')</span></p>' +
            listHtml;
        var el = buildToast(type, bodyHtml);
        attachTimer(el, duration);
        show(el, container);
    };

    window.dismissToast = function (el) {
        if (!el) return;
        clearTimeout(el._toastTimer);
        el.classList.add('translate-x-full', 'opacity-0');
        setTimeout(function () { if (el.parentNode) el.remove(); }, 300);
    };

    document.addEventListener('DOMContentLoaded', function () {
        @if (session('success'))
            window.showToast(@json(session('success')), 'success');
        @endif
        @if (session('error'))
            window.showToast(@json(session('error')), 'error', 7000);
        @endif
        @if (session('import_warnings'))
            window.showToastList('Advertencias de importación', @json(session('import_warnings')), 'warning', 10000);
        @endif
    });
}());
</script>
