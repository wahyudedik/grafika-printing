@extends('layouts.vendor')

@section('title', 'Pembayaran Gagal - ' . $transaksi->kode)

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-sm border border-danger/20 overflow-hidden">
            {{-- Header --}}
            <div class="bg-danger px-6 py-4 text-center">
                <h3 class="text-lg font-semibold text-white mb-0">
                    <i class="fas fa-times-circle mr-2"></i>Pembayaran Gagal
                </h3>
            </div>

            {{-- Body --}}
            <div class="p-6 text-center">
                {{-- Error Icon --}}
                <div class="mb-4">
                    <div class="w-16 h-16 mx-auto bg-danger/10 rounded-full flex items-center justify-center">
                        <i class="fas fa-times-circle text-danger text-4xl"></i>
                    </div>
                </div>

                <h4 class="text-lg font-semibold text-danger mb-4">Pembayaran Gagal</h4>

                {{-- Transaction Details --}}
                <div class="bg-danger/5 border border-danger/20 rounded-lg p-4 mb-4 text-left">
                    <h5 class="font-semibold text-gray-800 mb-3">Detail Transaksi</h5>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Faktur:</span>
                            <span class="font-medium">{{ $transaksi->kode }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pelanggan:</span>
                            <span class="font-medium">{{ $transaksi->pelanggan->nama }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jumlah:</span>
                            <span class="font-bold text-danger">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium">{{ ucfirst($transaksi->status ?? 'Gagal') }}</span>
                        </div>
                    </div>
                </div>

                {{-- What to Do --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 text-left">
                    <h6 class="font-semibold text-yellow-800 mb-2">
                        <i class="fas fa-exclamation-triangle mr-1"></i>Apa yang harus dilakukan?
                    </h6>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• Periksa status pembayaran dengan pelanggan</li>
                        <li>• Coba metode pembayaran lain</li>
                        <li>• Proses pembayaran tunai jika pelanggan hadir</li>
                        <li>• Hubungi pelanggan untuk kelanjutan pembayaran</li>
                    </ul>
                </div>

                {{-- Action Buttons --}}
                <div class="space-y-3">
                    <a href="{{ route('vendor.pos.payment.options', $transaksi->id) }}"
                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors">
                        <i class="fas fa-credit-card mr-2"></i>Coba Metode Lain
                    </a>
                    <a href="{{ route('vendor.pos.invoice.show', $transaksi->id) }}"
                        class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                        <i class="fas fa-file-invoice mr-2"></i>Lihat Faktur
                    </a>
                    <a href="{{ route('vendor.pos.index') }}"
                        class="w-full inline-flex items-center justify-center px-4 py-3 border border-primary text-primary rounded-lg font-medium hover:bg-primary/5 transition-colors">
                        <i class="fas fa-store mr-2"></i>Kembali ke POS
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
