@extends('dev.layouts.app')

@section('title', 'High Risk Audit Logs')
@section('content')
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-2" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 9v2m0 4v.01" />
                                <path
                                    d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.84 2.75" />
                            </svg>
                            High Risk Transactions
                        </h3>
                        <div class="card-actions d-flex align-items-center">
                            <span class="badge bg-danger me-2">{{ $logs->count() ?? 0 }} transaksi berisiko tinggi</span>
                            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M9 6l6 6l-6 6" />
                                </svg>
                                Kembali ke Audit Logs
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($logs->isEmpty())
                            <div class="empty">
                                <div class="empty-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 6l6 6l-6 6" />
                                    </svg>
                                </div>
                                <p class="empty-title">Tidak ada transaksi berisiko tinggi</p>
                                <p class="empty-subtitle text-muted">Semua transaksi dalam kondisi aman.</p>
                            </div>
                        @else
                            <!-- Responsive table -->
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
                                                            <span
                                                                class="avatar-initial rounded-circle bg-danger text-white">
                                                                {{ substr($log->user->name ?? 'A', 0, 1) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <div class="font-weight-medium">
                                                                {{ $log->user->name ?? 'N/A' }}
                                                            </div>
                                                            <div class="text-muted">
                                                                {{ $log->user->email ?? 'N/A' }}</div>
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
                                                    <span class="badge bg-info">
                                                        {{ ucfirst($log->entity_type) }}
                                                    </span>
                                                    <div class="text-muted">ID: {{ $log->entity_id }}</div>
                                                </td>
                                                <td>
                                                    @if ($log->amount)
                                                        <span class="font-weight-medium text-danger">
                                                            Rp {{ number_format($log->amount, 0, ',', '.') }}
                                                        </span>
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
                                                    <div class="text-muted">{{ $log->created_at->format('H:i:s') }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.audit-logs.show', $log->id) }}"
                                                        class="btn btn-sm btn-outline-danger">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                            height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M10 12a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" />
                                                            <path
                                                                d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                        </svg>
                                                        Detail
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
                                                                <path d="M9 6l6 6l-6 6" />
                                                            </svg>
                                                        </div>
                                                        <p class="empty-title">Tidak ada data</p>
                                                        <p class="empty-subtitle text-muted">Belum ada transaksi
                                                            berisiko tinggi.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if (method_exists($logs, 'links'))
                                <div class="d-flex justify-content-center">
                                    {{ $logs->links() }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
