@extends('dev.layouts.app')

@section('title', 'Shipping Details')
@section('content')
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Shipping Details</h3>
                        <div class="card-actions">
                            <a href="{{ route('admin.shipping.index') }}" class="btn btn-outline-secondary">
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
                                <h4>Shipping Information</h4>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>ID:</strong></td>
                                        <td>{{ $shippingInvoice->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Code:</strong></td>
                                        <td><code>{{ $shippingInvoice->kode }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $shippingInvoice->status == 'delivered' ? 'success' : ($shippingInvoice->status == 'failed' ? 'danger' : ($shippingInvoice->status == 'in_transit' ? 'info' : 'warning')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $shippingInvoice->status)) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Service:</strong></td>
                                        <td>{{ $shippingInvoice->service ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cost:</strong></td>
                                        <td>
                                            @if ($shippingInvoice->cost)
                                                <span class="font-weight-medium">Rp
                                                    {{ number_format($shippingInvoice->cost, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Resi:</strong></td>
                                        <td>
                                            @if ($shippingInvoice->resi)
                                                <code>{{ $shippingInvoice->resi }}</code>
                                                <button class="btn btn-sm btn-outline-info ms-2"
                                                    onclick="trackShipping({{ $shippingInvoice->id }})">
                                                    Track
                                                </button>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created At:</strong></td>
                                        <td>{{ $shippingInvoice->created_at->format('d M Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Updated At:</strong></td>
                                        <td>{{ $shippingInvoice->updated_at->format('d M Y H:i:s') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>Vendor Information</h4>
                                @if ($shippingInvoice->vendor)
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $shippingInvoice->vendor->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $shippingInvoice->vendor->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>{{ $shippingInvoice->vendor->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Address:</strong></td>
                                            <td>{{ $shippingInvoice->vendor->address }}</td>
                                        </tr>
                                    </table>
                                @else
                                    <p class="text-muted">No vendor information available</p>
                                @endif
                            </div>
                        </div>

                        @if ($shippingInvoice->transaction)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h4>Transaction Information</h4>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Transaction Code:</strong></td>
                                            <td><code>{{ $shippingInvoice->transaction->kode_transaksi }}</code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $shippingInvoice->transaction->status == 'completed' ? 'success' : ($shippingInvoice->transaction->status == 'pending' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($shippingInvoice->transaction->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Amount:</strong></td>
                                            <td>
                                                <span class="font-weight-medium">Rp
                                                    {{ number_format($shippingInvoice->transaction->total, 0, ',', '.') }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Method:</strong></td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $shippingInvoice->transaction->payment_method)) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created At:</strong></td>
                                            <td>{{ $shippingInvoice->transaction->created_at->format('d M Y H:i:s') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        @endif

                        @if ($shippingInvoice->deliveryConfirmation)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h4>Delivery Confirmation</h4>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Confirmation Code:</strong></td>
                                            <td><code>{{ $shippingInvoice->deliveryConfirmation->confirmation_code }}</code>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $shippingInvoice->deliveryConfirmation->status == 'confirmed' ? 'success' : ($shippingInvoice->deliveryConfirmation->status == 'rejected' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($shippingInvoice->deliveryConfirmation->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Customer Name:</strong></td>
                                            <td>{{ $shippingInvoice->deliveryConfirmation->customer_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Customer Phone:</strong></td>
                                            <td>{{ $shippingInvoice->deliveryConfirmation->customer_phone ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Delivery Address:</strong></td>
                                            <td>{{ $shippingInvoice->deliveryConfirmation->delivery_address ?? 'N/A' }}
                                            </td>
                                        </tr>
                                        @if ($shippingInvoice->deliveryConfirmation->admin_notes)
                                            <tr>
                                                <td><strong>Admin Notes:</strong></td>
                                                <td>{{ $shippingInvoice->deliveryConfirmation->admin_notes }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        @endif

                        <!-- Status Update Form -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4>Update Status</h4>
                                <form method="POST"
                                    action="{{ route('admin.shipping.update-status', $shippingInvoice->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select" required>
                                                <option value="pending"
                                                    {{ $shippingInvoice->status == 'pending' ? 'selected' : '' }}>Pending
                                                </option>
                                                <option value="in_transit"
                                                    {{ $shippingInvoice->status == 'in_transit' ? 'selected' : '' }}>In
                                                    Transit</option>
                                                <option value="delivered"
                                                    {{ $shippingInvoice->status == 'delivered' ? 'selected' : '' }}>
                                                    Delivered</option>
                                                <option value="failed"
                                                    {{ $shippingInvoice->status == 'failed' ? 'selected' : '' }}>Failed
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label">Notes</label>
                                            <textarea name="notes" class="form-control" rows="2" placeholder="Enter notes about the status update..."></textarea>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">Update Status</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function trackShipping(id) {
            fetch(`/admin/shipping/${id}/track`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Tracking Result',
                            html: `<pre>${JSON.stringify(data.data, null, 2)}</pre>`,
                            icon: 'success'
                        });
                    } else {
                        Swal.fire({
                            title: 'Tracking Failed',
                            text: data.message,
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to track shipment',
                        icon: 'error'
                    });
                });
        }
    </script>
@endsection
