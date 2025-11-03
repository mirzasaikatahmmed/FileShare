<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            <!-- Upload Settings -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Upload Settings</h3>

                    <!-- Allowed File Extensions -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Allowed File Extensions</label>
                        <p class="text-sm text-gray-500 mb-3">Select which file types users can upload. Be careful with executable files!</p>

                        @foreach ($allExtensions as $category => $extensions)
                            <div class="mb-4">
                                <h4 class="font-medium text-gray-700 mb-2">{{ $category }}</h4>
                                <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-2">
                                    @foreach ($extensions as $ext)
                                        @php
                                            $isDangerous = in_array($ext, $dangerousExtensions);
                                            $isChecked = in_array($ext, old('allowed_extensions', $settings['upload']['allowed_extensions']));
                                        @endphp
                                        <label class="flex items-center space-x-2 p-2 rounded {{ $isDangerous ? 'bg-red-50 border border-red-300' : 'bg-gray-50' }}">
                                            <input type="checkbox"
                                                   name="allowed_extensions[]"
                                                   value="{{ $ext }}"
                                                   {{ $isChecked ? 'checked' : '' }}
                                                   class="rounded {{ $isDangerous ? 'text-red-600 focus:ring-red-500' : 'text-blue-600 focus:ring-blue-500' }}">
                                            <span class="text-sm {{ $isDangerous ? 'text-red-700 font-medium' : 'text-gray-700' }}">
                                                .{{ $ext }}
                                                @if ($isDangerous)
                                                    <span class="text-red-600">⚠</span>
                                                @endif
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        @if (!empty(array_intersect(old('allowed_extensions', $settings['upload']['allowed_extensions']), $dangerousExtensions)))
                            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-300 rounded">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="confirm_dangerous" class="rounded text-yellow-600 focus:ring-yellow-500">
                                    <span class="text-sm text-yellow-800">I understand the security risks of allowing executable file types</span>
                                </label>
                            </div>
                        @endif
                    </div>

                    <!-- Max File Size -->
                    <div class="mb-4">
                        <label for="max_file_size" class="block text-sm font-medium text-gray-700 mb-2">
                            Maximum File Size (MB)
                        </label>
                        <input type="number"
                               id="max_file_size"
                               name="max_file_size"
                               value="{{ old('max_file_size', $settings['upload']['max_file_size']) }}"
                               min="1"
                               max="1000"
                               class="w-full md:w-1/4 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Maximum: 1000 MB</p>
                    </div>

                    <!-- Max Uploads Per Day -->
                    <div class="mb-4">
                        <label for="max_uploads_per_day" class="block text-sm font-medium text-gray-700 mb-2">
                            Maximum Uploads Per Day (Per IP)
                        </label>
                        <input type="number"
                               id="max_uploads_per_day"
                               name="max_uploads_per_day"
                               value="{{ old('max_uploads_per_day', $settings['upload']['max_uploads_per_day']) }}"
                               min="1"
                               max="1000"
                               class="w-full md:w-1/4 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Prevents abuse by limiting uploads per IP address</p>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Security Settings</h3>

                    <div class="space-y-4">
                        <label class="flex items-start space-x-3">
                            <input type="checkbox"
                                   name="require_password"
                                   value="1"
                                   {{ old('require_password', $settings['security']['require_password']) ? 'checked' : '' }}
                                   class="mt-1 rounded text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-medium text-gray-700">Require Password for All Uploads</span>
                                <p class="text-xs text-gray-500">Force users to set a password when uploading files</p>
                            </div>
                        </label>

                        <label class="flex items-start space-x-3">
                            <input type="checkbox"
                                   name="scan_files"
                                   value="1"
                                   {{ old('scan_files', $settings['security']['scan_files']) ? 'checked' : '' }}
                                   class="mt-1 rounded text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-medium text-gray-700">Scan Files for Malware</span>
                                <p class="text-xs text-gray-500">Check uploaded files for viruses and malware (requires ClamAV)</p>
                            </div>
                        </label>

                        <label class="flex items-start space-x-3">
                            <input type="checkbox"
                                   name="block_executable"
                                   value="1"
                                   {{ old('block_executable', $settings['security']['block_executable']) ? 'checked' : '' }}
                                   class="mt-1 rounded text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-medium text-gray-700">Block Executable Files</span>
                                <p class="text-xs text-gray-500">Automatically block .exe, .bat, .sh, and other executable formats</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Storage Settings -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Storage Settings</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="default_expiry_days" class="block text-sm font-medium text-gray-700 mb-2">
                                Default Expiry (Days)
                            </label>
                            <input type="number"
                                   id="default_expiry_days"
                                   name="default_expiry_days"
                                   value="{{ old('default_expiry_days', $settings['storage']['default_expiry_days']) }}"
                                   min="1"
                                   max="365"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="max_expiry_days" class="block text-sm font-medium text-gray-700 mb-2">
                                Maximum Expiry (Days)
                            </label>
                            <input type="number"
                                   id="max_expiry_days"
                                   name="max_expiry_days"
                                   value="{{ old('max_expiry_days', $settings['storage']['max_expiry_days']) }}"
                                   min="1"
                                   max="365"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="flex items-start space-x-3">
                            <input type="checkbox"
                                   name="auto_delete_after_expiry"
                                   value="1"
                                   {{ old('auto_delete_after_expiry', $settings['storage']['auto_delete_after_expiry']) ? 'checked' : '' }}
                                   class="mt-1 rounded text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-medium text-gray-700">Auto Delete After Expiry</span>
                                <p class="text-xs text-gray-500">Automatically delete files when they expire</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Download Settings -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Download Settings</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="default_max_downloads" class="block text-sm font-medium text-gray-700 mb-2">
                                Default Max Downloads
                            </label>
                            <input type="number"
                                   id="default_max_downloads"
                                   name="default_max_downloads"
                                   value="{{ old('default_max_downloads', $settings['download']['default_max_downloads']) }}"
                                   min="1"
                                   max="1000"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="max_downloads_limit" class="block text-sm font-medium text-gray-700 mb-2">
                                Maximum Downloads Limit
                            </label>
                            <input type="number"
                                   id="max_downloads_limit"
                                   name="max_downloads_limit"
                                   value="{{ old('max_downloads_limit', $settings['download']['max_downloads_limit']) }}"
                                   min="1"
                                   max="10000"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex items-center justify-end space-x-4 mb-6">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
