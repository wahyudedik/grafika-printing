@extends('dev.layouts.app')

@section('title', 'Transaksi Biaya Admin')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Transaksi Biaya Admin</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.admin-fees.index') }}" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M18 6l-12 12" />
                                <path d="M6 6l12 12" />
                            </svg>
                            Kembali ke Pengaturan
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Vendor</label>
                            <select class="form-select" name="vendor_id">
                                <option value="">Semua Vendor</option>
                                @foreach (\App\Models\Vendor::all() as $vendor)
                                    <option value="{{ $vendor->id }}"
                                        {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                    <path d="M21 21l-6 -6" />
                                </svg>
                                Filter
                            </button>
                            <a href="{{ route('admin.admin-fees.transactions') }}" class="btn btn-outline-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M18 6l-12 12" />
                                    <path d="M6 6l12 12" />
                                </svg>
                                Reset
                            </a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Lelang</th>
                                    <th>Vendor</th>
                                    <th>User</th>
                                    <th>Jumlah Lelang</th>
                                    <th>Biaya Admin</th>
                                    <th>Biaya Payment</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>
                                            <span class="text-muted">#{{ $transaction->id }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex py-1 align-items-center">
                                                <div class="flex-fill">
                                                    <div class="font-weight-medium">
                                                        {{ $transaction->auction->title ?? 'N/A' }}</div>
                                                    <div class="text-muted">{{ $transaction->auction->kode ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex py-1 align-items-center">
                                                <div class="flex-fill">
                                                    <div class="font-weight-medium">
                                                        {{ $transaction->vendor->name ?? 'N/A' }}</div>
                                                    <div class="text-muted">{{ $transaction->vendor->email ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex py-1 align-items-center">
                                                <div class="flex-fill">
                                                    <div class="font-weight-medium">{{ $transaction->user->name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-muted">{{ $transaction->user->email ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold">Rp
                                                {{ number_format($transaction->auction_amount, 0, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-warning">Rp
                                                {{ number_format($transaction->admin_fee_amount, 0, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-info">Rp
                                                {{ number_format($transaction->payment_gateway_fee, 0, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-primary">Rp
                                                {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->status_color }}-lt">
                                                {{ $transaction->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="text-muted">{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
                                            <div class="empty">
                                                <div class="empty-img">
                                                    <img src="{{ asset('demo/empty.svg') }}" height="128"
                                                        alt="">
                                                </div>
                                                <p class="empty-title">Tidak ada transaksi biaya admin</p>
                                                <p class="empty-subtitle text-muted">
                                                    Belum ada transaksi biaya admin yang tercatat.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($transactions->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
