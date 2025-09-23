@extends('layouts.vendor')

@section('title', 'Payment Options - ' . $transaksi->kode)

@section('content')
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">💳 Choose Payment Method</h3>
                    </div>
                    <div class="card-body">
                        <!-- Transaction Summary -->
                        <div class="alert alert-info">
                            <h5>Transaction Summary</h5>
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

                        <!-- Payment Methods -->
                        <div class="row">
                            <!-- Cash Payment -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-primary">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <svg class="text-primary" width="48" height="48" fill="currentColor"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M0 3a2 2 0 0 1 2-2h13a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H2a1 1 0 0 0 0 2h11a.5.5 0 0 1 0 1H2a2 2 0 0 1-2-2V3zm0 4a2 2 0 0 1 2-2h11a.5.5 0 0 1 0 1H2a1 1 0 0 0 0 2h11a.5.5 0 0 1 0 1H2a2 2 0 0 1-2-2V7zm0 4a2 2 0 0 1 2-2h11a.5.5 0 0 1 0 1H2a1 1 0 0 0 0 2h11a.5.5 0 0 1 0 1H2a2 2 0 0 1-2-2v-1z" />
                                            </svg>
                                        </div>
                                        <h5 class="card-title">Cash Payment</h5>
                                        <p class="card-text">Process payment with cash on hand</p>
                                        <ul class="list-unstyled text-start">
                                            <li>✅ Instant confirmation</li>
                                            <li>✅ No processing fees</li>
                                            <li>✅ Immediate receipt</li>
                                            <li>✅ No internet required</li>
                                        </ul>
                                        <a href="{{ route('vendor.pos.payment.cash', $transaksi->id) }}"
                                            class="btn btn-primary">
                                            💵 Process Cash Payment
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Online Payment -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-success">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <svg class="text-success" width="48" height="48" fill="currentColor"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2.5 0a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .5.5h11a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.5-.5h-11zm1 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1z" />
                                            </svg>
                                        </div>
                                        <h5 class="card-title">Online Payment</h5>
                                        <p class="card-text">Process payment via Xendit (Transfer/QRIS)</p>
                                        <ul class="list-unstyled text-start">
                                            <li>💳 Bank Transfer (VA)</li>
                                            <li>📱 E-Wallet (OVO, DANA, etc.)</li>
                                            <li>🏪 Retail Outlets</li>
                                            <li>🔒 Secure & Verified</li>
                                        </ul>
                                        <a href="{{ route('vendor.pos.payment.online', $transaksi->id) }}"
                                            class="btn btn-success">
                                            🌐 Process Online Payment
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Back Button -->
                        <div class="text-center mt-4">
                            <a href="{{ route('vendor.pos.invoice.show', $transaksi->id) }}"
                                class="btn btn-outline-secondary">
                                ← Back to Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
