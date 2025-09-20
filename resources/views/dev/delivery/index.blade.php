@extends('dev.layouts.app')

@section('title', 'Delivery Confirmations')
@section('content')
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Delivery Confirmations</h3>
                        <div class="card-actions">
                            <a href="{{ route('admin.delivery.export', request()->query()) }}" class="btn btn-primary">
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
                            <div class="col-md-2">
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
                                                        <path d="M9 12l2 2l4 -4" />
                                                        <path d="M21 12c-1 0 -3 -1 -3 -3s2 -3 3 -3s3 1 3 3s-2 3 -3 3" />
                                                        <path d="M3 12c1 0 3 -1 3 -3s-2 -3 -3 -3s-3 1 -3 3s2 3 3 3" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="font-weight-medium">Total</div>
                                                <div class="text-muted">{{ number_format($stats['total_confirmations']) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-warning text-white avatar">
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
                                                <div class="font-weight-medium">Pending</div>
                                                <div class="text-muted">{{ number_format($stats['pending_confirmations']) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
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
                                                        <path d="M9 12l2 2l4 -4" />
                                                        <path d="M21 12c-1 0 -3 -1 -3 -3s2 -3 3 -3s3 1 3 3s-2 3 -3 3" />
                                                        <path d="M3 12c1 0 3 -1 3 -3s-2 -3 -3 -3s-3 1 -3 3s2 3 3 3" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="font-weight-medium">Confirmed</div>
                                                <div class="text-muted">{{ number_format($stats['confirmed']) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
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
                                                <div class="font-weight-medium">Rejected</div>
                                                <div class="text-muted">{{ number_format($stats['rejected']) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
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
                                                <div class="text-muted">{{ number_format($stats['today_confirmations']) }}
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
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>
                                            Confirmed</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                            Rejected</option>
                                    </select>
                                </div>
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
                                    <label class="form-label">Date From</label>
                                    <input type="date" name="date_from" class="form-control"
                                        value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date To</label>
                                    <input type="date" name="date_to" class="form-control"
                                        value="{{ request('date_to') }}">
                                </div>
                            </div>
                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label">Search</label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search by code, transaction..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                                    <a href="{{ route('admin.delivery.index') }}"
                                        class="btn btn-outline-secondary">Clear</a>
                                </div>
                            </div>
                        </form>

                        <!-- Delivery Table -->
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Code</th>
                                        <th>Vendor</th>
                                        <th>Transaction</th>
                                        <th>Customer</th>
                                        <th>Status</th>
                                        <th>Address</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($deliveryConfirmations as $confirmation)
                                        <tr>
                                            <td>{{ $confirmation->id }}</td>
                                            <td>
                                                <code>{{ $confirmation->confirmation_code }}</code>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-2">
                                                        <span class="avatar-initial rounded-circle bg-primary text-white">
                                                            {{ substr($confirmation->vendor->name ?? 'V', 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-medium">
                                                            {{ $confirmation->vendor->name ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <code>{{ $confirmation->transaction->kode_transaksi ?? 'N/A' }}</code>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="font-weight-medium">
                                                        {{ $confirmation->customer_name ?? 'N/A' }}</div>
                                                    <div class="text-muted">{{ $confirmation->customer_phone ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $confirmation->status == 'confirmed' ? 'success' : ($confirmation->status == 'rejected' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($confirmation->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;"
                                                    title="{{ $confirmation->delivery_address }}">
                                                    {{ $confirmation->delivery_address ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div>{{ $confirmation->created_at->format('d M Y') }}</div>
                                                <div class="text-muted">{{ $confirmation->created_at->format('H:i:s') }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.delivery.show', $confirmation->id) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        View
                                                    </a>
                                                    @if ($confirmation->status == 'pending')
                                                        <button class="btn btn-sm btn-outline-success"
                                                            onclick="approveConfirmation({{ $confirmation->id }})">
                                                            Approve
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger"
                                                            onclick="rejectConfirmation({{ $confirmation->id }})">
                                                            Reject
                                                        </button>
                                                    @endif
                                                </div>
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
                                                            <path d="M9 12l2 2l4 -4" />
                                                            <path d="M21 12c-1 0 -3 -1 -3 -3s2 -3 3 -3s3 1 3 3s-2 3 -3 3" />
                                                            <path d="M3 12c1 0 3 -1 3 -3s-2 -3 -3 -3s-3 1 -3 3s2 3 3 3" />
                                                        </svg>
                                                    </div>
                                                    <p class="empty-title">No delivery confirmations found</p>
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
                            {{ $deliveryConfirmations->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function approveConfirmation(id) {
            Swal.fire({
                title: 'Approve Delivery Confirmation',
                text: 'Are you sure you want to approve this delivery confirmation?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Approve',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/delivery/${id}/approve`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PATCH';

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function rejectConfirmation(id) {
            Swal.fire({
                title: 'Reject Delivery Confirmation',
                input: 'textarea',
                inputLabel: 'Reason for rejection',
                inputPlaceholder: 'Enter reason for rejection...',
                inputValidator: (value) => {
                    if (!value) {
                        return 'You need to provide a reason for rejection!';
                    }
                },
                showCancelButton: true,
                confirmButtonText: 'Reject',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/delivery/${id}/reject`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PATCH';

                    const notesField = document.createElement('input');
                    notesField.type = 'hidden';
                    notesField.name = 'admin_notes';
                    notesField.value = result.value;

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    form.appendChild(notesField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endsection
