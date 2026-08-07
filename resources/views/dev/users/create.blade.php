@extends('dev.layouts.app')

@section('title', 'Create User')
@section('content')
    <div class="max-w-2xl mx-auto">
        <form action="{{ route('admin.users.store') }}" method="POST" class="bg-white rounded-xl border border-gray-200 overflow-hidden" data-loading>
            @csrf
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Create New User</h3>
            </div>
            <div class="p-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter full name"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter email address"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" placeholder="Enter password"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" placeholder="Confirm password"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('password_confirmation') border-red-500 @enderror">
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Usertype <span class="text-red-500">*</span></label>
                    <select name="usertype"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('usertype') border-red-500 @enderror">
                        <option value="">Select usertype</option>
                        <option value="dev" {{ old('usertype') == 'dev' ? 'selected' : '' }}>Dev</option>
                        <option value="vendor" {{ old('usertype') == 'vendor' ? 'selected' : '' }}>Vendor</option>
                    </select>
                    @error('usertype')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-4 py-3 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <x.ui.button type="button" variant="outline" href="{{ route('admin.users.index') }}">
                    <i class="fas fa-times mr-1"></i> Cancel
                </x.ui.button>
                <x.ui.button type="submit" variant="primary">
                    <i class="fas fa-save mr-1"></i> Save
                </x.ui.button>
            </div>
        </form>
    </div>
@endsection
