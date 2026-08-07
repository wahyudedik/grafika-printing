@extends('layouts.vendor')

@section('title', 'User Details')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">User Details</h3>
            </div>

            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Full Name</label>
                        <div class="text-sm text-gray-900">{{ $user->name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                        <div class="text-sm text-gray-900">{{ $user->email }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">User Type</label>
                        <div>
                            @if ($user->usertype == 'dev')
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Developer</span>
                            @else
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Vendor</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Created At</label>
                        <div class="text-sm text-gray-900">{{ $user->created_at->format('d M Y H:i') }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                        <div class="text-sm text-gray-900">{{ $user->updated_at->format('d M Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x.ui.button href="{{ route('vendor.users.edit', $user->id) }}" variant="warning" size="sm">
                        <i class="fas fa-pen mr-1"></i> Edit
                    </x.ui.button>
                    <form id="delete-user-show-{{ $user->id }}" action="{{ route('vendor.users.destroy', $user->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                    </form>
                    <x.ui.button type="button" variant="danger" size="sm"
                        onclick="confirmFormSubmit('delete-user-show-{{ $user->id }}', { title: 'Hapus Pengguna?', text: 'Pengguna ini akan dilepas dari vendor.', confirmText: 'Ya, Hapus', confirmColor: '#d33' })">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </x.ui.button>
                </div>
                <x.ui.button href="{{ route('vendor.users.index') }}" variant="outline" size="sm">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
                </x.ui.button>
            </div>
        </div>
    </div>
@endsection
