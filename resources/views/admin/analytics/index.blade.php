<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Analytics & Reports') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Overall Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Files -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-5">
                            <div class="text-sm font-medium text-gray-500">Total Files</div>
                            <div class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_files']) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Downloads -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </div>
                        <div class="ml-5">
                            <div class="text-sm font-medium text-gray-500">Total Downloads</div>
                            <div class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_downloads']) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Files -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5">
                            <div class="text-sm font-medium text-gray-500">Active Files</div>
                            <div class="text-2xl font-semibold text-gray-900">{{ number_format($stats['active_files']) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Storage -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                            </svg>
                        </div>
                        <div class="ml-5">
                            <div class="text-sm font-medium text-gray-500">Total Storage</div>
                            <div class="text-2xl font-semibold text-gray-900">{{ $stats['total_storage'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expired Files -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5">
                            <div class="text-sm font-medium text-gray-500">Expired Files</div>
                            <div class="text-2xl font-semibold text-gray-900">{{ number_format($stats['expired_files']) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Avg Downloads -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div class="ml-5">
                            <div class="text-sm font-medium text-gray-500">Avg Downloads/File</div>
                            <div class="text-2xl font-semibold text-gray-900">{{ $stats['avg_downloads_per_file'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Upload Trend (Last 30 Days) -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Upload Trend (Last 30 Days)</h3>
                    <div class="space-y-2">
                        @forelse($uploadStats as $stat)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">{{ \Carbon\Carbon::parse($stat->date)->format('M d') }}</span>
                                <span class="font-semibold text-gray-900">{{ $stat->count }} files ({{ round($stat->total_size / 1024 / 1024, 2) }} MB)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(($stat->count / max($uploadStats->max('count'), 1)) * 100, 100) }}%"></div>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-8">No upload data available</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Download Trend (Last 30 Days) -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Download Trend (Last 30 Days)</h3>
                    <div class="space-y-2">
                        @forelse($downloadStats as $stat)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">{{ \Carbon\Carbon::parse($stat->date)->format('M d') }}</span>
                                <span class="font-semibold text-gray-900">{{ $stat->count }} downloads</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ min(($stat->count / max($downloadStats->max('count'), 1)) * 100, 100) }}%"></div>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-8">No download data available</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- File Types Distribution -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">File Types Distribution</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach($fileTypes as $type)
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ $type->count }}</div>
                        <div class="text-sm text-gray-600 uppercase mt-1">.{{ $type->extension }}</div>
                        <div class="text-xs text-gray-500 mt-2">
                            {{ round($type->total_size / 1024 / 1024, 2) }} MB
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Top Downloaded Files -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 10 Downloaded Files</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Downloads</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($topFiles as $index => $file)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full
                                        @if($index === 0) bg-yellow-100 text-yellow-800
                                        @elseif($index === 1) bg-gray-100 text-gray-800
                                        @elseif($index === 2) bg-orange-100 text-orange-800
                                        @else bg-blue-100 text-blue-800
                                        @endif font-bold">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ Str::limit($file->title, 40) }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($file->original_name, 40) }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    .{{ strtoupper($file->extension) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $file->formatted_size }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ number_format($file->downloads_count) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $file->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    No files found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Storage by Month -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Storage by Month (Last 12 Months)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Files Uploaded</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Size</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Storage Bar</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($storageByMonth as $storage)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::create($storage->year, $storage->month, 1)->format('F Y') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($storage->count) }} files
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ round($storage->total_size / 1024 / 1024 / 1024, 2) }} GB
                                </td>
                                <td class="px-4 py-3">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ min(($storage->total_size / max($storageByMonth->max('total_size'), 1)) * 100, 100) }}%"></div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    No storage data available
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity (Last 20)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentActivity as $activity)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $activity->created_at->diffForHumans() }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($activity->action === 'upload') bg-green-100 text-green-800
                                        @elseif($activity->action === 'download') bg-blue-100 text-blue-800
                                        @elseif($activity->action === 'access') bg-yellow-100 text-yellow-800
                                        @elseif($activity->action === 'access_denied') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    @if($activity->file)
                                        {{ Str::limit($activity->file->title, 30) }}
                                    @else
                                        <span class="text-gray-400">File deleted</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $activity->description }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-mono">
                                    {{ $activity->ip_address }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    No activity logs yet
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
