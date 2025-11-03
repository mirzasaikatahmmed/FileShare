<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('File Details') }}
            </h2>
            <a href="{{ route('admin.files.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                ← Back to Files
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- File Information -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
            <div class="p-6">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $file->title }}</h3>
                        <p class="text-gray-600">{{ $file->original_name }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('file.access', $file->short_code) }}" target="_blank"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            View Public Link
                        </a>
                        <form method="POST" action="{{ route('admin.files.destroy', $file->id) }}" class="inline"
                            onsubmit="return confirm('Are you sure you want to delete this file? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                Delete File
                            </button>
                        </form>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- File Size -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm text-gray-600 mb-1">File Size</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $file->formatted_size }}</div>
                    </div>

                    <!-- Downloads -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm text-gray-600 mb-1">Total Downloads</div>
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($file->downloads_count) }}</div>
                        @if($file->max_downloads)
                        <div class="text-xs text-gray-500 mt-1">Limit: {{ $file->max_downloads }}</div>
                        @endif
                    </div>

                    <!-- Status -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm text-gray-600 mb-1">Status</div>
                        <div class="mt-1">
                            @if($file->isAvailable())
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                    Expired
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Uploaded -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm text-gray-600 mb-1">Uploaded</div>
                        <div class="text-lg font-semibold text-gray-900">{{ $file->created_at->format('M d, Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $file->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- File Metadata -->
            <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">File Metadata</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Short Code</dt>
                            <dd class="mt-1">
                                <code class="bg-gray-100 px-3 py-1 rounded text-sm">{{ $file->short_code }}</code>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">File Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">.{{ strtoupper($file->extension) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">MIME Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $file->mime_type }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Stored Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono truncate">{{ $file->stored_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Uploader IP</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $file->ip_address }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Expires At</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($file->expires_at)
                                    {{ $file->expires_at->format('M d, Y H:i') }}
                                    @if($file->isExpired())
                                        <span class="text-red-600">(Expired)</span>
                                    @endif
                                @else
                                    <span class="text-gray-500">Never</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Max Downloads</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($file->max_downloads)
                                    {{ $file->max_downloads }}
                                    @if($file->hasReachedDownloadLimit())
                                        <span class="text-red-600">(Limit Reached)</span>
                                    @endif
                                @else
                                    <span class="text-gray-500">Unlimited</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Public Link</dt>
                            <dd class="mt-1 text-sm">
                                <a href="{{ route('file.access', $file->short_code) }}" target="_blank" class="text-blue-600 hover:text-blue-800 truncate block">
                                    {{ route('file.access', $file->short_code) }}
                                </a>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- QR Code -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">QR Code</h3>
                    @if($file->qr_code_path)
                    <div class="text-center">
                        <img src="{{ asset('storage/' . $file->qr_code_path) }}" alt="QR Code" class="mx-auto rounded-lg border-2 border-gray-200">
                        <a href="{{ asset('storage/' . $file->qr_code_path) }}" download class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                            Download QR Code
                        </a>
                    </div>
                    @else
                    <p class="text-gray-500 text-center">No QR code available</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Download History -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Download History (Last 50)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Agent</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($file->downloads as $download)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $download->downloaded_at->format('M d, Y H:i:s') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-mono">
                                    {{ $download->ip_address }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    @if($download->city && $download->country)
                                        {{ $download->city }}, {{ $download->country }}
                                    @else
                                        <span class="text-gray-400">Unknown</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 max-w-md truncate">
                                    {{ $download->user_agent ?: 'Unknown' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    No downloads yet
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Activity Log -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Log (Last 20)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($file->activityLogs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $log->created_at->format('M d, Y H:i:s') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($log->action === 'upload') bg-green-100 text-green-800
                                        @elseif($log->action === 'download') bg-blue-100 text-blue-800
                                        @elseif($log->action === 'access') bg-yellow-100 text-yellow-800
                                        @elseif($log->action === 'access_denied') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $log->description }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-mono">
                                    {{ $log->ip_address }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
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
