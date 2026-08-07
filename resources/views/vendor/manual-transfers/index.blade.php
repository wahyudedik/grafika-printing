@extends('layouts.vendor')

@section('title', 'Manual Transfer Orders')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Manual Transfer Orders</h2>
        <div class="text-sm text-gray-500">Pesanan transfer bank manual</div>
    </div>
</div>

@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
        <div class="flex items-center gap-2 text-green-800">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button @click="show = false" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
    </div>
@endif

@if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center justify-between">
        <div class="flex items-center gap-2 text-red-800">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button @click="show = false" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
    </div>
@endif

{{-- Statistics --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-sm text-gray-500">Total</div>
        <div class="text-2xl font-bold mt-1">{{ $statistics['total'] }}</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-sm text-gray-500">Menunggu</div>
        <div class="text-2xl font-bold mt-1 text-amber-600">{{ $statistics['pending'] }}</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-sm text-gray-500">Dibayar</div>
        <div class="text-2xl font-bold mt-1 text-blue-600">{{ $statistics['paid'] }}</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-sm text-gray-500">Selesai</div>
        <div class="text-2xl font-bold mt-1 text-green-600">{{ $statistics['completed'] }}</div>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
    <form method="GET" class="flex flex-col sm:flex-row gap-3 items-end">
        <div class="flex-1 w-full">
            <input type="text" name="search" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Cari nomor order, nama, HP..." value="{{ request('search') }}">
        </div>
        <div class="w-full sm:w-auto">
            <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Dibayar</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <div class="flex gap-2">
            <x.ui.button type="submit" variant="primary" size="sm">Filter</x.ui.button>
            <x.ui.button href="{{ route('vendor.manual-transfers.index') }}" variant="outline" size="sm">Reset</x.ui.button>
        </div>
    </form>
</div>

{{-- Orders Table --}}
<div class="bg-white rounded-xl border border-gray-200">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="text-left py-3 px-4 font-semibold text-gray-600">No. Order</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-600">Pelanggan</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-600">Items</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-600">Total</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-600">Status</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-600">Tanggal</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4">
                            <code class="bg-gray-100 px-2 py-0.5 rounded text-sm">{{ $order->order_number }}</code>
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-medium">{{ $order->customer_name }}</div>
                            @if($order->customer_phone)
                                <div class="text-xs text-gray-500">{{ $order->customer_phone }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-4">{{ $order->items_summary }}</td>
                        <td class="py-3 px-4 font-bold">{{ $order->formatted_total }}</td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">{{ $order->status_label }}</span>
                        </td>
                        <td class="py-3 px-4 text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 px-4">
                            <x.ui.button href="{{ route('vendor.manual-transfers.show', $order) }}" variant="ghost" size="xs">Detail</x.ui.button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-500">
                            Belum ada order manual transfer.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-200">
        {{ $orders->links() }}
    </div>
</div>
@endsection
