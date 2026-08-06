@extends('layouts.vendor')

@section('title', 'Opsi Pembayaran - ' . $transaksi->kode)

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-credit-card mr-2 text-primary"></i>Pilih Metode Pembayaran
                </h3>
            </div>

            <div class="p-6">
                {{-- Transaction Summary --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h5 class="font-semibold text-blue-800 mb-3">
                        <i class="fas fa-receipt mr-1"></i>Ringkasan Transaksi
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p><span class="text-gray-600">Faktur:</span> <span class="font-medium">{{ $transaksi->kode }}</span></p>
                            <p><span class="text-gray-600">Pelanggan:</span> <span class="font-medium">{{ $transaksi->pelanggan->nama }}</span></p>
                        </div>
                        <div>
                            <p><span class="text-gray-600">Total:</span> <span class="font-bold text-primary">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span></p>
                            <p><span class="text-gray-600">Item:</span> <span class="font-medium">{{ $transaksi->transaksiItems->count() }} item</span></p>
                        </div>
                    </div>
                </div>

                {{-- Payment Methods --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- Cash Payment --}}
                    <div class="border border-primary/20 rounded-xl p-6 hover:shadow-md transition-shadow">
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-money-bill-wave text-primary text-2xl"></i>
                            </div>
                            <h5 class="font-semibold text-gray-800 mb-2">Pembayaran Tunai</h5>
                            <p class="text-sm text-gray-600 mb-4">Proses pembayaran dengan uang tunai</p>
                            <ul class="text-sm text-left space-y-1 mb-4">
                                <li class="text-green-600"><i class="fas fa-check mr-1"></i>Konfirmasi instan</li>
                                <li class="text-green-600"><i class="fas fa-check mr-1"></i>Tanpa biaya admin</li>
                                <li class="text-green-600"><i class="fas fa-check mr-1"></i>Struk langsung</li>
                                <li class="text-green-600"><i class="fas fa-check mr-1"></i>Tanpa internet</li>
                            </ul>
                            <a href="{{ route('vendor.pos.payment.cash', $transaksi->id) }}"
                                class="w-full inline-flex items-center justify-center px-4 py-3 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors">
                                <i class="fas fa-money-bill-wave mr-2"></i>Proses Pembayaran Tunai
                            </a>
                        </div>
                    </div>

                    {{-- Online Payment --}}
                    <div class="border border-success/20 rounded-xl p-6 hover:shadow-md transition-shadow">
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto bg-success/10 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-globe text-success text-2xl"></i>
                            </div>
                            <h5 class="font-semibold text-gray-800 mb-2">Pembayaran Online</h5>
                            <p class="text-sm text-gray-600 mb-4">Proses pembayaran via Xendit (Transfer/QRIS)</p>
                            <ul class="text-sm text-left space-y-1 mb-4">
                                <li class="text-blue-600"><i class="fas fa-university mr-1"></i>Transfer Bank (VA)</li>
                                <li class="text-blue-600"><i class="fas fa-mobile-alt mr-1"></i>E-Wallet (OVO, DANA, dll.)</li>
                                <li class="text-blue-600"><i class="fas fa-store mr-1"></i>Toko Retail</li>
                                <li class="text-blue-600"><i class="fas fa-shield-alt mr-1"></i>Aman & Terverifikasi</li>
                            </ul>
                            <a href="{{ route('vendor.pos.payment.online', $transaksi->id) }}"
                                class="w-full inline-flex items-center justify-center px-4 py-3 bg-success text-white rounded-lg font-medium hover:bg-success/90 transition-colors">
                                <i class="fas fa-globe mr-2"></i>Proses Pembayaran Online
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Back Button --}}
                <div class="text-center">
                    <a href="{{ route('vendor.pos.invoice.show', $transaksi->id) }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Faktur
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
