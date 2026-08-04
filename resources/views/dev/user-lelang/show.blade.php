@extends('dev.layouts.app')

@section('title', 'Detail User Lelang - ' . ($profile->user->name ?? 'Unknown'))

@section('content')
<div class="row row-deck row-cards">
    <!-- Profile Card -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar avatar-xl bg-primary-lt mb-3">
                    {{ strtoupper(substr($profile->user->name ?? 'U', 0, 2)) }}
                </div>
                <h3 class="card-title">{{ $profile->user->name ?? '-' }}</h3>
                <p class="text-muted">{{ $profile->user->email ?? '-' }}</p>
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-{{ $profile->status_color }}">
                        {{ $profile->status_label }}
                    </span>
                    @if($profile->is_verified)
                        <span class="badge bg-success-lt">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline" width="16" height="16"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 12l5 5l10 -10" />
                            </svg>
                            Terverifikasi
                        </span>
                    @else
                        <span class="badge bg-secondary-lt">Belum Verifikasi</span>
                    @endif
                </div>

                <!-- Quick Stats -->
                <div class="row g-2 mt-2">
                    <div class="col-4">
                        <div class="border rounded p-2">
                            <div class="h3 mb-0">{{ $profile->total_auctions }}</div>
                            <small class="text-muted">Lelang</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-2">
                            <div class="h3 mb-0 text-success">{{ $profile->total_won }}</div>
                            <small class="text-muted">Menang</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-2">
                            <div class="h3 mb-0 text-primary">{{ $profile->win_rate }}%</div>
                            <small class="text-muted">Win Rate</small>
                        </div>
                    </div>
                </div>

                <!-- Win Rate Progress -->
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">Tingkat Kemenangan</small>
                        <small class="fw-bold">{{ $profile->win_rate }}%</small>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: {{ $profile->win_rate }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Profil</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted small">Perusahaan</label>
                    <div class="fw-bold">{{ $profile->company_name ?? '-' }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Telepon</label>
                    <div class="fw-bold">{{ $profile->phone_number ?? '-' }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Alamat</label>
                    <div class="fw-bold">{{ $profile->address ?? '-' }}</div>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label text-muted small">Kota</label>
                        <div class="fw-bold">{{ $profile->city ?? '-' }}</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small">Provinsi</label>
                        <div class="fw-bold">{{ $profile->province ?? '-' }}</div>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label text-muted small">Total Belanja</label>
                    <div class="h3 text-primary mb-0">Rp {{ number_format($profile->total_spent, 0, ',', '.') }}</div>
                </div>
                @if($profile->total_auctions > 0)
                <div class="mt-2">
                    <label class="form-label text-muted small">Rata-rata per Lelang</label>
                    <div class="fw-bold">Rp {{ number_format($profile->total_spent / max($profile->total_auctions, 1), 0, ',', '.') }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Notes -->
        @if($profile->notes)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Catatan Admin</h3>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $profile->notes }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Actions Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Aksi</h3>
            </div>
            <div class="card-body">
                <div class="btn-list">
                    <a href="{{ route('admin.user-lelang.edit', $profile) }}" class="btn btn-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5 -5.5Z" />
                            <path d="M15 5l4 4" />
                        </svg>
                        Edit Profil
                    </a>

                    @if(!$profile->is_verified)
                        <form action="{{ route('admin.user-lelang.verify', $profile) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success"
                                onclick="return confirm('Yakin ingin memverifikasi profil ini?')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                                Verifikasi
                            </button>
                        </form>
                    @endif

                    @if($profile->isActive())
                        <!-- Suspend Button -->
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                            data-bs-target="#suspendModal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M9 12l6 0" />
                            </svg>
                            Tangguhkan
                        </button>
                    @elseif($profile->isSuspended())
                        <form action="{{ route('admin.user-lelang.reactivate', $profile) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success"
                                onclick="return confirm('Yakin ingin mengaktifkan kembali profil ini?')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                    <path d="M9 12l2 2l4 -4" />
                                </svg>
                                Aktifkan Kembali
                            </button>
                        </form>
                    @endif

                    <!-- Delete Button -->
                    <form action="{{ route('admin.user-lelang.destroy', $profile) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger"
                            onclick="return confirm('Yakin ingin menghapus profil ini? Tindakan ini tidak dapat dibatalkan.')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
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

                    <a href="{{ route('admin.user-lelang.index') }}" class="btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M15 11l-5 3l5 3v-6z" />
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Auction Statistics -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Statistik Lelang</h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6 col-lg-3">
                        <div class="border rounded p-3 text-center">
                            <div class="h2 mb-1">{{ $auctionStats['total'] }}</div>
                            <small class="text-muted">Total Lelang</small>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="border rounded p-3 text-center">
                            <div class="h2 mb-1 text-primary">{{ $auctionStats['active'] }}</div>
                            <small class="text-muted">Aktif</small>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="border rounded p-3 text-center">
                            <div class="h2 mb-1 text-success">{{ $auctionStats['completed'] }}</div>
                            <small class="text-muted">Selesai</small>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="border rounded p-3 text-center">
                            <div class="h2 mb-1 text-warning">{{ $auctionStats['won'] }}</div>
                            <small class="text-muted">Menang</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Spending Analytics -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 7v5l3 3"/></svg>
                    Analisis Pengeluaran
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-4">
                        <div class="border rounded p-3 text-center">
                            <div class="text-muted small mb-1">Total Pengeluaran</div>
                            <div class="h4 text-primary mb-0">Rp {{ number_format($profile->total_spent, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="border rounded p-3 text-center">
                            <div class="text-muted small mb-1">Rata-rata Bid</div>
                            @php
                                $avgBid = $recentAuctions->filter(fn($a) => $a->bids->count() > 0)
                                    ->flatMap(fn($a) => $a->bids)
                                    ->avg('bid_amount');
                            @endphp
                            <div class="h4 text-success mb-0">Rp {{ number_format($avgBid ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="border rounded p-3 text-center">
                            <div class="text-muted small mb-1">Bid Terakhir 30 Hari</div>
                            @php
                                $recentBids = \App\Models\AuctionBid::whereHas('auction', fn($q) => $q->where('user_id', $profile->user_id))
                                    ->where('created_at', '>=', now()->subDays(30))
                                    ->count();
                            @endphp
                            <div class="h4 text-warning mb-0">{{ $recentBids }} bid</div>
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
            <div class="card-body p-0">
                @if($recentAuctions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Status</th>
                                    <th>Harga Awal</th>
                                    <th>Bid Tertinggi</th>
                                    <th>Tanggal</th>
                                    <th class="w-1">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAuctions as $auction)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $auction->title }}</div>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'active' => 'success',
                                                    'completed' => 'primary',
                                                    'rejected' => 'danger',
                                                    'closed' => 'secondary',
                                                ];
                                                $statusColor = $statusColors[$auction->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }}">{{ ucfirst($auction->status) }}</span>
                                        </td>
                                        <td>Rp {{ number_format($auction->starting_price, 0, ',', '.') }}</td>
                                        <td>
                                            @php
                                                $lowestBid = $auction->getLowestBid();
                                            @endphp
                                            @if($lowestBid)
                                                Rp {{ number_format($lowestBid->bid_amount, 0, ',', '.') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $auction->created_at->format('d M Y') }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.auctions.show', $auction) }}"
                                                class="btn btn-sm btn-ghost-primary" title="Lihat">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty">
                        <p class="empty-title">Belum ada lelang</p>
                        <p class="empty-subtitle text-secondary">User ini belum membuat lelang apapun.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Suspend Modal -->
<div class="modal modal-blur fade" id="suspendModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-status bg-danger"></div>
            <form action="{{ route('admin.user-lelang.suspend', $profile) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tangguhkan User Lelang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menangguhkan profil <strong>{{ $profile->user->name ?? '-' }}</strong>?</p>
                    <p class="text-muted">User ini tidak akan bisa mengikuti lelang baru sampai profil diaktifkan kembali.</p>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Alasan Penangguhan <span class="text-danger">*</span></label>
                        <textarea name="reason" id="reason" class="form-control" rows="3"
                            placeholder="Masukkan alasan penangguhan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tangguhkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
