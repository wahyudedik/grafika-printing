<section x-data="{ showDeleteModal: false }">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-l-4 border-red-500 bg-red-50">
            <h3 class="text-lg font-semibold text-red-700">{{ __('Delete Account') }}</h3>
            <p class="text-sm text-red-600 mt-1">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
            </p>
        </div>
        <div class="px-6 py-4">
            <button type="button" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors"
                @click="showDeleteModal = true">
                {{ __('Delete Account') }}
            </button>
        </div>
    </div>

    {{-- Delete Confirmation Modal (Alpine.js) --}}
    <div x-show="showDeleteModal" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity"
            x-show="showDeleteModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            @click="showDeleteModal = false"></div>

        {{-- Modal Panel --}}
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-sm w-full transform transition-all"
                x-show="showDeleteModal" x-cloak
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.outside="showDeleteModal = false">

                <form method="post" action="{{ route('user.profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="h-1 bg-red-500 rounded-t-xl"></div>
                    <div class="px-6 py-6 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Are you sure you want to delete your account?') }}</h3>
                        <p class="text-sm text-gray-500">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>

                        <div class="mt-4">
                            <input id="password" name="password" type="password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition @error('password', 'userDeletion') border-red-500 @enderror"
                                placeholder="{{ __('Password') }}" />

                            @error('password', 'userDeletion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                        <button type="button" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                            @click="showDeleteModal = false">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                            {{ __('Delete Account') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
