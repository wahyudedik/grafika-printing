@extends('dev.layouts.app')

@section('title', 'Audit Logs')
@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Financial Audit Logs</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitor semua aktivitas keuangan dan transaksi</p>
            </div>
            <a href="{{ route('admin.audit-logs.export', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-download"></i>
                <span>Export CSV</span>
            </a>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                        <i class="fas fa-file-alt text-primary-600 dark:text-primary-400"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Logs</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_logs']) }}</div>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">High Risk</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($stats['high_risk']) }}</div>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center">
                        <i class="fas fa-clock text-sky-600 dark:text-sky-400"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Today</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($stats['today_logs']) }}</div>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Financial</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($stats['financial_actions']) }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Risk Level</label>
                        <select name="risk_level" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Semua Risk</option>
                            <option value="low" {{ request('risk_level') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ request('risk_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ request('risk_level') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="critical" {{ request('risk_level') == 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Action Type</label>
                        <select name="action_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Semua Aksi</option>
                            <option value="create" {{ request('action_type') == 'create' ? 'selected' : '' }}>Create</option>
                            <option value="update" {{ request('action_type') == 'update' ? 'selected' : '' }}>Update</option>
                            <option value="approve" {{ request('action_type') == 'approve' ? 'selected' : '' }}>Approve</option>
                            <option value="reject" {{ request('action_type') == 'reject' ? 'selected' : '' }}>Reject</option>
                            <option value="withdraw" {{ request('action_type') == 'withdraw' ? 'selected' : '' }}>Withdraw</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Entity Type</label>
                        <select name="entity_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Semua Entity</option>
                            <option value="withdrawal" {{ request('entity_type') == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                            <option value="wallet" {{ request('entity_type') == 'wallet' ? 'selected' : '' }}>Wallet</option>
                            <option value="payment" {{ request('entity_type') == 'payment' ? 'selected' : '' }}>Payment</option>
                            <option value="auction" {{ request('entity_type') == 'auction' ? 'selected' : '' }}>Auction</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date From</label>
                        <input type="date" name="date_from" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" value="{{ request('date_from') }}">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                        <input type="text" name="search" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Search by reference, notes, user..." value="{{ request('search') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date To</label>
                        <input type="date" name="date_to" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" value="{{ request('date_to') }}">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors text-sm font-medium">
                            Filter
                        </button>
                        <a href="{{ route('admin.audit-logs.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm font-medium">
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Audit Logs Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Entity</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Risk</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $log->id }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                                            <span class="text-sm font-medium text-primary-700 dark:text-primary-300">{{ substr($log->user->name ?? 'A', 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $log->user->name ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $log->action_type == 'approve' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' :
                                           ($log->action_type == 'reject' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : 'bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-300') }}">
                                        {{ ucfirst($log->action_type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300">
                                        {{ ucfirst($log->entity_type) }}
                                    </span>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">ID: {{ $log->entity_id }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                    @if($log->amount)
                                        Rp {{ number_format($log->amount, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $log->risk_level == 'critical' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' :
                                           ($log->risk_level == 'high' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' :
                                           ($log->risk_level == 'medium' ? 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300')) }}">
                                        {{ ucfirst($log->risk_level) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $log->status == 'completed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' :
                                           ($log->status == 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300') }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900 dark:text-white">{{ $log->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->created_at->format('H:i:s') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.audit-logs.show', $log->id) }}" class="inline-flex items-center px-3 py-1.5 border border-primary-300 dark:border-primary-600 text-primary-700 dark:text-primary-300 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 text-xs font-medium transition-colors">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                                            <i class="fas fa-file-alt text-gray-400 text-lg"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">No audit logs found</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Try adjusting your filters or search criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($logs as $log)
                    <div class="p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-medium text-primary-700 dark:text-primary-300">{{ substr($log->user->name ?? 'A', 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $log->user->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->user->email ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $log->action_type == 'approve' ? 'bg-emerald-100 text-emerald-800' : ($log->action_type == 'reject' ? 'bg-red-100 text-red-800' : 'bg-primary-100 text-primary-800') }}">
                                {{ ucfirst($log->action_type) }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-gray-500">Entity:</span>
                                <span class="ml-1 text-gray-900 dark:text-white">{{ ucfirst($log->entity_type) }} #{{ $log->entity_id }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Amount:</span>
                                <span class="ml-1 text-gray-900 dark:text-white font-medium">{{ $log->amount ? 'Rp ' . number_format($log->amount, 0, ',', '.') : '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Risk:</span>
                                <span class="ml-1 text-gray-900 dark:text-white">{{ ucfirst($log->risk_level) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Date:</span>
                                <span class="ml-1 text-gray-900 dark:text-white">{{ $log->created_at->format('d M Y H:i') }}</span>
                            </div>
                        </div>
                        <a href="{{ route('admin.audit-logs.show', $log->id) }}" class="block w-full text-center px-3 py-1.5 border border-primary-300 text-primary-700 rounded-lg text-xs font-medium">
                            View Details
                        </a>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-file-alt text-gray-400 text-lg"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">No audit logs found</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Try adjusting your filters or search criteria.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
@endsection
