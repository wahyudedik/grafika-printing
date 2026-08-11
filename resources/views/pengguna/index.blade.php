@extends('layouts.vendor')

@section('title', 'Users Management')
@section('content')
    <div class="bg-white rounded-xl shadow-sm">
        {{-- Header with search --}}
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <form action="{{ route('vendor.users.index') }}" method="GET" class="flex-1 flex gap-3">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fa-solid fa-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="block w-full rounded-lg border border-gray-300 pl-10 pr-3 py-2 text-sm placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 focus:outline-none"
                            placeholder="Search pengguna...">
                    </div>
                </form>
                <x.ui.button href="{{ route('vendor.users.create') }}" variant="primary" size="sm">
                    <i class="fas fa-plus mr-1"></i> Tambah Pengguna
                </x.ui.button>
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usertype</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-primary-100 text-primary-700">
                                    {{ $user->usertype }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('vendor.users.show', $user->id) }}"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg inline-flex items-center" title="Lihat">
                                        <i class="fa-solid fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('vendor.users.edit', $user->id) }}"
                                        class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg inline-flex items-center" title="Edit">
                                        <i class="fa-solid fa-pen text-sm"></i>
                                    </a>
                                    <form id="delete-user-{{ $user->id }}" action="{{ route('vendor.users.destroy', $user->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" onclick="confirmFormSubmit('delete-user-{{ $user->id }}', { title: 'Hapus Pengguna?', text: 'Pengguna ini akan dilepas dari vendor.', confirmText: 'Ya, Hapus', confirmColor: '#d33' })"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg inline-flex items-center" title="Hapus">
                                        <i class="fa-solid fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-users text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-sm text-gray-500">Tidak ada pengguna ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden divide-y divide-gray-100">
            @forelse ($users as $user)
                <div class="p-4 space-y-2">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('vendor.users.show', $user->id) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $user->name }}</a>
                            <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                        </div>
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-primary-100 text-primary-700 ml-2 flex-shrink-0">
                            {{ $user->usertype }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">{{ $user->created_at->format('d M Y') }}</span>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('vendor.users.show', $user->id) }}"
                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Lihat">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </a>
                            <a href="{{ route('vendor.users.edit', $user->id) }}"
                                class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg" title="Edit">
                                <i class="fa-solid fa-pen text-sm"></i>
                            </a>
                            <button type="button" onclick="confirmFormSubmit('delete-user-{{ $user->id }}', { title: 'Hapus Pengguna?', text: 'Pengguna ini akan dilepas dari vendor.', confirmText: 'Ya, Hapus', confirmColor: '#d33' })"
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Hapus">
                                <i class="fa-solid fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <i class="fa-solid fa-users text-gray-300 text-4xl mb-3"></i>
                    <p class="text-sm text-gray-500">Tidak ada pengguna ditemukan.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links('dev.components.pagination') }}
            </div>
        @endif
    </div>
@endsection
