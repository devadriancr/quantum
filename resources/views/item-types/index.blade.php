<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Item Types</h1>
            </div>

            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="{{ route('item-types.create') }}"
                    class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">Add
                    Item Type</a>
            </div>

        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
            <div class="p-6">
                <table class="w-full table-auto">
                    <thead>
                        <tr
                            class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700/50">
                            <th class="p-2 whitespace-nowrap">Code</th>
                            <th class="p-2 whitespace-nowrap">Name</th>
                            <th class="p-2 whitespace-nowrap">Status</th>
                            <th class="p-2 whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                        @foreach ($itemTypes as $itemType)
                            <tr>
                                <td class="p-2 whitespace-nowrap">{{ $itemType->code }}</td>
                                <td class="p-2 whitespace-nowrap">{{ $itemType->name }}</td>
                                <td class="p-2 whitespace-nowrap">{{ $itemType->status ? 'Active' : 'Inactive' }}</td>
                                <td class="p-2 whitespace-nowrap">
                                    <a href="{{ route('item-types.show', $itemType) }}"
                                        class="text-blue-600 hover:text-blue-800">View</a>
                                    <a href="{{ route('item-types.edit', $itemType) }}"
                                        class="text-green-600 hover:text-green-800 ml-2">Edit</a>
                                    <form action="{{ route('item-types.destroy', $itemType) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 ml-2"
                                            onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
