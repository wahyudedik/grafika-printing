@extends('layouts.vendor')

@section('title', 'Financial Audit Logs')

@section('content')
<x-ui.breadcrumb :items="[['label' => 'Riwayat Transaksi', 'url' => route('vendor.audit-logs.index')], ['label' => 'Financial Audit Logs']]" />

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Financial Audit Logs</h1>
        <p class="text-sm text-gray-500 mt-1">Riwayat transaksi keuangan vendor Anda</p>
    </div>
    <div class="flex items-center gap-2">
        <x.ui.button href="{{ route('vendor.audit-logs.index') }}" variant="outline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Audit Logs
        </x.ui.button>
        <x.ui.button href="{{ route('vendor.audit-logs.export', ['action_type' => request('action_type'), 'entity_type' => request('entity_type'), 'date_from' => request('date_from'), 'date_to' => request('date_to')]) }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export CSV
        </x.ui.button>
    </div>
</div>

{{-- Filter Form --}}
<div class="bg-white rounded-xl border border-gray-200 mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            Filter
        </h3>
    </div>
    <div class="p-6">
        <form method="GET" action="{{ route('vendor.audit-logs.financial') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Action Type</label>
                    <select name="action_type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Semua Action</option>
                        <option value="payment" {{ request('action_type') == 'payment' ? 'selected' : '' }}>Payment</option>
                        <option value="withdrawal" {{ request('action_type') == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                        <option value="transfer" {{ request('action_type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="refund" {{ request('action_type') == 'refund' ? 'selected' : '' }}>Refund</option>
                        <option value="admin_fee" {{ request('action_type') == 'admin_fee' ? 'selected' : '' }}>Admin Fee</option>
                        <option value="escrow_release" {{ request('action_type') == 'escrow_release' ? 'selected' : '' }}>Escrow Release</option>
                        <option value="wallet_credit" {{ request('action_type') == 'wallet_credit' ? 'selected' : '' }}>Wallet Credit</option>
                        <option value="wallet_debit" {{ request('action_type') == 'wallet_debit' ? 'selected' : '' }}>Wallet Debit</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Entity Type</label>
                    <select name="entity_type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Semua Entity</option>
                        <option value="order" {{ request('entity_type') == 'order' ? 'selected' : '' }}>Order</option>
                        <option value="auction" {{ request('entity_type') == 'auction' ? 'selected' : '' }}>Auction</option>
                        <option value="withdrawal" {{ request('entity_type') == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                        <option value="wallet" {{ request('entity_type') == 'wallet' ? 'selected' : '' }}>Wallet</option>
                        <option value="escrow" {{ request('entity_type') == 'escrow' ? 'selected' : '' }}>Escrow</option>
                        <option value="admin_fee" {{ request('entity_type') == 'admin_fee' ? 'selected' : '' }}>Admin Fee</option>
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Ref / Catatan..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="flex items-center gap-2 mt-4">
                <x.ui.button type="submit" variant="primary" size="sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Filter
                </x.ui.button>
                <x.ui.button href="{{ route('vendor.audit-logs.financial') }}" variant="outline" size="sm">Reset</x.ui.button>
            </div>
        </form>
    </div>
</div>

{{-- Logs Table --}}
<div class="bg-white rounded-xl border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Financial Logs ({{ $logs->total() }} total)
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Entity</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Risk</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-500">#{{ $log->id }}</td>
                    <td class="px-6 py-4">
                        <div class="text-gray-900">{{ $log->created_at->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $actionClasses = [
                                'payment' => 'bg-green-100 text-green-800',
                                'withdrawal' => 'bg-yellow-100 text-yellow-800',
                                'transfer' => 'bg-blue-100 text-blue-800',
                                'refund' => 'bg-red-100 text-red-800',
                                'admin_fee' => 'bg-gray-100 text-gray-800',
                                'escrow_release' => 'bg-purple-100 text-purple-800',
                                'wallet_credit' => 'bg-green-100 text-green-800',
                                'wallet_debit' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $actionClasses[$log->action_type] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $log->action_type }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ $log->entity_type }}
                        </span>
                        @if($log->entity_id)
                            <span class="text-xs text-gray-500 ml-1">#{{ $log->entity_id }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($log->amount !== null)
                            <span class="font-semibold {{ $log->amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                Rp {{ number_format($log->amount, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusClasses = [
                                'completed' => 'bg-green-100 text-green-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'failed' => 'bg-red-100 text-red-800',
                                'cancelled' => 'bg-gray-100 text-gray-800',
                                'processing' => 'bg-blue-100 text-blue-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$log->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $log->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $riskClasses = [
                                'low' => 'text-green-600',
                                'medium' => 'text-yellow-600',
                                'high' => 'text-red-600',
                                'critical' => 'text-red-700 font-bold',
                            ];
                        @endphp
                        <span class="{{ $riskClasses[$log->risk_level] ?? 'text-gray-500' }}">
                            {{ ucfirst($log->risk_level) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($log->transaction_reference)
                            <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $log->transaction_reference }}</code>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="block max-w-[200px] truncate text-gray-600" title="{{ $log->notes ?? '' }}">
                            {{ $log->notes ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <x.ui.button href="{{ route('vendor.audit-logs.show', $log->id) }}" variant="outline-primary" size="xs">Detail</x.ui.button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <h4 class="text-sm font-medium text-gray-900">Tidak ada data</h4>
                            <p class="text-sm text-gray-500 mt-1">Belum ada log transaksi keuangan ditemukan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <div class="text-sm text-gray-500">
            Menampilkan {{ $logs->firstItem() }} - {{ $logs->lastItem() }} dari {{ $logs->total() }} data
        </div>
        <div>
            {{ $logs->withQueryString()->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
