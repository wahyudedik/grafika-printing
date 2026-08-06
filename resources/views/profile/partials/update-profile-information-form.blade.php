<section>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Profile Information') }}</h2>

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('user.profile.update') }}">
            @csrf
            @method('patch')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="name">{{ __('Name') }}</label>
                <input id="name" name="name" type="text"
                    class="block w-full rounded-lg border {{ $errors->has('name') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-primary-500 focus:ring-primary-500' }} px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-opacity-50"
                    value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="email">{{ __('Email') }}</label>
                <input id="email" name="email" type="email"
                    class="block w-full rounded-lg border {{ $errors->has('email') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-primary-500 focus:ring-primary-500' }} px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-opacity-50"
                    value="{{ old('email', $user->email) }}" required autocomplete="username" />
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                    <div class="flex items-start gap-3 p-4 mt-3 text-yellow-800 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5"></i>
                        <div class="text-sm">
                            {{ __('Your email address is unverified.') }}
                            <button form="send-verification" class="font-medium text-primary-600 hover:text-primary-800 ml-1 underline">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </div>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <div class="flex items-center gap-3 p-4 mt-3 text-green-800 bg-green-50 border border-green-200 rounded-lg">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span class="text-sm font-medium">{{ __('A new verification link has been sent to your email address.') }}</span>
                        </div>
                    @endif
                @endif
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">
                    {{ __('Save') }}
                </button>

                @if (session('status') === 'profile-updated')
                    <span class="text-sm text-green-600 font-medium">{{ __('Saved.') }}</span>
                @endif
            </div>
        </form>
    </div>
</section>
