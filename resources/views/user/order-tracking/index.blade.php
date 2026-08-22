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
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
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
                                    'payment_received' => ['label' => 'Pembayaran Diterima', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
                                    'order_accepted' => ['label' => 'Pesanan Diterima', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
                                    'production_started' => ['label' => 'Proses Cetak', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
                                    'production_completed' => ['label' => 'Cetak Selesai', 'bg' => 'bg-orange-100', 'text' => 'text-orange-700'],
                                    'quality_check' => ['label' => 'Quality Check', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
                                    'packaging' => ['label' => 'Dikemas', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-700'],
                                    'shipped' => ['label' => 'Dikirim', 'bg' => 'bg-primary-100', 'text' => 'text-primary-700'],
                                    'delivered' => ['label' => 'Diterima', 'bg' => 'bg-teal-100', 'text' => 'text-teal-700'],
                                    'completed' => ['label' => 'Selesai', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
                                    'mediation' => ['label' => 'Mediasi', 'bg' => 'bg-red-100', 'text' => 'text-red-700'],
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
                                    <a href="{{ route('user.orders.show', $tracking) }}" class="inline-flex items-center justify-center border border-cyan-300 text-cyan-700 hover:bg-cyan-50 font-semibold text-sm py-1 px-3 rounded-lg transition">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-100">
                @foreach($orderTrackings as $tracking)
                    @php
                        $statusConfig = [
                            'payment_received' => ['label' => 'Pembayaran Diterima', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
                            'order_accepted' => ['label' => 'Pesanan Diterima', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
                            'production_started' => ['label' => 'Proses Cetak', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
                            'production_completed' => ['label' => 'Cetak Selesai', 'bg' => 'bg-orange-100', 'text' => 'text-orange-700'],
                            'quality_check' => ['label' => 'Quality Check', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
                            'packaging' => ['label' => 'Dikemas', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-700'],
                            'shipped' => ['label' => 'Dikirim', 'bg' => 'bg-primary-100', 'text' => 'text-primary-700'],
                            'delivered' => ['label' => 'Diterima', 'bg' => 'bg-teal-100', 'text' => 'text-teal-700'],
                            'completed' => ['label' => 'Selesai', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
                            'mediation' => ['label' => 'Mediasi', 'bg' => 'bg-red-100', 'text' => 'text-red-700'],
                        ];
                        $status = $statusConfig[$tracking->status] ?? $statusConfig['pending'];
                    @endphp
                    <div class="p-4 space-y-2">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900">#{{ $tracking->order_code ?? $tracking->id }}</div>
                                <p class="text-sm text-gray-500 truncate">{{ $tracking->auction->title ?? '-' }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['bg'] }} {{ $status['text'] }} ml-2 flex-shrink-0">
                                {{ $status['label'] }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">{{ $tracking->vendor->name ?? '-' }}</span>
                            <span class="text-gray-400 text-xs">{{ $tracking->created_at->format('d M Y') }}</span>
                        </div>
                        @if($tracking->tracking_number)
                            <div class="text-xs text-gray-500">
                                <i class="fas fa-truck mr-1"></i> {{ $tracking->tracking_number }}
                            </div>
                        @endif
                        <div class="flex items-center justify-end pt-1">
                            <a href="{{ route('user.orders.show', $tracking) }}" class="inline-flex items-center justify-center border border-cyan-300 text-cyan-700 hover:bg-cyan-50 font-semibold text-sm py-1 px-3 rounded-lg transition">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </a>
                        </div>
                    </div>
                @endforeach
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
                <a href="{{ route('user.auctions.index') }}" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                    <i class="fas fa-gavel mr-2"></i> Lihat Lelang
                </a>
            </div>
        @endif
    </div>
@endsection
