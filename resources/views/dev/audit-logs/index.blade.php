@extends('dev.layouts.app')

@section('title', 'Audit Logs')
@section('content')
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Financial Audit Logs</h3>
                        <div class="card-actions">
                            <a href="{{ route('admin.audit-logs.export', request()->query()) }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                    <path d="M9 9l1 1l3 -3" />
                                </svg>
                                Export CSV
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Statistics Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-primary text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path
                                                            d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                                        <path
                                                            d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="font-weight-medium">Total Logs</div>
                                                <div class="text-muted">{{ number_format($stats['total_logs']) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-danger text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M12 9v2m0 4v.01" />
                                                        <path
                                                            d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.84 2.75" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="font-weight-medium">High Risk</div>
                                                <div class="text-muted">{{ number_format($stats['high_risk']) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-info text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                        <path d="M12 7v5l3 3" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="font-weight-medium">Today</div>
                                                <div class="text-muted">{{ number_format($stats['today_logs']) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-success text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path
                                                            d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />
                                                        <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />
                                                        <path d="M16 5l3 3" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="font-weight-medium">Financial</div>
                                                <div class="text-muted">{{ number_format($stats['financial_actions']) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filters -->
                        <form method="GET" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Risk Level</label>
                                    <select name="risk_level" class="form-select">
                                        <option value="">All Risk Levels</option>
                                        <option value="low" {{ request('risk_level') == 'low' ? 'selected' : '' }}>Low
                                        </option>
                                        <option value="medium" {{ request('risk_level') == 'medium' ? 'selected' : '' }}>
                                            Medium</option>
                                        <option value="high" {{ request('risk_level') == 'high' ? 'selected' : '' }}>High
                                        </option>
                                        <option value="critical"
                                            {{ request('risk_level') == 'critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Action Type</label>
                                    <select name="action_type" class="form-select">
                                        <option value="">All Actions</option>
                                        <option value="create" {{ request('action_type') == 'create' ? 'selected' : '' }}>
                                            Create</option>
                                        <option value="update" {{ request('action_type') == 'update' ? 'selected' : '' }}>
                                            Update</option>
                                        <option value="approve"
                                            {{ request('action_type') == 'approve' ? 'selected' : '' }}>Approve</option>
                                        <option value="reject" {{ request('action_type') == 'reject' ? 'selected' : '' }}>
                                            Reject</option>
                                        <option value="withdraw"
                                            {{ request('action_type') == 'withdraw' ? 'selected' : '' }}>Withdraw</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Entity Type</label>
                                    <select name="entity_type" class="form-select">
                                        <option value="">All Entities</option>
                                        <option value="withdrawal"
                                            {{ request('entity_type') == 'withdrawal' ? 'selected' : '' }}>Withdrawal
                                        </option>
                                        <option value="wallet" {{ request('entity_type') == 'wallet' ? 'selected' : '' }}>
                                            Wallet</option>
                                        <option value="payment"
                                            {{ request('entity_type') == 'payment' ? 'selected' : '' }}>Payment</option>
                                        <option value="auction"
                                            {{ request('entity_type') == 'auction' ? 'selected' : '' }}>Auction</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date From</label>
                                    <input type="date" name="date_from" class="form-control"
                                        value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label">Search</label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search by reference, notes, user..."
                                        value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date To</label>
                                    <input type="date" name="date_to" class="form-control"
                                        value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                                    <a href="{{ route('admin.audit-logs.index') }}"
                                        class="btn btn-outline-secondary">Clear</a>
                                </div>
                            </div>
                        </form>

                        <!-- Audit Logs Table -->
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Entity</th>
                                        <th>Amount</th>
                                        <th>Risk</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                        <tr>
                                            <td>{{ $log->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-2">
                                                        <span class="avatar-initial rounded-circle bg-primary text-white">
                                                            {{ substr($log->user->name ?? 'A', 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-medium">{{ $log->user->name ?? 'N/A' }}
                                                        </div>
                                                        <div class="text-muted">{{ $log->user->email ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $log->action_type == 'approve' ? 'success' : ($log->action_type == 'reject' ? 'danger' : 'primary') }}">
                                                    {{ ucfirst($log->action_type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ ucfirst($log->entity_type) }}</span>
                                                <div class="text-muted">ID: {{ $log->entity_id }}</div>
                                            </td>
                                            <td>
                                                @if ($log->amount)
                                                    <span class="font-weight-medium">Rp
                                                        {{ number_format($log->amount, 0, ',', '.') }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $log->risk_level == 'critical' ? 'danger' : ($log->risk_level == 'high' ? 'warning' : ($log->risk_level == 'medium' ? 'info' : 'success')) }}">
                                                    {{ ucfirst($log->risk_level) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $log->status == 'completed' ? 'success' : ($log->status == 'failed' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($log->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>{{ $log->created_at->format('d M Y') }}</div>
                                                <div class="text-muted">{{ $log->created_at->format('H:i:s') }}</div>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.audit-logs.show', $log->id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    View Details
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="empty">
                                                    <div class="empty-icon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon"
                                                            width="24" height="24" viewBox="0 0 24 24"
                                                            stroke-width="2" stroke="currentColor" fill="none"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path
                                                                d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                                            <path
                                                                d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                                        </svg>
                                                    </div>
                                                    <p class="empty-title">No audit logs found</p>
                                                    <p class="empty-subtitle text-muted">Try adjusting your filters or
                                                        search criteria.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
