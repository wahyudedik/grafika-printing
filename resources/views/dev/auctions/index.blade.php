@extends('dev.layouts.app')

@section('title', 'Manajemen Lelang')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Manajemen Lelang</h2>
                    <p class="text-muted">Kelola semua lelang dan penawaran dari vendor</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.auctions.statistics') }}" class="btn btn-outline-info">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                        </svg>
                        Statistik
                    </a>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="card mb-4">
                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') == null ? 'active' : '' }}"
                                href="{{ route('admin.auctions.index') }}">
                                Semua
                                <span class="badge bg-secondary ms-1">{{ $auctions->total() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}"
                                href="{{ route('admin.auctions.index', ['status' => 'pending']) }}">
                                Menunggu Persetujuan
                                <span
                                    class="badge bg-warning ms-1">{{ $auctions->where('status', 'pending')->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') == 'active' ? 'active' : '' }}"
                                href="{{ route('admin.auctions.index', ['status' => 'active']) }}">
                                Aktif
                                <span
                                    class="badge bg-success ms-1">{{ $auctions->where('status', 'active')->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') == 'closed' ? 'active' : '' }}"
                                href="{{ route('admin.auctions.index', ['status' => 'closed']) }}">
                                Ditutup
                                <span class="badge bg-info ms-1">{{ $auctions->where('status', 'closed')->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') == 'rejected' ? 'active' : '' }}"
                                href="{{ route('admin.auctions.index', ['status' => 'rejected']) }}">
                                Ditolak
                                <span
                                    class="badge bg-danger ms-1">{{ $auctions->where('status', 'rejected')->count() }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Auctions List -->
            <div class="row">
                @forelse($auctions as $auction)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">{{ Str::limit($auction->title, 30) }}</h5>
                                    <span
                                        class="badge 
                                    @if ($auction->status === 'pending') bg-warning
                                    @elseif($auction->status === 'active') bg-success
                                    @elseif($auction->status === 'closed') bg-info
                                    @elseif($auction->status === 'rejected') bg-danger
                                    @else bg-secondary @endif">
                                        {{ ucfirst($auction->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="text-muted small">Oleh</div>
                                            <div class="fw-bold">{{ $auction->user->name }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small">Budget</div>
                                            <div class="fw-bold text-success">Rp {{ number_format($auction->budget) }}
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small">Jumlah</div>
                                            <div class="fw-bold">{{ number_format($auction->quantity) }} pcs</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small">Deadline</div>
                                            <div class="fw-bold">{{ $auction->deadline->format('d M Y') }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="text-muted small">Kategori</div>
                                    <div class="fw-bold">{{ $auction->category }}</div>
                                </div>

                                <div class="mb-3">
                                    <div class="text-muted small">Penawaran</div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">{{ $auction->bids->count() }} penawaran</span>
                                        @if ($auction->bids->count() > 0)
                                            <span class="text-success small">
                                                Terendah: Rp {{ number_format($auction->bids->min('bid_amount')) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-muted small">
                                    Dibuat: {{ $auction->created_at->format('d M Y H:i') }}
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.auctions.show', $auction) }}"
                                        class="btn btn-outline-primary btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                            <path
                                                d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                        </svg>
                                        Detail
                                    </a>
                                    <a href="{{ route('admin.auctions.edit', $auction) }}"
                                        class="btn btn-outline-warning btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                            <path
                                                d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                            <path d="M16 5l3 3" />
                                        </svg>
                                        Edit
                                    </a>

                                    @if ($auction->status === 'pending')
                                        <form action="{{ route('admin.auctions.approve', $auction) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm"
                                                onclick="return confirm('Setujui lelang ini?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16"
                                                    height="16" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M5 12l5 5l10 -10" />
                                                </svg>
                                                Setujui
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.auctions.reject', $auction) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Tolak lelang ini?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16"
                                                    height="16" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M18 6l-12 12" />
                                                    <path d="M6 6l12 12" />
                                                </svg>
                                                Tolak
                                            </button>
                                        </form>
                                    @endif

                                    @if ($auction->status === 'active')
                                        <form action="{{ route('admin.auctions.close', $auction) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm"
                                                onclick="return confirm('Tutup lelang ini?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16"
                                                    height="16" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M18 6l-12 12" />
                                                    <path d="M6 6l12 12" />
                                                </svg>
                                                Tutup
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.auctions.destroy', $auction) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Hapus lelang ini? Tindakan ini tidak dapat dibatalkan!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16"
                                                height="16" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 7l16 0" />
                                                <path d="M10 11l0 6" />
                                                <path d="M14 11l0 6" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty">
                            <div class="empty-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="128" height="128"
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
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($auctions->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $auctions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
