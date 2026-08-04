@extends('layouts.vendor')

@section('title', 'Detail Order ' . $order->order_number)

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="d-flex align-items-center mb-1">
                <a href="{{ route('vendor.manual-transfers.index') }}" class="btn btn-icon btn-ghost-secondary me-2">
                    ← Kembali
                </a>
                <h2 class="page-title">Order {{ $order->order_number }}</h2>
            </div>
        </div>
        <div class="col-auto">
            <span class="badge bg-{{ $order->status_color }} fs-5">{{ $order->status_label }}</span>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible">{{ session('success') }}<a class="btn-close" data-bs-dismiss="alert"></a></div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible">{{ session('error') }}<a class="btn-close" data-bs-dismiss="alert"></a></div>
@endif

<div class="row">
    <div class="col-lg-8">
        {{-- Customer Info --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Informasi Pelanggan</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <div class="text-muted">Nama</div>
                            <div class="fw-bold">{{ $order->customer_name }}</div>
                        </div>
                        <div class="mb-2">
                            <div class="text-muted">Telepon</div>
                            <div>{{ $order->customer_phone ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <div class="text-muted">Email</div>
                            <div>{{ $order->customer_email ?? '-' }}</div>
                        </div>
                        <div class="mb-2">
                            <div class="text-muted">Tanggal Order</div>
                            <div>{{ $order->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Items --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Items</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($order->items && is_array($order->items))
                            @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item['name'] ?? '-' }}</td>
                                    <td>Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</td>
                                    <td>{{ $item['quantity'] ?? 1 }}</td>
                                    <td class="text-end">Rp {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total</td>
                            <td class="text-end fw-bold fs-4">{{ $order->formatted_total }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Transfer Proof --}}
        @if($order->transfer_proof)
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Bukti Transfer</h3>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset('storage/manual_transfer_proofs/' . $order->transfer_proof) }}"
                         alt="Bukti Transfer" class="img-fluid rounded" style="max-height: 400px;">
                </div>
            </div>
        @endif

        {{-- Notes --}}
        @if($order->notes)
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Catatan</h3>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $order->notes }}</p>
                </div>
            </div>
        @endif

        {{-- Rejection Reason --}}
        @if($order->rejection_reason)
            <div class="card mb-3 border-danger">
                <div class="card-header bg-danger-lt">
                    <h3 class="card-title text-danger">Alasan Penolakan</h3>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $order->rejection_reason }}</p>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        {{-- Payment Info --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Info Pembayaran</h3>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <div class="text-muted">Bank</div>
                    <div class="fw-bold">{{ $order->bank_name ?? '-' }}</div>
                </div>
                <div class="mb-2">
                    <div class="text-muted">No. Rekening</div>
                    <div class="fw-bold font-monospace">{{ $order->account_number ?? '-' }}</div>
                </div>
                <div class="mb-2">
                    <div class="text-muted">Atas Nama</div>
                    <div class="fw-bold">{{ $order->account_name ?? '-' }}</div>
                </div>
                @if($order->paid_at)
                    <div class="mb-2">
                        <div class="text-muted">Dibayar Pada</div>
                        <div>{{ $order->paid_at->format('d/m/Y H:i') }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Aksi</h3>
            </div>
            <div class="card-body">
                @if($order->isPaid())
                    <form action="{{ route('vendor.manual-transfers.confirm', $order) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Konfirmasi order ini sebagai selesai?')">
                            ✅ Konfirmasi Selesai
                        </button>
                    </form>
                @endif

                @if($order->status !== 'completed' && $order->status !== 'rejected')
                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        ❌ Tolak Order
                    </button>
                @endif

                @if($order->status === 'pending')
                    <div class="text-muted text-center mt-3 small">
                        Menunggu bukti transfer dari pelanggan
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal modal-blur fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tolak Order</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('vendor.manual-transfers.reject', $order) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="Masukkan alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Order</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
