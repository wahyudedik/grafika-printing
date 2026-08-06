@extends('layouts.vendor')

@section('title', 'Manajemen Kategori Produk')
@section('content')
    <div class="bg-white rounded-xl shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Kategori Produk</h3>
                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <form action="{{ route('vendor.categories.index') }}" method="GET" class="flex-1">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Cari kategori...">
                        </div>
                    </form>

                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 whitespace-nowrap">
                            Sort: {{ request('sort', 'nama_kategori') }} ({{ request('order', 'asc') }})
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div x-show="open" x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="{{ route('vendor.categories.index', array_merge(request()->except(['sort', 'order']), ['sort' => 'nama_kategori', 'order' => 'asc'])) }}">Nama
                                (A-Z)</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="{{ route('vendor.categories.index', array_merge(request()->except(['sort', 'order']), ['sort' => 'nama_kategori', 'order' => 'desc'])) }}">Nama
                                (Z-A)</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="{{ route('vendor.categories.index', array_merge(request()->except(['sort', 'order']), ['sort' => 'created_at', 'order' => 'desc'])) }}">Terbaru</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="{{ route('vendor.categories.index', array_merge(request()->except(['sort', 'order']), ['sort' => 'created_at', 'order' => 'asc'])) }}">Terlama</a>
                        </div>
                    </div>

                    <a href="{{ route('vendor.categories.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors whitespace-nowrap">
                        <i class="fas fa-plus"></i> Tambah Kategori
                    </a>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama Kategori</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Slug</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jumlah Produk</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal Dibuat</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($kategori as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $item->nama_kategori }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                {{ $item->slug }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->produk->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('vendor.categories.show', $item->id) }}"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('vendor.categories.edit', $item->id) }}"
                                        class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors delete-btn"
                                        data-id="{{ $item->id }}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-folder-open text-gray-400 text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">Tidak ada data kategori</p>
                                    <p class="text-sm text-gray-500 mt-1">Silahkan tambahkan kategori produk baru atau ubah filter pencarian</p>
                                    <a href="{{ route('vendor.categories.create') }}"
                                        class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-plus"></i> Tambah Kategori
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($kategori->hasPages())
            <div class="border-t border-gray-200 px-6 py-4">
                {{ $kategori->links('components.pagination') }}
            </div>
        @endif
    </div>

    {{-- Hidden delete form --}}
    <form id="delete-form" action="" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const deleteButtons = document.querySelectorAll('.delete-btn');
                const deleteForm = document.getElementById('delete-form');

                // Setup delete buttons
                deleteButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        confirmDelete(id);
                    });
                });

                // Individual delete confirmation
                window.confirmDelete = function(id) {
                    Swal.fire({
                        title: 'Anda yakin?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            showLoading('Menghapus...');
                            deleteForm.action = `{{ route('vendor.categories.destroy', '') }}/${id}`;
                            deleteForm.submit();
                        }
                    });
                };
            });
        </script>
    @endpush
@endsection
