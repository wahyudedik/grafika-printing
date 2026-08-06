@extends('dev.layouts.app')

@section('title', 'Detail Pendapatan Vendor')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Data Pendapatan Vendor</p>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $vendor->name }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $vendor->email }} • {{ $vendor->phone }}</p>
        </div>
        <div>
            <a href="{{ route('admin.analytics.vendor-revenue') }}" class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium text-sm transition-colors">
                <i class="fas fa-arrow-right text-xs"></i>
                Kembali
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pendapatan</span>
            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">Rp {{ number_format($vendor->wallet ? $vendor->wallet->total_earnings : 0, 0, ',', '.') }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">Dari {{ $vendor->auctionBids()->where('status', 'accepted')->count() }} lelang</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Saldo Saat Ini</span>
            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-2">Rp {{ number_format($vendor->wallet ? $vendor->wallet->current_balance : 0, 0, ',', '.') }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">Tersedia untuk ditarik</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Ditarik</span>
            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">Rp {{ number_format($vendor->wallet ? $vendor->wallet->total_withdrawn : 0, 0, ',', '.') }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">Penarikan berhasil</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Penarikan</span>
            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-2">Rp {{ number_format($vendor->withdrawals()->where('status', 'pending')->sum('amount'), 0, ',', '.') }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">Menunggu persetujuan</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Transactions --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Transaksi Terbaru</h3>
            </div>
            <div class="p-5">
                @if ($recentTransactions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="text-left py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                                    <th class="text-left py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jenis</th>
                                    <th class="text-left py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jumlah</th>
                                    <th class="text-left py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($recentTransactions as $transaction)
                                    <tr>
                                        <td class="py-3 text-gray-700 dark:text-gray-300">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                        <td class="py-3">
                                            @if($transaction->type === 'credit')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Masuk</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Keluar</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-gray-700 dark:text-gray-300">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                        <td class="py-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Berhasil</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <i class="fas fa-receipt text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-1">Belum ada transaksi</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Transaksi akan muncul setelah ada pembayaran dari lelang.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Recent Withdrawals --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Penarikan Terbaru</h3>
            </div>
            <div class="p-5">
                @if ($recentWithdrawals->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="text-left py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                                    <th class="text-left py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jumlah</th>
                                    <th class="text-left py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="text-left py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Metode</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($recentWithdrawals as $withdrawal)
                                    @php
                                        $wStatusClass = match($withdrawal->status) {
                                            'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                            'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                            default => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="py-3 text-gray-700 dark:text-gray-300">{{ $withdrawal->created_at->format('d M Y H:i') }}</td>
                                        <td class="py-3 text-gray-700 dark:text-gray-300">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</td>
                                        <td class="py-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $wStatusClass }}">{{ ucfirst($withdrawal->status) }}</span>
                                        </td>
                                        <td class="py-3 text-gray-700 dark:text-gray-300">{{ $withdrawal->payment_method }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <i class="fas fa-wallet text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-1">Belum ada penarikan</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Penarikan akan muncul setelah vendor melakukan withdraw.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent Auction Wins --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Lelang yang Dimenangkan</h3>
        </div>
        <div class="p-5">
            @if ($recentAuctionWins->count() > 0)
                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                                <th class="text-left py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Judul Lelang</th>
                                <th class="text-left py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Harga Penawaran</th>
                                <th class="text-left py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($recentAuctionWins as $bid)
                                @php
                                    $aStatusClass = match($bid->auction->status) {
                                        'paid' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                        'waiting_payment' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                        default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                    };
                                    $aStatusText = match($bid->auction->status) {
                                        'paid' => 'Terbayar',
                                        'waiting_payment' => 'Menunggu Pembayaran',
                                        default => 'Belum Dibayar',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="py-3 text-gray-700 dark:text-gray-300">{{ $bid->created_at->format('d M Y H:i') }}</td>
                                    <td class="py-3">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $bid->auction->title }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $bid->auction->category }}</div>
                                    </td>
                                    <td class="py-3 text-gray-700 dark:text-gray-300">Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}</td>
                                    <td class="py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $aStatusClass }}">{{ $aStatusText }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden space-y-3">
                    @foreach ($recentAuctionWins as $bid)
                        @php
                            $aStatusClass = match($bid->auction->status) {
                                'paid' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                'waiting_payment' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                            };
                            $aStatusText = match($bid->auction->status) {
                                'paid' => 'Terbayar',
                                'waiting_payment' => 'Menunggu Pembayaran',
                                default => 'Belum Dibayar',
                            };
                        @endphp
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-3 space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $bid->auction->title }}</div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $aStatusClass }}">{{ $aStatusText }}</span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $bid->created_at->format('d M Y H:i') }} • {{ $bid->auction->category }}</div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <i class="fas fa-trophy text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-1">Belum ada lelang yang dimenangkan</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Lelang yang dimenangkan akan muncul di sini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
