@extends('layouts.vendor')

@section('title', 'Riwayat Transaksi Wallet')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col-auto">
            <div class="page-pretitle">Vendor Panel</div>
            <h2 class="page-title">Riwayat Transaksi Wallet</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('vendor.wallet.index') }}" class="btn btn-outline-primary">
                Kembali
            </a>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Wallet Summary -->
        <div class="row row-deck row-cards mb-3">
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small">Saldo Tersedia</div>
                                <div class="h3 mb-0 mt-1 text-success">Rp {{ number_format($wallet->available_balance ?? 0, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small">Total Pendapatan</div>
                                <div class="h3 mb-0 mt-1">Rp {{ number_format($wallet->total_earned ?? 0, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small">Total Ditarik</div>
                                <div class="h3 mb-0 mt-1">Rp {{ number_format($wallet->total_withdrawn ?? 0, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Semua Transaksi</h3>
            </div>
            <div class="card-body">
                @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Kategori</th>
                                <th>Deskripsi</th>
                                <th>Jumlah</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                            <tr>
                                <td>
                                    {{ $transaction->created_at->format('d M Y H:i') }}
                                </td>
                                <td>
                                    @if($transaction->type === 'credit')
                                        <span class="badge bg-success-lt">Masuk</span>
                                    @else
                                        <span class="badge bg-danger-lt">Keluar</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $transaction->category_label ?? $transaction->category }}
                                </td>
                                <td>
                                    {{ $transaction->description ?? '-' }}
                                </td>
                                <td>
                                    <span class="fw-bold {{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                        {{ $transaction->type === 'credit' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    Rp {{ number_format($transaction->balance_after ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $transactions->links() }}
                </div>
                @else
                <div class="empty">
                    <p class="empty-title">Belum ada transaksi</p>
                    <p class="empty-subtitle text-muted">Transaksi akan muncul di sini setelah ada aktivitas wallet</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
