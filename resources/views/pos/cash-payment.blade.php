@extends('layouts.pos')

@section('title', 'Pembayaran Tunai - ' . $transaksi->kode)

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-money-bill-wave mr-2 text-primary"></i>Pembayaran Tunai
                </h3>
            </div>

            <div class="p-6">
                {{-- Transaction Summary --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h5 class="font-semibold text-blue-800 mb-3">
                        <i class="fas fa-receipt mr-1"></i>Ringkasan Transaksi
                    </h5>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p><span class="text-gray-600">Faktur:</span> <span class="font-medium">{{ $transaksi->kode }}</span></p>
                            <p><span class="text-gray-600">Pelanggan:</span> <span class="font-medium">{{ $transaksi->pelanggan->nama }}</span></p>
                        </div>
                        <div>
                            <p><span class="text-gray-600">Total:</span> <span class="font-bold text-primary">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span></p>
                            <p><span class="text-gray-600">Item:</span> <span class="font-medium">{{ $transaksi->transaksiItem->count() }} item</span></p>
                        </div>
                    </div>
                </div>

                {{-- Cash Payment Form --}}
                <form action="{{ route('vendor.pos.payment.cash.process', $transaksi->id) }}" method="POST">
                    @csrf

                    {{-- Payment Amount --}}
                    <div class="mb-4">
                        <label for="payment_amount" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Pembayaran Diterima</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-medium">Rp</span>
                            <input type="number"
                                class="w-full pl-10 pr-4 py-3 border {{ $errors->has('payment_amount') ? 'border-danger focus:ring-danger' : 'border-gray-300 focus:ring-primary' }} rounded-lg focus:ring-2 focus:border-transparent outline-none transition @error('payment_amount') border-danger @enderror"
                                id="payment_amount" name="payment_amount"
                                value="{{ $transaksi->total_harga }}"
                                min="{{ $transaksi->total_harga }}" step="1000" required>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Minimum: Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>
                        @error('payment_amount')
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Change Amount --}}
                    <div class="mb-4">
                        <label for="change_amount" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kembalian</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-medium">Rp</span>
                            <input type="number"
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg bg-gray-50 outline-none"
                                id="change_amount" name="change_amount" value="0" min="0" step="1000" readonly>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Dihitung secara otomatis</p>
                        @error('change_amount')
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                        <textarea
                            class="w-full px-4 py-3 border {{ $errors->has('notes') ? 'border-danger focus:ring-danger' : 'border-gray-300 focus:ring-primary' }} rounded-lg focus:ring-2 focus:border-transparent outline-none transition resize-none @error('notes') border-danger @enderror"
                            id="notes" name="notes" rows="3"
                            placeholder="Catatan tambahan untuk pembayaran ini..."></textarea>
                        @error('notes')
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Payment Summary --}}
                    <div class="bg-success/5 border border-success/20 rounded-lg p-4 mb-6">
                        <h6 class="font-semibold text-gray-800 mb-3">
                            <i class="fas fa-calculator mr-1"></i>Ringkasan Pembayaran
                        </h6>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div class="space-y-1">
                                <p class="font-medium text-gray-600">Total:</p>
                                <p class="font-medium text-gray-600">Pembayaran Diterima:</p>
                                <p class="font-bold text-gray-800">Kembalian:</p>
                            </div>
                            <div class="space-y-1 text-right">
                                <p class="text-gray-800" id="total-amount">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>
                                <p class="text-gray-800" id="payment-received">Rp 0</p>
                                <p class="font-bold" id="change-amount">Rp 0</p>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-3 justify-end">
                        <a href="{{ route('vendor.pos.payment.options', $transaksi->id) }}"
                            class="inline-flex items-center justify-center px-4 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors order-2 sm:order-1">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Opsi Pembayaran
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center px-4 py-3 bg-success text-white rounded-lg font-medium hover:bg-success/90 transition-colors order-1 sm:order-2">
                            <i class="fas fa-money-bill-wave mr-2"></i>Proses Pembayaran Tunai
                        </button>
                    </div>
                </form>
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
                    changeAmountDisplay.className = 'font-bold text-success text-right';
                } else {
                    changeAmountDisplay.className = 'font-bold text-danger text-right';
                }
            }

            paymentAmountInput.addEventListener('input', updatePaymentSummary);

            // Initial calculation
            updatePaymentSummary();
        });
    </script>
@endsection
