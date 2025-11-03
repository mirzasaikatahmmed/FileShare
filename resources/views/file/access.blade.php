<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access File - {{ $file->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-50 to-blue-100 min-h-screen">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-md mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Password Required</h1>
                <p class="text-gray-600">This file is password protected</p>
            </div>

            <!-- File Info Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
                <div class="flex items-center gap-4 mb-6 pb-6 border-b">
                    <div class="text-4xl">📄</div>
                    <div class="flex-1">
                        <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $file->title }}</h2>
                        <p class="text-sm text-gray-600">{{ $file->original_name }}</p>
                        <p class="text-sm text-gray-500 mt-1">Size: {{ $file->formatted_size }}</p>
                    </div>
                </div>

                <!-- Password Form -->
                <form action="{{ route('file.verify', $file->short_code) }}" method="POST">
                    @csrf

                    @if($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                            @foreach($errors->all() as $error)
                                <p class="text-sm">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="mb-6">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            Enter Password
                        </label>
                        <input type="password"
                               name="password"
                               id="password"
                               required
                               autofocus
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               placeholder="Enter password to access file">
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                        🔓 Unlock File
                    </button>
                </form>
            </div>

            <!-- Additional Info -->
            <div class="bg-white rounded-lg p-4 text-sm text-gray-600">
                <div class="flex items-start gap-2">
                    <span class="text-blue-600">ℹ️</span>
                    <div>
                        <p class="font-semibold text-gray-900 mb-1">Need help?</p>
                        <p>Contact the person who shared this file with you to get the password.</p>
                        @if($file->expires_at)
                            <p class="mt-2 text-yellow-600">
                                <span class="font-semibold">⏰ Expires:</span> {{ $file->expires_at->diffForHumans() }}
                            </p>
                        @endif
                        @if($file->max_downloads)
                            <p class="mt-1">
                                <span class="font-semibold">Downloads:</span> {{ $file->downloads_count }} / {{ $file->max_downloads }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
