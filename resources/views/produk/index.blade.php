@extends('layouts.vendor')

@section('title', 'Manajemen Produk')
@section('content')
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row gap-3 justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Produk</h3>
                </div>
                <div class="flex gap-2 flex-grow justify-end">
                    <form action="{{ route('vendor.products.index') }}" method="GET" class="flex-grow">
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm"
                                placeholder="Cari produk...">
                        </div>
                    </form>

                    {{-- Filter Kategori --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary">
                            Kategori: {{ $selectedCategory ? $selectedCategory->nama_kategori : 'Semua' }}
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition
                            class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="{{ route('vendor.products.index', array_merge(request()->except('kategori_id'), ['kategori_id' => ''])) }}">Semua</a>
                            @foreach ($kategories as $kategori)
                                <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    href="{{ route('vendor.products.index', array_merge(request()->except('kategori_id'), ['kategori_id' => $kategori->id])) }}">
                                    {{ $kategori->nama_kategori }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ route('vendor.products.create') }}"
                        class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                        <i class="fas fa-plus"></i>
                        Tambah Produk
                    </a>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spesifikasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estimasi Waktu</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($produks as $produk)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if (!empty($produk->gambar) && isset($produk->gambar[0]))
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset($produk->gambar[0]) }}" alt="{{ $produk->nama_produk }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center text-sm font-semibold">
                                            {{ substr($produk->nama_produk, 0, 2) }}
                                        </div>
                                    @endif
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $produk->nama_produk }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($produk->deskripsi, 50) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $produk->kategori->nama_kategori ?? 'Tidak ada kategori' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $produk->spesifikasiProduk->count() }} spesifikasi</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($produk->estimasiProduk->count() > 0)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Tersedia</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-500">Belum diatur</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('vendor.products.show', $produk->id) }}"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('vendor.products.edit', $produk->id) }}"
                                        class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors delete-btn"
                                        data-id="{{ $produk->id }}" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-box text-6xl text-gray-300 mb-4"></i>
                                    <p class="text-lg font-medium text-gray-900 mb-1">Tidak ada data produk</p>
                                    <p class="text-sm text-gray-500 mb-4">Silahkan tambahkan produk baru atau ubah filter pencarian</p>
                                    <a href="{{ route('vendor.products.create') }}"
                                        class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                                        <i class="fas fa-plus"></i>
                                        Tambah Produk
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $produks->links('dev.components.pagination') }}
        </div>
    </div>

    <!-- Hidden delete form -->
    <form id="delete-form" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const deleteForm = document.getElementById('delete-form');
                const deleteButtons = document.querySelectorAll('.delete-btn');

                deleteButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        confirmDelete(id);
                    });
                });

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
                            deleteForm.action = `{{ route('vendor.products.destroy', '') }}/${id}`;
                            deleteForm.submit();
                        }
                    });
                };
            });
        </script>
    @endpush
@endsection
