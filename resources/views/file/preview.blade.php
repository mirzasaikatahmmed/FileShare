<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $file->title }} - File Preview</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <!-- File Info Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-start gap-4">
                        <div class="text-5xl">📄</div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $file->title }}</h1>
                            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                <span class="flex items-center gap-1">
                                    📝 {{ $file->original_name }}
                                </span>
                                <span class="flex items-center gap-1">
                                    💾 {{ $file->formatted_size }}
                                </span>
                                <span class="flex items-center gap-1">
                                    📅 {{ $file->created_at->format('M d, Y') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    ⬇️ {{ $file->downloads_count }} downloads
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Download Button -->
                <a href="{{ route('file.download', $file->short_code) }}"
                    class="block w-full text-center bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-4 px-8 rounded-lg transition transform hover:scale-105 shadow-lg mb-6">
                    ⬇️ Download File
                </a>

                <!-- File Preview -->
                @if(str_starts_with($file->mime_type, 'image/'))
                    <div class="border rounded-lg overflow-hidden bg-gray-50 p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Preview:</h3>
                        <img src="{{ route('file.download', $file->short_code) }}"
                             alt="{{ $file->title }}"
                             class="max-w-full h-auto mx-auto rounded-lg shadow-md">
                    </div>
                @elseif($file->mime_type === 'application/pdf')
                    <div class="border rounded-lg overflow-hidden bg-gray-50 p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">PDF Preview:</h3>
                        <iframe src="{{ route('file.download', $file->short_code) }}"
                                class="w-full h-96 rounded-lg"
                                frameborder="0"></iframe>
                    </div>
                @elseif(str_starts_with($file->mime_type, 'video/'))
                    <div class="border rounded-lg overflow-hidden bg-gray-50 p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Video Preview:</h3>
                        <video controls class="w-full rounded-lg">
                            <source src="{{ route('file.download', $file->short_code) }}" type="{{ $file->mime_type }}">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                @elseif(str_starts_with($file->mime_type, 'audio/'))
                    <div class="border rounded-lg overflow-hidden bg-gray-50 p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Audio Preview:</h3>
                        <audio controls class="w-full">
                            <source src="{{ route('file.download', $file->short_code) }}" type="{{ $file->mime_type }}">
                            Your browser does not support the audio element.
                        </audio>
                    </div>
                @else
                    <div class="border rounded-lg bg-gray-50 p-8 text-center">
                        <div class="text-6xl mb-4">📁</div>
                        <p class="text-gray-600 mb-2">Preview not available for this file type</p>
                        <p class="text-sm text-gray-500">Click the download button above to get the file</p>
                    </div>
                @endif
            </div>

            <!-- File Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg p-6 shadow">
                    <h3 class="font-semibold text-gray-900 mb-3">File Information</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Type:</span>
                            <span class="font-medium text-gray-900">{{ $file->mime_type }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Extension:</span>
                            <span class="font-medium text-gray-900">.{{ $file->extension }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Uploaded:</span>
                            <span class="font-medium text-gray-900">{{ $file->created_at->diffForHumans() }}</span>
                        </div>
                        @if($file->expires_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Expires:</span>
                            <span class="font-medium text-red-600">{{ $file->expires_at->diffForHumans() }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 shadow">
                    <h3 class="font-semibold text-gray-900 mb-3">Download Statistics</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Downloads:</span>
                            <span class="font-medium text-gray-900">{{ $file->downloads_count }}</span>
                        </div>
                        @if($file->max_downloads)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Remaining:</span>
                            <span class="font-medium text-blue-600">{{ $file->max_downloads - $file->downloads_count }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium text-green-600">✓ Available</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-6 text-center">
                <a href="{{ route('home') }}"
                    class="inline-block px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-900 font-semibold rounded-lg transition">
                    ← Upload Your Own File
                </a>
            </div>
        </div>
    </div>
</body>
</html>
