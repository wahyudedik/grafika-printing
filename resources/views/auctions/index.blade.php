@extends('layouts.vendor')

@section('title', 'Daftar Lelang')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Daftar Lelang</h2>
                    <p class="text-muted">Pilih lelang yang sesuai dengan kemampuan produksi Anda</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('vendor.auctions.my-bids') }}" class="btn btn-outline-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                        </svg>
                        Penawaran Saya
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if ($auctions->count() > 0)
                <div class="row g-4">
                    @foreach ($auctions as $auction)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title mb-0">{{ $auction->title }}</h5>
                                        <span
                                            class="badge bg-{{ $auction->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ $auction->status === 'active' ? 'Aktif' : ucfirst($auction->status) }}
                                        </span>
                                    </div>

                                    <p class="text-muted small mb-3">{{ Str::limit($auction->description, 100) }}</p>

                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="text-muted small">Kategori</div>
                                            <div class="fw-bold">{{ $auction->category }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small">Jumlah</div>
                                            <div class="fw-bold">{{ number_format($auction->quantity) }} pcs</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small">Budget</div>
                                            <div class="fw-bold text-success">Rp {{ number_format($auction->budget) }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small">Deadline</div>
                                            <div class="fw-bold">{{ $auction->deadline->format('d M Y') }}</div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="text-muted small">
                                            Oleh: <span class="fw-bold">{{ $auction->user->name }}</span>
                                        </div>
                                        <div class="text-muted small">
                                            {{ $auction->getBidCount() }} penawaran
                                        </div>
                                    </div>

                                    @if ($auction->getLowestBid())
                                        <div class="alert alert-info py-2 mb-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="small">Penawaran Terendah:</span>
                                                <span class="fw-bold">Rp
                                                    {{ number_format($auction->getLowestBid()) }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="d-flex gap-2">
                                        <a href="{{ route('vendor.auctions.show', $auction) }}"
                                            class="btn btn-primary btn-sm flex-fill">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16"
                                                height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                <path
                                                    d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                            </svg>
                                            Lihat Detail
                                        </a>
                                        @if ($auction->isActive())
                                            <a href="{{ route('vendor.auctions.bid', $auction) }}"
                                                class="btn btn-success btn-sm flex-fill">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16"
                                                    height="16" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M12 5l0 14" />
                                                    <path d="M5 12l14 0" />
                                                </svg>
                                                Beri Penawaran
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
                <div class="text-center py-5">
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
                        <p class="empty-title">Belum ada lelang aktif</p>
                        <p class="empty-subtitle text-muted">
                            Saat ini belum ada lelang yang tersedia. Silakan kembali lagi nanti.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
