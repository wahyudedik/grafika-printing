@extends('dev.layouts.app')

@section('title', 'Detail Lelang')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">{{ $auction->title }}</h2>
                    <p class="text-muted">Detail lelang dan semua penawaran</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.auctions.index') }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 6l6 6l-6 6" />
                        </svg>
                        Kembali
                    </a>
                    <a href="{{ route('admin.auctions.edit', $auction) }}" class="btn btn-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                            <path d="M16 5l3 3" />
                        </svg>
                        Edit Lelang
                    </a>

                    @if ($auction->status === 'pending')
                        <form action="{{ route('admin.auctions.approve', $auction) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('Setujui lelang ini?')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                                Setujui Lelang
                            </button>
                        </form>

                        <form action="{{ route('admin.auctions.reject', $auction) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Tolak lelang ini?')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M18 6l-12 12" />
                                    <path d="M6 6l12 12" />
                                </svg>
                                Tolak Lelang
                            </button>
                        </form>
                    @endif

                    @if ($auction->status === 'active')
                        <form action="{{ route('admin.auctions.close', $auction) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning" onclick="return confirm('Tutup lelang ini?')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M18 6l-12 12" />
                                    <path d="M6 6l12 12" />
                                </svg>
                                Tutup Lelang
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="row">
                <!-- Auction Details -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Detail Lelang</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Status</label>
                                        <div>
                                            <span
                                                class="badge 
                                                @if ($auction->status === 'pending') bg-warning
                                                @elseif($auction->status === 'active') bg-success
                                                @elseif($auction->status === 'closed') bg-info
                                                @elseif($auction->status === 'rejected') bg-danger
                                                @else bg-secondary @endif fs-6">
                                                {{ ucfirst($auction->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Kategori</label>
                                        <div class="fw-bold">{{ $auction->category }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Oleh</label>
                                        <div class="fw-bold">{{ $auction->user->name }}</div>
                                        <div class="text-muted small">{{ $auction->user->email }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Budget Maksimal</label>
                                        <div class="fw-bold text-success fs-5">Rp {{ number_format($auction->budget) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Jumlah Produksi</label>
                                        <div class="fw-bold">{{ number_format($auction->quantity) }} pcs</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Deadline</label>
                                        <div class="fw-bold">{{ $auction->deadline->format('d M Y H:i') }}</div>
                                        <div class="text-muted small">
                                            @if ($auction->deadline->isFuture())
                                                {{ $auction->deadline->diffForHumans() }}
                                            @else
                                                <span class="text-danger">Sudah lewat</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Deskripsi</label>
                                        <div class="border rounded p-3 bg-light">
                                            {{ $auction->description }}
                                        </div>
                                    </div>
                                </div>
                                @if ($auction->specifications)
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Spesifikasi</label>
                                            <div class="border rounded p-3 bg-light">
                                                <pre class="mb-0">{{ $auction->specifications }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if ($auction->file_path)
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label text-muted">File Lampiran</label>
                                            <div>
                                                <a href="{{ asset('storage/' . $auction->file_path) }}" target="_blank"
                                                    class="btn btn-outline-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                        <path
                                                            d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                        <path d="M9 9l1 1l3 -3" />
                                                    </svg>
                                                    Download File
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bids Summary -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Ringkasan Penawaran</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Total Penawaran</span>
                                        <span class="fw-bold fs-5">{{ $bids->count() }}</span>
                                    </div>
                                </div>
                                @if ($bids->count() > 0)
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Penawaran Terendah</span>
                                            <span class="fw-bold text-success">Rp
                                                {{ number_format($bids->min('bid_amount')) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Penawaran Tertinggi</span>
                                            <span class="fw-bold text-danger">Rp
                                                {{ number_format($bids->max('bid_amount')) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Rata-rata</span>
                                            <span class="fw-bold">Rp {{ number_format($bids->avg('bid_amount')) }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Winner Info -->
                    @if ($auction->winnerVendor)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3 class="card-title">Pemenang</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3">
                                        @if ($auction->winnerVendor->logo)
                                            <img src="{{ asset('storage/' . $auction->winnerVendor->logo) }}"
                                                alt="{{ $auction->winnerVendor->name }}" class="avatar-img">
                                        @else
                                            <div class="avatar-text">{{ substr($auction->winnerVendor->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $auction->winnerVendor->name }}</div>
                                        <div class="text-muted small">{{ $auction->winnerVendor->email }}</div>
                                        <div class="text-success fw-bold">Rp {{ number_format($auction->winning_bid) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Bids List -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Penawaran</h3>
                </div>
                <div class="card-body">
                    @if ($bids->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Vendor</th>
                                        <th>Harga Penawaran</th>
                                        <th>Pesan</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bids as $bid)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar me-2">
                                                        @if ($bid->vendor->logo)
                                                            <img src="{{ asset('storage/' . $bid->vendor->logo) }}"
                                                                alt="{{ $bid->vendor->name }}" class="avatar-img">
                                                        @else
                                                            <div class="avatar-text">
                                                                {{ substr($bid->vendor->name, 0, 1) }}</div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $bid->vendor->name }}</div>
                                                        <div class="text-muted small">{{ $bid->vendor->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-success">Rp {{ number_format($bid->bid_amount) }}
                                                </div>
                                                @if ($bid->bid_amount <= $auction->budget)
                                                    <span class="badge bg-success">Dalam Budget</span>
                                                @else
                                                    <span class="badge bg-warning">Melebihi Budget</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($bid->message)
                                                    <div class="text-truncate" style="max-width: 200px;"
                                                        title="{{ $bid->message }}">
                                                        {{ $bid->message }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge 
                                                @if ($bid->status === 'accepted') bg-success
                                                @elseif($bid->status === 'rejected') bg-danger
                                                @else bg-warning @endif">
                                                    {{ ucfirst($bid->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>{{ $bid->created_at->format('d M Y') }}</div>
                                                <div class="text-muted small">{{ $bid->created_at->format('H:i') }}</div>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#bidModal{{ $bid->id }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon"
                                                            width="16" height="16" viewBox="0 0 24 24"
                                                            stroke-width="2" stroke="currentColor" fill="none"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                            <path
                                                                d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty">
                            <div class="empty-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="64" height="64"
                                    viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                    <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                </svg>
                            </div>
                            <p class="empty-title">Belum ada penawaran</p>
                            <p class="empty-subtitle text-muted">
                                Belum ada vendor yang memberikan penawaran untuk lelang ini.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bid Detail Modals -->
    @foreach ($bids as $bid)
        <div class="modal modal-blur fade" id="bidModal{{ $bid->id }}" tabindex="-1" role="dialog"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Penawaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Vendor</label>
                                <div class="fw-bold">{{ $bid->vendor->name }}</div>
                                <div class="text-muted small">{{ $bid->vendor->email }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Harga Penawaran</label>
                                <div class="fw-bold text-success fs-4">Rp {{ number_format($bid->bid_amount) }}</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted">Pesan</label>
                                <div class="border rounded p-3 bg-light">
                                    {{ $bid->message ?: 'Tidak ada pesan' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Status</label>
                                <div>
                                    <span
                                        class="badge 
                                    @if ($bid->status === 'accepted') bg-success
                                    @elseif($bid->status === 'rejected') bg-danger
                                    @else bg-warning @endif fs-6">
                                        {{ ucfirst($bid->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Tanggal Penawaran</label>
                                <div>{{ $bid->created_at->format('d M Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
