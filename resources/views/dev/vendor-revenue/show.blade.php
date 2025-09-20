@extends('dev.layouts.app')

@section('title', 'Detail Pendapatan Vendor')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">Data Pendapatan Vendor</div>
                    <h2 class="page-title">{{ $vendor->name }}</h2>
                    <div class="text-muted mt-1">{{ $vendor->email }} • {{ $vendor->phone }}</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.analytics.vendor-revenue') }}" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 6l6 6l-6 6" />
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Vendor Summary Cards -->
            <div class="row row-deck row-cards mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Pendapatan</div>
                            </div>
                            <div class="h1 mb-3">Rp
                                {{ number_format($vendor->wallet ? $vendor->wallet->total_earnings : 0, 0, ',', '.') }}
                            </div>
                            <div class="d-flex mb-2">
                                <div>Dari {{ $vendor->auctionBids()->where('status', 'accepted')->count() }} lelang</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Saldo Saat Ini</div>
                            </div>
                            <div class="h1 mb-3 text-success">Rp
                                {{ number_format($vendor->wallet ? $vendor->wallet->current_balance : 0, 0, ',', '.') }}
                            </div>
                            <div class="d-flex mb-2">
                                <div>Tersedia untuk ditarik</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Ditarik</div>
                            </div>
                            <div class="h1 mb-3">Rp
                                {{ number_format($vendor->wallet ? $vendor->wallet->total_withdrawn : 0, 0, ',', '.') }}
                            </div>
                            <div class="d-flex mb-2">
                                <div>Penarikan berhasil</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Pending Penarikan</div>
                            </div>
                            <div class="h1 mb-3 text-warning">Rp
                                {{ number_format($vendor->withdrawals()->where('status', 'pending')->sum('amount'), 0, ',', '.') }}
                            </div>
                            <div class="d-flex mb-2">
                                <div>Menunggu persetujuan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Recent Transactions -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Transaksi Terbaru</h3>
                        </div>
                        <div class="card-body">
                            @if ($recentTransactions->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-vcenter">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Jenis</th>
                                                <th>Jumlah</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recentTransactions as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $transaction->type === 'credit' ? 'success' : 'danger' }}-lt">
                                                            {{ $transaction->type === 'credit' ? 'Masuk' : 'Keluar' }}
                                                        </span>
                                                    </td>
                                                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                                    <td>
                                                        <span class="badge bg-green-lt">Berhasil</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="empty">
                                    <div class="empty-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                        </svg>
                                    </div>
                                    <p class="empty-title">Belum ada transaksi</p>
                                    <p class="empty-subtitle text-muted">Transaksi akan muncul setelah ada pembayaran dari
                                        lelang.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Recent Withdrawals -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Penarikan Terbaru</h3>
                        </div>
                        <div class="card-body">
                            @if ($recentWithdrawals->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-vcenter">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Jumlah</th>
                                                <th>Status</th>
                                                <th>Metode</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recentWithdrawals as $withdrawal)
                                                <tr>
                                                    <td>{{ $withdrawal->created_at->format('d M Y H:i') }}</td>
                                                    <td>Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $withdrawal->status === 'completed' ? 'success' : ($withdrawal->status === 'pending' ? 'warning' : 'danger') }}-lt">
                                                            {{ ucfirst($withdrawal->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $withdrawal->payment_method }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="empty">
                                    <div class="empty-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                        </svg>
                                    </div>
                                    <p class="empty-title">Belum ada penarikan</p>
                                    <p class="empty-subtitle text-muted">Penarikan akan muncul setelah vendor melakukan
                                        withdraw.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Auction Wins -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Lelang yang Dimenangkan</h3>
                        </div>
                        <div class="card-body">
                            @if ($recentAuctionWins->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-vcenter">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Judul Lelang</th>
                                                <th>Harga Penawaran</th>
                                                <th>Status Pembayaran</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recentAuctionWins as $bid)
                                                <tr>
                                                    <td>{{ $bid->created_at->format('d M Y H:i') }}</td>
                                                    <td>
                                                        <div class="font-weight-medium">{{ $bid->auction->title }}</div>
                                                        <div class="text-muted small">{{ $bid->auction->category }}</div>
                                                    </td>
                                                    <td>Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}</td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $bid->auction->status === 'paid'
                                                                ? 'success'
                                                                : ($bid->auction->status === 'waiting_payment'
                                                                    ? 'warning'
                                                                    : 'secondary') }}-lt">
                                                            {{ $bid->auction->status === 'paid'
                                                                ? 'Terbayar'
                                                                : ($bid->auction->status === 'waiting_payment'
                                                                    ? 'Menunggu Pembayaran'
                                                                    : 'Belum Dibayar') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="empty">
                                    <div class="empty-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                        </svg>
                                    </div>
                                    <p class="empty-title">Belum ada lelang yang dimenangkan</p>
                                    <p class="empty-subtitle text-muted">Lelang yang dimenangkan akan muncul di sini.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
