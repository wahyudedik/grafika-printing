@extends('layouts.app')

@section('title', 'Pembayaran Gagal')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto text-center">
            <!-- Failure Icon -->
            <div class="mb-6">
                <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>

            <!-- Failure Message -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Pembayaran Gagal</h1>
            <p class="text-lg text-gray-600 mb-8">
                Maaf, pembayaran Anda tidak dapat diproses. Silakan coba lagi atau hubungi dukungan jika masalah
                berlanjut.
            </p>

            <!-- Auction Details -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6 text-left">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Detail Pesanan</h2>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Judul Lelang:</span>
                        <span class="font-medium">{{ $auction->title }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                            Pembayaran Gagal
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal Percobaan:</span>
                        <span class="font-medium">{{ now()->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Common Issues -->
            <div class="bg-yellow-50 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-yellow-900 mb-3">Masalah Pembayaran Umum</h3>
                <ul class="text-left text-yellow-800 space-y-2">
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        <span>Dana tidak cukup di akun Anda</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        <span>Detail pembayaran salah</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        <span>Masalah koneksi jaringan</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        <span>Metode pembayaran tidak didukung</span>
                    </li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('user.payments.confirmation', $auction) }}"
                    class="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                    Coba Bayar Lagi
                </a>

                <a href="{{ route('user.auctions.show', $auction) }}"
                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                    Kembali ke Lelang
                </a>

                <a href="{{ route('user.dashboard') }}"
                    class="px-6 py-3 border border-blue-300 text-blue-700 rounded-md hover:bg-blue-50 transition-colors">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection
