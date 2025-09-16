@extends('layouts.vendor')

@section('title', 'Penawaran Saya')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Penawaran Saya</h2>
                    <p class="text-muted">Kelola semua penawaran yang telah Anda berikan</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('vendor.auctions.index') }}" class="btn btn-outline-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                        </svg>
                        Lihat Lelang
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

            @if ($bids->count() > 0)
                <div class="row g-4">
                    @foreach ($bids as $bid)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title mb-0">{{ $bid->auction->title }}</h5>
                                        <span
                                            class="badge bg-{{ $bid->status === 'accepted' ? 'success' : ($bid->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ $bid->status === 'accepted' ? 'Diterima' : ($bid->status === 'rejected' ? 'Ditolak' : 'Menunggu') }}
                                        </span>
                                    </div>

                                    <p class="text-muted small mb-3">{{ Str::limit($bid->auction->description, 100) }}</p>

                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="text-muted small">Kategori</div>
                                            <div class="fw-bold">{{ $bid->auction->category }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small">Jumlah</div>
                                            <div class="fw-bold">{{ number_format($bid->auction->quantity) }} pcs</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small">Penawaran Saya</div>
                                            <div class="fw-bold text-success">Rp {{ number_format($bid->bid_amount) }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small">Budget Maks</div>
                                            <div class="fw-bold">Rp {{ number_format($bid->auction->budget) }}</div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="text-muted small">
                                            Oleh: <span class="fw-bold">{{ $bid->auction->user->name }}</span>
                                        </div>
                                        <div class="text-muted small">
                                            {{ $bid->created_at->format('d M Y') }}
                                        </div>
                                    </div>

                                    @if ($bid->message)
                                        <div class="alert alert-info py-2 mb-3">
                                            <div class="small">
                                                <strong>Pesan:</strong> {{ $bid->message }}
                                            </div>
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="text-muted small">
                                            Deadline: <span
                                                class="fw-bold">{{ $bid->auction->deadline->format('d M Y') }}</span>
                                        </div>
                                        <div class="text-muted small">
                                            Status Lelang:
                                            <span
                                                class="badge bg-{{ $bid->auction->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ $bid->auction->status === 'active' ? 'Aktif' : ucfirst($bid->auction->status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <a href="{{ route('vendor.auctions.show', $bid->auction) }}"
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
                                        @if ($bid->auction->isActive() && $bid->status === 'pending')
                                            <a href="{{ route('vendor.auctions.edit-bid', $bid) }}"
                                                class="btn btn-warning btn-sm flex-fill">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16"
                                                    height="16" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                    <path
                                                        d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                    <path d="M16 5l3 3" />
                                                </svg>
                                                Edit
                                            </a>
                                        @endif
                                    </div>

                                    @if ($bid->auction->isActive() && $bid->status === 'pending')
                                        <div class="mt-2">
                                            <form action="{{ route('vendor.auctions.destroy-bid', $bid) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus penawaran ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm w-100">
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
                                                    Hapus Penawaran
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $bids->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty">
                        <div class="empty-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="128" height="128"
                                viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg>
                        </div>
                        <p class="empty-title">Belum ada penawaran</p>
                        <p class="empty-subtitle text-muted">
                            Anda belum memberikan penawaran untuk lelang apapun. Mulai berikan penawaran untuk lelang yang
                            sesuai dengan kemampuan produksi Anda.
                        </p>
                        <div class="empty-action">
                            <a href="{{ route('vendor.auctions.index') }}" class="btn btn-primary">Lihat Lelang</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
