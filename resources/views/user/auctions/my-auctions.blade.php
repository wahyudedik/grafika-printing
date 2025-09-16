@extends('layouts.user')

@section('title', 'Lelang Saya')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Lelang Saya</h2>
                    <p class="text-muted">Kelola permintaan cetak yang telah Anda buat</p>
                </div>
                <a href="{{ route('auctions.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Buat Permintaan Baru
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($auctions->count() > 0)
                <div class="row g-4">
                    @foreach ($auctions as $auction)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-blue-lt">{{ $auction->category }}</span>
                                        <span
                                            class="badge bg-{{ $auction->status === 'active' ? 'green' : ($auction->status === 'closed' ? 'blue' : 'red') }}-lt">
                                            {{ ucfirst($auction->status) }}
                                        </span>
                                    </div>

                                    <h5 class="card-title mb-2">{{ $auction->title }}</h5>
                                    <p class="card-text text-muted small mb-3">{{ Str::limit($auction->description, 100) }}
                                    </p>

                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="d-flex align-items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm text-muted me-1"
                                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M7 7h10v10l-4 -4l-6 6" />
                                                </svg>
                                                <small class="text-muted">{{ number_format($auction->quantity) }}
                                                    pcs</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm text-muted me-1"
                                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M12 2l3.09 6.26l6.91 1.01l-5 4.87l1.18 6.88l-6.18 -3.25l-6.18 3.25l1.18 -6.88l-5 -4.87l6.91 -1.01z" />
                                                </svg>
                                                <small class="text-muted">Rp {{ number_format($auction->budget) }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm text-muted me-1"
                                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                                <path d="M16 3l0 4" />
                                                <path d="M8 3l0 4" />
                                                <path d="M4 11l16 0" />
                                            </svg>
                                            <small class="text-muted">{{ $auction->deadline->format('d M Y') }}</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm text-muted me-1"
                                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                            </svg>
                                            <small class="text-muted">{{ $auction->getBidCount() }} penawaran</small>
                                        </div>
                                    </div>

                                    @if ($auction->status === 'closed' && $auction->winnerVendor)
                                        <div class="alert alert-success small mb-3">
                                            <strong>Pemenang:</strong> {{ $auction->winnerVendor->name }}<br>
                                            <strong>Harga:</strong> Rp {{ number_format($auction->winning_bid) }}
                                        </div>
                                    @endif

                                    <div class="d-flex gap-2">
                                        <a href="{{ route('auctions.show', $auction) }}"
                                            class="btn btn-primary btn-sm flex-fill">
                                            Lihat Detail
                                        </a>
                                        @if ($auction->status === 'active')
                                            <a href="{{ route('auctions.edit', $auction) }}"
                                                class="btn btn-outline-secondary btn-sm">
                                                Edit
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $auctions->links() }}
                </div>
            @else
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
                        Anda belum membuat permintaan cetak. Buat permintaan pertama Anda sekarang!
                    </p>
                    <div class="empty-action">
                        <a href="{{ route('auctions.create') }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg>
                            Buat Permintaan Pertama
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
