@extends('dev.layouts.app')

@section('title', 'Users Management')
@section('content')
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        {{-- Header --}}
        <div class="px-4 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h3 class="text-lg font-semibold text-gray-900">Users List</h3>
                <div class="flex items-center gap-3">
                    <x.ui.button type="button" variant="primary" href="{{ route('admin.users.create') }}">
                        <i class="fas fa-plus text-xs mr-1"></i> Add User
                    </x.ui.button>
                </div>
            </div>
            {{-- Filters --}}
            <form action="{{ route('admin.users.index') }}" method="GET" class="mt-3">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                               placeholder="Search by name, email, or usertype...">
                    </div>
                    <select name="usertype" onchange="this.form.submit()"
                            class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        <option value="">All Types</option>
                        <option value="user" {{ request('usertype') === 'user' ? 'selected' : '' }}>User</option>
                        <option value="vendor" {{ request('usertype') === 'vendor' ? 'selected' : '' }}>Vendor</option>
                        <option value="dev" {{ request('usertype') === 'dev' ? 'selected' : '' }}>Admin</option>
                    </select>
                    <select name="lelang" onchange="this.form.submit()"
                            class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        <option value="">All Lelang Status</option>
                        <option value="with_profile" {{ request('lelang') === 'with_profile' ? 'selected' : '' }}>With Profile ({{ $lelangStats['total_profiles'] ?? 0 }})</option>
                        <option value="without_profile" {{ request('lelang') === 'without_profile' ? 'selected' : '' }}>Without Profile</option>
                        <option value="verified" {{ request('lelang') === 'verified' ? 'selected' : '' }}>Verified ({{ $lelangStats['verified'] ?? 0 }})</option>
                        <option value="suspended" {{ request('lelang') === 'suspended' ? 'selected' : '' }}>Suspended ({{ $lelangStats['suspended'] ?? 0 }})</option>
                    </select>
                    <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-filter text-xs mr-1"></i> Filter
                    </button>
                    @if(request()->has('search') || request()->has('usertype') || request()->has('lelang'))
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                            <i class="fas fa-times text-xs mr-1"></i> Clear
                        </a>
                    @endif
                </div>
            </form>
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
