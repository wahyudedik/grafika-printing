@extends('layouts.user')

@section('title', 'Edit Profile')

@section('content')
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Profile') }}</h1>

        {{-- vendor and user profile in one form --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Profile Information</h2>

            @if (session('status') === 'vendor-profile-updated' || session('status') === 'profile-updated')
                <div class="flex items-center gap-3 p-4 mb-4 text-green-800 bg-green-50 border border-green-200 rounded-lg">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <span class="text-sm font-medium">Profile has been updated.</span>
                </div>
            @endif

            <form method="post" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('patch')

                @if (auth()->user()->usertype === 'vendor' && isset($vendor))
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Vendor Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
                        <div class="md:col-span-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Logo</label>
                            @if ($vendor->logo)
                                <img src="{{ asset('vendors_logo/' . $vendor->logo) }}" alt="Vendor Logo"
                                    class="w-full max-h-36 object-contain rounded-lg border border-gray-200 mb-2">
                            @else
                                <p class="text-sm text-gray-500">No logo uploaded</p>
                            @endif
                            <label class="block text-sm font-medium text-gray-700 mb-1 mt-3">Update Logo</label>
                            <input type="file" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                                id="logo" name="logo" accept="image/png,image/jpeg,image/jpg">
                            @error('logo')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-8">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                                    <input type="text" class="block w-full rounded-lg border {{ $errors->has('vendor_name') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                        id="vendor_name" name="vendor_name" value="{{ old('vendor_name', $vendor->name) }}" required>
                                    @error('vendor_name')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Company Email</label>
                                    <input type="email" class="block w-full rounded-lg border {{ $errors->has('vendor_email') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                        id="vendor_email" name="vendor_email" value="{{ old('vendor_email', $vendor->email) }}" required>
                                    @error('vendor_email')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                    <input type="text" class="block w-full rounded-lg border {{ $errors->has('phone') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                        id="phone" name="phone" value="{{ old('phone', $vendor->phone) }}" required>
                                    @error('phone')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Website <span class="text-gray-400">(optional)</span></label>
                                    <input type="url" class="block w-full rounded-lg border {{ $errors->has('website') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                        id="website" name="website" value="{{ old('website', $vendor->website) }}">
                                    @error('website')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <textarea class="block w-full rounded-lg border {{ $errors->has('address') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                    id="address" name="address" rows="3" required>{{ old('address', $vendor->address) }}</textarea>
                                @error('address')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif

                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">User Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" class="block w-full rounded-lg border {{ $errors->has('name') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" class="block w-full rounded-lg border {{ $errors->has('email') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>

        <div class="mb-4">
            @include('profile.partials.update-password-form')
        </div>

        <div class="mb-4">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
@endsection
