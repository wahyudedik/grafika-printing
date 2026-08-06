@extends('dev.layouts.app')

@section('title', 'Wallet Transactions')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Wallet Transactions</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $wallet->vendor->name ?? 'N/A' }}</p>
        </div>
        <div>
            <a href="{{ route('admin.wallets.show', $wallet->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium text-sm transition-colors">
                <i class="fas fa-arrow-right text-xs"></i>
                Back to Wallet
            </a>
        </div>
    </div>

    {{-- Wallet Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Balance</span>
            <div class="text-3xl font-bold text-primary-600 dark:text-primary-400 mt-2">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Available Balance</span>
            <div class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-2">Rp {{ number_format($wallet->available_balance, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Balance</span>
            <div class="text-3xl font-bold text-amber-600 dark:text-amber-400 mt-2">Rp {{ number_format($wallet->pending_balance, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
        <form method="GET" action="{{ route('admin.wallets.transactions', $wallet->id) }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                    <select name="category" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                        <option value="">All Categories</option>
                        <option value="auction_payment" {{ request('category') == 'auction_payment' ? 'selected' : '' }}>Auction Payment</option>
                        <option value="withdrawal" {{ request('category') == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                        <option value="refund" {{ request('category') == 'refund' ? 'selected' : '' }}>Refund</option>
                        <option value="admin_fee" {{ request('category') == 'admin_fee' ? 'selected' : '' }}>Admin Fee</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                        <option value="">All Status</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date From</label>
                    <input type="date" name="date_from" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500" value="{{ request('date_from') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date To</label>
                    <input type="date" name="date_to" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500" value="{{ request('date_to') }}">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium text-sm transition-colors">
                        <i class="fas fa-filter text-xs"></i>
                        Filter
                    </button>
                    <a href="{{ route('admin.wallets.transactions', $wallet->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 font-medium text-sm transition-colors">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Transactions Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Transaction History</h3>
        </div>
        @if ($transactions->count() > 0)
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
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reference</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($transactions as $transaction)
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
                                    @elseif($transaction->status === 'failed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Failed</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ ucfirst($transaction->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $transaction->description ?? 'N/A' }}</td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $transaction->transaction_code ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($transactions as $transaction)
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
                        <div class="flex items-center justify-between text-xs">
                            @if ($transaction->status === 'completed')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Completed</span>
                            @elseif($transaction->status === 'pending')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Pending</span>
                            @elseif($transaction->status === 'failed')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Failed</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ ucfirst($transaction->status) }}</span>
                            @endif
                            @if($transaction->transaction_code)
                                <span class="text-gray-500 dark:text-gray-400">{{ $transaction->transaction_code }}</span>
                            @endif
                        </div>
                        @if($transaction->description)
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $transaction->description }}</div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-center">
                {{ $transactions->links() }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <i class="fas fa-receipt text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No transactions found</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">No transactions match your current filters.</p>
            </div>
        @endif
    </div>
</div>
@endsection
