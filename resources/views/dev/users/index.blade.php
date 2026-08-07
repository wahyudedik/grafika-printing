@extends('dev.layouts.app')

@section('title', 'Users Management')
@section('content')
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        {{-- Header --}}
        <div class="px-4 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h3 class="text-lg font-semibold text-gray-900">Users List</h3>
                <div class="flex items-center gap-3">
                    <form action="{{ route('admin.users.index') }}" method="GET" class="flex-1 sm:flex-none">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   class="w-full sm:w-64 pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                   placeholder="Search users...">
                        </div>
                    </form>
                    <x.ui.button type="button" variant="primary" href="{{ route('admin.users.create') }}">
                        <i class="fas fa-plus text-xs mr-1"></i> Add User
                    </x.ui.button>
                </div>
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Name</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Email</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Usertype</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Created At</th>
                        <th class="text-right px-4 py-3 font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">{{ $user->usertype }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-sm">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <x.ui.button type="button" variant="ghost" size="icon-sm" href="{{ route('admin.users.show', $user->id) }}" title="View">
                                        <i class="fas fa-eye text-sm"></i>
                                    </x.ui.button>
                                    <x.ui.button type="button" variant="ghost" size="icon-sm" href="{{ route('admin.users.edit', $user->id) }}" title="Edit">
                                        <i class="fas fa-edit text-sm"></i>
                                    </x.ui.button>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" id="delete-form-{{ $user->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <x.ui.button type="button" variant="ghost" size="icon-sm" title="Delete" onclick="confirmDelete('delete-form-{{ $user->id }}')">
                                            <i class="fas fa-trash text-sm"></i>
                                        </x.ui.button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden divide-y divide-gray-100">
            @foreach ($users as $user)
                <div class="p-4 space-y-2">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 ml-2 flex-shrink-0">{{ $user->usertype }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">{{ $user->created_at->format('d M Y') }}</span>
                        <div class="flex items-center gap-1">
                            <x.ui.button type="button" variant="ghost" size="icon-sm" href="{{ route('admin.users.show', $user->id) }}"><i class="fas fa-eye text-sm"></i></x.ui.button>
                            <x.ui.button type="button" variant="ghost" size="icon-sm" href="{{ route('admin.users.edit', $user->id) }}"><i class="fas fa-edit text-sm"></i></x.ui.button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $users->links('dev.components.pagination') }}
        </div>
    </div>
@endsection
