<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div class="grid grid-cols-1 gap-6">
            <div>
                <x-input-label for="update_password_current_password" :value="__('Current Password')" class="text-sm font-semibold text-gray-700" />
                <div class="relative mt-2">
                    <x-text-input id="update_password_current_password" name="current_password" type="password" class="block w-full px-4 py-3 rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" autocomplete="current-password" />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                </div>
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="update_password_password" :value="__('New Password')" class="text-sm font-semibold text-gray-700" />
                    <div class="relative mt-2">
                        <x-text-input id="update_password_password" name="password" type="password" class="block w-full px-4 py-3 rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" autocomplete="new-password" />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="text-sm font-semibold text-gray-700" />
                    <div class="relative mt-2">
                        <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="block w-full px-4 py-3 rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" autocomplete="new-password" />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-purple-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-sm text-purple-800">
                        <p class="font-semibold mb-1">Password Requirements:</p>
                        <ul class="list-disc list-inside space-y-1 text-purple-700">
                            <li>Minimum 8 characters long</li>
                            <li>Include uppercase and lowercase letters</li>
                            <li>Include at least one number</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-gray-200">
            <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:from-purple-700 hover:to-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="inline-flex items-center px-4 py-2 bg-green-50 border border-green-200 rounded-lg"
                >
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium text-green-700">{{ __('Password updated successfully!') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>
