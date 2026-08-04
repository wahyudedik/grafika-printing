@extends('dev.layouts.app')

@section('title', 'Invoice Pengiriman')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col-auto">
            <div class="page-pretitle">Admin Panel</div>
            <h2 class="page-title">Invoice Pengiriman</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('admin.shipping.index') }}" class="btn btn-outline-primary">
                Kembali ke Shipping
            </a>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        {{-- Filter --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.shipping.invoices') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Status Pembayaran</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Vendor</label>
                            <select name="vendor_id" class="form-select">
                                <option value="">Semua Vendor</option>
                                @if(isset($vendors))
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                            {{ $vendor->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-filter"></i> Filter
                            </button>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.shipping.export', request()->query()) }}" class="btn btn-outline-success w-100">
                                <i class="ti ti-download"></i> Export
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Invoice Pengiriman</h3>
                <div class="card-actions">
                    <span class="text-muted">{{ $shippingInvoices->total() }} invoice</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Transaksi</th>
                                <th>Vendor</th>
                                <th>Kurir</th>
                                <th>No. Resi</th>
                                <th>Ongkir</th>
                                <th>Status Bayar</th>
                                <th>Status Kirim</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shippingInvoices as $invoice)
                            <tr>
                                <td>
                                    <span class="badge bg-blue-lt">{{ $invoice->kode }}</span>
                                </td>
                                <td>
                                    @if($invoice->transaction)
                                        <a href="{{ route('admin.shipping.show', $invoice->id) }}" class="text-decoration-none">
                                            {{ $invoice->transaction->kode_transaksi ?? 'N/A' }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $invoice->vendor->name ?? 'N/A' }}
                                </td>
                                <td>
                                    <span class="text-muted">{{ $invoice->courier ?? '-' }}</span>
                                    @if($invoice->service)
                                        <br><small class="text-muted">{{ $invoice->service }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($invoice->waybill_number)
                                        <code>{{ $invoice->waybill_number }}</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $invoice->formatted_shipping_cost ?? ('Rp ' . number_format($invoice->shipping_cost ?? 0, 0, ',', '.')) }}</span>
                                </td>
                                <td>
                                    @php
                                        $paymentColors = [
                                            'pending' => 'bg-yellow-lt text-yellow-fg',
                                            'paid' => 'bg-green-lt text-green-fg',
                                            'cancelled' => 'bg-red-lt text-red-fg',
                                        ];
                                    @endphp
                                    <span class="badge {{ $paymentColors[$invoice->payment_status] ?? 'bg-gray-lt text-gray-fg' }}">
                                        {{ ucfirst($invoice->payment_status ?? 'unknown') }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $shippingColors = [
                                            'pending' => 'bg-secondary-lt text-secondary-fg',
                                            'processing' => 'bg-info-lt text-info-fg',
                                            'shipped' => 'bg-primary-lt text-primary-fg',
                                            'delivered' => 'bg-green-lt text-green-fg',
                                            'failed' => 'bg-red-lt text-red-fg',
                                        ];
                                    @endphp
                                    <span class="badge {{ $shippingColors[$invoice->shipping_status] ?? 'bg-gray-lt text-gray-fg' }}">
                                        {{ ucfirst(str_replace('_', ' ', $invoice->shipping_status ?? 'unknown')) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.shipping.show', $invoice->id) }}" class="btn btn-sm btn-ghost-primary">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="ti ti-package-off icon-lg mb-2"></i>
                                    <br>
                                    Tidak ada invoice pengiriman
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($shippingInvoices->hasPages())
            <div class="card-footer d-flex justify-content-center">
                {{ $shippingInvoices->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
