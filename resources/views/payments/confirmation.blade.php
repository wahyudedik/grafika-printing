@extends('layouts.app')

@section('title', 'Konfirmasi Pembayaran')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Konfirmasi Pembayaran</h1>
                <p class="text-gray-600">Silakan review detail pembayaran Anda sebelum melanjutkan</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Auction Details -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Detail Lelang</h2>

                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Judul Lelang</label>
                            <p class="text-gray-900">{{ $auction->title }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Deskripsi</label>
                            <p class="text-gray-900">{{ $auction->description }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Vendor Pemenang</label>
                            <p class="text-gray-900">{{ $winningBid->vendor->name }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Jumlah Tawaran Menang</label>
                            <p class="text-2xl font-bold text-green-600">Rp
                                {{ number_format($winningBid->bid_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Breakdown -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Rincian Pembayaran</h2>

                    <div class="space-y-3">
                        <!-- Bid Amount -->
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-gray-600">Tawaran Menang</span>
                            <span class="font-medium">Rp {{ number_format($winningBid->bid_amount, 0, ',', '.') }}</span>
                        </div>

                        <!-- Admin Fee -->
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-gray-600">Biaya Admin ({{ $feeCalculation['admin_fee_percentage'] }}%)</span>
                            <span class="font-medium text-orange-600">+ Rp
                                {{ number_format($feeCalculation['admin_fee'], 0, ',', '.') }}</span>
                        </div>

                        <!-- Payment Gateway Fee -->
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-gray-600">Biaya Payment Gateway</span>
                            <span class="font-medium text-orange-600">+ Rp
                                {{ number_format($feeCalculation['payment_gateway_fee'], 0, ',', '.') }}</span>
                        </div>

                        <!-- Total -->
                        <div class="flex justify-between items-center py-3 bg-gray-50 rounded-lg px-4">
                            <span class="text-lg font-semibold text-gray-900">Total Pembayaran</span>
                            <span class="text-2xl font-bold text-green-600">Rp
                                {{ number_format($feeCalculation['total_amount'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Vendor Receives -->
                    <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-blue-700">Vendor menerima</span>
                            <span class="font-semibold text-blue-800">Rp
                                {{ number_format($feeCalculation['vendor_receives'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Method Selection -->
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Pilih Metode Pembayaran</h2>

                <form action="{{ route('user.payments.process', $auction) }}" method="POST" id="paymentForm" data-loading>
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <!-- Bank Transfer -->
                        <label class="relative">
                            <input type="radio" name="payment_method" value="bank_transfer" class="sr-only" checked>
                            <div
                                class="payment-method-card cursor-pointer border-2 border-gray-200 rounded-lg p-4 text-center hover:border-blue-500 transition-colors">
                                <div class="text-2xl mb-2">🏦</div>
                                <div class="font-medium">Transfer Bank</div>
                                <div class="text-sm text-gray-500">1.5% fee</div>
                            </div>
                        </label>

                        <!-- Credit Card -->
                        <label class="relative">
                            <input type="radio" name="payment_method" value="credit_card" class="sr-only">
                            <div
                                class="payment-method-card cursor-pointer border-2 border-gray-200 rounded-lg p-4 text-center hover:border-blue-500 transition-colors">
                                <div class="text-2xl mb-2">💳</div>
                                <div class="font-medium">Kartu Kredit</div>
                                <div class="text-sm text-gray-500">2.9% fee</div>
                            </div>
                        </label>

                        <!-- E-Wallet -->
                        <label class="relative">
                            <input type="radio" name="payment_method" value="ewallet" class="sr-only">
                            <div
                                class="payment-method-card cursor-pointer border-2 border-gray-200 rounded-lg p-4 text-center hover:border-blue-500 transition-colors">
                                <div class="text-2xl mb-2">📱</div>
                                <div class="font-medium">E-Wallet</div>
                                <div class="text-sm text-gray-500">2.0% fee</div>
                            </div>
                        </label>

                        <!-- Retail Outlet -->
                        <label class="relative">
                            <input type="radio" name="payment_method" value="retail_outlet" class="sr-only">
                            <div
                                class="payment-method-card cursor-pointer border-2 border-gray-200 rounded-lg p-4 text-center hover:border-blue-500 transition-colors">
                                <div class="text-2xl mb-2">🏪</div>
                                <div class="font-medium">Toko Retail</div>
                                <div class="text-sm text-gray-500">1.0% fee</div>
                            </div>
                        </label>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mb-6">
                        <label class="flex items-start">
                            <input type="checkbox" name="agree_terms" class="mt-1 mr-3" required>
                            <span class="text-sm text-gray-600">
                                Saya setuju dengan <a href="#" class="text-blue-600 hover:underline">Syarat dan
                                    Ketentuan</a>
                                dan <a href="#" class="text-blue-600 hover:underline">Kebijakan Privasi</a>
                            </span>
                        </label>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center">
                        <a href="{{ route('user.auctions.show', $auction) }}"
                            class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                            Batal
                        </a>

                        <button type="submit"
                            class="px-8 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors font-medium">
                            Lanjutkan ke Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .payment-method-card input:checked+div {
            border-color: #3B82F6;
            background-color: #EFF6FF;
        }

        .payment-method-card:hover {
            border-color: #3B82F6;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
            const cards = document.querySelectorAll('.payment-method-card');

            paymentMethods.forEach((method, index) => {
                method.addEventListener('change', function() {
                    // Remove selected class from all cards
                    cards.forEach(card => {
                        card.classList.remove('border-blue-500', 'bg-blue-50');
                        card.classList.add('border-gray-200');
                    });

                    // Add selected class to current card
                    if (this.checked) {
                        cards[index].classList.remove('border-gray-200');
                        cards[index].classList.add('border-blue-500', 'bg-blue-50');
                    }
                });
            });

            // Set initial state
            if (paymentMethods[0].checked) {
                cards[0].classList.remove('border-gray-200');
                cards[0].classList.add('border-blue-500', 'bg-blue-50');
            }
        });
    </script>
@endsection
