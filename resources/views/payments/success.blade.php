@extends('layouts.app')

@section('title', 'Pembayaran Berhasil')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto text-center">
            <!-- Success Icon -->
            <div class="mb-6">
                <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>

            <!-- Success Message -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Pembayaran Berhasil!</h1>
            <p class="text-lg text-gray-600 mb-8">
                Pembayaran Anda berhasil diproses. Vendor akan diberitahu dan mulai mengerjakan pesanan Anda.
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
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                            Lunas
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal Pembayaran:</span>
                        <span class="font-medium">{{ now()->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="bg-blue-50 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-3">Apa langkah selanjutnya?</h3>
                <ul class="text-left text-blue-800 space-y-2">
                    <li class="flex items-start">
                        <span class="mr-2">1.</span>
                        <span>Vendor akan diberitahu tentang pembayaran Anda</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">2.</span>
                        <span>Vendor akan mulai mengerjakan pesanan Anda</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">3.</span>
                        <span>Anda akan menerima pembaruan progres</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">4.</span>
                        <span>Lacak pesanan Anda di dashboard</span>
                    </li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('user.auctions.show', $auction) }}"
                    class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Lihat Detail Lelang
                </a>

                <a href="{{ route('user.dashboard') }}"
                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection
