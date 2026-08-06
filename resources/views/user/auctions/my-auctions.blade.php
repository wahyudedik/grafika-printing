@extends('layouts.user')

@section('title', 'Lelang Saya')

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Lelang Saya</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola permintaan cetak yang telah Anda buat</p>
        </div>
        <a href="{{ route('user.auctions.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
            <i class="fas fa-plus mr-2"></i> Buat Permintaan Baru
        </a>
    </div>

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="mb-6 bg-green-50 border border-green-200 rounded-lg px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-green-600"></i>
                <span class="text-sm text-green-800">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if ($auctions->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($auctions as $auction)
                @php
                    $statusConfig = [
                        'pending' => ['label' => 'Menunggu Verifikasi', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'dot' => 'bg-yellow-500'],
                        'approved' => ['label' => 'Disetujui', 'bg' => 'bg-green-100', 'text' => 'text-green-700', 'dot' => 'bg-green-500'],
                        'rejected' => ['label' => 'Ditolak', 'bg' => 'bg-red-100', 'text' => 'text-red-700', 'dot' => 'bg-red-500'],
                        'active' => ['label' => 'Berlangsung', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'dot' => 'bg-blue-500'],
                        'bidding' => ['label' => 'Berlangsung', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'dot' => 'bg-blue-500'],
                        'closed' => ['label' => 'Selesai', 'bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'dot' => 'bg-gray-500'],
                        'won' => ['label' => 'Dimenangkan', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'dot' => 'bg-purple-500'],
                        'paid' => ['label' => 'Dibayar', 'bg' => 'bg-green-100', 'text' => 'text-green-700', 'dot' => 'bg-green-500'],
                        'completed' => ['label' => 'Selesai', 'bg' => 'bg-green-100', 'text' => 'text-green-700', 'dot' => 'bg-green-500'],
                    ];
                    $sc = $statusConfig[$auction->status] ?? ['label' => ucfirst($auction->status), 'bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'dot' => 'bg-gray-500'];
                @endphp
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow flex flex-col">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-primary-100 text-primary-700">
                                {{ $auction->category }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sc['bg'] }} {{ $sc['text'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }} mr-1.5"></span>
                                {{ $sc['label'] }}
                            </span>
                        </div>
                    </div>
                    <div class="px-5 py-4 flex-1 flex flex-col">
                        <h3 class="font-semibold text-gray-900 mb-1">{{ $auction->title }}</h3>
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ Str::limit($auction->description, 100) }}</p>

                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="flex items-center gap-1.5 text-sm text-gray-600">
                                <i class="fas fa-box text-gray-400 text-xs"></i>
                                {{ number_format($auction->quantity) }} pcs
                            </div>
                            <div class="flex items-center gap-1.5 text-sm text-gray-600">
                                <i class="fas fa-tag text-gray-400 text-xs"></i>
                                Rp {{ number_format($auction->budget) }}
                            </div>
                            <div class="flex items-center gap-1.5 text-sm text-gray-600">
                                <i class="fas fa-calendar text-gray-400 text-xs"></i>
                                {{ $auction->deadline->format('d M Y') }}
                            </div>
                            <div class="flex items-center gap-1.5 text-sm text-gray-600">
                                <i class="fas fa-gavel text-gray-400 text-xs"></i>
                                {{ $auction->getBidCount() }} penawaran
                            </div>
                        </div>

                        {{-- Status Alerts --}}
                        @if ($auction->status === 'pending')
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-3 py-2 mb-4">
                                <p class="text-xs font-medium text-yellow-800">⏳ Menunggu Verifikasi</p>
                                <p class="text-xs text-yellow-700 mt-0.5">Lelang sedang dalam proses verifikasi oleh admin.</p>
                            </div>
                        @elseif ($auction->status === 'rejected')
                            <div class="bg-red-50 border border-red-200 rounded-lg px-3 py-2 mb-4">
                                <p class="text-xs font-medium text-red-800">❌ Lelang Ditolak</p>
                                @if ($auction->rejection_reason)
                                    <p class="text-xs text-red-700 mt-0.5">Alasan: {{ $auction->rejection_reason }}</p>
                                @endif
                            </div>
                        @elseif ($auction->status === 'closed' && $auction->winnerVendor)
                            <div class="bg-green-50 border border-green-200 rounded-lg px-3 py-2 mb-4">
                                <p class="text-xs font-medium text-green-800">🏆 Pemenang: {{ $auction->winnerVendor->name }}</p>
                                <p class="text-xs text-green-700 mt-0.5">Harga: Rp {{ number_format($auction->winning_bid) }}</p>
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 mt-auto pt-2">
                            <a href="{{ route('user.auctions.show', $auction) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2 text-xs font-medium text-primary-700 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors">
                                <i class="fas fa-eye mr-1"></i> Lihat Detail
                            </a>
                            @if(in_array($auction->status, ['pending', 'approved', 'active', 'bidding']))
                                <a href="{{ route('user.auctions.edit', $auction) }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-edit"></i>
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
                Anda belum membuat permintaan cetak. Buat permintaan pertama Anda sekarang!
            </p>
            <a href="{{ route('user.auctions.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-plus mr-2"></i> Buat Permintaan Pertama
            </a>
        </div>
    @endif
@endsection
