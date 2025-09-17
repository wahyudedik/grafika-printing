@extends('dev.layouts.app')

@section('title', 'Statistik Lelang')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Statistik Lelang</h2>
                    <p class="text-muted">Ringkasan data lelang dan penawaran</p>
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
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Lelang</div>
                            </div>
                            <div class="h1 mb-3">{{ $stats['total_auctions'] }}</div>
                            <div class="d-flex mb-2">
                                <div class="text-muted">Semua lelang yang pernah dibuat</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Lelang Aktif</div>
                            </div>
                            <div class="h1 mb-3 text-success">{{ $stats['active_auctions'] }}</div>
                            <div class="d-flex mb-2">
                                <div class="text-muted">Sedang berlangsung</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Menunggu Persetujuan</div>
                            </div>
                            <div class="h1 mb-3 text-warning">{{ $stats['pending_auctions'] }}</div>
                            <div class="d-flex mb-2">
                                <div class="text-muted">Belum disetujui</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Penawaran</div>
                            </div>
                            <div class="h1 mb-3 text-info">{{ $stats['total_bids'] }}</div>
                            <div class="d-flex mb-2">
                                <div class="text-muted">Dari semua vendor</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Stats -->
            <div class="row g-4 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Lelang Ditutup</div>
                            </div>
                            <div class="h1 mb-3 text-info">{{ $stats['closed_auctions'] }}</div>
                            <div class="d-flex mb-2">
                                <div class="text-muted">Sudah selesai</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Lelang Ditolak</div>
                            </div>
                            <div class="h1 mb-3 text-danger">{{ $stats['rejected_auctions'] }}</div>
                            <div class="d-flex mb-2">
                                <div class="text-muted">Tidak disetujui</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total User</div>
                            </div>
                            <div class="h1 mb-3 text-primary">{{ $stats['total_users'] }}</div>
                            <div class="d-flex mb-2">
                                <div class="text-muted">User yang membuat lelang</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Rata-rata Penawaran</div>
                            </div>
                            <div class="h1 mb-3 text-success">
                                @if ($stats['total_bids'] > 0)
                                    {{ number_format($stats['total_bids'] / $stats['total_auctions'], 1) }}
                                @else
                                    0
                                @endif
                            </div>
                            <div class="d-flex mb-2">
                                <div class="text-muted">Per lelang</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Auctions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lelang Terbaru</h3>
                </div>
                <div class="card-body">
                    @if ($recentAuctions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Judul Lelang</th>
                                        <th>User</th>
                                        <th>Budget</th>
                                        <th>Penawaran</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentAuctions as $auction)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ Str::limit($auction->title, 30) }}</div>
                                                <div class="text-muted small">{{ $auction->category }}</div>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $auction->user->name }}</div>
                                                <div class="text-muted small">{{ $auction->user->email }}</div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-success">Rp {{ number_format($auction->budget) }}
                                                </div>
                                                <div class="text-muted small">{{ number_format($auction->quantity) }} pcs
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $auction->bids->count() }} penawaran</div>
                                                @if ($auction->bids->count() > 0)
                                                    <div class="text-success small">
                                                        Terendah: Rp {{ number_format($auction->bids->min('bid_amount')) }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge 
                                                @if ($auction->status === 'pending') bg-warning
                                                @elseif($auction->status === 'active') bg-success
                                                @elseif($auction->status === 'closed') bg-info
                                                @elseif($auction->status === 'rejected') bg-danger
                                                @else bg-secondary @endif">
                                                    {{ ucfirst($auction->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>{{ $auction->created_at->format('d M Y') }}</div>
                                                <div class="text-muted small">{{ $auction->created_at->format('H:i') }}
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.auctions.show', $auction) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16"
                                                        height="16" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                        <path
                                                            d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                    </svg>
                                                    Detail
                                                </a>
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
                            <p class="empty-title">Belum ada lelang</p>
                            <p class="empty-subtitle text-muted">
                                Belum ada lelang yang tersedia saat ini.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
