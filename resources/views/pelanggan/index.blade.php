@extends('layouts.vendor')

@section('title', 'Pelanggan Manajemen')
@section('content')
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row gap-3 justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Pelanggan</h3>
                <div class="flex gap-2 flex-grow justify-end">
                    <form action="{{ route('vendor.customers.index') }}" method="GET" class="flex-grow max-w-xs" data-no-loading>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400 text-sm"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary"
                                placeholder="Cari pelanggan...">
                        </div>
                    </form>
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" data-no-loading
                            class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Filter Status:
                            {{ request('status') ? (request('status') == 'active' ? 'Aktif' : 'Non-Aktif') : 'Semua' }}
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-no-loading
                                href="{{ route('vendor.customers.index', array_merge(request()->except('status'), ['status' => ''])) }}">Semua</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-no-loading
                                href="{{ route('vendor.customers.index', array_merge(request()->except('status'), ['status' => 'active'])) }}">Aktif</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-no-loading
                                href="{{ route('vendor.customers.index', array_merge(request()->except('status'), ['status' => 'inactive'])) }}">Non-Aktif</a>
                        </div>
                    </div>
                    <a href="{{ route('vendor.customers.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition">
                        <i class="fas fa-plus"></i>
                        Tambah Pelanggan
                    </a>
                </div>
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pelanggan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaksi Terakhir</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pelanggan as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $item->kode }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->nama }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if ($item->email)
                                    <div>{{ $item->email }}</div>
                                @endif
                                @if ($item->no_telp)
                                    <div>{{ $item->no_telp }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($item->alamat, 50) }}</td>
                            <td class="px-6 py-4">
                                @if ($item->transaksi_terakhir)
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                        {{ $item->transaksi_terakhir->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-500">Belum ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('vendor.customers.show', $item->id) }}"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('vendor.customers.edit', $item->id) }}"
                                        class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition delete-btn"
                                        data-id="{{ $item->id }}" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-user-slash text-gray-300 text-5xl mb-4"></i>
                                    <p class="text-sm font-medium text-gray-900 mb-1">Tidak ada data pelanggan</p>
                                    <p class="text-sm text-gray-500 mb-4">Silahkan tambahkan pelanggan baru atau ubah filter pencarian</p>
                                    <a href="{{ route('vendor.customers.create') }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition">
                                        <i class="fas fa-plus"></i>
                                        Tambah Pelanggan
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden divide-y divide-gray-100">
            @forelse ($pelanggan as $item)
                <div class="p-4 space-y-2">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('vendor.customers.show', $item->id) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $item->nama }}</a>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $item->kode }}</p>
                        </div>
                        @if ($item->transaksi_terakhir)
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700 ml-2 flex-shrink-0">
                                {{ $item->transaksi_terakhir->format('d M Y') }}
                            </span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-500 ml-2 flex-shrink-0">Belum ada</span>
                        @endif
                    </div>
                    <div class="text-sm text-gray-500 space-y-0.5">
                        @if ($item->email)
                            <div class="flex items-center gap-1.5"><i class="fas fa-envelope text-xs text-gray-400 w-4"></i> {{ $item->email }}</div>
                        @endif
                        @if ($item->no_telp)
                            <div class="flex items-center gap-1.5"><i class="fas fa-phone text-xs text-gray-400 w-4"></i> {{ $item->no_telp }}</div>
                        @endif
                        @if ($item->alamat)
                            <div class="flex items-center gap-1.5"><i class="fas fa-map-marker-alt text-xs text-gray-400 w-4"></i> {{ \Illuminate\Support\Str::limit($item->alamat, 40) }}</div>
                        @endif
                    </div>
                    <div class="flex items-center justify-end gap-1 pt-1">
                        <a href="{{ route('vendor.customers.show', $item->id) }}"
                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Lihat">
                            <i class="fas fa-eye text-sm"></i>
                        </a>
                        <a href="{{ route('vendor.customers.edit', $item->id) }}"
                            class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit">
                            <i class="fas fa-edit text-sm"></i>
                        </a>
                        <button type="button"
                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition delete-btn"
                            data-id="{{ $item->id }}" title="Hapus">
                            <i class="fas fa-trash-alt text-sm"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <i class="fas fa-user-slash text-gray-300 text-4xl mb-3"></i>
                    <p class="text-sm font-medium text-gray-900 mb-1">Tidak ada data pelanggan</p>
                    <p class="text-sm text-gray-500">Silahkan tambahkan pelanggan baru</p>
                </div>
            @endforelse
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $pelanggan->links('components.pagination') }}
        </div>
    </div>

    <form id="delete-form" action="" method="POST" style="display: none;" data-no-loading>
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const deleteButtons = document.querySelectorAll('.delete-btn');
                const deleteForm = document.getElementById('delete-form');

                deleteButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
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
                                deleteForm.action =
                                    '{{ route('vendor.customers.destroy', '__ID__') }}'.replace('__ID__', id);
                                deleteForm.submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
@endsection
