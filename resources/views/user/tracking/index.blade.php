@extends('layouts.user')

@section('title', 'Tracking Pesanan')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tracking Pesanan Lelang</h1>
        <p class="text-sm text-gray-500 mt-1">Lacak status pesanan dari lelang yang Anda menangkan</p>
    </div>

    @if ($auctions->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($auctions as $auction)
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    {{-- Header --}}
                    <div class="px-5 py-4 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900">{{ $auction->title }}</h3>
                            @php
                                $status = $auction->transaksi->tracking_status ?? 'menunggu';
                                $statusColors = [
                                    'menunggu' => 'bg-gray-100 text-gray-700',
                                    'diproses' => 'bg-blue-100 text-blue-700',
                                    'dicetak' => 'bg-yellow-100 text-yellow-700',
                                    'dikirim' => 'bg-primary-100 text-primary-700',
                                    'selesai' => 'bg-green-100 text-green-700',
                                ];
                                $progressPercentages = [
                                    'menunggu' => 20,
                                    'diproses' => 40,
                                    'dicetak' => 60,
                                    'dikirim' => 80,
                                    'selesai' => 100,
                                ];
                                $progressColors = [
                                    'menunggu' => 'bg-gray-400',
                                    'diproses' => 'bg-blue-500',
                                    'dicetak' => 'bg-yellow-500',
                                    'dikirim' => 'bg-primary-500',
                                    'selesai' => 'bg-green-500',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$status] }}">
                                {{ ucfirst($status) }}
                            </span>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="px-5 py-4">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-xs text-gray-500 mb-0.5">Vendor</p>
                                <p class="text-sm font-medium text-gray-900">{{ $auction->winnerVendor->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-0.5">Kode Lelang</p>
                                <p class="text-sm font-medium text-gray-900">{{ $auction->kode }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-0.5">Total Harga</p>
                                <p class="text-sm font-medium text-gray-900">Rp {{ number_format($auction->transaksi->total_harga) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-0.5">Kode Transaksi</p>
                                <p class="text-sm font-medium text-gray-900">{{ $auction->transaksi->kode }}</p>
                            </div>
                            @if ($auction->transaksi->ongkir > 0)
                            <div>
                                <p class="text-xs text-gray-500 mb-0.5">Ongkir</p>
                                <p class="text-sm font-medium text-gray-900">Rp {{ number_format($auction->transaksi->ongkir) }}</p>
                            </div>
                            @endif
                            @if ($auction->transaksi->no_resi)
                            <div>
                                <p class="text-xs text-gray-500 mb-0.5">No. Resi</p>
                                <p class="text-sm font-medium text-gray-900">{{ $auction->transaksi->no_resi }}</p>
                            </div>
                            @endif
                        </div>

                        {{-- Progress Bar --}}
                        <div class="mb-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $progressColors[$status] }} h-2 rounded-full transition-all duration-500" style="width: {{ $progressPercentages[$status] }}%"></div>
                            </div>
                            <div class="flex justify-between mt-1.5">
                                <span class="text-[10px] text-gray-400">Menunggu</span>
                                <span class="text-[10px] text-gray-400">Diproses</span>
                                <span class="text-[10px] text-gray-400">Dicetak</span>
                                <span class="text-[10px] text-gray-400">Dikirim</span>
                                <span class="text-[10px] text-gray-400">Selesai</span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2">
                            <a href="{{ route('user.orders.show', $auction) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-primary-700 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors">
                                <i class="fas fa-eye mr-1"></i> Detail Tracking
                            </a>
                            @if ($status === 'selesai')
                                <a href="{{ route('vendor.ratings.create', $auction) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-yellow-700 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                                    <i class="fas fa-star mr-1"></i> Beri Rating
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-16 text-center">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                <i class="fas fa-truck text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Belum ada pesanan untuk dilacak</h3>
            <p class="text-sm text-gray-500 mb-6 max-w-md mx-auto">
                Pesanan akan muncul di sini setelah lelang Anda dimenangkan oleh vendor.
            </p>
            <a href="{{ route('user.auctions.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-gavel mr-2"></i> Lihat Lelang
            </a>
        </div>
    @endif
@endsection
