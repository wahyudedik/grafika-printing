@extends('dev.layouts.app')

@section('title', 'Transaksi Biaya Admin')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Pengaturan
                    </div>
                    <h2 class="page-title">
                        Transaksi Biaya Admin
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.admin-fees.index') }}" class="btn btn-outline-secondary">
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
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Daftar Transaksi Biaya Admin</h3>
                            <div class="card-actions">
                                <div class="dropdown">
                                    <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                            <path d="M12 7v5l3 3" />
                                        </svg>
                                        Filter
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['status' => '']) }}">Semua Status</a>
                                        <a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}">Pending</a>
                                        <a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['status' => 'paid']) }}">Paid</a>
                                        <a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['status' => 'failed']) }}">Failed</a>
                                        <a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['status' => 'refunded']) }}">Refunded</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($transactions->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table">
                                        <thead>
                                            <tr>
                                                <th>ID Transaksi</th>
                                                <th>Lelang</th>
                                                <th>Vendor</th>
                                                <th>User</th>
                                                <th>Jumlah Lelang</th>
                                                <th>Biaya Admin</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Tanggal</th>
                                                <th class="w-1">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transactions as $transaction)
                                                <tr>
                                                    <td>
                                                        <div class="text-muted">#{{ $transaction->id }}</div>
                                                    </td>
                                                    <td>
                                                        @if ($transaction->auction)
                                                            <div class="d-flex py-1 align-items-center">
                                                                <div class="flex-fill">
                                                                    <div class="font-weight-medium">
                                                                        {{ Str::limit($transaction->auction->title, 30) }}
                                                                    </div>
                                                                    <div class="text-muted">
                                                                        <small>{{ $transaction->auction->kode }}</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($transaction->vendor)
                                                            <div class="d-flex py-1 align-items-center">
                                                                <div class="flex-fill">
                                                                    <div class="font-weight-medium">
                                                                        {{ $transaction->vendor->name }}</div>
                                                                    <div class="text-muted">
                                                                        <small>{{ $transaction->vendor->email }}</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($transaction->user)
                                                            <div class="d-flex py-1 align-items-center">
                                                                <div class="flex-fill">
                                                                    <div class="font-weight-medium">
                                                                        {{ $transaction->user->name }}</div>
                                                                    <div class="text-muted">
                                                                        <small>{{ $transaction->user->email }}</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="text-muted">Rp
                                                            {{ number_format($transaction->auction_amount, 0, ',', '.') }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-warning">Rp
                                                            {{ number_format($transaction->admin_fee_amount, 0, ',', '.') }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="font-weight-medium">Rp
                                                            {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $transaction->status_color }}">
                                                            {{ $transaction->status_label }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="text-muted">
                                                            <small>{{ $transaction->created_at->format('d/m/Y H:i') }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#transactionModal{{ $transaction->id }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon"
                                                                    width="24" height="24" viewBox="0 0 24 24"
                                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                                    <path
                                                                        d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-center">
                                    {{ $transactions->links() }}
                                </div>
                            @else
                                <div class="empty">
                                    <div class="empty-img">
                                        <img src="{{ asset('static/illustrations/undraw_printing_invoices_5r4r.svg') }}"
                                            height="128" alt="">
                                    </div>
                                    <p class="empty-title">Belum ada transaksi biaya admin</p>
                                    <p class="empty-subtitle text-muted">
                                        Transaksi akan muncul setelah ada lelang yang menggunakan biaya admin.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Detail Modals -->
    @foreach ($transactions as $transaction)
        <div class="modal modal-blur fade" id="transactionModal{{ $transaction->id }}" tabindex="-1" role="dialog"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Transaksi #{{ $transaction->id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">ID Transaksi</label>
                                    <div class="form-control-plaintext">#{{ $transaction->id }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-control-plaintext">
                                        <span class="badge bg-{{ $transaction->status_color }}">
                                            {{ $transaction->status_label }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($transaction->auction)
                            <div class="mb-3">
                                <label class="form-label">Lelang</label>
                                <div class="form-control-plaintext">
                                    <strong>{{ $transaction->auction->title }}</strong><br>
                                    <small class="text-muted">{{ $transaction->auction->kode }}</small>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Vendor</label>
                                    <div class="form-control-plaintext">
                                        @if ($transaction->vendor)
                                            {{ $transaction->vendor->name }}<br>
                                            <small class="text-muted">{{ $transaction->vendor->email }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">User</label>
                                    <div class="form-control-plaintext">
                                        @if ($transaction->user)
                                            {{ $transaction->user->name }}<br>
                                            <small class="text-muted">{{ $transaction->user->email }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Lelang</label>
                                    <div class="form-control-plaintext">Rp
                                        {{ number_format($transaction->auction_amount, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Biaya Admin</label>
                                    <div class="form-control-plaintext text-warning">Rp
                                        {{ number_format($transaction->admin_fee_amount, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Biaya Payment Gateway</label>
                                    <div class="form-control-plaintext">Rp
                                        {{ number_format($transaction->payment_gateway_fee, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Total Pembayaran</label>
                                    <div class="form-control-plaintext font-weight-bold">Rp
                                        {{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Vendor Menerima</label>
                                    <div class="form-control-plaintext text-success">Rp
                                        {{ number_format($transaction->vendor_receives, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Admin Menerima</label>
                                    <div class="form-control-plaintext text-primary">Rp
                                        {{ number_format($transaction->admin_receives, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Dibuat</label>
                            <div class="form-control-plaintext">{{ $transaction->created_at->format('d F Y H:i:s') }}
                            </div>
                        </div>

                        @if ($transaction->updated_at != $transaction->created_at)
                            <div class="mb-3">
                                <label class="form-label">Terakhir Diperbarui</label>
                                <div class="form-control-plaintext">{{ $transaction->updated_at->format('d F Y H:i:s') }}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
