@extends('dev.layouts.app')

@section('title', 'Wallet Management')

@section('content')
<div class="space-y-6" x-data="{ showFreezeModal: false, showUnfreezeModal: false, freezeWalletId: null, unfreezeWalletId: null, freezeReason: '', unfreezeReason: '' }">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Wallet Management</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage vendor wallets and transactions</p>
        </div>
        <div class="hidden sm:block">
            <x.ui.button href="{{ route('admin.wallets.statistics') }}" variant="outline-primary" size="sm">
                <i class="fas fa-chart-pie text-xs"></i>
                Statistics
            </x.ui.button>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Wallets</span>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['total_wallets'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Wallets</span>
            <div class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-2">{{ $stats['active_wallets'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Frozen Wallets</span>
            <div class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2">{{ $stats['frozen_wallets'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Balance</span>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-2">Rp {{ number_format($stats['total_balance'], 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
        <form method="GET" action="{{ route('admin.wallets.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vendor</label>
                    <select name="vendor_id" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                        <option value="">All Vendors</option>
                        @foreach ($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="frozen" {{ request('status') == 'frozen' ? 'selected' : '' }}>Frozen</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <input type="text" name="search" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500" placeholder="Search by vendor name or email..." value="{{ request('search') }}">
                </div>
                <div class="flex items-end gap-2">
                    <x.ui.button type="submit" variant="primary" size="sm">
                        <i class="fas fa-filter text-xs"></i>
                        Filter
                    </x.ui.button>
                    <x.ui.button href="{{ route('admin.wallets.index') }}" variant="secondary" size="sm">Reset</x.ui.button>
                </div>
            </div>
        </form>
    </div>

    {{-- Wallets Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Vendor Wallets</h3>
        </div>
        @if ($wallets->count() > 0)
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vendor</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Balance</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Available</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pending</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Transaction</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($wallets as $wallet)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $wallet->vendor->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $wallet->vendor->email ?? 'N/A' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</div>
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
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if ($wallet->transactions_count > 0)
                                        {{ $wallet->transactions->first()->created_at->diffForHumans() }}
                                    @else
                                        No transactions
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        <x.ui.button href="{{ route('admin.wallets.show', $wallet->id) }}" variant="outline-primary" size="xs">
                                            <i class="fas fa-eye text-xs"></i> View
                                        </x.ui.button>
                                        <x.ui.button href="{{ route('admin.wallets.transactions', $wallet->id) }}" variant="info" size="xs">
                                            <i class="fas fa-list text-xs"></i> Transactions
                                        </x.ui.button>
                                        @if ($wallet->status === 'active')
                                            <x.ui.button type="button" variant="outline-danger" size="xs" @click="freezeWalletId = {{ $wallet->id }}; freezeReason = ''; showFreezeModal = true">
                                                <i class="fas fa-snowflake text-xs"></i> Freeze
                                            </x.ui.button>
                                        @elseif($wallet->status === 'frozen')
                                            <x.ui.button type="button" variant="outline-success" size="xs" @click="unfreezeWalletId = {{ $wallet->id }}; unfreezeReason = ''; showUnfreezeModal = true">
                                                <i class="fas fa-sun text-xs"></i> Unfreeze
                                            </x.ui.button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($wallets as $wallet)
                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $wallet->vendor->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $wallet->vendor->email ?? 'N/A' }}</div>
                            </div>
                            @if ($wallet->status === 'active')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Active</span>
                            @elseif($wallet->status === 'frozen')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Frozen</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ ucfirst($wallet->status) }}</span>
                            @endif
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-sm">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 text-xs">Balance:</span>
                                <div class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</div>
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
                        <div class="flex gap-2">
                            <x.ui.button href="{{ route('admin.wallets.show', $wallet->id) }}" variant="outline-primary" size="xs">
                                <i class="fas fa-eye text-xs"></i> View
                            </x.ui.button>
                            <x.ui.button href="{{ route('admin.wallets.transactions', $wallet->id) }}" variant="info" size="xs">
                                <i class="fas fa-list text-xs"></i> Transactions
                            </x.ui.button>
                            @if ($wallet->status === 'active')
                                <x.ui.button type="button" variant="outline-danger" size="xs" @click="freezeWalletId = {{ $wallet->id }}; freezeReason = ''; showFreezeModal = true">
                                    <i class="fas fa-snowflake text-xs"></i> Freeze
                                </x.ui.button>
                            @elseif($wallet->status === 'frozen')
                                <x.ui.button type="button" variant="outline-success" size="xs" @click="unfreezeWalletId = {{ $wallet->id }}; unfreezeReason = ''; showUnfreezeModal = true">
                                    <i class="fas fa-sun text-xs"></i> Unfreeze
                                </x.ui.button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-center">
                {{ $wallets->links() }}
            </div>
        @else
            <x-ui.empty-state icon="fas fa-wallet" title="No wallets found" description="No vendor wallets match your current filters." size="lg" />
        @endif
    </div>

    {{-- Freeze Wallet Modal (Alpine.js) --}}
    <div x-show="showFreezeModal" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-end sm:items-center justify-center p-4">
            <div x-show="showFreezeModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/75" @click="showFreezeModal = false"></div>
            <div x-show="showFreezeModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative w-full max-w-lg transform overflow-hidden rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xl transition-all">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Freeze Wallet</h3>
                        <button type="button" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300" @click="showFreezeModal = false">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>
                <form :action="'{{ url('/admin/wallets') }}/' + freezeWalletId + '/freeze'" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason for freezing</label>
                            <textarea x-model="freezeReason" name="reason" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500" rows="3" placeholder="Enter reason for freezing this wallet..." required></textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                        <x.ui.button type="button" variant="secondary" size="sm" @click="showFreezeModal = false">Cancel</x.ui.button>
                        <x.ui.button type="submit" variant="danger" size="sm">Freeze Wallet</x.ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Unfreeze Wallet Modal (Alpine.js) --}}
    <div x-show="showUnfreezeModal" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-end sm:items-center justify-center p-4">
            <div x-show="showUnfreezeModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/75" @click="showUnfreezeModal = false"></div>
            <div x-show="showUnfreezeModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative w-full max-w-lg transform overflow-hidden rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xl transition-all">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Unfreeze Wallet</h3>
                        <button type="button" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300" @click="showUnfreezeModal = false">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>
                <form :action="'{{ url('/admin/wallets') }}/' + unfreezeWalletId + '/unfreeze'" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason for unfreezing</label>
                            <textarea x-model="unfreezeReason" name="reason" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500" rows="3" placeholder="Enter reason for unfreezing this wallet..." required></textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                        <x.ui.button type="button" variant="secondary" size="sm" @click="showUnfreezeModal = false">Cancel</x.ui.button>
                        <x.ui.button type="submit" variant="success" size="sm">Unfreeze Wallet</x.ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
