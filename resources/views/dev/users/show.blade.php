@extends('dev.layouts.app')

@section('title', 'User Details')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">User Details</h3>
            </div>
            <div class="p-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-500">Full Name</label>
                        <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Email</label>
                        <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">User Type</label>
                        <p class="mt-1">
                            @if ($user->usertype == 'dev')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Developer</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Vendor</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Created At</label>
                        <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $user->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Last Updated</label>
                        <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $user->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-4 py-3 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
                <a href="{{ route('admin.users.edit', $user->id) }}" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
            </div>
        </div>
    </div>
@endsection
