<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Item Type Details</h1>
            </div>

            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('item-types.edit', $itemType) }}"
                    class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">Edit</a>
                <a href="{{ route('item-types.index') }}" class="btn bg-gray-500 text-white hover:bg-gray-600">Back</a>
            </div>

        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
            <div class="p-6">
                <div class="mb-4">
                    <strong>Code:</strong> {{ $itemType->code }}
                </div>
                <div class="mb-4">
                    <strong>Name:</strong> {{ $itemType->name }}
                </div>
                <div class="mb-4">
                    <strong>Status:</strong> {{ $itemType->status ? 'Active' : 'Inactive' }}
                </div>
                <div class="mb-4">
                    <strong>Created At:</strong> {{ $itemType->created_at->format('Y-m-d H:i:s') }}
                </div>
                <div class="mb-4">
                    <strong>Updated At:</strong> {{ $itemType->updated_at->format('Y-m-d H:i:s') }}
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
