@extends('dev.layouts.app')

@section('title', 'Wallet Statistics')

@section('content')
    <div class="container-xl">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">Wallet Statistics</h2>
                    <div class="text-muted mt-1">Comprehensive wallet analytics and insights</div>
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Overview Statistics -->
        <div class="row row-deck row-cards mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Wallets</div>
                        </div>
                        <div class="h1 mb-3">{{ $stats['total_wallets'] }}</div>
                        <div class="d-flex mb-2">
                            <div>Active: {{ $stats['active_wallets'] }}</div>
                            <div class="ms-auto text-success">
                                {{ round(($stats['active_wallets'] / $stats['total_wallets']) * 100, 1) }}%</div>
                        </div>
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
                        <div class="d-flex mb-2">
                            <div>Percentage</div>
                            <div class="ms-auto text-danger">
                                {{ round(($stats['frozen_wallets'] / $stats['total_wallets']) * 100, 1) }}%</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Balance</div>
                        </div>
                        <div class="h1 mb-3 text-primary">Rp {{ number_format($stats['total_balance'], 0, ',', '.') }}</div>
                        <div class="d-flex mb-2">
                            <div>Available: Rp {{ number_format($stats['total_available_balance'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Average Balance</div>
                        </div>
                        <div class="h1 mb-3 text-info">Rp {{ number_format($stats['average_balance'], 0, ',', '.') }}</div>
                        <div class="d-flex mb-2">
                            <div>Per wallet</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Balance Distribution -->
        <div class="row row-deck row-cards mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Balance Distribution</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Available Balance</div>
                                    </div>
                                    <div class="h2 text-success">Rp
                                        {{ number_format($stats['total_available_balance'], 0, ',', '.') }}</div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-success"
                                            style="width: {{ ($stats['total_available_balance'] / $stats['total_balance']) * 100 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Pending Balance</div>
                                    </div>
                                    <div class="h2 text-warning">Rp
                                        {{ number_format($stats['total_pending_balance'], 0, ',', '.') }}</div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-warning"
                                            style="width: {{ ($stats['total_pending_balance'] / $stats['total_balance']) * 100 }}%">
                                        </div>
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
                        <h3 class="card-title">Wallet Status</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Active Wallets</div>
                                    </div>
                                    <div class="h2 text-success">{{ $stats['active_wallets'] }}</div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-success"
                                            style="width: {{ ($stats['active_wallets'] / $stats['total_wallets']) * 100 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Frozen Wallets</div>
                                    </div>
                                    <div class="h2 text-danger">{{ $stats['frozen_wallets'] }}</div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-danger"
                                            style="width: {{ ($stats['frozen_wallets'] / $stats['total_wallets']) * 100 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Wallets -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Top Wallets by Balance</h3>
            </div>
            <div class="card-body">
                @if ($stats['top_wallets']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Vendor</th>
                                    <th>Balance</th>
                                    <th>Available</th>
                                    <th>Pending</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stats['top_wallets'] as $index => $wallet)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($index < 3)
                                                    <span
                                                        class="badge bg-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'success') }}">
                                                        {{ $index + 1 }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-info">{{ $index + 1 }}</span>
                                                @endif
                                            </div>
                                        </td>
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
                                            <div class="font-weight-medium text-primary">Rp
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
                                    d="M17 8v-3a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1v12a1 1 0 0 0 1 1h10a1 1 0 0 0 1 -1v-3" />
                            </svg>
                        </div>
                        <p class="empty-title">No wallets found</p>
                        <p class="empty-subtitle text-muted">
                            No vendor wallets available for statistics.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
