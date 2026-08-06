@extends('layouts.vendor')

@section('title', 'Create User')
@section('content')
    <div class="max-w-2xl mx-auto">
        <form action="{{ route('vendor.users.store') }}" method="POST"
            onsubmit="showLoading('Creating user...')" enctype="multipart/form-data">
            @csrf

            <div class="bg-white rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Create New User</h3>
                </div>

                <div class="px-6 py-6 space-y-5">
                    {{-- Full Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter full name"
                            class="block w-full rounded-lg border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter email address"
                            class="block w-full rounded-lg border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" placeholder="Enter password"
                            class="block w-full rounded-lg border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" placeholder="Confirm password"
                            class="block w-full rounded-lg border {{ $errors->has('password_confirmation') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Usertype --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Usertype <span class="text-red-500">*</span></label>
                        <select name="usertype"
                            class="block w-full rounded-lg border {{ $errors->has('usertype') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                            <option value="">Select usertype</option>
                            <option value="dev" {{ old('usertype') == 'dev' ? 'selected' : '' }}>Dev</option>
                            <option value="vendor" {{ old('usertype') == 'vendor' ? 'selected' : '' }}>Vendor</option>
                        </select>
                        @error('usertype')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-end gap-3">
                    <a href="{{ route('vendor.users.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fa-solid fa-xmark"></i> Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                        <i class="fa-solid fa-floppy-disk"></i> Save
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
