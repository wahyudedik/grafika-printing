@extends('dev.layouts.app')

@section('title', 'Audit Log Details')
@section('content')
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Audit Log Details</h3>
                        <div class="card-actions">
                            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M9 6l6 6l-6 6" />
                                </svg>
                                Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Basic Information</h4>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Log ID:</strong></td>
                                        <td>{{ $log->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>User:</strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded-circle bg-primary text-white">
                                                        {{ substr($log->user->name ?? 'A', 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="font-weight-medium">{{ $log->user->name ?? 'N/A' }}</div>
                                                    <div class="text-muted">{{ $log->user->email ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Vendor:</strong></td>
                                        <td>{{ $log->vendor->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Action:</strong></td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $log->action_type == 'approve' ? 'success' : ($log->action_type == 'reject' ? 'danger' : 'primary') }}">
                                                {{ ucfirst($log->action_type) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Entity Type:</strong></td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst($log->entity_type) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Entity ID:</strong></td>
                                        <td>{{ $log->entity_id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Amount:</strong></td>
                                        <td>
                                            @if ($log->amount)
                                                <span class="font-weight-medium">Rp
                                                    {{ number_format($log->amount, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $log->status == 'completed' ? 'success' : ($log->status == 'failed' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Risk Level:</strong></td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $log->risk_level == 'critical' ? 'danger' : ($log->risk_level == 'high' ? 'warning' : ($log->risk_level == 'medium' ? 'info' : 'success')) }}">
                                                {{ ucfirst($log->risk_level) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Transaction Reference:</strong></td>
                                        <td>{{ $log->transaction_reference ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created At:</strong></td>
                                        <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>Technical Details</h4>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>IP Address:</strong></td>
                                        <td><code>{{ $log->ip_address }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>User Agent:</strong></td>
                                        <td>
                                            <small class="text-muted">{{ Str::limit($log->user_agent, 50) }}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Notes:</strong></td>
                                        <td>{{ $log->notes ?? 'N/A' }}</td>
                                    </tr>
                                </table>

                                @if ($log->old_data || $log->new_data)
                                    <h4 class="mt-4">Data Changes</h4>

                                    @if ($log->old_data)
                                        <div class="mb-3">
                                            <h5 class="text-danger">Old Data</h5>
                                            <div class="card">
                                                <div class="card-body">
                                                    <pre class="mb-0"><code>{{ json_encode($log->masked_old_data, JSON_PRETTY_PRINT) }}</code></pre>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($log->new_data)
                                        <div class="mb-3">
                                            <h5 class="text-success">New Data</h5>
                                            <div class="card">
                                                <div class="card-body">
                                                    <pre class="mb-0"><code>{{ json_encode($log->masked_new_data, JSON_PRETTY_PRINT) }}</code></pre>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
