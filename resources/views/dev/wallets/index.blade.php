@extends('dev.layouts.app')

@section('title', 'Wallet Management')

@section('content')
    <div class="container-xl">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">Wallet Management</h2>
                    <div class="text-muted mt-1">Manage vendor wallets and transactions</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.wallets.statistics') }}"
                            class="btn btn-outline-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 19c-4.3 0 -8 -3.7 -8 -8s3.7 -8 8 -8s8 3.7 8 8s-3.7 8 -8 8" />
                                <path d="M15 13l-3 -3l-3 3" />
                            </svg>
                            Statistics
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row row-deck row-cards mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Wallets</div>
                        </div>
                        <div class="h1 mb-3">{{ $stats['total_wallets'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Active Wallets</div>
                        </div>
                        <div class="h1 mb-3 text-success">{{ $stats['active_wallets'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Frozen Wallets</div>
                        </div>
                        <div class="h1 mb-3 text-danger">{{ $stats['frozen_wallets'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Balance</div>
                        </div>
                        <div class="h1 mb-3">Rp {{ number_format($stats['total_balance'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.wallets.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Vendor</label>
                            <select name="vendor_id" class="form-select">
                                <option value="">All Vendors</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}"
                                        {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="frozen" {{ request('status') == 'frozen' ? 'selected' : '' }}>Frozen
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control"
                                placeholder="Search by vendor name or email..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('admin.wallets.index') }}" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Wallets Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Vendor Wallets</h3>
            </div>
            <div class="card-body">
                @if ($wallets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Vendor</th>
                                    <th>Balance</th>
                                    <th>Available</th>
                                    <th>Pending</th>
                                    <th>Status</th>
                                    <th>Last Transaction</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($wallets as $wallet)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <div class="font-weight-medium">{{ $wallet->vendor->name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-muted">{{ $wallet->vendor->email ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="font-weight-medium">Rp
                                                {{ number_format($wallet->balance, 0, ',', '.') }}</div>
                                        </td>
                                        <td>
                                            <div class="text-success">Rp
                                                {{ number_format($wallet->available_balance, 0, ',', '.') }}</div>
                                        </td>
                                        <td>
                                            <div class="text-warning">Rp
                                                {{ number_format($wallet->pending_balance, 0, ',', '.') }}</div>
                                        </td>
                                        <td>
                                            @if ($wallet->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($wallet->status === 'frozen')
                                                <span class="badge bg-danger">Frozen</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($wallet->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($wallet->transactions->count() > 0)
                                                <div class="text-muted">
                                                    {{ $wallet->transactions->first()->created_at->diffForHumans() }}</div>
                                            @else
                                                <div class="text-muted">No transactions</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-list">
                                                <a href="{{ route('admin.wallets.show', $wallet->id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    View
                                                </a>
                                                <a href="{{ route('admin.wallets.transactions', $wallet->id) }}"
                                                    class="btn btn-sm btn-outline-info">
                                                    Transactions
                                                </a>
                                                @if ($wallet->status === 'active')
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="freezeWallet({{ $wallet->id }})">
                                                        Freeze
                                                    </button>
                                                @elseif($wallet->status === 'frozen')
                                                    <button type="button" class="btn btn-sm btn-outline-success"
                                                        onclick="unfreezeWallet({{ $wallet->id }})">
                                                        Unfreeze
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $wallets->links() }}
                    </div>
                @else
                    <div class="empty">
                        <div class="empty-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M17 8v-3a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1v12a1 1 0 0 0 1 1h10a1 1 0 0 0 1 -1v-3" />
                            </svg>
                        </div>
                        <p class="empty-title">No wallets found</p>
                        <p class="empty-subtitle text-muted">
                            No vendor wallets match your current filters.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Freeze Wallet Modal -->
    <div class="modal modal-blur fade" id="freezeModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Freeze Wallet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="freezeForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Reason for freezing</label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="Enter reason for freezing this wallet..."
                                required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Freeze Wallet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Unfreeze Wallet Modal -->
    <div class="modal modal-blur fade" id="unfreezeModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Unfreeze Wallet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="unfreezeForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Reason for unfreezing</label>
                            <textarea name="reason" class="form-control" rows="3"
                                placeholder="Enter reason for unfreezing this wallet..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Unfreeze Wallet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function freezeWallet(walletId) {
            document.getElementById('freezeForm').action = `/admin/wallets/${walletId}/freeze`;
            new bootstrap.Modal(document.getElementById('freezeModal')).show();
        }

        function unfreezeWallet(walletId) {
            document.getElementById('unfreezeForm').action = `/admin/wallets/${walletId}/unfreeze`;
            new bootstrap.Modal(document.getElementById('unfreezeModal')).show();
        }
    </script>
@endsection
