@extends('layouts.vendor')

@section('title', 'Katalog Produk - ' . $linktree->title)

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <h2 class="page-title">📦 Katalog Produk Linktree</h2>
            <div class="text-muted mt-1">{{ $linktree->title }} (/{{ $linktree->custom_url }})</div>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('vendor.linktree.show', $linktree) }}" class="btn btn-ghost-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l-5l5l5l5"/><path d="M15 11l5l-5l-5-5"/></svg>
                Kembali
            </a>
            <a href="{{ url('/l/' . $linktree->custom_url) }}" target="_blank" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/></svg>
                Lihat Publik
            </a>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
    <div class="d-flex">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/></svg>
        </div>
        <div>{{ session('success') }}</div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible" role="alert">
    <div class="d-flex">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4"/><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/><path d="M12 16h.01"/></svg>
        </div>
        <div>{{ session('error') }}</div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endif

<div class="row">
    <!-- Form Tambah Produk -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">➕ Tambah Produk</h3>
            </div>
            <div class="card-body">
                @if($availableProduks->count() > 0)
                <form action="{{ route('vendor.linktree.products.store', $linktree) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Pilih Produk <span class="text-danger">*</span></label>
                        <select name="produk_id" class="form-select" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($availableProduks as $produk)
                            <option value="{{ $produk->id }}">
                                {{ $produk->nama_produk }}
                                @if(isset($produk->harga_dasar)) - Rp {{ number_format($produk->harga_dasar, 0, ',', '.') }} @endif
                            </option>
                            @endforeach
                        </select>
                        @error('produk_id')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga Khusus (Opsional)</label>
                        <input type="text" name="custom_price" class="form-control" placeholder="Contoh: Rp 50.000" value="{{ old('custom_price') }}">
                        <div class="form-hint">Kosongkan untuk menggunakan harga default produk</div>
                        @error('custom_price')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi Khusus (Opsional)</label>
                        <textarea name="custom_description" class="form-control" rows="3" placeholder="Deskripsi untuk linktree...">{{ old('custom_description') }}</textarea>
                        <div class="form-hint">Kosongkan untuk menggunakan deskripsi default produk</div>
                        @error('custom_description')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                        Tambah ke Katalog
                    </button>
                </form>
                @else
                <div class="text-center text-muted py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4"/><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/><path d="M12 16h.01"/></svg>
                    <p>Semua produk sudah ditambahkan ke linktree ini.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Info -->
        <div class="card mt-3">
            <div class="card-body">
                <h4 class="card-title">ℹ️ Informasi</h4>
                <ul class="list-unstyled list-unstyled-py-3 mb-0">
                    <li class="mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                        Produk yang ditambahkan akan tampil di halaman publik linktree Anda
                    </li>
                    <li class="mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                        Geser produk untuk mengubah urutan tampilan
                    </li>
                    <li class="mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                        Aktifkan/nonaktifkan produk tanpa menghapusnya
                    </li>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                        Harga dan deskripsi bisa dikhususkan untuk linktree
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Daftar Produk di Linktree -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">📋 Produk di Linktree ({{ $linktree->linktreeProducts->count() }})</h3>
            </div>
            <div class="card-body p-0">
                @if($linktree->linktreeProducts->count() > 0)
                <div id="product-list" class="list-group list-group-flush">
                    @foreach($linktree->linktreeProducts as $product)
                    <div class="list-group-item product-item" data-id="{{ $product->id }}" style="cursor: grab;">
                        <div class="row align-items-center">
                            <!-- Drag Handle -->
                            <div class="col-auto text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/><path d="M9 5a2 2 0 0 0 -2 2v2a2 2 0 0 0 2 2h2a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2z"/><path d="M9 5a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/><path d="M9 19a2 2 0 0 0 2 2h2a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2v2z"/><path d="M14 5a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/><path d="M14 19a2 2 0 0 0 2 2h2a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2v2z"/></svg>
                            </div>

                            <!-- Product Image -->
                            <div class="col-auto">
                                @if($product->display_image)
                                <img src="{{ asset('produk_gambar/' . $product->display_image) }}" alt="{{ $product->display_name }}" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                <div class="rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: #f1f5f9; color: #94a3b8;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/></svg>
                                </div>
                                @endif
                            </div>

                            <!-- Product Info -->
                            <div class="col">
                                <div class="d-flex align-items-center gap-2">
                                    <strong>{{ $product->display_name }}</strong>
                                    @if($product->is_active)
                                    <span class="badge bg-success-lt">Aktif</span>
                                    @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </div>
                                <div class="text-muted small mt-1">
                                    @if($product->display_price)
                                    💰 {{ $product->display_price }}
                                    @else
                                    <span class="text-muted">Harga tidak diatur</span>
                                    @endif
                                </div>
                                @if($product->custom_description)
                                <div class="text-muted small mt-1" style="max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    📝 {{ Str::limit($product->custom_description, 80) }}
                                </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="col-auto">
                                <div class="btn-list">
                                    <!-- Toggle Active -->
                                    <form action="{{ route('vendor.linktree.products.toggle', [$linktree, $product]) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $product->is_active ? 'btn-success' : 'btn-secondary' }}" title="{{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            @if($product->is_active)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                                            @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/></svg>
                                            @endif
                                        </button>
                                    </form>

                                    <!-- Edit -->
                                    <button type="button" class="btn btn-sm btn-ghost-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $product->id }}" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"/><path d="M9 12l2 2l4 -4"/></svg>
                                    </button>

                                    <!-- Delete -->
                                    <form action="{{ route('vendor.linktree.products.destroy', [$linktree, $product]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus produk ini dari linktree?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-ghost-danger" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7l0 -3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1l0 3"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Modal -->
                    <div class="modal modal-blur fade" id="editModal{{ $product->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <form action="{{ route('vendor.linktree.products.update', [$linktree, $product]) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit: {{ $product->display_name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Harga Khusus</label>
                                            <input type="text" name="custom_price" class="form-control" placeholder="Contoh: Rp 50.000" value="{{ $product->custom_price }}">
                                            <div class="form-hint">Kosongkan untuk menggunakan harga default</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Deskripsi Khusus</label>
                                            <textarea name="custom_description" class="form-control" rows="3" placeholder="Deskripsi untuk linktree...">{{ $product->custom_description }}</textarea>
                                            <div class="form-hint">Kosongkan untuk menggunakan deskripsi default</div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-ghost-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted mb-3" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    <h4 class="text-muted">Belum Ada Produk</h4>
                    <p class="text-muted">Tambahkan produk dari form di sebelah kiri untuk menampilkannya di linktree.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productList = document.getElementById('product-list');
    if (productList) {
        new Sortable(productList, {
            handle: '.text-muted',
            animation: 150,
            ghostClass: 'bg-light',
            onEnd: function() {
                const order = Array.from(productList.querySelectorAll('.product-item'))
                    .map(item => item.dataset.id);

                fetch('{{ route("vendor.linktree.products.reorder", $linktree) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_order: order })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show subtle success feedback
                        const toast = document.createElement('div');
                        toast.className = 'position-fixed bottom-0 end-0 p-3';
                        toast.style.zIndex = '9999';
                        toast.innerHTML = '<div class="toast show" role="alert"><div class="toast-body text-success">✅ Urutan produk diperbarui</div></div>';
                        document.body.appendChild(toast);
                        setTimeout(() => toast.remove(), 2000);
                    }
                })
                .catch(() => {});
            }
        });
    }
});
</script>
@endpush
@endsection
