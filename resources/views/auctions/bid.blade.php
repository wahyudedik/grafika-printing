@extends('layouts.vendor')

@section('title', 'Beri Penawaran')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Beri Penawaran</h2>
                    <p class="text-muted">Berikan penawaran terbaik Anda untuk lelang ini</p>
                </div>
                <a href="{{ route('vendor.auctions.show', $auction) }}" class="btn btn-outline-secondary">
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
                            <h3 class="card-title">Form Penawaran</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('vendor.auctions.store-bid', $auction) }}" method="POST">
                                @csrf

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Harga Penawaran <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number"
                                                class="form-control @error('bid_amount') is-invalid @enderror"
                                                name="bid_amount" value="{{ old('bid_amount') }}"
                                                placeholder="Masukkan harga penawaran" min="0" step="1000"
                                                required>
                                            @error('bid_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-text">
                                            Budget maksimal: <strong>Rp {{ number_format($auction->budget) }}</strong>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Pesan (Opsional)</label>
                                        <textarea class="form-control @error('message') is-invalid @enderror" name="message" rows="4"
                                            placeholder="Tambahkan pesan atau catatan untuk pemilik lelang...">{{ old('message') }}</textarea>
                                        @error('message')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            Maksimal 1000 karakter
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="alert alert-info">
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
                                                            <li>Pastikan harga yang Anda berikan sudah termasuk semua biaya
                                                                produksi</li>
                                                            <li>Anda hanya bisa memberikan satu penawaran per lelang</li>
                                                            <li>Penawaran dapat diedit selama lelang masih aktif</li>
                                                            <li>Pemilik lelang akan memilih pemenang secara manual</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-success">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M12 5l0 14" />
                                                    <path d="M5 12l14 0" />
                                                </svg>
                                                Kirim Penawaran
                                            </button>
                                            <a href="{{ route('vendor.auctions.show', $auction) }}"
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
                                    <div class="fw-bold">{{ $auction->title }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="form-label">Kategori</div>
                                    <div>{{ $auction->category }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="form-label">Jumlah Produksi</div>
                                    <div class="fw-bold">{{ number_format($auction->quantity) }} pcs</div>
                                </div>
                                <div class="col-12">
                                    <div class="form-label">Budget Maksimal</div>
                                    <div class="fw-bold text-success">Rp {{ number_format($auction->budget) }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="form-label">Deadline</div>
                                    <div class="fw-bold">{{ $auction->deadline->format('d M Y H:i') }}</div>
                                    <div class="text-muted small">{{ $auction->deadline->diffForHumans() }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="form-label">Pemilik</div>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">{{ substr($auction->user->name, 0, 2) }}</div>
                                        <div>
                                            <div class="fw-bold">{{ $auction->user->name }}</div>
                                            <div class="text-muted small">{{ $auction->user->email }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Statistik</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="text-muted small">Total Penawaran</div>
                                    <div class="fw-bold h4">{{ $auction->getBidCount() }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Penawaran Terendah</div>
                                    <div class="fw-bold h4 text-success">
                                        @if ($auction->getLowestBid())
                                            Rp {{ number_format($auction->getLowestBid()) }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
