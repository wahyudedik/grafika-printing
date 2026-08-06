@extends('dev.layouts.app')

@section('title', 'Edit Vendor')
@section('content')
    <div class="max-w-3xl mx-auto">
        <form action="{{ route('admin.vendors.update', $vendor->id) }}" method="POST" class="space-y-6" data-loading enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Company Information --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Company Information</h3>
                </div>
                <div class="p-4 grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Company Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $vendor->name ?? '') }}" placeholder="Enter company name"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('name') border-red-500 @enderror">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $vendor->email) }}" placeholder="Enter company email"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('email') border-red-500 @enderror">
                        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $vendor->phone) }}" placeholder="Enter company phone"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('phone') border-red-500 @enderror">
                        @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                        <input type="text" name="website" value="{{ old('website', $vendor->website) }}" placeholder="Enter company website"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('website') border-red-500 @enderror">
                        @error('website') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                        <textarea name="address" rows="3" placeholder="Enter company address"
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('address') border-red-500 @enderror">{{ old('address', $vendor->address) }}</textarea>
                        @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Company Logo</label>
                        @if ($vendor->logo)
                            <div class="mb-2">
                                <img src="{{ asset('vendors_logo/' . $vendor->logo) }}" alt="{{ $vendor->name }} Logo" class="h-20 rounded-lg object-cover">
                            </div>
                        @endif
                        <input type="file" name="logo" accept="image/png,image/jpeg,image/jpg"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('logo') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Only PNG, JPG, and JPEG. Max 2MB. Leave empty to keep current logo.</p>
                        @error('logo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <div class="flex items-center gap-3">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $vendor->is_active) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-primary-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                            </label>
                            <span class="text-sm font-medium text-gray-700">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Account Manager --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Account Manager</h3>
                </div>
                <div class="p-4">
                    @if ($users)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Associated User <span class="text-red-500">*</span></label>
                            <input type="text" value="{{ $users->name }} ({{ $users->email }})" readonly
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-500 bg-gray-50 cursor-not-allowed">
                            <input type="hidden" name="user_id" value="{{ $users->id }}">
                            <p class="mt-1 text-xs text-gray-500">The user who manages this vendor account</p>
                        </div>
                    @else
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle text-amber-600"></i>
                                <p class="text-sm text-amber-800">No user account is associated with this vendor. Please create a user first.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.vendors.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times mr-1"></i> Cancel
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-save mr-1"></i> Update
                </button>
            </div>
        </form>
    </div>
@endsection
