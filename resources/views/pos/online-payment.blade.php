@extends('layouts.vendor')

@section('title', 'Online Payment - ' . $transaksi->kode)

@section('content')
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">🌐 Online Payment Setup</h3>
                    </div>
                    <div class="card-body">
                        <!-- Transaction Summary -->
                        <div class="alert alert-info">
                            <h5>Transaction Details</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Invoice:</strong> {{ $transaksi->kode }}</p>
                                    <p><strong>Customer:</strong> {{ $transaksi->pelanggan->nama }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Total Amount:</strong> Rp
                                        {{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>
                                    <p><strong>Items:</strong> {{ $transaksi->transaksiItems->count() }} items</p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Form -->
                        <form action="{{ route('vendor.pos.payment.online.process', $transaksi->id) }}" method="POST">
                            @csrf

                            <div class="row">
                                <!-- Payment Method Selection -->
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label">Payment Method</label>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="payment_type"
                                                        id="bank_transfer" value="bank_transfer" checked>
                                                    <label class="form-check-label" for="bank_transfer">
                                                        <i class="fas fa-university"></i> Bank Transfer
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="payment_type"
                                                        id="ewallet" value="ewallet">
                                                    <label class="form-check-label" for="ewallet">
                                                        <i class="fas fa-mobile-alt"></i> E-Wallet
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="payment_type"
                                                        id="retail" value="retail">
                                                    <label class="form-check-label" for="retail">
                                                        <i class="fas fa-store"></i> Retail Outlet
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="payment_type"
                                                        id="qris" value="qris">
                                                    <label class="form-check-label" for="qris">
                                                        <i class="fas fa-qrcode"></i> QRIS
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Customer Information -->
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="customer_email" class="form-label">Customer Email</label>
                                        <input type="email"
                                            class="form-control @error('customer_email') is-invalid @enderror"
                                            id="customer_email" name="customer_email"
                                            value="{{ $transaksi->pelanggan->email ?? '' }}" required>
                                        @error('customer_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="customer_phone" class="form-label">Customer Phone</label>
                                        <input type="tel"
                                            class="form-control @error('customer_phone') is-invalid @enderror"
                                            id="customer_phone" name="customer_phone"
                                            value="{{ $transaksi->pelanggan->telepon ?? '' }}" required>
                                        @error('customer_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Instructions -->
                            <div class="alert alert-warning">
                                <h6>Payment Instructions</h6>
                                <ul class="mb-0">
                                    <li>Customer will receive payment link via email/SMS</li>
                                    <li>Payment must be completed within 24 hours</li>
                                    <li>Transaction will be automatically confirmed upon payment</li>
                                    <li>Receipt will be generated after successful payment</li>
                                </ul>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('vendor.pos.payment.options', $transaksi->id) }}"
                                    class="btn btn-outline-secondary me-md-2">
                                    ← Back to Payment Options
                                </a>
                                <button type="submit" class="btn btn-success">
                                    🚀 Create Payment Link
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Update form based on payment type selection
            const paymentTypes = document.querySelectorAll('input[name="payment_type"]');
            const customerEmail = document.getElementById('customer_email');
            const customerPhone = document.getElementById('customer_phone');

            paymentTypes.forEach(type => {
                type.addEventListener('change', function() {
                    // Update required fields based on payment type
                    if (this.value === 'bank_transfer' || this.value === 'qris') {
                        customerEmail.required = true;
                        customerPhone.required = true;
                    } else if (this.value === 'ewallet') {
                        customerEmail.required = true;
                        customerPhone.required = true;
                    } else if (this.value === 'retail') {
                        customerEmail.required = false;
                        customerPhone.required = true;
                    }
                });
            });
        });
    </script>
@endsection
