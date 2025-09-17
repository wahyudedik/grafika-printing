@extends('layouts.vendor')

@section('title', 'Wallet Dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Wallet Dashboard</h2>
                    <p class="text-muted">Kelola saldo dan penarikan dana Anda</p>
                </div>
                <div>
                    <a href="{{ route('vendor.wallet.create-withdrawal') }}" class="btn btn-primary">
                        <i class="fas fa-money-bill-wave me-1"></i>
                        Tarik Dana
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

            <!-- Wallet Overview -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Saldo Tersedia</div>
                            </div>
                            <div class="h1 mb-3">Rp {{ number_format($stats['available_balance']) }}</div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Pendapatan</div>
                            </div>
                            <div class="h1 mb-3">Rp {{ number_format($stats['total_earned']) }}</div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Ditarik</div>
                            </div>
                            <div class="h1 mb-3">Rp {{ number_format($stats['total_withdrawn']) }}</div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-info" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Pending Penarikan</div>
                            </div>
                            <div class="h1 mb-3">Rp {{ number_format($stats['pending_withdrawals']) }}</div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Recent Transactions -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Transaksi Terbaru</h3>
                            <div class="card-actions">
                                <a href="{{ route('vendor.wallet.transactions') }}" class="btn btn-outline-primary btn-sm">
                                    Lihat Semua
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($transactions->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Jenis</th>
                                                <th>Kategori</th>
                                                <th>Jumlah</th>
                                                <th>Saldo</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transactions as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $transaction->type === 'credit' ? 'success' : 'danger' }}">
                                                            {{ $transaction->type === 'credit' ? 'Masuk' : 'Keluar' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $transaction->category_label }}</td>
                                                    <td
                                                        class="fw-bold {{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                                        {{ $transaction->formatted_amount }}
                                                    </td>
                                                    <td>Rp {{ number_format($transaction->balance_after) }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $transaction->status_color }}">
                                                            {{ $transaction->status_label }}
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
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="64" height="64"
                                            viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                            <path
                                                d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                            <path d="M9 12l2 2l4 -4" />
                                        </svg>
                                    </div>
                                    <p class="empty-title">Belum ada transaksi</p>
                                    <p class="empty-subtitle text-muted">
                                        Transaksi akan muncul di sini setelah ada pembayaran dari lelang.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Pending Withdrawals -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Penarikan Pending</h3>
                            <div class="card-actions">
                                <a href="{{ route('vendor.wallet.withdrawals') }}" class="btn btn-outline-primary btn-sm">
                                    Lihat Semua
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($pendingWithdrawals->count() > 0)
                                @foreach ($pendingWithdrawals as $withdrawal)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-fill">
                                            <div class="fw-bold">Rp {{ number_format($withdrawal->amount) }}</div>
                                            <div class="text-muted small">{{ $withdrawal->created_at->format('d M Y') }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge bg-{{ $withdrawal->status_color }}">
                                                {{ $withdrawal->status_label }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="empty">
                                    <div class="empty-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="32" height="32"
                                            viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                            <path
                                                d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                            <path d="M9 12l2 2l4 -4" />
                                        </svg>
                                    </div>
                                    <p class="empty-title">Tidak ada penarikan pending</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
