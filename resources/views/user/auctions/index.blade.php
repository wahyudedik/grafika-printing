@extends('layouts.user')

@section('title', 'Lelang Saya')

@section('breadcrumbs')
    <x-breadcrumbs :items="[
        ['label' => 'Dasbor', 'url' => route('user.dashboard')],
        ['label' => 'Lelang Saya'],
    ]" />
@endsection

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Lelang Saya</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar semua lelang yang telah Anda buat</p>
        </div>
        <a href="{{ route('user.auctions.create') }}" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i> Buat Lelang Baru
        </a>
    </div>

    @if ($auctions->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($auctions as $auction)
                @php
                    $statusConfig = [
                        'pending' => ['label' => 'Menunggu Persetujuan', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'dot' => 'bg-yellow-500'],
                        'approved' => ['label' => 'Disetujui', 'bg' => 'bg-green-100', 'text' => 'text-green-700', 'dot' => 'bg-green-500'],
                        'rejected' => ['label' => 'Ditolak', 'bg' => 'bg-red-100', 'text' => 'text-red-700', 'dot' => 'bg-red-500'],
                        'bidding' => ['label' => 'Lelang Berlangsung', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'dot' => 'bg-blue-500'],
                        'closed' => ['label' => 'Selesai', 'bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'dot' => 'bg-gray-500'],
                        'won' => ['label' => 'Dimenangkan', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'dot' => 'bg-purple-500'],
                        'paid' => ['label' => 'Dibayar', 'bg' => 'bg-green-100', 'text' => 'text-green-700', 'dot' => 'bg-green-500'],
                        'completed' => ['label' => 'Selesai', 'bg' => 'bg-green-100', 'text' => 'text-green-700', 'dot' => 'bg-green-500'],
                    ];
                    $sc = $statusConfig[$auction->status] ?? ['label' => ucfirst($auction->status), 'bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'dot' => 'bg-gray-500'];
                @endphp
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900 truncate pr-2">{{ $auction->title }}</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sc['bg'] }} {{ $sc['text'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }} mr-1.5"></span>
                                {{ $sc['label'] }}
                            </span>
                        </div>
                    </div>
                    <div class="px-5 py-4">
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Harga Maksimum</span>
                                <span class="font-medium text-gray-900">Rp {{ number_format($auction->max_price) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Jumlah Bid</span>
                                <span class="font-medium text-gray-900">{{ $auction->bids_count ?? $auction->bids->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Batas Waktu</span>
                                <span class="font-medium text-gray-900">{{ $auction->deadline->format('d M Y H:i') }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('user.auctions.show', $auction) }}" class="inline-flex items-center justify-center border border-cyan-300 text-cyan-700 hover:bg-cyan-50 font-semibold text-sm py-1 px-3 rounded-lg transition flex-1">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </a>
                            @if(in_array($auction->status, ['pending', 'approved', 'bidding']))
                                <a href="{{ route('user.auctions.edit', $auction) }}" class="inline-flex items-center justify-center border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold text-sm py-1 px-3 rounded-lg transition flex-1">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($auctions->hasPages())
            <div class="mt-6">
                {{ $auctions->links('user.components.pagination') }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-16 text-center">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                <i class="fas fa-gavel text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Belum ada lelang</h3>
            <p class="text-sm text-gray-500 mb-6 max-w-md mx-auto">
                Mulai membuat lelang pertama Anda untuk mendapatkan penawaran terbaik dari vendor percetakan.
            </p>
            <a href="{{ route('user.auctions.create') }}" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i> Buat Lelang Baru
            </a>
        </div>
    @endif
@endsection
