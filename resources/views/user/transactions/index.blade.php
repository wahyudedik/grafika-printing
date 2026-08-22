@extends('layouts.user')

@section('title', 'Riwayat Pesanan Saya')

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Riwayat Pesanan Saya</h1>
            <p class="text-sm text-gray-500 mt-1">Lihat semua transaksi pembelian Anda</p>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('user.transactions.index') }}" class="space-y-4">
            {{-- Search Bar --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari kode transaksi..." class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                    </div>
                </div>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Cari
                </button>
                @if($filters['search'] || $filters['status'])
                    <a href="{{ route('user.transactions.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reset
                    </a>
                @endif
            </div>

            {{-- Status Filter Badges --}}
            <div class="flex flex-wrap gap-2">
                @php
                    $statusFilters = [
                        '' => ['label' => 'Semua', 'active' => 'bg-primary-600 text-white', 'inactive' => 'bg-gray-100 text-gray-700 hover:bg-gray-200'],
                        'pending' => ['label' => 'Menunggu', 'active' => 'bg-yellow-500 text-white', 'inactive' => 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100'],
                        'processing' => ['label' => 'Diproses', 'active' => 'bg-blue-500 text-white', 'inactive' => 'bg-blue-50 text-blue-700 hover:bg-blue-100'],
                        'quality_check' => ['label' => 'Quality Check', 'active' => 'bg-purple-500 text-white', 'inactive' => 'bg-purple-50 text-purple-700 hover:bg-purple-100'],
                        'completed' => ['label' => 'Selesai', 'active' => 'bg-green-500 text-white', 'inactive' => 'bg-green-50 text-green-700 hover:bg-green-100'],
                        'cancelled' => ['label' => 'Dibatalkan', 'active' => 'bg-red-500 text-white', 'inactive' => 'bg-red-50 text-red-700 hover:bg-red-100'],
                    ];
                @endphp
                @foreach($statusFilters as $key => $config)
                    <a href="{{ route('user.transactions.index', array_merge(request()->except('status', 'page'), $key ? ['status' => $key] : [])) }}"
                       class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium transition-colors {{ ($filters['status'] ?? '') === $key ? $config['active'] : $config['inactive'] }}">
                        {{ $config['label'] }}
                    </a>
                @endforeach
            </div>
        </form>
    </div>

    {{-- Transaction List --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if($transaksi->count() > 0)
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="text-right px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="text-center px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="text-right px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($transaksi as $item)
                            @php
                                $statusConfig = match($item->status) {
                                    'pending' => ['label' => 'Menunggu', 'variant' => 'warning'],
                                    'processing' => ['label' => 'Diproses', 'variant' => 'info'],
                                    'quality_check' => ['label' => 'Quality Check', 'variant' => 'purple'],
                                    'completed' => ['label' => 'Selesai', 'variant' => 'success'],
                                    'cancelled' => ['label' => 'Dibatalkan', 'variant' => 'danger'],
                                    default => ['label' => ucfirst($item->status), 'variant' => 'secondary'],
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-4">
                                    <a href="{{ route('user.transactions.show', $item->id) }}" class="font-medium text-primary-600 hover:text-primary-800 transition-colors">
                                        #{{ $item->kode }}
                                    </a>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($item->vendor && $item->vendor->logo)
                                            <img src="{{ asset('vendors_logo/' . $item->vendor->logo) }}" alt="{{ $item->vendor->name }}" class="w-8 h-8 rounded-full object-cover border border-gray-200">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                                                <span class="text-xs font-semibold text-primary-700">{{ strtoupper(substr($item->vendor->name ?? 'V', 0, 1)) }}</span>
                                            </div>
                                        @endif
                                        <span class="text-sm text-gray-700">{{ $item->vendor->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-500">
                                    {{ $item->created_at->format('d M Y') }}
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-900 text-right font-medium">
                                    Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <x-ui.badge :variant="$statusConfig['variant']">{{ $statusConfig['label'] }}</x-ui.badge>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('user.transactions.show', $item->id) }}" class="inline-flex items-center justify-center border border-primary-300 text-primary-700 hover:bg-primary-50 font-semibold text-sm py-1 px-3 rounded-lg transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-100">
                @foreach($transaksi as $item)
                    @php
                        $statusConfig = match($item->status) {
                            'pending' => ['label' => 'Menunggu', 'variant' => 'warning'],
                            'processing' => ['label' => 'Diproses', 'variant' => 'info'],
                            'quality_check' => ['label' => 'Quality Check', 'variant' => 'purple'],
                            'completed' => ['label' => 'Selesai', 'variant' => 'success'],
                            'cancelled' => ['label' => 'Dibatalkan', 'variant' => 'danger'],
                            default => ['label' => ucfirst($item->status), 'variant' => 'secondary'],
                        };
                    @endphp
                    <a href="{{ route('user.transactions.show', $item->id) }}" class="block p-4 space-y-2 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-primary-600">#{{ $item->kode }}</div>
                                <p class="text-sm text-gray-500 mt-0.5">{{ $item->vendor->name ?? '-' }}</p>
                            </div>
                            <x-ui.badge :variant="$statusConfig['variant']">{{ $statusConfig['label'] }}</x-ui.badge>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">{{ $item->created_at->format('d M Y') }}</span>
                            <span class="text-sm font-semibold text-gray-900">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</span>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($transaksi->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    <x-ui.pagination :paginator="$transaksi" />
                </div>
            @endif
        @else
            <x-ui.empty-state
                icon="fas fa-shopping-bag"
                title="Belum ada riwayat pesanan"
                description="Anda belum memiliki transaksi pembelian. Mulai berbelanja untuk melihat riwayat pesanan di sini."
            />
        @endif
    </div>
@endsection
