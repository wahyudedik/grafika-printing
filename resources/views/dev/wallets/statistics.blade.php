@extends('dev.layouts.app')

@section('title', 'Wallet Statistics')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Wallet Statistics</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Comprehensive wallet analytics and insights</p>
        </div>
        <div>
            <x.ui.button type="button" variant="outline" href="{{ route('admin.wallets.index') }}">
                <i class="fas fa-arrow-left mr-1"></i> Back to Wallets
            </x.ui.button>
        </div>
    </div>

    {{-- Overview Statistics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Wallets</span>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['total_wallets'] }}</div>
            <div class="flex items-center justify-between mt-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">Active: {{ $stats['active_wallets'] }}</span>
                <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">{{ round(($stats['active_wallets'] / $stats['total_wallets']) * 100, 1) }}%</span>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Frozen Wallets</span>
            <div class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2">{{ $stats['frozen_wallets'] }}</div>
            <div class="flex items-center justify-between mt-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">Percentage</span>
                <span class="text-sm font-medium text-red-600 dark:text-red-400">{{ round(($stats['frozen_wallets'] / $stats['total_wallets']) * 100, 1) }}%</span>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Balance</span>
            <div class="text-3xl font-bold text-primary-600 dark:text-primary-400 mt-2">Rp {{ number_format($stats['total_balance'], 0, ',', '.') }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">Available: Rp {{ number_format($stats['total_available_balance'], 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Average Balance</span>
            <div class="text-3xl font-bold text-sky-600 dark:text-sky-400 mt-2">Rp {{ number_format($stats['average_balance'], 0, ',', '.') }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">Per wallet</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Balance Distribution --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Balance Distribution</h3>
            </div>
            <div class="p-5 space-y-6">
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Available Balance</div>
                    <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">Rp {{ number_format($stats['total_available_balance'], 0, ',', '.') }}</div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mt-2">
                        <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $stats['total_balance'] > 0 ? ($stats['total_available_balance'] / $stats['total_balance']) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Balance</div>
                    <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">Rp {{ number_format($stats['total_pending_balance'], 0, ',', '.') }}</div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mt-2">
                        <div class="bg-amber-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $stats['total_balance'] > 0 ? ($stats['total_pending_balance'] / $stats['total_balance']) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Wallet Status --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Wallet Status</h3>
            </div>
            <div class="p-5 space-y-6">
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Wallets</div>
                    <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['active_wallets'] }}</div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mt-2">
                        <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $stats['total_wallets'] > 0 ? ($stats['active_wallets'] / $stats['total_wallets']) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Frozen Wallets</div>
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $stats['frozen_wallets'] }}</div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mt-2">
                        <div class="bg-red-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $stats['total_wallets'] > 0 ? ($stats['frozen_wallets'] / $stats['total_wallets']) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Wallets --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Top Wallets by Balance</h3>
        </div>
        @if ($stats['top_wallets']->count() > 0)
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rank</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vendor</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Balance</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Available</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pending</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($stats['top_wallets'] as $index => $wallet)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-5 py-4">
                                    @if ($index < 3)
                                        @php
                                            $rankClass = match($index) {
                                                0 => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                                1 => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                                2 => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold {{ $rankClass }}">{{ $index + 1 }}</span>
                                    @else
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400">{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $wallet->vendor->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $wallet->vendor->email ?? 'N/A' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-medium text-primary-600 dark:text-primary-400">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-emerald-600 dark:text-emerald-400">Rp {{ number_format($wallet->available_balance, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-amber-600 dark:text-amber-400">Rp {{ number_format($wallet->pending_balance, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    @if ($wallet->status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Active</span>
                                    @elseif($wallet->status === 'frozen')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Frozen</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ ucfirst($wallet->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($stats['top_wallets'] as $index => $wallet)
                    <div class="p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                @if ($index < 3)
                                    @php
                                        $rankClass = match($index) {
                                            0 => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                            1 => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                            2 => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold {{ $rankClass }}">{{ $index + 1 }}</span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400">{{ $index + 1 }}</span>
                                @endif
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $wallet->vendor->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $wallet->vendor->email ?? 'N/A' }}</div>
                                </div>
                            </div>
                            @if ($wallet->status === 'active')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Active</span>
                            @elseif($wallet->status === 'frozen')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Frozen</span>
                            @endif
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-sm">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 text-xs">Balance:</span>
                                <div class="font-medium text-primary-600 dark:text-primary-400">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</div>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 text-xs">Available:</span>
                                <div class="font-medium text-emerald-600 dark:text-emerald-400">Rp {{ number_format($wallet->available_balance, 0, ',', '.') }}</div>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 text-xs">Pending:</span>
                                <div class="font-medium text-amber-600 dark:text-amber-400">Rp {{ number_format($wallet->pending_balance, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <i class="fas fa-wallet text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-1">No wallets found</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">No vendor wallets available for statistics.</p>
            </div>
        @endif
    </div>
</div>
@endsection
