@extends('dev.layouts.app')

@section('title', 'Vendors Management')
@section('content')
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        {{-- Header --}}
        <div class="px-4 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h3 class="text-lg font-semibold text-gray-900">Vendors List</h3>
                <div class="flex items-center gap-3">
                    <form action="{{ route('admin.vendors.index') }}" method="GET" class="flex-1 sm:flex-none">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   class="w-full sm:w-64 pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                   placeholder="Search vendors...">
                        </div>
                    </form>
                    <a href="{{ route('admin.vendors.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors flex-shrink-0">
                        <i class="fas fa-plus text-xs"></i> Add Vendor
                    </a>
                </div>
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Logo</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Name</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Email</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Phone</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Website</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                        <th class="text-right px-4 py-3 font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($vendors as $vendor)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                @if ($vendor->logo)
                                    <img src="{{ asset('vendors_logo/' . $vendor->logo) }}" alt="{{ $vendor->name }}" class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold">
                                        {{ substr($vendor->name, 0, 2) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $vendor->name }}</div>
                                <div class="text-xs text-gray-500">{{ $vendor->address }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $vendor->email }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $vendor->phone }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $vendor->website }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $vendor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.vendors.show', $vendor->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <form action="{{ route('admin.vendors.destroy', $vendor->id) }}" method="POST" class="inline" id="delete-form-{{ $vendor->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete" onclick="confirmDelete('delete-form-{{ $vendor->id }}')">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
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
            @foreach ($vendors as $vendor)
                <div class="p-4 space-y-2">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            @if ($vendor->logo)
                                <img src="{{ asset('vendors_logo/' . $vendor->logo) }}" alt="{{ $vendor->name }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-sm font-bold flex-shrink-0">
                                    {{ substr($vendor->name, 0, 2) }}
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 truncate">{{ $vendor->name }}</p>
                                <p class="text-sm text-gray-500 truncate">{{ $vendor->email }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $vendor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} ml-2 flex-shrink-0">
                            {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">{{ $vendor->phone }}</span>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.vendors.show', $vendor->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"><i class="fas fa-eye text-sm"></i></a>
                            <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"><i class="fas fa-edit text-sm"></i></a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $vendors->links('dev.components.pagination') }}
        </div>
    </div>
@endsection
