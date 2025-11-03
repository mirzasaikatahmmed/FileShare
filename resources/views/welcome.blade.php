<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Share - Upload & Share Files Securely</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-5xl font-bold text-gray-900 mb-3">
                    <span class="text-blue-600">📁</span> File Share
                </h1>
                <p class="text-lg text-gray-600">Upload and share files securely with password protection</p>
            </div>

            <!-- Upload Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                @if($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf

                    <!-- File Upload -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Select File <span class="text-red-500">*</span>
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-blue-400 transition" id="dropArea">
                            <input type="file" name="file" id="fileInput" class="hidden" required>
                            <div id="filePreview">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">
                                    <span class="font-semibold text-blue-600">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-xs text-gray-500 mt-1">Max 100MB</p>
                            </div>
                            <div id="fileName" class="hidden mt-2 text-sm text-gray-700"></div>
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                            File Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Enter a descriptive title for your file">
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" id="password" required minlength="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Enter password to protect your file">
                        <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded-lg transition duration-200 transform hover:scale-105 shadow-lg">
                        🚀 Upload & Generate Share Link
                    </button>
                </form>
            </div>

            <!-- Features -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-2xl mb-2">🔒</div>
                    <h3 class="font-semibold text-gray-800">Secure</h3>
                    <p class="text-sm text-gray-600">Password protected</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-2xl mb-2">📱</div>
                    <h3 class="font-semibold text-gray-800">QR Code</h3>
                    <p class="text-sm text-gray-600">Easy sharing</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-2xl mb-2">⚡</div>
                    <h3 class="font-semibold text-gray-800">Fast</h3>
                    <p class="text-sm text-gray-600">Instant uploads</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const dropArea = document.getElementById('dropArea');
        const fileInput = document.getElementById('fileInput');
        const filePreview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');

        dropArea.addEventListener('click', () => fileInput.click());

        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.classList.add('border-blue-400', 'bg-blue-50');
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.classList.remove('border-blue-400', 'bg-blue-50');
        });

        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.classList.remove('border-blue-400', 'bg-blue-50');
            const files = e.dataTransfer.files;
            fileInput.files = files;
            updateFileName();
        });

        fileInput.addEventListener('change', updateFileName);

        function updateFileName() {
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                filePreview.classList.add('hidden');
                fileName.classList.remove('hidden');
                fileName.innerHTML = `
                    <div class="flex items-center justify-center">
                        <span class="font-semibold">📄 ${file.name}</span>
                        <span class="ml-2 text-gray-500">(${formatBytes(file.size)})</span>
                    </div>
                `;
            }
        }

        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
    </script>
</body>
</html>
