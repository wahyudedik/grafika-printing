@extends('dev.layouts.app')

@section('title', 'Delivery Confirmation Details')
@section('content')
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Delivery Confirmation Details</h3>
                        <div class="card-actions">
                            <a href="{{ route('admin.delivery.index') }}" class="btn btn-outline-secondary">
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
                                <h4>Confirmation Information</h4>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>ID:</strong></td>
                                        <td>{{ $deliveryConfirmation->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Code:</strong></td>
                                        <td><code>{{ $deliveryConfirmation->confirmation_code }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $deliveryConfirmation->status == 'confirmed' ? 'success' : ($deliveryConfirmation->status == 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($deliveryConfirmation->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created At:</strong></td>
                                        <td>{{ $deliveryConfirmation->created_at->format('d M Y H:i:s') }}</td>
                                    </tr>
                                    @if ($deliveryConfirmation->confirmed_at)
                                        <tr>
                                            <td><strong>Confirmed At:</strong></td>
                                            <td>{{ $deliveryConfirmation->confirmed_at->format('d M Y H:i:s') }}</td>
                                        </tr>
                                    @endif
                                    @if ($deliveryConfirmation->rejected_at)
                                        <tr>
                                            <td><strong>Rejected At:</strong></td>
                                            <td>{{ $deliveryConfirmation->rejected_at->format('d M Y H:i:s') }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>Vendor Information</h4>
                                @if ($deliveryConfirmation->vendor)
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $deliveryConfirmation->vendor->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $deliveryConfirmation->vendor->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>{{ $deliveryConfirmation->vendor->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Address:</strong></td>
                                            <td>{{ $deliveryConfirmation->vendor->address }}</td>
                                        </tr>
                                    </table>
                                @else
                                    <p class="text-muted">No vendor information available</p>
                                @endif
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h4>Customer Information</h4>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>{{ $deliveryConfirmation->customer_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td>{{ $deliveryConfirmation->customer_phone ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $deliveryConfirmation->customer_email ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Delivery Address:</strong></td>
                                        <td>{{ $deliveryConfirmation->delivery_address ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>Transaction Information</h4>
                                @if ($deliveryConfirmation->transaction)
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Transaction Code:</strong></td>
                                            <td><code>{{ $deliveryConfirmation->transaction->kode_transaksi }}</code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $deliveryConfirmation->transaction->status == 'completed' ? 'success' : ($deliveryConfirmation->transaction->status == 'pending' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($deliveryConfirmation->transaction->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Amount:</strong></td>
                                            <td>
                                                <span class="font-weight-medium">Rp
                                                    {{ number_format($deliveryConfirmation->transaction->total, 0, ',', '.') }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Method:</strong></td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $deliveryConfirmation->transaction->payment_method)) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created At:</strong></td>
                                            <td>{{ $deliveryConfirmation->transaction->created_at->format('d M Y H:i:s') }}
                                            </td>
                                        </tr>
                                    </table>
                                @else
                                    <p class="text-muted">No transaction information available</p>
                                @endif
                            </div>
                        </div>

                        @if ($deliveryConfirmation->shippingInvoice)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h4>Shipping Information</h4>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Shipping Code:</strong></td>
                                            <td><code>{{ $deliveryConfirmation->shippingInvoice->kode }}</code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Resi:</strong></td>
                                            <td>
                                                @if ($deliveryConfirmation->shippingInvoice->resi)
                                                    <code>{{ $deliveryConfirmation->shippingInvoice->resi }}</code>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Service:</strong></td>
                                            <td>{{ $deliveryConfirmation->shippingInvoice->service ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Cost:</strong></td>
                                            <td>
                                                @if ($deliveryConfirmation->shippingInvoice->cost)
                                                    <span class="font-weight-medium">Rp
                                                        {{ number_format($deliveryConfirmation->shippingInvoice->cost, 0, ',', '.') }}</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $deliveryConfirmation->shippingInvoice->status == 'delivered' ? 'success' : ($deliveryConfirmation->shippingInvoice->status == 'failed' ? 'danger' : ($deliveryConfirmation->shippingInvoice->status == 'in_transit' ? 'info' : 'warning')) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $deliveryConfirmation->shippingInvoice->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        @endif

                        @if ($deliveryConfirmation->admin_notes)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h4>Admin Notes</h4>
                                    <div class="alert alert-info">
                                        {{ $deliveryConfirmation->admin_notes }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        @if ($deliveryConfirmation->status == 'pending')
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h4>Actions</h4>
                                    <div class="btn-group">
                                        <button class="btn btn-success"
                                            onclick="approveConfirmation({{ $deliveryConfirmation->id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M9 12l2 2l4 -4" />
                                                <path d="M21 12c-1 0 -3 -1 -3 -3s2 -3 3 -3s3 1 3 3s-2 3 -3 3" />
                                                <path d="M3 12c1 0 3 -1 3 -3s-2 -3 -3 -3s-3 1 -3 3s2 3 3 3" />
                                            </svg>
                                            Approve
                                        </button>
                                        <button class="btn btn-danger"
                                            onclick="rejectConfirmation({{ $deliveryConfirmation->id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M12 9v2m0 4v.01" />
                                                <path
                                                    d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.84 2.75" />
                                            </svg>
                                            Reject
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
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
