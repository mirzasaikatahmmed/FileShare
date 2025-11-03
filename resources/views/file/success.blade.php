<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Successful - File Share</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-100 min-h-screen">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-3xl mx-auto">
            <!-- Success Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Upload Successful!</h1>
                <p class="text-lg text-gray-600">Your file has been uploaded and is ready to share</p>
            </div>

            <!-- File Info Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
                <div class="flex items-start gap-4 mb-6 pb-6 border-b">
                    <div class="text-4xl">📄</div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $file->title }}</h2>
                        <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                            <div>
                                <span class="font-medium">Filename:</span>
                                <span class="block text-gray-900">{{ $file->original_name }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Size:</span>
                                <span class="block text-gray-900">{{ $file->formatted_size }}</span>
                            </div>
                            @if($file->expires_at)
                            <div>
                                <span class="font-medium">Expires:</span>
                                <span class="block text-gray-900">{{ $file->expires_at->format('M d, Y H:i') }}</span>
                            </div>
                            @endif
                            @if($file->max_downloads)
                            <div>
                                <span class="font-medium">Max Downloads:</span>
                                <span class="block text-gray-900">{{ $file->max_downloads }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Share URL -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Share Link</label>
                    <div class="flex gap-2">
                        <input type="text" id="shareUrl" value="{{ $shortUrl }}" readonly
                            class="flex-1 px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none text-gray-900">
                        <button onclick="copyToClipboard()" id="copyBtn"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                            📋 Copy
                        </button>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Scan QR Code to Share</h3>
                    <div class="inline-block p-4 bg-white border-4 border-gray-200 rounded-xl">
                        <img src="{{ $qrCodeUrl }}" alt="QR Code" class="w-64 h-64">
                    </div>
                    <div class="mt-4">
                        <a href="{{ $qrCodeUrl }}" download="qr-code-{{ $file->short_code }}.png"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                            📥 Download QR Code
                        </a>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-4">
                <a href="{{ route('home') }}"
                    class="flex-1 text-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                    Upload Another File
                </a>
                <a href="{{ $shortUrl }}" target="_blank"
                    class="flex-1 text-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-900 font-semibold rounded-lg transition">
                    Test Link
                </a>
            </div>

            <!-- Warning -->
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <span class="text-yellow-600 text-xl">⚠️</span>
                    <div>
                        <h4 class="font-semibold text-yellow-900 mb-1">Important!</h4>
                        <p class="text-sm text-yellow-800">
                            Save this link and share it with others. You won't be able to retrieve it later.
                            Recipients will need the password you set to download the file.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard() {
            const urlField = document.getElementById('shareUrl');
            const copyBtn = document.getElementById('copyBtn');

            urlField.select();
            urlField.setSelectionRange(0, 99999); // For mobile devices

            navigator.clipboard.writeText(urlField.value).then(() => {
                copyBtn.textContent = '✅ Copied!';
                copyBtn.classList.add('bg-green-600');
                copyBtn.classList.remove('bg-blue-600');

                setTimeout(() => {
                    copyBtn.textContent = '📋 Copy';
                    copyBtn.classList.remove('bg-green-600');
                    copyBtn.classList.add('bg-blue-600');
                }, 2000);
            });
        }
    </script>
</body>
</html>
