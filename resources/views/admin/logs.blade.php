<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Activity Logs') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('admin.logs') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Action Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                            <select name="action" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Actions</option>
                                <option value="upload" {{ request('action') === 'upload' ? 'selected' : '' }}>Upload</option>
                                <option value="download" {{ request('action') === 'download' ? 'selected' : '' }}>Download</option>
                                <option value="access" {{ request('action') === 'access' ? 'selected' : '' }}>Access</option>
                                <option value="access_denied" {{ request('action') === 'access_denied' ? 'selected' : '' }}>Access Denied</option>
                                <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>Delete</option>
                            </select>
                        </div>

                        <!-- IP Address Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">IP Address</label>
                            <input type="text" name="ip_address" value="{{ request('ip_address') }}"
                                placeholder="e.g., 192.168.1.1"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

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
                        <a href="{{ route('admin.logs') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Clear Filters
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Summary -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-green-600">
                    {{ $logs->where('action', 'upload')->count() }}
                </div>
                <div class="text-sm text-gray-600">Uploads</div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-blue-600">
                    {{ $logs->where('action', 'download')->count() }}
                </div>
                <div class="text-sm text-gray-600">Downloads</div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-yellow-600">
                    {{ $logs->where('action', 'access')->count() }}
                </div>
                <div class="text-sm text-gray-600">Access Attempts</div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-red-600">
                    {{ $logs->where('action', 'access_denied')->count() }}
                </div>
                <div class="text-sm text-gray-600">Failed Access</div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-gray-600">
                    {{ $logs->where('action', 'delete')->count() }}
                </div>
                <div class="text-sm text-gray-600">Deletions</div>
            </div>
        </div>

        <!-- Activity Logs Table -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Agent</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ $log->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->created_at->format('h:i:s A') }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($log->action === 'upload') bg-green-100 text-green-800
                                        @elseif($log->action === 'download') bg-blue-100 text-blue-800
                                        @elseif($log->action === 'access') bg-yellow-100 text-yellow-800
                                        @elseif($log->action === 'access_denied') bg-red-100 text-red-800
                                        @elseif($log->action === 'delete') bg-gray-100 text-gray-800
                                        @else bg-purple-100 text-purple-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($log->file)
                                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($log->file->title, 30) }}</div>
                                        <div class="text-xs text-gray-500">
                                            <code class="bg-gray-100 px-1 rounded">{{ $log->file->short_code }}</code>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400 italic">File deleted</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $log->description }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <code class="text-xs bg-gray-100 px-2 py-1 rounded font-mono">{{ $log->ip_address }}</code>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate">
                                    {{ $log->user_agent ?: 'Unknown' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    No activity logs found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $logs->withQueryString()->links() }}
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="font-semibold text-blue-900 mb-2">Activity Types Legend:</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 text-sm">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Upload</span>
                    <span class="text-gray-700">New file uploaded to the system</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Download</span>
                    <span class="text-gray-700">File successfully downloaded</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Access</span>
                    <span class="text-gray-700">File access page visited</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Access Denied</span>
                    <span class="text-gray-700">Failed password attempt</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Delete</span>
                    <span class="text-gray-700">File deleted by admin</span>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
