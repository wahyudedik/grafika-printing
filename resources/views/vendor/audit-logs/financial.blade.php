@extends('layouts.vendor')

@section('title', 'Financial Audit Logs')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <h2 class="page-title">Financial Audit Logs</h2>
            <p class="text-muted">Riwayat transaksi keuangan vendor Anda</p>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('vendor.audit-logs.index') }}" class="btn btn-outline-secondary me-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 11l-5 3l5 3v-6z"/><path d="M18 11v-2a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h4"/></svg>
                Kembali ke Audit Logs
            </a>
            <a href="{{ route('vendor.audit-logs.export', ['action_type' => request('action_type'), 'entity_type' => request('entity_type'), 'date_from' => request('date_from'), 'date_to' => request('date_to')]) }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 14l-2 -2m0 0l2 -2m-2 2h9a4 4 0 0 0 1 -8v-1"/><path d="M14 11a4 4 0 1 0 -4 4"/></svg>
                Export CSV
            </a>
        </div>
    </div>
</div>

<!-- Filter Form -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z"/></svg>
            Filter
        </h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('vendor.audit-logs.financial') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Action Type</label>
                    <select name="action_type" class="form-select">
                        <option value="">Semua Action</option>
                        <option value="payment" {{ request('action_type') == 'payment' ? 'selected' : '' }}>Payment</option>
                        <option value="withdrawal" {{ request('action_type') == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                        <option value="transfer" {{ request('action_type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="refund" {{ request('action_type') == 'refund' ? 'selected' : '' }}>Refund</option>
                        <option value="admin_fee" {{ request('action_type') == 'admin_fee' ? 'selected' : '' }}>Admin Fee</option>
                        <option value="escrow_release" {{ request('action_type') == 'escrow_release' ? 'selected' : '' }}>Escrow Release</option>
                        <option value="wallet_credit" {{ request('action_type') == 'wallet_credit' ? 'selected' : '' }}>Wallet Credit</option>
                        <option value="wallet_debit" {{ request('action_type') == 'wallet_debit' ? 'selected' : '' }}>Wallet Debit</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Entity Type</label>
                    <select name="entity_type" class="form-select">
                        <option value="">Semua Entity</option>
                        <option value="order" {{ request('entity_type') == 'order' ? 'selected' : '' }}>Order</option>
                        <option value="auction" {{ request('entity_type') == 'auction' ? 'selected' : '' }}>Auction</option>
                        <option value="withdrawal" {{ request('entity_type') == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                        <option value="wallet" {{ request('entity_type') == 'wallet' ? 'selected' : '' }}>Wallet</option>
                        <option value="escrow" {{ request('entity_type') == 'escrow' ? 'selected' : '' }}>Escrow</option>
                        <option value="admin_fee" {{ request('entity_type') == 'admin_fee' ? 'selected' : '' }}>Admin Fee</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Cari</label>
                    <input type="text" name="search" class="form-control" placeholder="Ref / Catatan..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"/><path d="M21 21l-6 -6"/></svg>
                        Filter
                    </button>
                    <a href="{{ route('vendor.audit-logs.financial') }}" class="btn btn-outline-secondary ms-2">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Logs Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6l0 13"/><path d="M12 6l0 13"/><path d="M21 6l0 13"/></svg>
            Financial Logs ({{ $logs->total() }} total)
        </h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th style="width: 1%">ID</th>
                    <th>Waktu</th>
                    <th>Action</th>
                    <th>Entity</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Risk</th>
                    <th>Reference</th>
                    <th>Catatan</th>
                    <th style="width: 1%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="text-muted">#{{ $log->id }}</td>
                    <td>
                        <div>{{ $log->created_at->format('d/m/Y') }}</div>
                        <div class="text-muted small">{{ $log->created_at->format('H:i:s') }}</div>
                    </td>
                    <td>
                        @php
                            $actionColors = [
                                'payment' => 'bg-success-lt',
                                'withdrawal' => 'bg-warning-lt',
                                'transfer' => 'bg-info-lt',
                                'refund' => 'bg-danger-lt',
                                'admin_fee' => 'bg-secondary-lt',
                                'escrow_release' => 'bg-primary-lt',
                                'wallet_credit' => 'bg-success-lt',
                                'wallet_debit' => 'bg-danger-lt',
                            ];
                        @endphp
                        <span class="badge {{ $actionColors[$log->action_type] ?? 'bg-secondary-lt' }}">
                            {{ $log->action_type }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark">{{ $log->entity_type }}</span>
                        @if($log->entity_id)
                            <small class="text-muted">#{{ $log->entity_id }}</small>
                        @endif
                    </td>
                    <td>
                        @if($log->amount !== null)
                            <strong class="{{ $log->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($log->amount, 0, ',', '.') }}
                            </strong>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $statusColors = [
                                'completed' => 'bg-success',
                                'pending' => 'bg-warning',
                                'failed' => 'bg-danger',
                                'cancelled' => 'bg-secondary',
                                'processing' => 'bg-info',
                            ];
                        @endphp
                        <span class="badge {{ $statusColors[$log->status] ?? 'bg-secondary' }}">
                            {{ $log->status }}
                        </span>
                    </td>
                    <td>
                        @php
                            $riskColors = [
                                'low' => 'text-success',
                                'medium' => 'text-warning',
                                'high' => 'text-danger',
                                'critical' => 'text-danger fw-bold',
                            ];
                        @endphp
                        <span class="{{ $riskColors[$log->risk_level] ?? 'text-muted' }}">
                            {{ ucfirst($log->risk_level) }}
                        </span>
                    </td>
                    <td>
                        @if($log->transaction_reference)
                            <code class="small">{{ $log->transaction_reference }}</code>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $log->notes ?? '' }}">
                            {{ $log->notes ?? '-' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('vendor.audit-logs.show', $log->id) }}" class="btn btn-sm btn-outline-primary">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-4 text-muted">
                        <div class="py-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 14l-2 -2m0 0l2 -2m-2 2h9a4 4 0 0 0 1 -8v-1"/><path d="M14 11a4 4 0 1 0 -4 4"/></svg>
                            <h4>Tidak ada data</h4>
                            <p>Belum ada log transaksi keuangan ditemukan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer d-flex align-items-center">
        <div class="text-muted text-sm">
            Menampilkan {{ $logs->firstItem() }} - {{ $logs->lastItem() }} dari {{ $logs->total() }} data
        </div>
        <div class="ms-auto">
            {{ $logs->withQueryString()->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
