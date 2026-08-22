@extends('layouts.pos')

@section('title', 'Keranjang Belanja')

@section('content')
    {{-- Add CSRF token meta tag --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Header --}}
    <div class="px-4 pt-4">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Point of Sale</h2>
                        <p class="text-sm text-gray-500 mt-1"><i class="fas fa-calendar-alt mr-2"></i>{{ date('Y-m-d') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="px-4 mt-3 pb-4">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6">
                {{-- Cart Details --}}
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800">Detail Keranjang</h3>
                    <a href="{{ route('vendor.pos.index') }}" data-no-loading
                        class="inline-flex items-center px-4 py-2 border border-primary text-primary rounded-full text-sm font-medium hover:bg-primary/5 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Produk
                    </a>
                </div>

                @if (empty($cartItems))
                    <div class="text-center py-12">
                        <i class="fas fa-shopping-cart text-5xl text-gray-300 mb-4"></i>
                        <h4 class="text-lg font-semibold text-gray-600">Keranjang Kosong</h4>
                        <p class="text-gray-500 mt-1">Tambahkan produk ke keranjang untuk melanjutkan belanja</p>
                        <a href="{{ route('vendor.pos.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors mt-4">
                            <i class="fas fa-shopping-bag mr-2"></i>Lihat Produk
                        </a>
                    </div>
                @else
                    <div id="cartItems" class="space-y-4">
                        @foreach ($cartItems as $index => $item)
                            <div class="bg-white border border-gray-200 rounded-xl p-4">
                                <div class="flex justify-between items-center mb-3">
                                    <h5 class="font-bold text-gray-800">{{ $item['product_name'] }}</h5>
                                    <button class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-danger/10 hover:text-danger transition-colors"
                                        type="button" onclick="removeItem({{ $index }})">
                                        <i class="fas fa-times text-sm"></i>
                                    </button>
                                </div>

                                <div class="flex justify-between items-center border-b border-gray-100 py-2">
                                    <span class="text-sm text-gray-500">Jumlah</span>
                                    <span class="text-sm font-medium text-gray-700">{{ $item['quantity'] }} pcs</span>
                                </div>

                                {{-- Specifications --}}
                                <div class="specifications-container">
                                    @foreach ($item['specifications'] as $specId => $spec)
                                        @php
                                            $spesifikasiProduk = \App\Models\Vendor\SpesifikasiProduk::with([
                                                'spesifikasi',
                                                'bahans',
                                            ])->find($specId);
                                            $bahan = \App\Models\Vendor\Bahan::with('wholesalePrice')->find(
                                                $spec['bahan_id'],
                                            );
                                            $wholesalePrice = new \App\Models\Vendor\WholesalePrice();

                                            if ($spec['input_type'] === 'select' && $bahan) {
                                                $pricePerUnit = $wholesalePrice->calculateFinalPrice(
                                                    (float) ($bahan->hpp ?? 0),
                                                    $item['quantity'],
                                                    $bahan->id,
                                                );
                                            } elseif ($bahan) {
                                                $pricePerUnit = $wholesalePrice->calculateFinalPrice(
                                                    (float) ($bahan->hpp ?? 0),
                                                    $spec['value'],
                                                    $bahan->id,
                                                );
                                            } else {
                                                $pricePerUnit = 0;
                                            }
                                        @endphp
                                        <div class="flex justify-between items-center border-b border-gray-100 py-2">
                                            <span class="text-sm text-gray-500">
                                                {{ $spec['nama_spesifikasi'] }}
                                            </span>
                                            <span class="text-sm font-medium text-gray-700">
                                                @if ($spec['input_type'] === 'select' && $bahan)
                                                    {{ $bahan->nama_bahan }}: {{ $item['quantity'] }} x Rp
                                                    {{ number_format($pricePerUnit, 0, ',', '.') }} = Rp
                                                    {{ number_format($spec['price'], 0, ',', '.') }}
                                                @elseif ($bahan && $spesifikasiProduk && $spesifikasiProduk->spesifikasi)
                                                    {{ number_format($spec['value'], 2, ',', '.') }}
                                                    {{ $spesifikasiProduk->spesifikasi->satuan }}
                                                    x Rp {{ number_format($pricePerUnit, 0, ',', '.') }} = Rp
                                                    {{ number_format($spec['price'], 0, ',', '.') }}
                                                @else
                                                    Rp {{ number_format($spec['price'], 0, ',', '.') }}
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach

                                    {{-- Production Time --}}
                                    @php
                                        $product = \App\Models\Vendor\Produk::with('estimasiProduk.alat')->find(
                                            $item['product_id'],
                                        );
                                        $estimatedTime = $product
                                            ? $product->getEstimatedProductionTime($item['quantity'])
                                            : 0;
                                    @endphp
                                    <div class="flex justify-between items-center border-b border-gray-100 py-2">
                                        <span class="text-sm text-gray-500">Estimasi Waktu Produksi</span>
                                        <span class="text-sm font-medium text-gray-700">{{ $estimatedTime }} menit</span>
                                    </div>

                                    {{-- Equipment Used --}}
                                    <div class="flex justify-between items-center border-b border-gray-100 py-2">
                                        <span class="text-sm text-gray-500">Alat Produksi</span>
                                        <span class="text-sm font-medium text-gray-700">
                                            @if ($product && $product->estimasiProduk)
                                                {{ $product->estimasiProduk->pluck('alat.nama_alat')->filter()->implode(', ') ?: 'Tidak ada alat' }}
                                            @else
                                                Tidak ada alat
                                            @endif
                                        </span>
                                    </div>

                                    {{-- Total Price --}}
                                    <div class="flex justify-between items-center pt-3">
                                        <span class="font-bold text-gray-800">Total Item</span>
                                        <span class="font-bold text-primary">
                                            Rp {{ number_format($item['total_price'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Order Summary --}}
                        <div class="mt-4 pt-4">
                            <div class="bg-white border border-gray-200 rounded-xl p-4">
                                <h5 class="font-bold text-gray-800 mb-3">Ringkasan Pesanan</h5>

                                <div class="flex justify-between items-center border-b border-gray-100 py-2">
                                    <span class="text-sm text-gray-500">Total Item</span>
                                    <span class="text-sm font-medium text-gray-700">{{ count($cartItems) }} item</span>
                                </div>

                                <div class="flex justify-between items-center border-b border-gray-100 py-2">
                                    <span class="text-sm text-gray-500">Total Jumlah</span>
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ collect($cartItems)->sum('quantity') }} pcs
                                    </span>
                                </div>

                                <div class="flex justify-between items-center border-b border-gray-100 py-2">
                                    <span class="text-sm text-gray-500">Total Waktu Produksi</span>
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ collect($cartItems)->sum(function ($item) {
                                            $product = \App\Models\Vendor\Produk::with('estimasiProduk.alat')->find($item['product_id']);
                                            return $product ? $product->getEstimatedProductionTime($item['quantity']) : 0;
                                        }) }}
                                        menit
                                    </span>
                                </div>

                                <div class="flex justify-between items-center pt-3">
                                    <h4 class="font-bold text-gray-800">Total</h4>
                                    <h4 class="font-bold text-primary">
                                        Rp
                                        {{ number_format(collect($cartItems)->sum('total_price'), 0, ',', '.') }}
                                    </h4>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex justify-end gap-3 mt-6">
                            <button class="inline-flex items-center px-4 py-2 border border-danger text-danger rounded-full text-sm font-medium hover:bg-danger/5 transition-colors"
                                type="button" onclick="clearCart()">
                                <i class="fas fa-trash mr-2"></i>Kosongkan Keranjang
                            </button>
                            <button class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-full text-sm font-medium hover:bg-primary/90 transition-colors"
                                type="button" onclick="proceedToCheckout()">
                                <i class="fas fa-shopping-cart mr-2"></i>Lanjut ke Pembayaran
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function removeItem(index) {
            Swal.fire({
                title: 'Hapus Item?',
                text: "Apakah Anda yakin ingin menghapus item ini dari keranjang?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading('Menghapus item...');
                    window.location.href = '{{ route('vendor.pos.removeItem', '__ID__') }}'.replace('__ID__', index);
                }
            });
        }

        function clearCart() {
            Swal.fire({
                title: 'Kosongkan Keranjang?',
                text: "Apakah Anda yakin ingin mengosongkan seluruh keranjang?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, kosongkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading('Mengosongkan keranjang...');
                    window.location.href = "{{ route('vendor.pos.clearCart') }}";
                }
            });
        }

        function proceedToCheckout() {
            showLoading('Melanjutkan ke pembayaran...');
            window.location.href = "{{ route('vendor.pos.checkout') }}";
        }
    </script>
@endsection
