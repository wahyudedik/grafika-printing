@extends('layouts.vendor')

@section('title', 'Riwayat Transaksi')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Riwayat Transaksi</h3>
            <a href="{{ route('vendor.audit-logs.export', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3v4a1 1 0 001 1h4M17 21H7a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2zM9 9l1 1 3-3"/></svg>
                Export CSV
            </a>
        </div>
        <div class="p-5">
            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Total Transaksi</div>
                            <div class="text-sm text-gray-500">{{ number_format($stats['total_logs']) }}</div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Hari Ini</div>
                            <div class="text-sm text-gray-500">{{ number_format($stats['today_logs']) }}</div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h3m-3 3h3m-3 3h3M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Finansial</div>
                            <div class="text-sm text-gray-500">{{ number_format($stats['financial_actions']) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <form method="GET" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Action Type</label>
                        <select name="action_type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Semua Aksi</option>
                            <option value="create" {{ request('action_type') == 'create' ? 'selected' : '' }}>Create</option>
                            <option value="update" {{ request('action_type') == 'update' ? 'selected' : '' }}>Update</option>
                            <option value="approve" {{ request('action_type') == 'approve' ? 'selected' : '' }}>Approve</option>
                            <option value="reject" {{ request('action_type') == 'reject' ? 'selected' : '' }}>Reject</option>
                            <option value="withdraw" {{ request('action_type') == 'withdraw' ? 'selected' : '' }}>Withdraw</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Entity Type</label>
                        <select name="entity_type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Semua Entity</option>
                            <option value="withdrawal" {{ request('entity_type') == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                            <option value="wallet" {{ request('entity_type') == 'wallet' ? 'selected' : '' }}>Wallet</option>
                            <option value="payment" {{ request('entity_type') == 'payment' ? 'selected' : '' }}>Payment</option>
                            <option value="auction" {{ request('entity_type') == 'auction' ? 'selected' : '' }}>Auction</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                        <input type="date" name="date_from" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" value="{{ request('date_from') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                        <input type="date" name="date_to" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mt-4">
                    <div class="md:col-span-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                        <input type="text" name="search" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Cari berdasarkan referensi, catatan..." value="{{ request('search') }}">
                    </div>
                    <div class="md:col-span-2 flex items-end gap-2">
                        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium text-sm">Filter</button>
                        <a href="{{ route('vendor.audit-logs.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm">Reset</a>
                    </div>
                </div>
            </form>

            {{-- Transaction Logs Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">ID</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Aksi</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Entity</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Jumlah</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Status</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Tanggal</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4">{{ $log->id }}</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $log->action_type == 'approve' ? 'bg-green-100 text-green-800' : ($log->action_type == 'reject' ? 'bg-red-100 text-red-800' : 'bg-primary-100 text-primary-800') }}">
                                        {{ ucfirst($log->action_type) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($log->entity_type) }}</span>
                                    <div class="text-xs text-gray-500 mt-1">ID: {{ $log->entity_id }}</div>
                                </td>
                                <td class="py-3 px-4 font-medium">
                                    @if($log->amount)
                                        Rp {{ number_format($log->amount, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $log->status == 'completed' ? 'bg-green-100 text-green-800' : ($log->status == 'failed' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div>{{ $log->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <a href="{{ route('vendor.audit-logs.show', $log->id) }}" class="text-primary-600 hover:text-primary-700 font-medium text-sm">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <p class="text-sm font-medium text-gray-900">Tidak ada log ditemukan</p>
                                    <p class="text-xs text-gray-500 mt-1">Coba ubah filter atau kriteria pencarian Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4 flex justify-center">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
