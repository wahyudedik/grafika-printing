@extends('layouts.vendor')

@section('title', 'Detail Lelang')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">{{ $auction->title }}</h2>
                    <p class="text-muted">Detail lelang dan penawaran</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('vendor.auctions.index') }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 6l6 6l-6 6" />
                        </svg>
                        Kembali
                    </a>
                    @if ($auction->isActive() && !$myBid)
                        <a href="{{ route('vendor.auctions.bid', $auction) }}" class="btn btn-success">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg>
                            Beri Penawaran
                        </a>
                    @endif
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

            @if (session('info'))
                <div class="alert alert-info">
                    {{ session('info') }}
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Lelang</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-label">Judul Lelang</div>
                                    <div class="fw-bold">{{ $auction->title }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-label">Kategori</div>
                                    <div class="fw-bold">{{ $auction->category }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-label">Jumlah Produksi</div>
                                    <div class="fw-bold">{{ number_format($auction->quantity) }} pcs</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-label">Budget Maksimal</div>
                                    <div class="fw-bold text-success">Rp {{ number_format($auction->budget) }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-label">Deadline</div>
                                    <div class="fw-bold">{{ $auction->deadline->format('d M Y H:i') }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-label">Status</div>
                                    <div>
                                        <span
                                            class="badge bg-{{ $auction->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ $auction->status === 'active' ? 'Aktif' : ucfirst($auction->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-label">Deskripsi</div>
                                    <div class="text-muted">{{ $auction->description }}</div>
                                </div>
                                @if ($auction->specifications)
                                    <div class="col-12">
                                        <div class="form-label">Spesifikasi Khusus</div>
                                        <div class="text-muted">{{ $auction->specifications }}</div>
                                    </div>
                                @endif
                                @if ($auction->file_path)
                                    <div class="col-12">
                                        <div class="form-label">File Lampiran</div>
                                        <div>
                                            <a href="{{ asset('storage/auction_files/' . $auction->file_path) }}"
                                                target="_blank" class="btn btn-outline-primary btn-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16"
                                                    height="16" viewBox="0 0 24 24" stroke-width="2"
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
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Penawaran Saya</h3>
                        </div>
                        <div class="card-body">
                            @if ($myBid)
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-label">Harga Penawaran</div>
                                        <div class="fw-bold text-success">Rp {{ number_format($myBid->bid_amount) }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-label">Status</div>
                                        <div>
                                            <span
                                                class="badge bg-{{ $myBid->status === 'accepted' ? 'success' : ($myBid->status === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ $myBid->status === 'accepted' ? 'Diterima' : ($myBid->status === 'rejected' ? 'Ditolak' : 'Menunggu') }}
                                            </span>
                                        </div>
                                    </div>
                                    @if ($myBid->message)
                                        <div class="col-12">
                                            <div class="form-label">Pesan</div>
                                            <div class="text-muted">{{ $myBid->message }}</div>
                                        </div>
                                    @endif
                                    <div class="col-12">
                                        <div class="form-label">Dikirim pada</div>
                                        <div class="text-muted">{{ $myBid->created_at->format('d M Y H:i') }}</div>
                                    </div>
                                </div>

                                @if ($auction->isActive() && $myBid->status === 'pending')
                                    <div class="d-flex gap-2 mt-3">
                                        <a href="{{ route('vendor.auctions.edit-bid', $myBid) }}"
                                            class="btn btn-warning btn-sm">
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
                                            Edit Penawaran
                                        </a>
                                        <form action="{{ route('vendor.auctions.destroy-bid', $myBid) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus penawaran ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
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
                            @else
                                <div class="text-center py-4">
                                    <div class="empty">
                                        <div class="empty-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="64"
                                                height="64" viewBox="0 0 24 24" stroke-width="1"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M12 5l0 14" />
                                                <path d="M5 12l14 0" />
                                            </svg>
                                        </div>
                                        <p class="empty-title">Belum ada penawaran</p>
                                        <p class="empty-subtitle text-muted">
                                            Anda belum memberikan penawaran untuk lelang ini.
                                        </p>
                                        @if ($auction->isActive())
                                            <div class="empty-action">
                                                <a href="{{ route('vendor.auctions.bid', $auction) }}"
                                                    class="btn btn-primary">
                                                    Beri Penawaran
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Pemilik</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-sm me-3">{{ substr($auction->user->name, 0, 2) }}</div>
                                <div>
                                    <div class="fw-bold">{{ $auction->user->name }}</div>
                                    <div class="text-muted small">{{ $auction->user->email }}</div>
                                </div>
                            </div>
                            <div class="text-muted small">
                                <div>Bergabung: {{ $auction->user->created_at->format('M Y') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Statistik Lelang</h3>
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
                                <div class="col-6">
                                    <div class="text-muted small">Sisa Waktu</div>
                                    <div class="fw-bold">
                                        @if ($auction->deadline > now())
                                            {{ $auction->deadline->diffForHumans() }}
                                        @else
                                            <span class="text-danger">Berakhir</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Dibuat</div>
                                    <div class="fw-bold">{{ $auction->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
