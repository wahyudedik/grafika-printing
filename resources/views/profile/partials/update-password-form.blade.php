<section>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Update Password') }}</h2>

        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="update_password_current_password">{{ __('Current Password') }}</label>
                <input id="update_password_current_password" name="current_password" type="password"
                    class="block w-full rounded-lg border {{ $errors->has('current_password') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-primary-500 focus:ring-primary-500' }} px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-opacity-50"
                    autocomplete="current-password" />
                @error('current_password', 'updatePassword')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="update_password_password">{{ __('New Password') }}</label>
                <input id="update_password_password" name="password" type="password"
                    class="block w-full rounded-lg border {{ $errors->has('password') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-primary-500 focus:ring-primary-500' }} px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-opacity-50"
                    autocomplete="new-password" />
                @error('password', 'updatePassword')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="update_password_password_confirmation">{{ __('Confirm Password') }}</label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                    class="block w-full rounded-lg border {{ $errors->has('password_confirmation') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-primary-500 focus:ring-primary-500' }} px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-opacity-50"
                    autocomplete="new-password" />
                @error('password_confirmation', 'updatePassword')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">
                    {{ __('Save') }}
                </button>

                @if (session('status') === 'password-updated')
                    <span class="text-sm text-green-600 font-medium">{{ __('Saved.') }}</span>
                @endif
            </div>
        </form>
    </div>
</section>
