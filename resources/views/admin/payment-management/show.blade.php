@extends('dev.layouts.app')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Payment Management
                    </div>
                    <h2 class="page-title">
                        Payment Details
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 6l6 6l-6 6" />
                            </svg>
                            Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Payment Information</h3>
                            <div class="card-actions">
                                <span
                                    class="badge bg-{{ $payment->status === 'paid' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Payment ID</label>
                                        <div class="form-control-plaintext">#{{ $payment->id }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">External ID</label>
                                        <div class="form-control-plaintext">{{ $payment->external_id }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Amount</label>
                                        <div class="form-control-plaintext">Rp
                                            {{ number_format($payment->amount, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <div class="form-control-plaintext">
                                            <span
                                                class="badge bg-{{ $payment->status === 'paid' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Created At</label>
                                        <div class="form-control-plaintext">
                                            {{ $payment->created_at->format('d M Y H:i:s') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Expires At</label>
                                        <div class="form-control-plaintext">
                                            {{ $payment->expires_at ? $payment->expires_at->format('d M Y H:i:s') : 'N/A' }}
                                            @if ($payment->expires_at && $payment->expires_at < now())
                                                <span class="text-danger">(Expired)</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($payment->checkout_url)
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label">Payment URL</label>
                                            <div class="form-control-plaintext">
                                                <a href="{{ $payment->checkout_url }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">
                                                    Open Payment Link
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($payment->auction)
                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="mt-4 mb-3">Auction Information</h4>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Auction ID</label>
                                                    <div class="form-control-plaintext">
                                                        <a href="{{ route('admin.auctions.show', $payment->auction) }}"
                                                            class="text-decoration-none">
                                                            #{{ $payment->auction->id }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Auction Title</label>
                                                    <div class="form-control-plaintext">{{ $payment->auction->title }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">User</label>
                                                    <div class="form-control-plaintext">
                                                        {{ $payment->auction->user->name ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Winning Vendor</label>
                                                    <div class="form-control-plaintext">
                                                        {{ $payment->auction->winnerVendor->name ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="btn-list">
                                        @if ($payment->status === 'pending')
                                            <button type="button" class="btn btn-primary"
                                                onclick="checkPaymentStatus({{ $payment->id }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                    <path d="M12 7v5l3 3" />
                                                </svg>
                                                Check Status
                                            </button>
                                        @endif

                                        <button type="button" class="btn btn-outline-info"
                                            onclick="resendNotification({{ $payment->id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M3 20l1.3 -3.9a9 8 0 1 1 3.4 5.9l-4.7 1" />
                                                <path d="M12 12l4 4l8 -8" />
                                            </svg>
                                            Resend Notification
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

        function resendNotification(paymentId) {
            fetch(`/admin/payments/${paymentId}/resend-notification`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Notification sent successfully');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error sending notification: ' + error.message);
                });
        }
    </script>
@endpush
