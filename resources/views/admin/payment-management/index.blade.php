@extends('dev.layouts.app')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Manajemen
                    </div>
                    <h2 class="page-title">
                        Payment Management
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <button type="button" class="btn btn-outline-primary" onclick="bulkCheckStatus()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                <path d="M12 7v5l3 3" />
                            </svg>
                            Bulk Check Status
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Statistics Cards -->
            <div class="row row-deck row-cards mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Pending Payments</div>
                            </div>
                            <div class="h1 mb-3">{{ $stats['pending_payments'] }}</div>
                            <div class="d-flex mb-2">
                                <div>Total Amount: Rp {{ number_format($stats['total_amount_pending'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Paid Payments</div>
                            </div>
                            <div class="h1 mb-3">{{ $stats['paid_payments'] }}</div>
                            <div class="d-flex mb-2">
                                <div>Total Amount: Rp {{ number_format($stats['total_amount_paid'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Failed Payments</div>
                            </div>
                            <div class="h1 mb-3">{{ $stats['failed_payments'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Stuck Payments</div>
                            </div>
                            <div class="h1 mb-3">{{ $stats['stuck_payments'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stuck Payments -->
            @if ($stuckPayments->count() > 0)
                <div class="row row-deck row-cards mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Stuck Payments (24+ hours)</h3>
                                <div class="card-actions">
                                    <span class="badge bg-warning">{{ $stuckPayments->count() }} payments</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-vcenter">
                                        <thead>
                                            <tr>
                                                <th>Auction ID</th>
                                                <th>User</th>
                                                <th>Vendor</th>
                                                <th>Amount</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($stuckPayments as $auction)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('admin.auctions.show', $auction) }}"
                                                            class="text-decoration-none">
                                                            #{{ $auction->id }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $auction->user->name ?? 'N/A' }}</td>
                                                    <td>{{ $auction->winnerVendor->name ?? 'N/A' }}</td>
                                                    <td>Rp {{ number_format($auction->winning_bid, 0, ',', '.') }}</td>
                                                    <td>{{ $auction->created_at->diffForHumans() }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                            onclick="createNewPaymentLink({{ $auction->id }})">
                                                            Create New Link
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Expired Payments -->
            @if ($expiredPayments->count() > 0)
                <div class="row row-deck row-cards mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Expired Payments</h3>
                                <div class="card-actions">
                                    <span class="badge bg-danger">{{ $expiredPayments->count() }} payments</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-vcenter">
                                        <thead>
                                            <tr>
                                                <th>Payment ID</th>
                                                <th>User</th>
                                                <th>Amount</th>
                                                <th>Expired At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($expiredPayments as $payment)
                                                <tr>
                                                    <td>#{{ $payment->id }}</td>
                                                    <td>{{ $payment->auction->user->name ?? 'N/A' }}</td>
                                                    <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                                    <td>{{ $payment->expires_at->diffForHumans() }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-info"
                                                            onclick="checkPaymentStatus({{ $payment->id }})">
                                                            Check Status
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Failed Payments -->
            @if ($failedPayments->count() > 0)
                <div class="row row-deck row-cards">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Failed Payments</h3>
                                <div class="card-actions">
                                    <span class="badge bg-danger">{{ $failedPayments->count() }} payments</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-vcenter">
                                        <thead>
                                            <tr>
                                                <th>Payment ID</th>
                                                <th>User</th>
                                                <th>Amount</th>
                                                <th>Failed At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($failedPayments as $payment)
                                                <tr>
                                                    <td>#{{ $payment->id }}</td>
                                                    <td>{{ $payment->auction->user->name ?? 'N/A' }}</td>
                                                    <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                                    <td>{{ $payment->updated_at->diffForHumans() }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-info"
                                                            onclick="checkPaymentStatus({{ $payment->id }})">
                                                            Check Status
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function checkPaymentStatus(paymentId) {
            fetch(`/admin/payments/${paymentId}/check-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Payment status updated successfully');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error checking payment status: ' + error.message);
                });
        }

        function createNewPaymentLink(auctionId) {
            if (confirm('Are you sure you want to create a new payment link for this auction?')) {
                fetch(`/admin/payments/create-link/${auctionId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('New payment link created successfully');
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Error creating payment link: ' + error.message);
                    });
            }
        }

        function bulkCheckStatus() {
            if (confirm('This will check the status of all pending payments. Continue?')) {
                // Implementation for bulk check
                alert('Bulk check functionality will be implemented');
            }
        }
    </script>
@endpush
