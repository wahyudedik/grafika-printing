@extends('layouts.vendor')

@section('title', 'Edit Penawaran')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Edit Penawaran</h2>
                    <p class="text-muted">Perbarui penawaran Anda untuk lelang ini</p>
                </div>
                <a href="{{ route('vendor.auctions.show', $bid->auction) }}" class="btn btn-outline-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M9 6l6 6l-6 6" />
                    </svg>
                    Kembali
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Penawaran</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('vendor.auctions.update-bid', $bid) }}" method="POST" data-loading>
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Harga Penawaran <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number"
                                                class="form-control @error('bid_amount') is-invalid @enderror"
                                                name="bid_amount" value="{{ old('bid_amount', $bid->bid_amount) }}"
                                                placeholder="Masukkan harga penawaran" min="0" step="1000"
                                                required>
                                            @error('bid_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-text">
                                            Budget maksimal: <strong>Rp {{ number_format($bid->auction->budget) }}</strong>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Pesan (Opsional)</label>
                                        <textarea class="form-control @error('message') is-invalid @enderror" name="message" rows="4"
                                            placeholder="Tambahkan pesan atau catatan untuk pemilik lelang...">{{ old('message', $bid->message) }}</textarea>
                                        @error('message')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            Maksimal 1000 karakter
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="alert alert-warning">
                                            <div class="d-flex">
                                                <div class="me-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M12 9v2m0 4v.01" />
                                                        <path d="M21 12a9 9 0 1 1 -18 0a9 9 0 0 1 18 0" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h4 class="alert-title">Perhatian!</h4>
                                                    <div class="text-muted">
                                                        <ul class="mb-0">
                                                            <li>Mengubah penawaran akan memperbarui waktu penawaran</li>
                                                            <li>Pastikan harga yang Anda berikan sudah termasuk semua biaya
                                                                produksi</li>
                                                            <li>Penawaran dapat diedit selama lelang masih aktif</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-warning">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                    <path
                                                        d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                    <path d="M16 5l3 3" />
                                                </svg>
                                                Update Penawaran
                                            </button>
                                            <a href="{{ route('vendor.auctions.show', $bid->auction) }}"
                                                class="btn btn-outline-secondary">
                                                Batal
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Detail Lelang</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-label">Judul</div>
                                    <div class="fw-bold">{{ $bid->auction->title }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="form-label">Kategori</div>
                                    <div>{{ $bid->auction->category }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="form-label">Jumlah Produksi</div>
                                    <div class="fw-bold">{{ number_format($bid->auction->quantity) }} pcs</div>
                                </div>
                                <div class="col-12">
                                    <div class="form-label">Budget Maksimal</div>
                                    <div class="fw-bold text-success">Rp {{ number_format($bid->auction->budget) }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="form-label">Deadline</div>
                                    <div class="fw-bold">{{ $bid->auction->deadline->format('d M Y H:i') }}</div>
                                    <div class="text-muted small">{{ $bid->auction->deadline->diffForHumans() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Penawaran Saat Ini</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-label">Harga</div>
                                    <div class="fw-bold text-success">Rp {{ number_format($bid->bid_amount) }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="form-label">Status</div>
                                    <div>
                                        <span
                                            class="badge bg-{{ $bid->status === 'accepted' ? 'success' : ($bid->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ $bid->status === 'accepted' ? 'Diterima' : ($bid->status === 'rejected' ? 'Ditolak' : 'Menunggu') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-label">Dikirim</div>
                                    <div class="text-muted">{{ $bid->created_at->format('d M Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
