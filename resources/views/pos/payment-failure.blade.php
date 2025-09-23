@extends('layouts.vendor')

@section('title', 'Payment Failed - ' . $transaksi->kode)

@section('content')
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white text-center">
                        <h3 class="mb-0">❌ Payment Failed</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <svg class="text-danger" width="64" height="64" fill="currentColor" viewBox="0 0 16 16">
                                <path
                                    d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z" />
                            </svg>
                        </div>

                        <h4 class="text-danger mb-3">Payment Failed</h4>

                        <div class="alert alert-danger">
                            <h5>Transaction Details</h5>
                            <p><strong>Invoice:</strong> {{ $transaksi->kode }}</p>
                            <p><strong>Customer:</strong> {{ $transaksi->pelanggan->nama }}</p>
                            <p><strong>Amount:</strong> Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>
                            <p><strong>Status:</strong> {{ ucfirst($transaksi->status ?? 'Failed') }}</p>
                        </div>

                        <div class="alert alert-warning">
                            <h6>What to do next?</h6>
                            <ul class="list-unstyled mb-0">
                                <li>• Check payment status with customer</li>
                                <li>• Try alternative payment method</li>
                                <li>• Process cash payment if customer is present</li>
                                <li>• Contact customer for payment completion</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('vendor.pos.payment.options', $transaksi->id) }}" class="btn btn-primary">
                                💳 Try Different Payment
                            </a>
                            <a href="{{ route('vendor.pos.invoice.show', $transaksi->id) }}"
                                class="btn btn-outline-secondary">
                                📄 View Invoice
                            </a>
                            <a href="{{ route('vendor.pos.index') }}" class="btn btn-outline-primary">
                                🏪 Back to POS
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
