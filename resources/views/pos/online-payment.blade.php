@extends('layouts.pos')

@section('title', 'Pembayaran Online - ' . $transaksi->kode)

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-globe mr-2 text-success"></i>Pengaturan Pembayaran Online
                </h3>
            </div>

            <div class="p-6">
                {{-- Transaction Summary --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h5 class="font-semibold text-blue-800 mb-3">
                        <i class="fas fa-receipt mr-1"></i>Detail Transaksi
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
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

                {{-- Payment Form --}}
                <form action="{{ route('vendor.pos.payment.online.process', $transaksi->id) }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        {{-- Payment Method Selection --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Metode Pembayaran</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="payment_type" value="bank_transfer" checked
                                        class="peer sr-only">
                                    <div class="border-2 border-gray-200 peer-checked:border-primary peer-checked:bg-primary/5 rounded-lg p-3 text-center transition-all hover:border-gray-300">
                                        <i class="fas fa-university text-lg text-gray-600 peer-checked:text-primary mb-1"></i>
                                        <p class="text-sm font-medium text-gray-700">Transfer Bank</p>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer">
                                    <input type="radio" name="payment_type" value="ewallet"
                                        class="peer sr-only">
                                    <div class="border-2 border-gray-200 peer-checked:border-primary peer-checked:bg-primary/5 rounded-lg p-3 text-center transition-all hover:border-gray-300">
                                        <i class="fas fa-mobile-alt text-lg text-gray-600 peer-checked:text-primary mb-1"></i>
                                        <p class="text-sm font-medium text-gray-700">E-Wallet</p>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer">
                                    <input type="radio" name="payment_type" value="retail"
                                        class="peer sr-only">
                                    <div class="border-2 border-gray-200 peer-checked:border-primary peer-checked:bg-primary/5 rounded-lg p-3 text-center transition-all hover:border-gray-300">
                                        <i class="fas fa-store text-lg text-gray-600 peer-checked:text-primary mb-1"></i>
                                        <p class="text-sm font-medium text-gray-700">Toko Retail</p>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer">
                                    <input type="radio" name="payment_type" value="qris"
                                        class="peer sr-only">
                                    <div class="border-2 border-gray-200 peer-checked:border-primary peer-checked:bg-primary/5 rounded-lg p-3 text-center transition-all hover:border-gray-300">
                                        <i class="fas fa-qrcode text-lg text-gray-600 peer-checked:text-primary mb-1"></i>
                                        <p class="text-sm font-medium text-gray-700">QRIS</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Customer Information --}}
                        <div>
                            <div class="mb-4">
                                <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Email Pelanggan</label>
                                <input type="email"
                                    class="w-full px-4 py-3 border {{ $errors->has('customer_email') ? 'border-danger focus:ring-danger' : 'border-gray-300 focus:ring-primary' }} rounded-lg focus:ring-2 focus:border-transparent outline-none transition @error('customer_email') border-danger @enderror"
                                    id="customer_email" name="customer_email"
                                    value="{{ $transaksi->pelanggan->email ?? '' }}" required>
                                @error('customer_email')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Telepon Pelanggan</label>
                                <input type="tel"
                                    class="w-full px-4 py-3 border {{ $errors->has('customer_phone') ? 'border-danger focus:ring-danger' : 'border-gray-300 focus:ring-primary' }} rounded-lg focus:ring-2 focus:border-transparent outline-none transition @error('customer_phone') border-danger @enderror"
                                    id="customer_phone" name="customer_phone"
                                    value="{{ $transaksi->pelanggan->no_telp ?? '' }}" required>
                                @error('customer_phone')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Payment Instructions --}}
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <h6 class="font-semibold text-yellow-800 mb-2">
                            <i class="fas fa-info-circle mr-1"></i>Instruksi Pembayaran
                        </h6>
                        <ul class="text-sm text-yellow-700 space-y-1">
                            <li>• Pelanggan akan menerima link pembayaran via email/SMS</li>
                            <li>• Pembayaran harus diselesaikan dalam 24 jam</li>
                            <li>• Transaksi akan otomatis terkonfirmasi setelah pembayaran</li>
                            <li>• Struk akan dibuat setelah pembayaran berhasil</li>
                        </ul>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-3 justify-end">
                        <a href="{{ route('vendor.pos.payment.options', $transaksi->id) }}"
                            class="inline-flex items-center justify-center px-4 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors order-2 sm:order-1">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Opsi Pembayaran
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center px-4 py-3 bg-success text-white rounded-lg font-medium hover:bg-success/90 transition-colors order-1 sm:order-2">
                            <i class="fas fa-paper-plane mr-2"></i>Buat Link Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentTypes = document.querySelectorAll('input[name="payment_type"]');
            const customerEmail = document.getElementById('customer_email');
            const customerPhone = document.getElementById('customer_phone');

            paymentTypes.forEach(type => {
                type.addEventListener('change', function() {
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
