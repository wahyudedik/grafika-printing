@extends('layouts.user')

@section('title', 'Konfirmasi Pengiriman')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Konfirmasi Pengiriman</h1>
            <p class="text-sm text-gray-500">Daftar semua konfirmasi pengiriman pesanan Anda</p>
        </div>
    </div>

    {{-- Stats Summary --}}
    @php
        $totalConfirmations = $confirmations->total();
        $pendingCount = $confirmations->getCollection()->where('delivery_status', 'pending')->count();
        $deliveredCount = $confirmations->getCollection()->where('delivery_status', 'delivered')->count();
        $confirmedCount = $confirmations->getCollection()->where('delivery_status', 'confirmed')->count();
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ $totalConfirmations }}</div>
            <div class="text-xs text-gray-500 mt-1">Total</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-amber-600">{{ $pendingCount }}</div>
            <div class="text-xs text-gray-500 mt-1">Menunggu</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $deliveredCount }}</div>
            <div class="text-xs text-gray-500 mt-1">Diterima</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $confirmedCount }}</div>
            <div class="text-xs text-gray-500 mt-1">Dikonfirmasi</div>
        </div>
    </div>

    {{-- Confirmation List --}}
    @if($confirmations->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Lelang</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Vendor</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Tanggal</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                            <th class="text-right px-4 py-3 font-medium text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($confirmations as $confirmation)
                            @php
                                $statusConfig = [
                                    'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'label' => 'Menunggu'],
                                    'delivered' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Diterima'],
                                    'confirmed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Dikonfirmasi'],
                                    'disputed' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Masalah'],
                                    'resolved' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Selesai'],
                                ];
                                $config = $statusConfig[$confirmation->delivery_status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => 'Unknown'];
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <span class="font-medium text-gray-900">{{ $confirmation->auction->title ?? 'N/A' }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $confirmation->vendor->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-500 text-sm">
                                    {{ $confirmation->delivery_date ? $confirmation->delivery_date->format('d M Y H:i') : '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['bg'] }} {{ $config['text'] }}">
                                        {{ $config['label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <x-ui.button :href="route('user.delivery-confirmation.show', $confirmation)" variant="outline-info" size="sm">
                                        <i class="fas fa-eye text-xs"></i> Detail
                                    </x-ui.button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-100">
                @foreach($confirmations as $confirmation)
                    @php
                        $statusConfig = [
                            'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'label' => 'Menunggu'],
                            'delivered' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Diterima'],
                            'confirmed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Dikonfirmasi'],
                            'disputed' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Masalah'],
                            'resolved' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Selesai'],
                        ];
                        $config = $statusConfig[$confirmation->delivery_status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => 'Unknown'];
                    @endphp
                    <div class="p-4 space-y-2">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 truncate">{{ $confirmation->auction->title ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">{{ $confirmation->vendor->name ?? 'N/A' }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['bg'] }} {{ $config['text'] }} ml-2 flex-shrink-0">
                                {{ $config['label'] }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">
                                {{ $confirmation->delivery_date ? $confirmation->delivery_date->format('d M Y H:i') : '-' }}
                            </span>
                            <x-ui.button :href="route('user.delivery-confirmation.show', $confirmation)" variant="outline-info" size="sm">
                                <i class="fas fa-eye text-xs"></i> Detail
                            </x-ui.button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $confirmations->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <i class="fas fa-box text-gray-300 text-5xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada konfirmasi pengiriman</h3>
            <p class="text-sm text-gray-500 mb-4 max-w-md mx-auto">Konfirmasi pengiriman akan muncul di sini setelah Anda menerima barang dari lelang yang sudah dibayar.</p>
            <x-ui.button :href="route('user.auctions.index')" variant="primary">
                <i class="fas fa-list text-xs"></i> Lihat Lelang Saya
            </x-ui.button>
        </div>
    @endif
</div>
@endsection
