@extends('layouts.vendor')

@section('title', 'Pembayaran Berhasil - ' . $transaksi->kode)

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-sm border border-success/20 overflow-hidden">
            {{-- Header --}}
            <div class="bg-success px-6 py-4 text-center">
                <h3 class="text-lg font-semibold text-white mb-0">
                    <i class="fas fa-check-circle mr-2"></i>Pembayaran Berhasil
                </h3>
            </div>

            {{-- Body --}}
            <div class="p-6 text-center">
                {{-- Success Icon --}}
                <div class="mb-4">
                    <div class="w-16 h-16 mx-auto bg-success/10 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-success text-4xl"></i>
                    </div>
                </div>

                <h4 class="text-lg font-semibold text-success mb-4">Pembayaran Berhasil Diselesaikan!</h4>

                {{-- Transaction Details --}}
                <div class="bg-success/5 border border-success/20 rounded-lg p-4 mb-4 text-left">
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
                            <span class="font-bold text-success">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Metode Pembayaran:</span>
                            <span class="font-medium">{{ ucfirst($transaksi->payment_method ?? 'Online') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium">{{ ucfirst($transaksi->status ?? 'Selesai') }}</span>
                        </div>
                        @if ($transaksi->xendit_payment_id)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Payment ID:</span>
                                <span class="font-mono text-xs">{{ $transaksi->xendit_payment_id }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Next Steps --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-left">
                    <h6 class="font-semibold text-blue-800 mb-2">
                        <i class="fas fa-info-circle mr-1"></i>Langkah Selanjutnya
                    </h6>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Cetak struk untuk pelanggan</li>
                        <li>• Proses pemenuhan pesanan</li>
                        <li>• Perbarui inventaris jika diperlukan</li>
                        <li>• Kirim konfirmasi ke pelanggan</li>
                    </ul>
                </div>

                {{-- Action Buttons --}}
                <div class="space-y-3">
                    <a href="{{ route('vendor.pos.invoice.print', $transaksi->id) }}"
                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-success text-white rounded-lg font-medium hover:bg-success/90 transition-colors">
                        <i class="fas fa-print mr-2"></i>Cetak Struk
                    </a>
                    <a href="{{ route('vendor.pos.invoice.show', $transaksi->id) }}"
                        class="w-full inline-flex items-center justify-center px-4 py-3 border border-primary text-primary rounded-lg font-medium hover:bg-primary/5 transition-colors">
                        <i class="fas fa-file-invoice mr-2"></i>Lihat Faktur
                    </a>
                    <a href="{{ route('vendor.pos.index') }}"
                        class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                        <i class="fas fa-store mr-2"></i>Transaksi Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
