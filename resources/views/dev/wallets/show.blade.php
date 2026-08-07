@extends('dev.layouts.app')

@section('title', 'Wallet Details')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Wallet Details</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $wallet->vendor->name ?? 'N/A' }}</p>
        </div>
        <div class="flex gap-2">
            <x.ui.button type="button" variant="outline" href="{{ route('admin.wallets.index') }}">
                <i class="fas fa-arrow-left mr-1"></i> Back to Wallets
            </x.ui.button>
            <x.ui.button type="button" variant="primary" href="{{ route('admin.wallets.transactions', $wallet->id) }}">
                <i class="fas fa-receipt mr-1"></i> View Transactions
            </x.ui.button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Wallet Information --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Wallet Information</h3>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vendor</span>
                        <div class="mt-1 text-gray-900 dark:text-white font-medium">{{ $wallet->vendor->name ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</span>
                        <div class="mt-1 text-gray-900 dark:text-white font-medium">{{ $wallet->vendor->email ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</span>
                        <div class="mt-1">
                            @if ($wallet->status === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Active</span>
                            @elseif($wallet->status === 'frozen')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Frozen</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ ucfirst($wallet->status) }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</span>
                        <div class="mt-1 text-gray-900 dark:text-white font-medium">{{ $wallet->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Balance Information --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Balance Information</h3>
            </div>
            <div class="p-5 space-y-5">
                <div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Balance</span>
                    <div class="mt-1 text-2xl font-bold text-primary-600 dark:text-primary-400">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Available Balance</span>
                        <div class="mt-1 text-lg font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($wallet->available_balance, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pending Balance</span>
                        <div class="mt-1 text-lg font-bold text-amber-600 dark:text-amber-400">Rp {{ number_format($wallet->pending_balance, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Recent Transactions</h3>
        </div>
        @if ($wallet->transactions->count() > 0)
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($wallet->transactions as $transaction)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-5 py-4 text-gray-700 dark:text-gray-300">{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-5 py-4">
                                    @if ($transaction->type === 'credit')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Credit</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Debit</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400">{{ ucfirst(str_replace('_', ' ', $transaction->category)) }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-medium {{ $transaction->type === 'credit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $transaction->type === 'credit' ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @if ($transaction->status === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Completed</span>
                                    @elseif($transaction->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Pending</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ ucfirst($transaction->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $transaction->description ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($wallet->transactions as $transaction)
                    <div class="p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                            @if ($transaction->type === 'credit')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Credit</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Debit</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium {{ $transaction->type === 'credit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $transaction->type === 'credit' ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400">{{ ucfirst(str_replace('_', ' ', $transaction->category)) }}</span>
                        </div>
                        @if($transaction->description)
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $transaction->description }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <i class="fas fa-receipt text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-1">No transactions found</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">This wallet has no transactions yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection
