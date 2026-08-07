@extends('dev.layouts.app')

@section('title', 'Create Vendor')
@section('content')
    <div class="max-w-2xl mx-auto">
        <form action="{{ route('admin.vendors.store') }}" method="POST" class="bg-white rounded-xl border border-gray-200 overflow-hidden" data-loading enctype="multipart/form-data">
            @csrf
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Create New Vendor</h3>
            </div>
            <div class="p-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter vendor name"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('name') border-red-500 @enderror">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter email address"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('email') border-red-500 @enderror">
                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="Enter phone number 0876*******"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('phone') border-red-500 @enderror">
                    @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                    <textarea name="address" rows="3" placeholder="Enter address"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                    @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                    <input type="url" name="website" value="{{ old('website') }}" placeholder="Enter website URL"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('website') border-red-500 @enderror">
                    @error('website') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                    <input type="file" name="logo" accept="image/png,image/jpeg,image/jpg"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('logo') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Only PNG, JPG, and JPEG files are allowed. Maximum size: 2MB.</p>
                    @error('logo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Users <span class="text-red-500">*</span></label>
                    <select name="user_id"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('user_id') border-red-500 @enderror">
                        <option value="">Select User</option>
                        @foreach ($users as $user)
                            @if ($user->usertype == 'vendor')
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('user_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-primary-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                    </label>
                    <span class="text-sm font-medium text-gray-700">Active Status</span>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-4 py-3 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <x.ui.button type="button" variant="outline" href="{{ route('admin.vendors.index') }}">
                    <i class="fas fa-times mr-1"></i> Cancel
                </x.ui.button>
                <x.ui.button type="submit" variant="primary">
                    <i class="fas fa-save mr-1"></i> Save
                </x.ui.button>
            </div>
        </form>
    </div>
@endsection
