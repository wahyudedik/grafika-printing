@extends('layouts.vendor')

@section('title', 'Cash Payment - ' . $transaksi->kode)

@section('content')
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">💵 Cash Payment</h3>
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

                        <!-- Cash Payment Form -->
                        <form action="{{ route('vendor.pos.payment.cash.process', $transaksi->id) }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="payment_amount" class="form-label">Payment Amount Received</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('payment_amount') is-invalid @enderror"
                                        id="payment_amount" name="payment_amount" value="{{ $transaksi->total_harga }}"
                                        min="{{ $transaksi->total_harga }}" step="1000" required>
                                    @error('payment_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Minimum: Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="change_amount" class="form-label">Change Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('change_amount') is-invalid @enderror"
                                        id="change_amount" name="change_amount" value="0" min="0" step="1000"
                                        readonly>
                                    @error('change_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Calculated automatically</div>
                            </div>

                            <div class="mb-4">
                                <label for="notes" class="form-label">Notes (Optional)</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                                    placeholder="Additional notes for this payment..."></textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Payment Summary -->
                            <div class="alert alert-success">
                                <h6>Payment Summary</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Total Amount:</strong></p>
                                        <p class="mb-1"><strong>Payment Received:</strong></p>
                                        <p class="mb-0"><strong>Change:</strong></p>
                                    </div>
                                    <div class="col-6 text-end">
                                        <p class="mb-1" id="total-amount">Rp
                                            {{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>
                                        <p class="mb-1" id="payment-received">Rp 0</p>
                                        <p class="mb-0" id="change-amount">Rp 0</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('vendor.pos.payment.options', $transaksi->id) }}"
                                    class="btn btn-outline-secondary me-md-2">
                                    ← Back to Payment Options
                                </a>
                                <button type="submit" class="btn btn-success">
                                    💵 Process Cash Payment
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
            const paymentAmountInput = document.getElementById('payment_amount');
            const changeAmountInput = document.getElementById('change_amount');
            const totalAmount = {{ $transaksi->total_harga }};

            const totalAmountDisplay = document.getElementById('total-amount');
            const paymentReceivedDisplay = document.getElementById('payment-received');
            const changeAmountDisplay = document.getElementById('change-amount');

            function updatePaymentSummary() {
                const paymentAmount = parseFloat(paymentAmountInput.value) || 0;
                const changeAmount = Math.max(0, paymentAmount - totalAmount);

                // Update change amount input
                changeAmountInput.value = changeAmount;

                // Update display
                paymentReceivedDisplay.textContent = 'Rp ' + paymentAmount.toLocaleString('id-ID');
                changeAmountDisplay.textContent = 'Rp ' + changeAmount.toLocaleString('id-ID');

                // Update colors based on payment amount
                if (paymentAmount >= totalAmount) {
                    changeAmountDisplay.parentElement.className = 'col-6 text-end text-success';
                } else {
                    changeAmountDisplay.parentElement.className = 'col-6 text-end text-danger';
                }
            }

            paymentAmountInput.addEventListener('input', updatePaymentSummary);

            // Initial calculation
            updatePaymentSummary();
        });
    </script>
@endsection
