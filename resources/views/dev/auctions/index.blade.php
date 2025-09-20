@extends('dev.layouts.app')

@section('title', 'Manajemen Lelang')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Manajemen Lelang
                    </h2>
                    <div class="text-muted mt-1">Kelola semua lelang dan moderasi konten</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.auctions.statistics') }}" class="btn btn-outline-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 3v18h18" />
                                <path d="M18.7 17l-5.1-5.2l-2.8 3.3l-2.2-2.2l-6.6 8" />
                            </svg>
                            Statistik
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Filter Tabs -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="btn-group w-100" role="group">
                        <a href="{{ route('admin.auctions.index', ['status' => '']) }}"
                            class="btn {{ request('status') == '' ? 'btn-primary' : 'btn-outline-primary' }}">
                            Semua ({{ \App\Models\Auction::count() }})
                        </a>
                        <a href="{{ route('admin.auctions.index', ['status' => 'pending']) }}"
                            class="btn {{ request('status') == 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                            Pending ({{ \App\Models\Auction::where('status', 'pending')->count() }})
                        </a>
                        <a href="{{ route('admin.auctions.index', ['status' => 'active']) }}"
                            class="btn {{ request('status') == 'active' ? 'btn-success' : 'btn-outline-success' }}">
                            Aktif ({{ \App\Models\Auction::where('status', 'active')->count() }})
                        </a>
                        <a href="{{ route('admin.auctions.index', ['status' => 'rejected']) }}"
                            class="btn {{ request('status') == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                            Ditolak ({{ \App\Models\Auction::where('status', 'rejected')->count() }})
                        </a>
                    </div>
                </div>
            </div>

            <!-- Auctions List -->
            <div class="row">
                @forelse($auctions as $auction)
                    <div class="col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="card-title">{{ Str::limit($auction->title, 30) }}</h3>
                                    <span
                                        class="badge bg-{{ $auction->status == 'pending' ? 'warning' : ($auction->status == 'active' ? 'success' : ($auction->status == 'rejected' ? 'danger' : 'secondary')) }}">
                                        {{ ucfirst($auction->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-muted">User</div>
                                        <div class="fw-bold">{{ $auction->user->name }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted">Budget</div>
                                        <div class="fw-bold">Rp {{ number_format($auction->budget, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-6">
                                        <div class="text-muted">Kategori</div>
                                        <div>{{ $auction->category }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted">Quantity</div>
                                        <div>{{ $auction->quantity }} pcs</div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <div class="text-muted">Deadline</div>
                                        <div>{{ $auction->deadline->format('d M Y H:i') }}</div>
                                    </div>
                                </div>
                                @if ($auction->status == 'rejected' && $auction->rejection_reason)
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <div class="text-muted">Alasan Ditolak</div>
                                            <div class="text-danger">{{ $auction->rejection_reason }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer">
                                <div class="btn-group w-100">
                                    <a href="{{ route('admin.auctions.show', $auction) }}" class="btn btn-outline-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                            <path
                                                d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                        </svg>
                                        Detail
                                    </a>
                                    @if ($auction->status == 'pending')
                                        <form action="{{ route('admin.auctions.approve', $auction) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success"
                                                onclick="return confirm('Setujui lelang ini?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M5 12l5 5l10 -10" />
                                                </svg>
                                                Setujui
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#rejectModal{{ $auction->id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M18 6l-12 12" />
                                                <path d="M6 6l12 12" />
                                            </svg>
                                            Tolak
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reject Modal -->
                    @if ($auction->status == 'pending')
                        <div class="modal modal-blur fade" id="rejectModal{{ $auction->id }}" tabindex="-1"
                            role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <form action="{{ route('admin.auctions.reject', $auction) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Tolak Lelang</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Alasan Penolakan</label>
                                                <textarea class="form-control" name="rejection_reason" rows="4"
                                                    placeholder="Masukkan alasan penolakan lelang..." required></textarea>
                                                <div class="form-hint">Alasan ini akan dikirim ke user yang membuat lelang.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Tolak Lelang</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-12">
                        <div class="empty">
                            <div class="empty-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 3v18h18" />
                                    <path d="M18.7 17l-5.1-5.2l-2.8 3.3l-2.2-2.2l-6.6 8" />
                                </svg>
                            </div>
                            <p class="empty-title">Tidak ada lelang</p>
                            <p class="empty-subtitle text-muted">
                                Belum ada lelang yang sesuai dengan filter yang dipilih.
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $auctions->links() }}
            </div>
        </div>
    </div>
@endsection
