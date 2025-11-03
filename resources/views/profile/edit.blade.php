<x-admin-layout>
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center space-x-6">
                <!-- Profile Avatar -->
                <div class="relative">
                    <div class="w-24 h-24 rounded-full bg-white shadow-lg flex items-center justify-center text-3xl font-bold text-blue-600">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <span class="absolute bottom-0 right-0 block h-6 w-6 rounded-full bg-green-400 ring-4 ring-white"></span>
                </div>

                <!-- Profile Info -->
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-white">{{ Auth::user()->name }}</h1>
                    <p class="text-blue-100 mt-1">{{ Auth::user()->email }}</p>
                    <div class="flex items-center mt-3 space-x-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-500 text-white">
                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                            Administrator
                        </span>
                        <span class="text-blue-100 text-sm">
                            Member since {{ Auth::user()->created_at->format('M Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Forms -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Profile Information Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <h2 class="text-xl font-bold text-gray-900">Profile Information</h2>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">Update your account's profile information and email address.</p>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- Update Password Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <h2 class="text-xl font-bold text-gray-900">Security Settings</h2>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">Ensure your account is using a long, random password to stay secure.</p>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Delete Account Card -->
                <div class="bg-white rounded-xl shadow-sm border border-red-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-red-50 to-orange-50 px-6 py-4 border-b border-red-200">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <h2 class="text-xl font-bold text-gray-900">Danger Zone</h2>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">Permanently delete your account and all associated data.</p>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

            <!-- Right Column - Stats & Info -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Account Stats -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Overview</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">Total Files</p>
                                    <p class="text-xs text-gray-500">Uploaded</p>
                                </div>
                            </div>
                            <span class="text-2xl font-bold text-blue-600">{{ \App\Models\File::count() }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">Downloads</p>
                                    <p class="text-xs text-gray-500">Total</p>
                                </div>
                            </div>
                            <span class="text-2xl font-bold text-green-600">{{ \App\Models\Download::count() }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">Active Since</p>
                                    <p class="text-xs text-gray-500">Member</p>
                                </div>
                            </div>
                            <span class="text-lg font-bold text-purple-600">{{ Auth::user()->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl shadow-sm p-6 text-white">
                    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center p-3 bg-white bg-opacity-10 hover:bg-opacity-20 rounded-lg transition">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('admin.files.index') }}" class="flex items-center p-3 bg-white bg-opacity-10 hover:bg-opacity-20 rounded-lg transition">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span>Manage Files</span>
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center p-3 bg-white bg-opacity-10 hover:bg-opacity-20 rounded-lg transition">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Settings</span>
                        </a>
                    </div>
                </div>

                <!-- Account Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Details</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Role</span>
                            <span class="font-semibold text-gray-900">Administrator</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Status</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <span class="w-1.5 h-1.5 bg-green-600 rounded-full mr-1.5"></span>
                                Active
                            </span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Last Login</span>
                            <span class="font-semibold text-gray-900">{{ now()->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Email Verified</span>
                            @if(Auth::user()->hasVerifiedEmail())
                                <span class="text-green-600 font-semibold">Yes</span>
                            @else
                                <span class="text-orange-600 font-semibold">Pending</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
