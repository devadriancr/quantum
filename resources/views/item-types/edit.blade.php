<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="md:grid md:grid-cols-3 md:gap-6">
            <x-section-title>
                <x-slot name="title">Edit Item Type</x-slot>
                <x-slot name="description">Update the item type details.</x-slot>
            </x-section-title>

            <div class="mt-5 md:mt-0 md:col-span-2">
                <div class="px-4 py-5 bg-white dark:bg-gray-800 sm:p-6 shadow-sm sm:rounded-md">
                    <form action="{{ route('item-types.update', $itemType) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <x-label for="code" value="Code" />
                                <x-input id="code" type="text" name="code"
                                    value="{{ old('code', $itemType->code) }}" required autofocus />
                                <x-input-error for="code" />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <x-label for="name" value="Name" />
                                <x-input id="name" type="text" name="name"
                                    value="{{ old('name', $itemType->name) }}" required />
                                <x-input-error for="name" />
                            </div>

                            <div class="col-span-6">
                                <label class="flex items-center">
                                    <input type="checkbox" name="status" value="1"
                                        {{ old('status', $itemType->status) ? 'checked' : '' }}
                                        class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-700">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-secondary-button onclick="window.history.back()">Cancel</x-secondary-button>
                            <x-button class="ml-3">Update</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
