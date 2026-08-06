@extends('layouts.vendor')

@section('title', 'Katalog Produk - ' . $linktree->title)

@section('content')
<div x-data="productsManager()" class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-box-open mr-2 text-primary-600"></i>Katalog Produk Linktree
            </h1>
            <p class="mt-1 text-sm text-gray-500">{{ $linktree->title }} (/{{ $linktree->custom_url }})</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('vendor.linktree.show', $linktree) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <a href="{{ url('/l/' . $linktree->custom_url) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">
                <i class="fas fa-external-link-alt mr-2"></i>Lihat Publik
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between">
        <div class="flex items-center gap-3"><i class="fas fa-check-circle text-emerald-600"></i><span class="text-sm text-emerald-800">{{ session('success') }}</span></div>
        <button @click="show = false" class="text-emerald-600 hover:text-emerald-800"><i class="fas fa-times"></i></button>
    </div>
    @endif
    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center justify-between">
        <div class="flex items-center gap-3"><i class="fas fa-exclamation-circle text-red-600"></i><span class="text-sm text-red-800">{{ session('error') }}</span></div>
        <button @click="show = false" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Add Product Form --}}
        <div class="lg:col-span-1 space-y-5">
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900"><i class="fas fa-plus mr-2 text-primary-600"></i>Tambah Produk</h2>
                </div>
                <div class="p-5">
                    @if($availableProduks->count() > 0)
                    <form action="{{ route('vendor.linktree.products.store', $linktree) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Produk <span class="text-red-500">*</span></label>
                                <select name="produk_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($availableProduks as $produk)
                                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }} @if(isset($produk->harga_dasar)) - Rp {{ number_format($produk->harga_dasar, 0, ',', '.') }} @endif</option>
                                    @endforeach
                                </select>
                                @error('produk_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Khusus (Opsional)</label>
                                <input type="text" name="custom_price" placeholder="Contoh: Rp 50.000" value="{{ old('custom_price') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <p class="mt-1 text-xs text-gray-500">Kosongkan untuk menggunakan harga default produk</p>
                                @error('custom_price')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Khusus (Opsional)</label>
                                <textarea name="custom_description" rows="3" placeholder="Deskripsi untuk linktree..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('custom_description') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Kosongkan untuk menggunakan deskripsi default produk</p>
                                @error('custom_description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">
                                <i class="fas fa-plus mr-2"></i>Tambah ke Katalog
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="text-center py-6">
                        <i class="fas fa-check-circle text-3xl text-gray-300 mb-3"></i>
                        <p class="text-sm text-gray-500">Semua produk sudah ditambahkan ke linktree ini.</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Info --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-3"><i class="fas fa-info-circle mr-2 text-primary-600"></i>Informasi</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start gap-2"><i class="fas fa-check text-blue-500 mt-0.5"></i>Produk yang ditambahkan akan tampil di halaman publik linktree Anda</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-blue-500 mt-0.5"></i>Geser produk untuk mengubah urutan tampilan</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-blue-500 mt-0.5"></i>Aktifkan/nonaktifkan produk tanpa menghapusnya</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-blue-500 mt-0.5"></i>Harga dan deskripsi bisa dikhususkan untuk linktree</li>
                </ul>
            </div>
        </div>

        {{-- Product List --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">
                        <i class="fas fa-list mr-2 text-primary-600"></i>Produk di Linktree ({{ $linktree->linktreeProducts->count() }})
                    </h2>
                </div>
                <div class="p-0">
                    @if($linktree->linktreeProducts->count() > 0)
                    <div id="product-list" class="divide-y divide-gray-100">
                        @foreach($linktree->linktreeProducts as $product)
                        <div class="product-item flex items-center gap-4 px-5 py-4 hover:bg-gray-50" data-id="{{ $product->id }}" style="cursor: grab;">
                            {{-- Drag Handle --}}
                            <div class="text-gray-400 cursor-grab">
                                <i class="fas fa-grip-vertical"></i>
                            </div>
                            {{-- Product Image --}}
                            <div class="flex-shrink-0">
                                @if($product->display_image)
                                    <img src="{{ asset('produk_gambar/' . $product->display_image) }}" alt="{{ $product->display_name }}" class="w-14 h-14 rounded-lg object-cover">
                                @else
                                    <div class="w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400"><i class="fas fa-image"></i></div>
                                @endif
                            </div>
                            {{-- Product Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-900 truncate">{{ $product->display_name }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </div>
                                <div class="text-sm text-gray-500 mt-0.5">
                                    @if($product->display_price)💰 {{ $product->display_price }} @else <span class="text-gray-400">Harga tidak diatur</span> @endif
                                </div>
                                @if($product->custom_description)
                                    <div class="text-xs text-gray-400 mt-0.5 truncate max-w-md">📝 {{ Str::limit($product->custom_description, 80) }}</div>
                                @endif
                            </div>
                            {{-- Actions --}}
                            <div class="flex items-center gap-2">
                                <form action="{{ route('vendor.linktree.products.toggle', [$linktree, $product]) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-1.5 rounded-lg {{ $product->is_active ? 'text-emerald-600 hover:bg-emerald-50' : 'text-gray-400 hover:bg-gray-100' }}" title="{{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="fas {{ $product->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                    </button>
                                </form>
                                <button @click="openEditModal({{ $product->id }}, '{{ addslashes($product->custom_price) }}', '{{ addslashes($product->custom_description) }}')" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('vendor.linktree.products.destroy', [$linktree, $product]) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk ini dari linktree?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>

                        {{-- Edit Modal --}}
                        <div x-show="editProductId === {{ $product->id }}" x-transition class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                            <div class="flex items-center justify-center min-h-screen px-4">
                                <div class="fixed inset-0 bg-gray-500/75" @click="editProductId = null"></div>
                                <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit: {{ $product->display_name }}</h3>
                                    <form action="{{ route('vendor.linktree.products.update', [$linktree, $product]) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Khusus</label>
                                                <input type="text" name="custom_price" :value="editPrice" placeholder="Contoh: Rp 50.000" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                                <p class="mt-1 text-xs text-gray-500">Kosongkan untuk menggunakan harga default</p>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Khusus</label>
                                                <textarea name="custom_description" rows="3" placeholder="Deskripsi untuk linktree..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" x-text="editDescription"></textarea>
                                                <p class="mt-1 text-xs text-gray-500">Kosongkan untuk menggunakan deskripsi default</p>
                                            </div>
                                        </div>
                                        <div class="mt-6 flex justify-end gap-3">
                                            <button type="button" @click="editProductId = null" class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center"><i class="fas fa-plus text-2xl text-gray-400"></i></div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Produk</h3>
                        <p class="text-sm text-gray-500">Tambahkan produk dari form di sebelah kiri untuk menampilkannya di linktree.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
function productsManager() {
    return {
        editProductId: null,
        editPrice: '',
        editDescription: '',
        openEditModal(id, price, description) {
            this.editProductId = id;
            this.editPrice = price;
            this.editDescription = description;
        },
        init() {
            const productList = document.getElementById('product-list');
            if (productList) {
                new Sortable(productList, {
                    handle: '.fa-grip-vertical',
                    animation: 150,
                    ghostClass: 'bg-gray-50',
                    onEnd: function() {
                        const order = Array.from(productList.querySelectorAll('.product-item'))
                            .map(item => item.dataset.id);
                        fetch('{{ route("vendor.linktree.products.reorder", $linktree) }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                            body: JSON.stringify({ product_order: order })
                        }).then(r => r.json()).then(data => {
                            if (data.success) {
                                const toast = document.createElement('div');
                                toast.className = 'fixed bottom-4 right-4 z-50 px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg shadow-lg';
                                toast.textContent = '✅ Urutan produk diperbarui';
                                document.body.appendChild(toast);
                                setTimeout(() => toast.remove(), 2000);
                            }
                        }).catch(() => {});
                    }
                });
            }
        }
    };
}
</script>
@endpush
@endsection
