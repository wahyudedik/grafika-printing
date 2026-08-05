@extends('layouts.vendor')

@section('title', 'Transaction Details')
@section('content')
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Transaction Details</h3>
                        <div class="card-actions">
                            <a href="{{ route('vendor.audit-logs.index') }}" class="btn btn-outline-secondary">
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
                                <h4>Transaction Information</h4>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Transaction ID:</strong></td>
                                        <td>{{ $log->id }}</td>
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
                                        <td><strong>Transaction Reference:</strong></td>
                                        <td>{{ $log->transaction_reference ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Date:</strong></td>
                                        <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Notes:</strong></td>
                                        <td>{{ $log->notes ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>Transaction Details</h4>

                                @if ($log->old_data || $log->new_data)
                                    <div class="mb-3">
                                        <h5>Data Changes</h5>

                                        @if ($log->old_data)
                                            <div class="mb-3">
                                                <h6 class="text-danger">Previous Data</h6>
                                                <div class="card">
                                                    <div class="card-body">
                                                        <pre class="mb-0"><code>{{ json_encode($log->masked_old_data, JSON_PRETTY_PRINT) }}</code></pre>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($log->new_data)
                                            <div class="mb-3">
                                                <h6 class="text-success">Current Data</h6>
                                                <div class="card">
                                                    <div class="card-body">
                                                        <pre class="mb-0"><code>{{ json_encode($log->masked_new_data, JSON_PRETTY_PRINT) }}</code></pre>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <div class="alert alert-info">
                                    <h4 class="alert-title">Security Notice</h4>
                                    <p class="mb-0">Sensitive information such as account numbers and bank details are
                                        masked for security purposes. Only authorized personnel can view the complete data.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
