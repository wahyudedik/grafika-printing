@extends('layouts.user')

@section('title', 'Detail Pesanan #' . $transaksi->kode)

@section('content')
    {{-- Breadcrumbs --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6 print:hidden">
        <a href="{{ route('user.dashboard') }}" class="hover:text-primary-600 transition-colors">Beranda</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('user.transactions.index') }}" class="hover:text-primary-600 transition-colors">Riwayat Pesanan</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-900 font-medium">Detail</span>
    </nav>

    @php
        $statusConfig = match($transaksi->status) {
            'pending' => ['label' => 'Menunggu Pembayaran', 'variant' => 'warning', 'icon' => 'fas fa-clock'],
            'processing' => ['label' => 'Sedang Diproses', 'variant' => 'info', 'icon' => 'fas fa-cog'],
            'quality_check' => ['label' => 'Quality Check', 'variant' => 'purple', 'icon' => 'fas fa-check-double'],
            'completed' => ['label' => 'Selesai', 'variant' => 'success', 'icon' => 'fas fa-check-circle'],
            'cancelled' => ['label' => 'Dibatalkan', 'variant' => 'danger', 'icon' => 'fas fa-times-circle'],
            default => ['label' => ucfirst($transaksi->status), 'variant' => 'secondary', 'icon' => 'fas fa-info-circle'],
        };

        $statusSteps = ['pending', 'processing', 'quality_check', 'completed'];
        $currentStepIndex = array_search($transaksi->status, $statusSteps);
        if ($currentStepIndex === false) $currentStepIndex = -1;

        $paymentStatusConfig = match($transaksi->payment_status) {
            'paid' => ['label' => 'Lunas', 'variant' => 'success'],
            'partial' => ['label' => 'Sebagian', 'variant' => 'warning'],
            'pending' => ['label' => 'Belum Bayar', 'variant' => 'danger'],
            default => ['label' => $transaksi->payment_status ?? '-', 'variant' => 'secondary'],
        };
    @endphp

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pesanan #{{ $transaksi->kode }}</h1>
            <p class="text-sm text-gray-500 mt-1">Dipesan pada {{ $transaksi->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <x-ui.badge :variant="$statusConfig['variant']">
                <i class="{{ $statusConfig['icon'] }} mr-1"></i>
                {{ $statusConfig['label'] }}
            </x-ui.badge>
        </div>
    </div>

    {{-- Status Timeline --}}
    @if(in_array($transaksi->status, $statusSteps) || $transaksi->status === 'cancelled')
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6 print:hidden">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">Status Pesanan</h2>
            @if($transaksi->status === 'cancelled')
                <div class="flex items-center gap-3 p-4 bg-red-50 rounded-lg border border-red-200">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-red-800">Pesanan Dibatalkan</p>
                        <p class="text-xs text-red-600 mt-0.5">Pesanan ini telah dibatalkan</p>
                    </div>
                </div>
            @else
                <div class="flex items-center justify-between">
                    @php
                        $steps = [
                            ['key' => 'pending', 'label' => 'Menunggu', 'icon' => 'fas fa-clock'],
                            ['key' => 'processing', 'label' => 'Diproses', 'icon' => 'fas fa-cog'],
                            ['key' => 'quality_check', 'label' => 'QC', 'icon' => 'fas fa-check-double'],
                            ['key' => 'completed', 'label' => 'Selesai', 'icon' => 'fas fa-check-circle'],
                        ];
                    @endphp
                    @foreach($steps as $index => $step)
                        @php
                            $isActive = $currentStepIndex >= $index;
                            $isCurrent = $currentStepIndex === $index;
                        @endphp
                        <div class="flex flex-col items-center flex-1 {{ $index < count($steps) - 1 ? 'relative' : '' }}">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center transition-colors {{ $isActive ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-400' }} {{ $isCurrent ? 'ring-4 ring-primary-100' : '' }}">
                                <i class="{{ $step['icon'] }} text-sm"></i>
                            </div>
                            <span class="text-xs mt-2 text-center font-medium {{ $isActive ? 'text-primary-700' : 'text-gray-400' }}">{{ $step['label'] }}</span>
                            @if($isCurrent && $transaksi->status === 'processing' && $transaksi->diproses_at)
                                <span class="text-xs text-gray-400 mt-0.5">{{ $transaksi->diproses_at->format('d M') }}</span>
                            @endif
                        </div>
                        @if($index < count($steps) - 1)
                            <div class="absolute top-5 left-1/2 w-full h-0.5 {{ $currentStepIndex > $index ? 'bg-primary-600' : 'bg-gray-200' }}" style="transform: translateX(50%);"></div>
                        @endif
                    @endforeach
                </div>
                @if($transaksi->progress_percentage > 0 && $transaksi->status !== 'completed')
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                            <span>Progress</span>
                            <span class="font-medium text-primary-600">{{ $transaksi->progress_percentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-primary-600 h-2 rounded-full transition-all duration-500" style="width: {{ $transaksi->progress_percentage }}%"></div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column — Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Items Table --}}
            <x-ui.card title="Detail Pesanan">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50">
                                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                <th class="text-center px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="text-right px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($transaksi->transaksiItem as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($item->produk && $item->produk->gambar)
                                                <img src="{{ asset('produk_gambar/' . $item->produk->gambar) }}" alt="{{ $item->produk->nama ?? 'Produk' }}" class="w-12 h-12 rounded-lg object-cover border border-gray-200">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $item->produk->nama ?? 'Produk tidak tersedia' }}</div>
                                                @if($item->transaksiItemSpecifications && $item->transaksiItemSpecifications->count())
                                                    <div class="mt-1 space-y-0.5">
                                                        @foreach($item->transaksiItemSpecifications as $spec)
                                                            <div class="text-xs text-gray-500">
                                                                {{ $spec->bahan->nama ?? $spec->nama ?? '-' }}: {{ $spec->nilai ?? $spec->value ?? '-' }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center text-sm text-gray-700">{{ $item->kuantitas }}</td>
                                    <td class="px-4 py-4 text-right text-sm text-gray-700">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">Rp {{ number_format($item->kuantitas * $item->harga_satuan, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Summary --}}
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    <div class="flex justify-end">
                        <div class="w-full max-w-xs space-y-2">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($transaksi->total_harga - ($transaksi->ongkir ?? 0) - ($transaksi->admin_fee ?? 0), 0, ',', '.') }}</span>
                            </div>
                            @if($transaksi->ongkir > 0)
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Ongkos Kirim</span>
                                    <span>Rp {{ number_format($transaksi->ongkir, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($transaksi->admin_fee > 0)
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Admin Fee</span>
                                    <span>Rp {{ number_format($transaksi->admin_fee, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="border-t border-gray-300 pt-2 flex justify-between text-base font-bold text-gray-900">
                                <span>Total</span>
                                <span class="text-primary-600">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            {{-- Payment Info --}}
            <x-ui.card title="Informasi Pembayaran">
                <div class="p-6 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Metode Pembayaran</span>
                        <span class="text-sm font-medium text-gray-900">{{ $transaksi->payment_method ? ucfirst(str_replace('_', ' ', $transaksi->payment_method)) : '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Status Pembayaran</span>
                        <x-ui.badge :variant="$paymentStatusConfig['variant']">{{ $paymentStatusConfig['label'] }}</x-ui.badge>
                    </div>
                    @if($transaksi->paid_at)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Tanggal Pembayaran</span>
                            <span class="text-sm font-medium text-gray-900">{{ $transaksi->paid_at->format('d M Y, H:i') }}</span>
                        </div>
                    @endif
                    @if($transaksi->is_cod)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Tipe Pembayaran</span>
                            <x-ui.badge variant="indigo">COD (Cash on Delivery)</x-ui.badge>
                        </div>
                        @if($transaksi->payment_amount)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Jumlah Dibayar</span>
                                <span class="text-sm font-medium text-gray-900">Rp {{ number_format($transaksi->payment_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($transaksi->change_amount > 0)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Kembalian</span>
                                <span class="text-sm font-medium text-green-600">Rp {{ number_format($transaksi->change_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    @endif
                </div>
            </x-ui.card>

            {{-- Shipping Info (if exists) --}}
            @if($transaksi->kurir || $transaksi->no_resi || $transaksi->alamat_pengiriman)
                <x-ui.card title="Informasi Pengiriman">
                    <div class="p-6 space-y-3">
                        @if($transaksi->kurir)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Kurir</span>
                                <span class="text-sm font-medium text-gray-900">{{ $transaksi->kurir }}</span>
                            </div>
                        @endif
                        @if($transaksi->no_resi)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">No. Resi</span>
                                <span class="text-sm font-medium text-primary-600">{{ $transaksi->no_resi }}</span>
                            </div>
                        @endif
                        @if($transaksi->alamat_pengiriman)
                            <div class="flex justify-between items-start gap-4">
                                <span class="text-sm text-gray-600 flex-shrink-0">Alamat</span>
                                <span class="text-sm font-medium text-gray-900 text-right">{{ $transaksi->alamat_pengiriman }}</span>
                            </div>
                        @endif
                    </div>
                </x-ui.card>
            @endif
        </div>

        {{-- Right Column — Sidebar --}}
        <div class="space-y-6">
            {{-- Vendor Info --}}
            <x-ui.card>
                <div class="p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Informasi Vendor</h3>
                    <div class="flex items-center gap-3">
                        @if($transaksi->vendor && $transaksi->vendor->logo)
                            <img src="{{ asset('vendors_logo/' . $transaksi->vendor->logo) }}" alt="{{ $transaksi->vendor->name }}" class="w-12 h-12 rounded-full object-cover border border-gray-200">
                        @else
                            <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center">
                                <span class="text-lg font-bold text-primary-700">{{ strtoupper(substr($transaksi->vendor->name ?? 'V', 0, 1)) }}</span>
                            </div>
                        @endif
                        <div>
                            <div class="font-medium text-gray-900">{{ $transaksi->vendor->name ?? '-' }}</div>
                            @if($transaksi->vendor && $transaksi->vendor->address)
                                <div class="text-xs text-gray-500 mt-0.5">{{ $transaksi->vendor->address }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </x-ui.card>

            {{-- Customer Info --}}
            <x-ui.card>
                <div class="p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Informasi Pelanggan</h3>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-gray-700">{{ $transaksi->pelanggan->nama ?? '-' }}</span>
                        </div>
                        @if($transaksi->pelanggan && $transaksi->pelanggan->telepon)
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <span class="text-gray-700">{{ $transaksi->pelanggan->telepon }}</span>
                            </div>
                        @endif
                        @if($transaksi->pelanggan && $transaksi->pelanggan->alamat)
                            <div class="flex items-start gap-2 text-sm">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-gray-700">{{ $transaksi->pelanggan->alamat }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </x-ui.card>

            {{-- Catatan --}}
            @if($transaksi->catatan)
                <x-ui.card>
                    <div class="p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">Catatan</h3>
                        <p class="text-sm text-gray-600">{{ $transaksi->catatan }}</p>
                    </div>
                </x-ui.card>
            @endif

            {{-- Review Section --}}
            @if($transaksi->transactionReview)
                <x-ui.card>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-gray-900">Ulasan Anda</h3>
                            <span class="text-xs text-gray-400">{{ $transaksi->transactionReview->created_at->format('d M Y, H:i') }}</span>
                        </div>

                        {{-- Star Rating --}}
                        <div class="flex items-center gap-1 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $transaksi->transactionReview->rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                            @endfor
                            <span class="text-sm font-medium text-gray-700 ml-1">{{ $transaksi->transactionReview->rating }}/5</span>
                        </div>

                        {{-- Detail Ratings --}}
                        @if($transaksi->transactionReview->quality_rating || $transaksi->transactionReview->speed_rating || $transaksi->transactionReview->service_rating)
                            <div class="grid grid-cols-3 gap-3 mb-3">
                                @if($transaksi->transactionReview->quality_rating)
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500 mb-1">Kualitas</div>
                                        <div class="flex items-center justify-center gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star text-xs {{ $i <= $transaksi->transactionReview->quality_rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                @endif
                                @if($transaksi->transactionReview->speed_rating)
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500 mb-1">Kecepatan</div>
                                        <div class="flex items-center justify-center gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star text-xs {{ $i <= $transaksi->transactionReview->speed_rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                @endif
                                @if($transaksi->transactionReview->service_rating)
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500 mb-1">Pelayanan</div>
                                        <div class="flex items-center justify-center gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star text-xs {{ $i <= $transaksi->transactionReview->service_rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Comment --}}
                        @if($transaksi->transactionReview->comment)
                            <p class="text-sm text-gray-600 mb-3">{{ $transaksi->transactionReview->comment }}</p>
                        @endif

                        {{-- Delete Button --}}
                        <div x-data="{ showConfirm: false }">
                            <button @click="showConfirm = true" type="button" class="inline-flex items-center text-xs text-red-500 hover:text-red-700 transition-colors">
                                <i class="fas fa-trash-alt mr-1"></i>
                                Hapus Ulasan
                            </button>

                            {{-- Confirmation Dialog --}}
                            <div x-show="showConfirm" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
                                <div class="fixed inset-0 bg-gray-900/50" @click="showConfirm = false"></div>
                                <div class="relative bg-white rounded-xl shadow-xl max-w-sm w-full p-6">
                                    <div class="text-center">
                                        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-trash-alt text-red-600"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Hapus Ulasan?</h3>
                                        <p class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin menghapus ulasan ini? Tindakan ini tidak dapat dibatalkan.</p>
                                        <div class="flex items-center gap-3">
                                            <button @click="showConfirm = false" type="button" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                                                Batal
                                            </button>
                                            <form action="{{ route('user.reviews.destroy', $transaksi->transactionReview->id) }}" method="POST" class="flex-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full px-4 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                                                    Ya, Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-ui.card>
            @elseif($transaksi->status === 'completed')
                <x-ui.card>
                    <div class="p-6 text-center">
                        <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-star text-yellow-500"></i>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-1">Belum Ada Ulasan</h3>
                        <p class="text-xs text-gray-500 mb-4">Beri penilaian Anda untuk transaksi ini</p>
                        <a href="{{ route('user.transactions.review.create', $transaksi->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-lg hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors">
                            <i class="fas fa-star mr-2"></i>
                            Beri Ulasan
                        </a>
                    </div>
                </x-ui.card>
            @endif

            {{-- Action Buttons --}}
            <div class="space-y-3">
                <a href="{{ route('user.transactions.invoice', $transaksi->id) }}" class="flex items-center justify-center w-full px-4 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors print:hidden">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Lihat Invoice
                </a>
                @if($transaksi->status === 'completed' && Route::has('user.transactions.review.create'))
                    <a href="{{ route('user.transactions.review.create', $transaksi->id) }}" class="flex items-center justify-center w-full px-4 py-2.5 bg-yellow-500 text-white text-sm font-medium rounded-lg hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors print:hidden">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        Beri Ulasan
                    </a>
                @endif
                <a href="{{ route('user.transactions.index') }}" class="flex items-center justify-center w-full px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors print:hidden">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Riwayat
                </a>
            </div>
        </div>
    </div>
@endsection
