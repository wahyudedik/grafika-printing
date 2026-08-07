@extends('layouts.user')

@section('title', 'Order Tracking')

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Order Tracking</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau status pesanan dari lelang yang Anda menangkan</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if($orderTrackings->count() > 0)
            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Pesanan</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Resi</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="text-right px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($orderTrackings as $tracking)
                            @php
                                $statusConfig = [
                                    'pending' => ['label' => 'Menunggu', 'bg' => 'bg-gray-100', 'text' => 'text-gray-700'],
                                    'confirmed' => ['label' => 'Dikonfirmasi', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
                                    'processing' => ['label' => 'Diproses', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
                                    'shipped' => ['label' => 'Dikirim', 'bg' => 'bg-primary-100', 'text' => 'text-primary-700'],
                                    'delivered' => ['label' => 'Diterima', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
                                    'completed' => ['label' => 'Selesai', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
                                ];
                                $status = $statusConfig[$tracking->status] ?? $statusConfig['pending'];
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="font-medium text-gray-900">#{{ $tracking->order_code ?? $tracking->id }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $tracking->auction->title ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-700">
                                    {{ $tracking->vendor->name ?? '-' }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['bg'] }} {{ $status['text'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-700">
                                    {{ $tracking->tracking_number ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-500">
                                    {{ $tracking->created_at->format('d M Y') }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <x-ui.button :href="route('user.orders.show', $tracking)" variant="outline-info" size="sm">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </x-ui.button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($orderTrackings->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $orderTrackings->links('user.components.pagination') }}
                </div>
            @endif
        @else
            {{-- Empty State --}}
            <div class="px-6 py-16 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-shipping-fast text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Belum ada order tracking</h3>
                <p class="text-sm text-gray-500 mb-6 max-w-md mx-auto">
                    Order tracking akan muncul di sini setelah lelang Anda dimenangkan oleh vendor.
                </p>
                <x-ui.button :href="route('user.auctions.index')" variant="primary">
                    <i class="fas fa-gavel mr-2"></i> Lihat Lelang
                </x-ui.button>
            </div>
        @endif
    </div>
@endsection
