@extends('dev.layouts.app')

@section('title', 'Wallet Details')

@section('content')
    <div class="container-xl">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">Wallet Details</h2>
                    <div class="text-muted mt-1">{{ $wallet->vendor->name ?? 'N/A' }}</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.wallets.index') }}" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 6l6 6l-6 6" />
                            </svg>
                            Back to Wallets
                        </a>
                        <a href="{{ route('admin.wallets.transactions', $wallet->id) }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                            </svg>
                            View Transactions
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wallet Information -->
        <div class="row row-deck row-cards mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Wallet Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Vendor</label>
                                    <div class="form-control-plaintext">{{ $wallet->vendor->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <div class="form-control-plaintext">{{ $wallet->vendor->email ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div>
                                        @if ($wallet->status === 'active')
                                            <span class="badge bg-success">Active</span>
                                        @elseif($wallet->status === 'frozen')
                                            <span class="badge bg-danger">Frozen</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($wallet->status) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Created</label>
                                    <div class="form-control-plaintext">{{ $wallet->created_at->format('d M Y, H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Balance Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Total Balance</label>
                                    <div class="h2 text-primary">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Available Balance</label>
                                    <div class="h4 text-success">Rp
                                        {{ number_format($wallet->available_balance, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Pending Balance</label>
                                    <div class="h4 text-warning">Rp
                                        {{ number_format($wallet->pending_balance, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Transactions</h3>
            </div>
            <div class="card-body">
                @if ($wallet->transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($wallet->transactions as $transaction)
                                    <tr>
                                        <td>
                                            <div class="text-muted">{{ $transaction->created_at->format('d M Y, H:i') }}
                                            </div>
                                        </td>
                                        <td>
                                            @if ($transaction->type === 'credit')
                                                <span class="badge bg-success">Credit</span>
                                            @else
                                                <span class="badge bg-danger">Debit</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $transaction->category)) }}</span>
                                        </td>
                                        <td>
                                            <div
                                                class="font-weight-medium {{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                                {{ $transaction->type === 'credit' ? '+' : '-' }}Rp
                                                {{ number_format($transaction->amount, 0, ',', '.') }}
                                            </div>
                                        </td>
                                        <td>
                                            @if ($transaction->status === 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($transaction->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-muted">{{ $transaction->description ?? 'N/A' }}</div>
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
                                <path
                                    d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                            </svg>
                        </div>
                        <p class="empty-title">No transactions found</p>
                        <p class="empty-subtitle text-muted">
                            This wallet has no transactions yet.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
