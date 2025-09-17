@extends('dev.layouts.app')

@section('title', 'Penawaran Lelang')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">{{ $auction->title }}</h2>
                    <p class="text-muted">Semua penawaran untuk lelang ini</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.auctions.show', $auction) }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 6l6 6l-6 6" />
                        </svg>
                        Kembali ke Detail
                    </a>
                </div>
            </div>

            <!-- Auction Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Informasi Lelang</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="text-muted small">Status</div>
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
                        <div class="col-md-3">
                            <div class="text-muted small">Budget</div>
                            <div class="fw-bold text-success">Rp {{ number_format($auction->budget) }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Jumlah</div>
                            <div class="fw-bold">{{ number_format($auction->quantity) }} pcs</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Deadline</div>
                            <div class="fw-bold">{{ $auction->deadline->format('d M Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bids List -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Penawaran ({{ $bids->count() }})</h3>
                </div>
                <div class="card-body">
                    @if ($bids->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Vendor</th>
                                        <th>Harga Penawaran</th>
                                        <th>Pesan</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bids as $index => $bid)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar me-2">
                                                        @if ($bid->vendor->logo)
                                                            <img src="{{ asset('storage/' . $bid->vendor->logo) }}"
                                                                alt="{{ $bid->vendor->name }}" class="avatar-img">
                                                        @else
                                                            <div class="avatar-text">{{ substr($bid->vendor->name, 0, 1) }}
                                                            </div>
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
