@extends('layouts.user')

@section('title', 'User Dashboard')

@section('content')
    <div class="row row-deck row-cards mb-4">
        <!-- Welcome Card -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="card-title">Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
                            <p class="text-muted mb-0">Kelola lelang, pesanan, dan aktivitas Anda di Grafika Printing.</p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('user.auctions.create') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                Buat Lelang Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Lelang</div>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between">
                        <div class="h1 mb-0 me-2">{{ $myAuctionsCount ?? 0 }}</div>
                        <span class="badge bg-blue-lt">{{ $activeAuctionsCount ?? 0 }} aktif</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Pesanan Aktif</div>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between">
                        <div class="h1 mb-0 me-2">{{ $pendingOrdersCount ?? 0 }}</div>
                        <span class="badge bg-green-lt">{{ $ordersCount ?? 0 }} total</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Lelang Selesai</div>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between">
                        <div class="h1 mb-0 me-2">{{ $completedAuctionsCount ?? 0 }}</div>
                        <span class="badge bg-green-lt">✓</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Pengeluaran</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">Rp {{ number_format($totalSpent ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Auctions -->
        @if(isset($recentAuctions) && $recentAuctions->count() > 0)
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lelang Terbaru</h3>
                    <div class="card-actions">
                        <a href="{{ route('user.auctions.my') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAuctions as $auction)
                                <tr>
                                    <td>
                                        <a href="{{ route('user.auctions.show', $auction) }}" class="text-decoration-none">
                                            {{ Str::limit($auction->title, 30) }}
                                        </a>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-lt',
                                                'active' => 'bg-green-lt',
                                                'closed' => 'bg-secondary-lt',
                                                'completed' => 'bg-blue-lt',
                                                'paid' => 'bg-indigo-lt',
                                            ];
                                            $color = $statusColors[$auction->status] ?? 'bg-secondary-lt';
                                        @endphp
                                        <span class="badge {{ $color }}">{{ ucfirst($auction->status) }}</span>
                                    </td>
                                    <td class="text-muted">{{ $auction->created_at->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Orders -->
        @if(isset($recentOrders) && $recentOrders->count() > 0)
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pesanan Terbaru</h3>
                    <div class="card-actions">
                        <a href="{{ route('user.orders.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Lelang</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('user.orders.show', $order) }}" class="text-decoration-none">
                                            {{ Str::limit($order->auction->title ?? 'N/A', 30) }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge {{ $order->status_color ?? 'bg-secondary-lt' }}">
                                            {{ $order->status_label ?? ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ $order->created_at->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Lelang Saya</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">🏆</div>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted">Lihat semua lelang yang telah Anda buat</p>
                        <a href="{{ route('user.auctions.my') }}" class="btn btn-primary btn-sm">Lihat Lelang</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Tracking Pesanan</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">📦</div>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted">Lacak status pesanan dari lelang yang Anda menangkan</p>
                        <a href="{{ route('user.orders.index') }}" class="btn btn-primary btn-sm">Lacak Pesanan</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Profil Saya</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">👤</div>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted">Kelola informasi profil dan akun Anda</p>
                        <a href="{{ route('user.profile.edit') }}" class="btn btn-outline-primary btn-sm">Edit Profil</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Info -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Akun</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <div class="form-control-plaintext">{{ auth()->user()->name }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <div class="form-control-plaintext">{{ auth()->user()->email }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipe Akun</label>
                                <div class="form-control-plaintext">
                                    <span class="badge bg-blue-lt">{{ ucfirst(auth()->user()->usertype) }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status Verifikasi</label>
                                <div class="form-control-plaintext">
                                    @if (auth()->user()->email_verified_at)
                                        <span class="badge bg-green-lt">✓ Terverifikasi</span>
                                    @else
                                        <span class="badge bg-yellow-lt">⚠ Belum Terverifikasi</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Info -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Fitur Tersedia</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <span class="avatar bg-blue-lt">🏆</span>
                                </div>
                                <div>
                                    <div class="font-weight-medium">Sistem Lelang</div>
                                    <div class="text-muted">Buat permintaan cetak dan terima penawaran dari vendor</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <span class="avatar bg-green-lt">💳</span>
                                </div>
                                <div>
                                    <div class="font-weight-medium">Pembayaran Xendit</div>
                                    <div class="text-muted">Integrasi dengan Xendit untuk pembayaran yang aman dan mudah</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <span class="avatar bg-green-lt">⭐</span>
                                </div>
                                <div>
                                    <div class="font-weight-medium">Rating Vendor</div>
                                    <div class="text-muted">Beri rating dan review untuk vendor setelah pesanan selesai</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
