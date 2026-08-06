@extends('dev.layouts.app')

@section('title', 'Vendor Details')
@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        {{-- Company Information --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Company Information</h3>
            </div>
            <div class="p-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-500">Company Name</label>
                        <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $vendor->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Email</label>
                        <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $vendor->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Phone</label>
                        <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $vendor->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Website</label>
                        <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $vendor->website ?? 'Not provided' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-xs text-gray-500">Address</label>
                        <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $vendor->address ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Status</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $vendor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                </div>

                @if ($vendor->logo)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <label class="text-xs text-gray-500">Company Logo</label>
                        <div class="mt-1">
                            <img src="{{ asset('vendors_logo/' . $vendor->logo) }}" alt="{{ $vendor->name }} Logo" class="h-20 rounded-lg object-cover">
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Account Manager --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Account Manager</h3>
            </div>
            <div class="p-4">
                @if ($users)
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-gray-500">Full Name</label>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $users->name }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Email</label>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $users->email }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">User Type</label>
                            <p class="mt-1">
                                @if ($users->usertype == 'dev')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Developer</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Vendor</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Email Verified</label>
                            <p class="mt-1">
                                @if ($users->email_verified_at)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Verified ({{ $users->email_verified_at->format('d M Y H:i') }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Not Verified</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Account Created</label>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $users->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Last Updated</label>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $users->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                @else
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            <p class="text-sm text-blue-800">No user account is associated with this vendor.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.vendors.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
            <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
        </div>
    </div>
@endsection
