@extends('layouts.vendor')

@section('title', 'Manajemen Produk')
@section('content')
    <div class="bg-white rounded-xl shadow-sm" x-data="bulkActions()">
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

        {{-- Bulk Action Bar --}}
        <div x-show="selectedIds.length > 0" x-transition
            class="px-6 py-3 bg-primary/5 border-b border-primary/20 flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <span class="text-sm font-medium text-primary">
                <span x-text="selectedIds.length"></span> produk dipilih
            </span>
            <div class="flex items-center gap-2 flex-wrap">
                <select x-model="bulkField"
                    class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">Pilih field...</option>
                    <option value="kategori_id">Kategori</option>
                    <option value="harga_jual">Harga Jual (Rp)</option>
                </select>
                <template x-if="bulkField === 'kategori_id'">
                    <select x-model="bulkValue"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                        <option value="">Pilih kategori...</option>
                        @foreach ($kategories as $kategori)
                            <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                        @endforeach
                    </select>
                </template>
                <template x-if="bulkField === 'harga_jual'">
                    <input type="number" x-model="bulkValue" min="0" step="100"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary w-36"
                        placeholder="Harga (Rp)">
                </template>
                <button @click="submitBulk()" :disabled="!bulkField || !bulkValue"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-save text-xs"></i>
                    Terapkan
                </button>
                <button @click="clearSelection()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                    Batal
                </button>
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" @change="toggleAll($event)"
                                :checked="selectedIds.length === items.length && items.length > 0"
                                class="rounded border-gray-300 text-primary focus:ring-primary">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spesifikasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estimasi Waktu</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($produks as $produk)
                        <tr class="hover:bg-gray-50" :class="selectedIds.includes({{ $produk->id }}) ? 'bg-primary/5' : ''">
                            <td class="px-6 py-4">
                                <input type="checkbox" value="{{ $produk->id }}"
                                    @change="toggleItem({{ $produk->id }})"
                                    :checked="selectedIds.includes({{ $produk->id }})"
                                    class="rounded border-gray-300 text-primary focus:ring-primary">
                            </td>
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                @if($produk->harga_jual)
                                    Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
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
                            <td colspan="7" class="px-6 py-12 text-center">
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

        {{-- Mobile Cards --}}
        <div class="md:hidden divide-y divide-gray-100">
            @forelse ($produks as $produk)
                <div class="p-4 space-y-2" :class="selectedIds.includes({{ $produk->id }}) ? 'bg-primary/5' : ''">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            @if (!empty($produk->gambar) && isset($produk->gambar[0]))
                                <img class="h-10 w-10 rounded-full object-cover flex-shrink-0" src="{{ asset($produk->gambar[0]) }}" alt="{{ $produk->nama_produk }}">
                            @else
                                <div class="h-10 w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center text-sm font-semibold flex-shrink-0">
                                    {{ substr($produk->nama_produk, 0, 2) }}
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('vendor.products.show', $produk->id) }}" class="font-medium text-gray-900 hover:text-blue-600 truncate block">{{ $produk->nama_produk }}</a>
                                <p class="text-sm text-gray-500 truncate">{{ $produk->kategori->nama_kategori ?? 'Tidak ada kategori' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 ml-2 flex-shrink-0">
                            <input type="checkbox" value="{{ $produk->id }}"
                                @change="toggleItem({{ $produk->id }})"
                                :checked="selectedIds.includes({{ $produk->id }})"
                                class="rounded border-gray-300 text-primary focus:ring-primary">
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-gray-900">
                            @if($produk->harga_jual)
                                Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </span>
                        @if ($produk->estimasiProduk->count() > 0)
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Tersedia</span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-500">Belum diatur</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-end gap-1 pt-1">
                        <a href="{{ route('vendor.products.show', $produk->id) }}"
                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat">
                            <i class="fas fa-eye text-sm"></i>
                        </a>
                        <a href="{{ route('vendor.products.edit', $produk->id) }}"
                            class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                            <i class="fas fa-edit text-sm"></i>
                        </a>
                        <button type="button"
                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors delete-btn"
                            data-id="{{ $produk->id }}" title="Hapus">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <i class="fas fa-box text-4xl text-gray-300 mb-3"></i>
                    <p class="text-sm font-medium text-gray-900 mb-1">Tidak ada data produk</p>
                    <p class="text-sm text-gray-500">Silahkan tambahkan produk baru</p>
                </div>
            @endforelse
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $produks->links('components.pagination') }}
        </div>
    </div>

    <!-- Hidden delete form -->
    <form id="delete-form" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- Hidden bulk form -->
    <form id="bulk-form" action="{{ route('vendor.products.bulk-update') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="field" :value="bulkField">
        <input type="hidden" name="value" :value="bulkValue">
    </form>

    @push('scripts')
        <script>
            function bulkActions() {
                return {
                    selectedIds: [],
                    bulkField: '',
                    bulkValue: '',
                    items: @json($produks->pluck('id')),
                    toggleAll(event) {
                        this.selectedIds = event.target.checked ? [...this.items] : [];
                    },
                    toggleItem(id) {
                        if (this.selectedIds.includes(id)) {
                            this.selectedIds = this.selectedIds.filter(i => i !== id);
                        } else {
                            this.selectedIds.push(id);
                        }
                    },
                    clearSelection() {
                        this.selectedIds = [];
                        this.bulkField = '';
                        this.bulkValue = '';
                    },
                    submitBulk() {
                        if (!this.bulkField || !this.bulkValue || this.selectedIds.length === 0) return;
                        Swal.fire({
                            title: 'Ubah massal?',
                            text: `Perbarui ${this.selectedIds.length} produk?`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, terapkan!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const form = document.getElementById('bulk-form');
                                const fieldInput = form.querySelector('input[name="field"]');
                                const valueInput = form.querySelector('input[name="value"]');
                                // Remove old hidden inputs for ids
                                form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
                                // Add new hidden inputs
                                this.selectedIds.forEach(id => {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'ids[]';
                                    input.value = id;
                                    form.appendChild(input);
                                });
                                fieldInput.value = this.bulkField;
                                valueInput.value = this.bulkValue;
                                form.submit();
                            }
                        });
                    }
                };
            }

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
                            deleteForm.action = '{{ route('vendor.products.destroy', '__ID__') }}'.replace('__ID__', id);
                            deleteForm.submit();
                        }
                    });
                };
            });
        </script>
    @endpush
@endsection
