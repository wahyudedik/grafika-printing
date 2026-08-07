@extends('dev.layouts.app')

@section('title', 'Statistik Lelang')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Statistik Lelang</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ringkasan data lelang dan penawaran</p>
        </div>
        <x.ui.button type="button" variant="outline" href="{{ route('admin.auctions.index') }}">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </x.ui.button>
    </div>

    {{-- Statistics Cards - Row 1 --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Lelang</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_auctions'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Semua lelang yang pernah dibuat</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Lelang Aktif</p>
            <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['active_auctions'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sedang berlangsung</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Menunggu Persetujuan</p>
            <p class="text-3xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $stats['pending_auctions'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Belum disetujui</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Penawaran</p>
            <p class="text-3xl font-bold text-sky-600 dark:text-sky-400 mt-1">{{ $stats['total_bids'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Dari semua vendor</p>
        </div>
    </div>

    {{-- Statistics Cards - Row 2 --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Lelang Ditutup</p>
            <p class="text-3xl font-bold text-sky-600 dark:text-sky-400 mt-1">{{ $stats['closed_auctions'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sudah selesai</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Lelang Ditolak</p>
            <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $stats['rejected_auctions'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tidak disetujui</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total User</p>
            <p class="text-3xl font-bold text-primary-600 dark:text-primary-400 mt-1">{{ $stats['total_users'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">User yang membuat lelang</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Rata-rata Penawaran</p>
            <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                @if($stats['total_bids'] > 0)
                    {{ number_format($stats['total_bids'] / max($stats['total_auctions'], 1), 1) }}
                @else
                    0
                @endif
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Per lelang</p>
        </div>
    </div>

    {{-- Recent Auctions --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Lelang Terbaru</h2>
        </div>
        <div class="p-6">
            @if($recentAuctions->count() > 0)
                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Judul Lelang</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">User</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Budget</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Penawaran</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Status</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Tanggal</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($recentAuctions as $auction)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="py-3 px-4">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ Str::limit($auction->title, 30) }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $auction->category }}</p>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $auction->user->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $auction->user->email }}</p>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($auction->budget) }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($auction->quantity) }} pcs</p>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $auction->bids->count() }} penawaran</p>
                                        @if($auction->bids->count() > 0)
                                            <p class="text-xs text-emerald-600 dark:text-emerald-400">Terendah: Rp {{ number_format($auction->bids->min('bid_amount')) }}</p>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($auction->status === 'pending') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300
                                            @elseif($auction->status === 'active') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300
                                            @elseif($auction->status === 'closed') bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300
                                            @elseif($auction->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                            {{ ucfirst($auction->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="text-sm text-gray-900 dark:text-white">{{ $auction->created_at->format('d M Y') }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $auction->created_at->format('H:i') }}</p>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <x.ui.button type="button" variant="ghost" size="icon-sm" href="{{ route('admin.auctions.show', $auction) }}" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </x.ui.button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden space-y-3">
                    @foreach($recentAuctions as $auction)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ Str::limit($auction->title, 25) }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    @if($auction->status === 'pending') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300
                                    @elseif($auction->status === 'active') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300
                                    @elseif($auction->status === 'closed') bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300
                                    @elseif($auction->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                    {{ ucfirst($auction->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $auction->user->name }} • {{ $auction->created_at->format('d M Y') }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($auction->budget) }}</span>
                                <a href="{{ route('admin.auctions.show', $auction) }}" class="text-primary-600 dark:text-primary-400 text-sm font-medium">Detail <i class="fas fa-arrow-right text-xs"></i></a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-gavel text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">Belum ada lelang</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada lelang yang tersedia saat ini.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
