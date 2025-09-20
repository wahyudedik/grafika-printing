@extends('dev.layouts.app')

@section('title', 'Wallet Transactions')

@section('content')
    <div class="container-xl">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">Wallet Transactions</h2>
                    <div class="text-muted mt-1">{{ $wallet->vendor->name ?? 'N/A' }}</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.wallets.show', $wallet->id) }}" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 6l6 6l-6 6" />
                            </svg>
                            Back to Wallet
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wallet Summary -->
        <div class="row row-deck row-cards mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Balance</div>
                        </div>
                        <div class="h1 mb-3 text-primary">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Available Balance</div>
                        </div>
                        <div class="h1 mb-3 text-success">Rp {{ number_format($wallet->available_balance, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Pending Balance</div>
                        </div>
                        <div class="h1 mb-3 text-warning">Rp {{ number_format($wallet->pending_balance, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.wallets.transactions', $wallet->id) }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                <option value="auction_payment"
                                    {{ request('category') == 'auction_payment' ? 'selected' : '' }}>Auction Payment
                                </option>
                                <option value="withdrawal" {{ request('category') == 'withdrawal' ? 'selected' : '' }}>
                                    Withdrawal</option>
                                <option value="refund" {{ request('category') == 'refund' ? 'selected' : '' }}>Refund
                                </option>
                                <option value="admin_fee" {{ request('category') == 'admin_fee' ? 'selected' : '' }}>Admin
                                    Fee</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                    Completed</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('admin.wallets.transactions', $wallet->id) }}"
                                    class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Transaction History</h3>
            </div>
            <div class="card-body">
                @if ($transactions->count() > 0)
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
                                    <th>Reference</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
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
                                            @elseif($transaction->status === 'failed')
                                                <span class="badge bg-danger">Failed</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-muted">{{ $transaction->description ?? 'N/A' }}</div>
                                        </td>
                                        <td>
                                            <div class="text-muted">{{ $transaction->transaction_code ?? 'N/A' }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $transactions->links() }}
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
                            No transactions match your current filters.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
