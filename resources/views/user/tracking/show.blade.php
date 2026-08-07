@extends('layouts.user')

@section('title', 'Detail Tracking - ' . $auction->title)

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $auction->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kode: {{ $auction->kode }} &bull; Transaksi: {{ $auction->transaksi->kode }}</p>
        </div>
        @php
            $status = $auction->transaksi->tracking_status;
            $statusColors = [
                'menunggu' => 'bg-gray-100 text-gray-700',
                'diproses' => 'bg-blue-100 text-blue-700',
                'dicetak' => 'bg-yellow-100 text-yellow-700',
                'dikirim' => 'bg-primary-100 text-primary-700',
                'selesai' => 'bg-green-100 text-green-700',
            ];
        @endphp
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-700' }}">
            {{ ucfirst($status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Informasi Pesanan --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Informasi Pesanan</h3>
            </div>
            <div class="px-5 py-4">
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Vendor</p>
                        <p class="text-sm font-medium text-gray-900">{{ $auction->winnerVendor->name }}</p>
                        <p class="text-xs text-gray-500">{{ $auction->winnerVendor->email }}</p>
                    </div>
                    @php
                        $ongkir = (float) ($auction->transaksi->ongkir ?? 0);
                        $subtotalBarang = (float) $auction->transaksi->total_harga - $ongkir;
                    @endphp
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Subtotal Barang</p>
                        <p class="text-sm font-medium text-gray-900">Rp {{ number_format($subtotalBarang) }}</p>
                    </div>
                    @if ($ongkir > 0)
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Ongkos Kirim</p>
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-medium text-gray-900">Rp {{ number_format($ongkir) }}</p>
                                @if ($auction->transaksi->is_cod)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">COD - Bayar di Tempat</span>
                                @endif
                            </div>
                            @if ($auction->transaksi->kurir)
                                <p class="text-xs text-gray-400 mt-0.5">Kurir: {{ $auction->transaksi->kurir }}</p>
                            @endif
                        </div>
                    @endif
                    <div class="pt-2 border-t border-gray-100">
                        <p class="text-xs text-gray-500 mb-0.5">Total Pembayaran</p>
                        <p class="text-sm font-bold text-gray-900">Rp {{ number_format($auction->transaksi->total_harga) }}</p>
                        @if ($auction->transaksi->is_cod && $ongkir > 0)
                            <p class="text-xs text-blue-600 mt-0.5">
                                <i class="fas fa-info-circle"></i>
                                Termasuk ongkir Rp {{ number_format($ongkir) }} yang dibayar di tempat
                            </p>
                        @endif
                    </div>
                    @if ($auction->transaksi->no_resi)
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">No. Resi</p>
                            <p class="text-sm font-medium text-gray-900">{{ $auction->transaksi->no_resi }}</p>
                            @if ($auction->transaksi->kurir)
                                <p class="text-xs text-gray-500">({{ $auction->transaksi->kurir }})</p>
                            @endif
                        </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Estimasi Selesai</p>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $auction->transaksi->estimasi_selesai ? $auction->transaksi->estimasi_selesai->format('d M Y H:i') : 'Belum ditentukan' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Timeline Tracking --}}
        <div class="bg-white rounded-xl border border-gray-200" x-data="trackingTimeline()">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Timeline Tracking</h3>
            </div>
            <div class="px-5 py-4">
                @php
                    $timelineStatuses = [
                        'menunggu' => [
                            'label' => 'Menunggu Konfirmasi',
                            'desc' => 'Pesanan diterima, menunggu konfirmasi vendor',
                            'color' => 'gray',
                            'time' => $auction->transaksi->created_at->format('d M Y H:i'),
                        ],
                        'diproses' => [
                            'label' => 'Sedang Diproses',
                            'desc' => 'Vendor sedang memproses pesanan Anda',
                            'color' => 'blue',
                            'time' => $auction->transaksi->diproses_at ? $auction->transaksi->diproses_at->format('d M Y H:i') : null,
                        ],
                        'dicetak' => [
                            'label' => 'Sedang Dicetak',
                            'desc' => 'Pesanan sedang dalam proses pencetakan',
                            'color' => 'yellow',
                            'time' => $auction->transaksi->dicetak_at ? $auction->transaksi->dicetak_at->format('d M Y H:i') : null,
                        ],
                        'dikirim' => [
                            'label' => 'Sedang Dikirim',
                            'desc' => 'Pesanan sedang dalam perjalanan',
                            'color' => 'primary',
                            'time' => $auction->transaksi->dikirim_at ? $auction->transaksi->dikirim_at->format('d M Y H:i') : null,
                        ],
                        'selesai' => [
                            'label' => 'Selesai',
                            'desc' => 'Pesanan telah selesai dan diterima',
                            'color' => 'green',
                            'time' => $auction->transaksi->selesai_at ? $auction->transaksi->selesai_at->format('d M Y H:i') : null,
                        ],
                    ];
                @endphp

                <div class="relative pl-8">
                    <div class="absolute left-3 top-1 bottom-1 w-0.5 bg-gray-200"></div>

                    @foreach($timelineStatuses as $key => $item)
                        @php
                            $isActive = $status === $key;
                            $isCompleted = array_search($status, array_keys($timelineStatuses)) > array_search($key, array_keys($timelineStatuses));
                            $isPending = array_search($status, array_keys($timelineStatuses)) < array_search($key, array_keys($timelineStatuses));
                        @endphp
                        <div class="relative pb-6 last:pb-0">
                            <div class="absolute -left-8 top-0.5 w-6 h-6 rounded-full border-2 flex items-center justify-center z-10
                                {{ $isActive ? "border-{$item['color']}-500 bg-{$item['color']}-500" : ($isCompleted ? 'border-green-500 bg-green-500' : 'border-gray-300 bg-white') }}">
                                @if($isCompleted)
                                    <i class="fas fa-check text-white text-[10px]"></i>
                                @elseif($isActive)
                                    <div class="w-2 h-2 bg-white rounded-full"></div>
                                @endif
                            </div>
                            <div class="{{ $isPending ? 'opacity-50' : '' }}">
                                <p class="text-sm font-semibold text-gray-900 {{ $isActive ? 'text-primary-600' : '' }}">{{ $item['label'] }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $item['desc'] }}</p>
                                @if($item['time'])
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $item['time'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-wrap items-center gap-3">
        <x-ui.button :href="route('user.orders.index')" variant="outline">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </x-ui.button>
        @if ($status === 'selesai')
            <x-ui.button :href="route('vendor.ratings.create', $auction)" variant="warning">
                <i class="fas fa-star mr-2"></i> Beri Rating
            </x-ui.button>
        @endif
        @if ($auction->transaksi->no_resi && $auction->transaksi->kurir)
            <x-ui.button @click="trackShipment('{{ $auction->transaksi->no_resi }}', '{{ $auction->transaksi->kurir }}')" variant="info">
                <i class="fas fa-truck mr-2"></i> Lacak Pengiriman
            </x-ui.button>
        @endif
    </div>

    {{-- Tracking Modal --}}
    <div x-show="showModal" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            {{-- Overlay --}}
            <div x-show="showModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showModal = false"></div>

            {{-- Modal Content --}}
            <div x-show="showModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full mx-auto">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Detail Pengiriman</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <div class="px-6 py-5">
                    {{-- Loading --}}
                    <div x-show="loading" class="text-center py-8">
                        <div class="inline-flex items-center gap-2 text-gray-500">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm">Memuat data pengiriman...</span>
                        </div>
                    </div>

                    {{-- Success --}}
                    <div x-show="!loading && trackingData.success" class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-green-800">Status Pengiriman</h4>
                                <p class="text-sm text-green-700 mt-1" x-text="trackingData.message || 'Data pengiriman berhasil diambil'"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Error --}}
                    <div x-show="!loading && !trackingData.success" class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-red-800">Error</h4>
                                <p class="text-sm text-red-700 mt-1" x-text="trackingData.message || 'Terjadi kesalahan saat melacak pengiriman'"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@once
<script>
    function trackingTimeline() {
        return {
            showModal: false,
            loading: false,
            trackingData: {},

            async trackShipment(awb, courier) {
                this.showModal = true;
                this.loading = true;
                this.trackingData = {};

                try {
                    const response = await fetch('/api/track-shipment', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ awb, courier })
                    });
                    const data = await response.json();
                    this.trackingData = data;
                } catch (error) {
                    this.trackingData = {
                        success: false,
                        message: 'Terjadi kesalahan saat melacak pengiriman'
                    };
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endonce
