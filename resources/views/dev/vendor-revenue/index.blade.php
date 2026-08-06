@extends('dev.layouts.app')

@section('title', 'Data Pendapatan Vendor')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Data Pendapatan Vendor</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitoring pendapatan dan penarikan dana vendor dari lelang</p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Vendor</span>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $summary['total_vendors'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">Vendor aktif: {{ $summary['total_vendors'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pendapatan</span>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-2">Rp {{ number_format($summary['total_earnings'], 0, ',', '.') }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">Dari {{ $summary['total_auctions_won'] }} lelang</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Ditarik</span>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-2">Rp {{ number_format($summary['total_withdrawn'], 0, ',', '.') }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">Penarikan berhasil</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Penarikan</span>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-2">Rp {{ number_format($summary['total_pending_withdrawal'], 0, ',', '.') }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">Menunggu persetujuan</div>
        </div>
    </div>

    {{-- Vendor Revenue Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Data Pendapatan Vendor</h3>
            <button onclick="location.reload()" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium text-sm transition-colors">
                <i class="fas fa-sync-alt text-xs"></i>
                Refresh Data
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vendor</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Pendapatan</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Saldo Saat Ini</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Ditarik</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pending Penarikan</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lelang Menang</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Terakhir Penarikan</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($vendors as $vendor)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $vendor['logo'] ?? '/demo/avatars/1.jpg' }}" alt="{{ $vendor['name'] }}" class="w-8 h-8 rounded-full object-cover">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $vendor['name'] }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $vendor['email'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($vendor['total_earnings'], 0, ',', '.') }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $vendor['wallet_transactions_count'] }} transaksi</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-emerald-600 dark:text-emerald-400">Rp {{ number_format($vendor['current_balance'], 0, ',', '.') }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($vendor['total_withdrawn'], 0, ',', '.') }}</div>
                            </td>
                            <td class="px-5 py-4">
                                @if ($vendor['pending_withdrawal'] > 0)
                                    <div class="font-medium text-amber-600 dark:text-amber-400">Rp {{ number_format($vendor['pending_withdrawal'], 0, ',', '.') }}</div>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $vendor['total_auctions_won'] }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Rp {{ number_format($vendor['total_auction_earnings'], 0, ',', '.') }}</div>
                            </td>
                            <td class="px-5 py-4">
                                @if ($vendor['last_withdrawal'])
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $vendor['last_withdrawal']->created_at->diffForHumans() }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Rp {{ number_format($vendor['last_withdrawal']->amount, 0, ',', '.') }}</div>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">Belum pernah</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.analytics.vendor-revenue.show', $vendor['id']) }}" class="inline-flex items-center gap-2 px-3 py-1.5 border border-primary-300 dark:border-primary-700 text-primary-700 dark:text-primary-400 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 font-medium text-xs transition-colors">
                                    <i class="fas fa-eye text-xs"></i>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-users text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-1">Tidak ada data vendor</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada vendor yang terdaftar dalam sistem.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        // Auto refresh every 30 seconds
        setInterval(function() {
            // You can implement AJAX refresh here if needed
        }, 30000);
    </script>
@endpush
