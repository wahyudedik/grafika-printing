@extends('layouts.vendor')

@section('title', 'Spesifikasi Manajemen')
@section('content')
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row gap-3 justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Spesifikasi</h3>
                <div class="flex gap-2 flex-grow justify-end">
                    <form action="{{ route('vendor.specifications.index') }}" method="GET" class="flex-grow max-w-xs">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400 text-sm"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary"
                                placeholder="Cari spesifikasi...">
                        </div>
                    </form>
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Filter Tipe: {{ request('tipe_input') ? ucfirst(request('tipe_input')) : 'Semua' }}
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="{{ route('vendor.specifications.index', array_merge(request()->except('tipe_input'), ['tipe_input' => ''])) }}">Semua</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="{{ route('vendor.specifications.index', array_merge(request()->except('tipe_input'), ['tipe_input' => 'number'])) }}">Number</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="{{ route('vendor.specifications.index', array_merge(request()->except('tipe_input'), ['tipe_input' => 'select'])) }}">Select</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="{{ route('vendor.specifications.index', array_merge(request()->except('tipe_input'), ['tipe_input' => 'text'])) }}">Text</a>
                        </div>
                    </div>
                    <a href="{{ route('vendor.specifications.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition">
                        <i class="fas fa-plus"></i>
                        Tambah Spesifikasi
                    </a>
                </div>
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Spesifikasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe Input</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($spesifikasi as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->nama_spesifikasi }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                    {{ $item->isNumeric() ? 'bg-blue-100 text-blue-700' : ($item->isSelect() ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700') }}">
                                    {{ $item->tipe_input }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $item->satuan ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('vendor.specifications.show', $item->id) }}"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Show">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('vendor.specifications.edit', $item->id) }}"
                                        class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('vendor.specifications.destroy', $item->id) }}" method="POST"
                                        class="inline" id="delete-form-{{ $item->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="Delete"
                                            onclick="confirmDelete('delete-form-{{ $item->id }}')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if ($spesifikasi->count() == 0)
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <x-ui.empty-state icon="fas fa-database" title="Tidak ada data" description="Tidak ada data spesifikasi yang tersedia.">
                                    <x-slot:actions>
                                        <a href="{{ route('vendor.specifications.create') }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition">
                                            <i class="fas fa-plus"></i>
                                            Tambah Spesifikasi
                                        </a>
                                    </x-slot:actions>
                                </x-ui.empty-state>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden divide-y divide-gray-100">
            @forelse ($spesifikasi as $item)
                <div class="p-4 space-y-2">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('vendor.specifications.show', $item->id) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $item->nama_spesifikasi }}</a>
                            <p class="text-sm text-gray-500 mt-0.5">{{ $item->satuan ?? '-' }}</p>
                        </div>
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full ml-2 flex-shrink-0
                            {{ $item->isNumeric() ? 'bg-blue-100 text-blue-700' : ($item->isSelect() ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700') }}">
                            {{ $item->tipe_input }}
                        </span>
                    </div>
                    <div class="flex items-center justify-end gap-1 pt-1">
                        <a href="{{ route('vendor.specifications.show', $item->id) }}"
                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Lihat">
                            <i class="fas fa-eye text-sm"></i>
                        </a>
                        <a href="{{ route('vendor.specifications.edit', $item->id) }}"
                            class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit">
                            <i class="fas fa-edit text-sm"></i>
                        </a>
                        <form action="{{ route('vendor.specifications.destroy', $item->id) }}" method="POST"
                            class="inline" id="delete-form-m-{{ $item->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                title="Hapus"
                                onclick="confirmDelete('delete-form-m-{{ $item->id }}')">
                                <i class="fas fa-trash-alt text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <i class="fas fa-database text-gray-300 text-4xl mb-3"></i>
                    <p class="text-sm font-medium text-gray-900 mb-1">Tidak ada data spesifikasi</p>
                    <a href="{{ route('vendor.specifications.create') }}"
                        class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition">
                        <i class="fas fa-plus"></i> Tambah Spesifikasi
                    </a>
                </div>
            @endforelse
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $spesifikasi->links('dev.components.pagination') }}
        </div>
    </div>

    {{-- confirmDelete(formId) is now globally available via components/alert.blade.php --}}
@endsection
