<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('File Management') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Search and Filters -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('admin.files.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Title, filename, code..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                        </div>

                        <!-- Type Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">File Type</label>
                            <select name="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Types</option>
                                @foreach($fileTypes as $type)
                                <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                                    .{{ strtoupper($type) }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sort -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                            <select name="sort" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest First</option>
                                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="downloads" {{ request('sort') === 'downloads' ? 'selected' : '' }}>Most Downloads</option>
                                <option value="size" {{ request('sort') === 'size' ? 'selected' : '' }}>Largest Size</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Date From -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Date To -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Apply Filters
                        </button>
                        <a href="{{ route('admin.files.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Clear Filters
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
            <div class="p-4">
                <form id="bulkDeleteForm" method="POST" action="{{ route('admin.files.bulk-delete') }}" onsubmit="return confirm('Are you sure you want to delete selected files?');">
                    @csrf
                    <div class="flex items-center gap-4">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:bg-gray-300" id="bulkDeleteBtn" disabled>
                            Delete Selected (<span id="selectedCount">0</span>)
                        </button>
                        <button type="button" onclick="toggleSelectAll()" class="text-sm text-blue-600 hover:text-blue-800">
                            Select All / Deselect All
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Files Table -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Short Code</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Downloads</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($files as $file)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="file_ids[]" value="{{ $file->id }}" class="file-checkbox" form="bulkDeleteForm" onchange="updateBulkActions()">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded flex items-center justify-center">
                                            <span class="text-xs font-semibold text-gray-600">.{{ strtoupper($file->extension) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ Str::limit($file->title, 40) }}</div>
                                            <div class="text-sm text-gray-500">{{ Str::limit($file->original_name, 40) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $file->short_code }}</code>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $file->formatted_size }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        {{ $file->downloads_count }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($file->is_active && !$file->isExpired() && !$file->hasReachedDownloadLimit())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Expired
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ $file->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $file->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.files.show', $file->id) }}" class="text-blue-600 hover:text-blue-900">
                                            View
                                        </a>
                                        <a href="{{ route('file.access', $file->short_code) }}" target="_blank" class="text-green-600 hover:text-green-900">
                                            Link
                                        </a>
                                        <form method="POST" action="{{ route('admin.files.destroy', $file->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this file?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    No files found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $files->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateBulkActions() {
            const checkboxes = document.querySelectorAll('.file-checkbox:checked');
            const count = checkboxes.length;
            document.getElementById('selectedCount').textContent = count;
            document.getElementById('bulkDeleteBtn').disabled = count === 0;
        }

        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.file-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateBulkActions();
        }
    </script>
</x-admin-layout>
