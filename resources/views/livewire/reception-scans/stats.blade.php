<div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

    {{-- Coincidentes --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 p-4 flex items-center gap-4">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-green-100 dark:bg-green-500/20 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 text-green-600 dark:text-green-400">
                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-400 dark:text-gray-500">Coincidentes</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $matched }}</p>
        </div>
    </div>

    {{-- Sin coincidencia --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 p-4 flex items-center gap-4">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-yellow-100 dark:bg-yellow-500/20 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 text-yellow-600 dark:text-yellow-400">
                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-400 dark:text-gray-500">Sin coincidencia</p>
            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $unmatched }}</p>
        </div>
    </div>

    {{-- Duplicados --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 p-4 flex items-center gap-4">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-500/20 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 text-orange-600 dark:text-orange-400">
                <path d="M7 3.5A1.5 1.5 0 0 1 8.5 2h3.879a1.5 1.5 0 0 1 1.06.44l3.122 3.12A1.5 1.5 0 0 1 17 6.622V12.5a1.5 1.5 0 0 1-1.5 1.5h-1v-3.379a3 3 0 0 0-.879-2.121L10.5 5.379A3 3 0 0 0 8.379 4.5H7v-1Z"/>
                <path d="M4.5 6A1.5 1.5 0 0 0 3 7.5v9A1.5 1.5 0 0 0 4.5 18h7a1.5 1.5 0 0 0 1.5-1.5v-5.879a1.5 1.5 0 0 0-.44-1.06L9.44 6.439A1.5 1.5 0 0 0 8.378 6H4.5Z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-400 dark:text-gray-500">Duplicados</p>
            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $duplicate }}</p>
        </div>
    </div>

    {{-- Errores --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 p-4 flex items-center gap-4">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-red-100 dark:bg-red-500/20 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 text-red-600 dark:text-red-400">
                <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-400 dark:text-gray-500">Errores</p>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $error }}</p>
        </div>
    </div>

</div>
