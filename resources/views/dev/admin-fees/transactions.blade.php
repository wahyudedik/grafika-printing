@extends('dev.layouts.app')

@section('title', 'Transaksi Biaya Admin')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-900">Transaksi Biaya Admin</h1>
        <x.ui.button href="{{ route('admin.admin-fees.index') }}" variant="outline">
            <i class="fas fa-times mr-1"></i>Kembali ke Pengaturan
        </x.ui.button>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                    class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vendor</label>
                <select name="vendor_id" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Semua Vendor</option>
                    @foreach (\App\Models\Vendor::all() as $vendor)
                        <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <x.ui.button type="submit" variant="primary">
                    <i class="fas fa-search mr-1"></i>Filter
                </x.ui.button>
                <x.ui.button href="{{ route('admin.admin-fees.transactions') }}" variant="outline">
                    Reset
                </x.ui.button>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lelang</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Lelang</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya Admin</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya Payment</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="text-sm text-gray-500">#{{ $transaction->id }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="text-sm font-medium text-gray-900">{{ $transaction->auction->title ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $transaction->auction->kode ?? 'N/A' }}</div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="text-sm font-medium text-gray-900">{{ $transaction->vendor->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $transaction->vendor->email ?? 'N/A' }}</div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="text-sm font-medium text-gray-900">{{ $transaction->user->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $transaction->user->email ?? 'N/A' }}</div>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="text-sm font-bold text-gray-900">Rp {{ number_format($transaction->auction_amount, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="text-sm font-bold text-yellow-600">Rp {{ number_format($transaction->admin_fee_amount, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="text-sm font-bold text-cyan-600">Rp {{ number_format($transaction->payment_gateway_fee, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="text-sm font-bold text-primary-600">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'paid' => 'bg-green-100 text-green-800',
                                        'failed' => 'bg-red-100 text-red-800',
                                        'refunded' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $colorClass = $statusColors[$transaction->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $colorClass }}">
                                    {{ $transaction->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="text-sm text-gray-500">{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-receipt text-gray-400 text-xl"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900 mb-1">Tidak ada transaksi biaya admin</p>
                                    <p class="text-xs text-gray-500">Belum ada transaksi biaya admin yang tercatat.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($transactions->hasPages())
            <div class="px-5 py-4 border-t border-gray-100 flex justify-center">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
@endsection
