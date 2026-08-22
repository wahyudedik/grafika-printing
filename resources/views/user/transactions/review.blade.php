@extends('layouts.user')

@section('title', 'Beri Ulasan Pesanan #' . $transaksi->kode)

@section('content')
    {{-- Breadcrumbs --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6 print:hidden">
        <a href="{{ route('user.dashboard') }}" class="hover:text-primary-600 transition-colors">Beranda</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('user.transactions.index') }}" class="hover:text-primary-600 transition-colors">Riwayat Pesanan</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('user.transactions.show', $transaksi->id) }}" class="hover:text-primary-600 transition-colors">Detail</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-900 font-medium">Beri Ulasan</span>
    </nav>

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Beri Ulasan</h1>
        <p class="text-sm text-gray-500 mt-1">Beri penilaian Anda untuk transaksi dari <span class="font-medium text-gray-700">{{ $transaksi->vendor->name ?? 'Vendor' }}</span></p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column — Form Review --}}
        <div class="lg:col-span-2">
            <form action="{{ route('user.transactions.review.store', $transaksi->id) }}" method="POST">
                @csrf
                @method('POST')

                <x-ui.card>
                    <div class="p-6 space-y-6">
                        {{-- Error Messages --}}
                        @if($errors->any())
                            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-exclamation-circle text-red-500"></i>
                                    <span class="text-sm font-medium text-red-800">Terjadi kesalahan:</span>
                                </div>
                                <ul class="mt-2 text-sm text-red-600 list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Overall Rating --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Penilaian Keseluruuhan <span class="text-red-500">*</span></label>
                            <div x-data="{ rating: {{ old('rating', 0) }}, hover: 0 }">
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button"
                                            @click="rating = {{ $i }}"
                                            @mouseenter="hover = {{ $i }}"
                                            @mouseleave="hover = 0"
                                            class="text-3xl transition-colors focus:outline-none"
                                            :class="(hover >= {{ $i }}) ? 'text-yellow-400' : ((rating >= {{ $i }}) ? 'text-yellow-400' : 'text-gray-300')">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" :value="rating" x-model="rating">
                                <p class="text-xs text-gray-400 mt-1" x-show="rating > 0" x-transition>
                                    <span x-text="rating === 1 ? 'Sangat Buruk' : (rating === 2 ? 'Buruk' : (rating === 3 ? 'Cukup' : (rating === 4 ? 'Bagus' : 'Sangat Bagus')))"></span>
                                </p>
                            </div>
                            @error('rating')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <hr class="border-gray-100">

                        {{-- Detailed Ratings --}}
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-gray-900">Penilaian Detail (Opsional)</h3>

                            {{-- Quality Rating --}}
                            <div>
                                <label class="block text-sm text-gray-700 mb-1.5">Kualitas Produk</label>
                                <div x-data="{ rating: {{ old('quality_rating', 0) }}, hover: 0 }">
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button"
                                                @click="rating = {{ $i }}"
                                                @mouseenter="hover = {{ $i }}"
                                                @mouseleave="hover = 0"
                                                class="text-xl transition-colors focus:outline-none"
                                                :class="(hover >= {{ $i }}) ? 'text-yellow-400' : ((rating >= {{ $i }}) ? 'text-yellow-400' : 'text-gray-300')">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="quality_rating" :value="rating" x-model="rating">
                                </div>
                            </div>

                            {{-- Speed Rating --}}
                            <div>
                                <label class="block text-sm text-gray-700 mb-1.5">Kecepatan Layanan</label>
                                <div x-data="{ rating: {{ old('speed_rating', 0) }}, hover: 0 }">
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button"
                                                @click="rating = {{ $i }}"
                                                @mouseenter="hover = {{ $i }}"
                                                @mouseleave="hover = 0"
                                                class="text-xl transition-colors focus:outline-none"
                                                :class="(hover >= {{ $i }}) ? 'text-yellow-400' : ((rating >= {{ $i }}) ? 'text-yellow-400' : 'text-gray-300')">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="speed_rating" :value="rating" x-model="rating">
                                </div>
                            </div>

                            {{-- Service Rating --}}
                            <div>
                                <label class="block text-sm text-gray-700 mb-1.5">Kualitas Pelayanan</label>
                                <div x-data="{ rating: {{ old('service_rating', 0) }}, hover: 0 }">
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button"
                                                @click="rating = {{ $i }}"
                                                @mouseenter="hover = {{ $i }}"
                                                @mouseleave="hover = 0"
                                                class="text-xl transition-colors focus:outline-none"
                                                :class="(hover >= {{ $i }}) ? 'text-yellow-400' : ((rating >= {{ $i }}) ? 'text-yellow-400' : 'text-gray-300')">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="service_rating" :value="rating" x-model="rating">
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        {{-- Comment --}}
                        <div>
                            <label for="comment" class="block text-sm font-semibold text-gray-900 mb-2">Tulis Ulasan Anda</label>
                            <textarea
                                id="comment"
                                name="comment"
                                rows="4"
                                maxlength="1000"
                                placeholder="Ceritakan pengalaman Anda dengan vendor ini..."
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                            >{{ old('comment') }}</textarea>
                            <div class="flex justify-end mt-1">
                                <span class="text-xs text-gray-400" x-data="{ len: '{{ old('comment', '') }}'.length }" x-init="$el.textContent = len + '/1000'"></span>
                            </div>
                            @error('comment')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Kirim Ulasan
                            </button>
                            <a href="{{ route('user.transactions.show', $transaksi->id) }}" class="inline-flex items-center px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                                Batal
                            </a>
                        </div>
                    </div>
                </x-ui.card>
            </form>
        </div>

        {{-- Right Column — Sidebar --}}
        <div class="space-y-6">
            {{-- Transaksi Info --}}
            <x-ui.card>
                <div class="p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Informasi Pesanan</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Kode Pesanan</span>
                            <span class="text-sm font-medium text-gray-900">#{{ $transaksi->kode }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total</span>
                            <span class="text-sm font-bold text-primary-600">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            {{-- Items List --}}
            <x-ui.card>
                <div class="p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Item yang Dibeli</h3>
                    <div class="space-y-3">
                        @foreach($transaksi->transaksiItem as $item)
                            <div class="flex items-center gap-3">
                                @if($item->produk && $item->produk->gambar)
                                    <img src="{{ asset('produk_gambar/' . $item->produk->gambar) }}" alt="{{ $item->produk->nama ?? 'Produk' }}" class="w-10 h-10 rounded-lg object-cover border border-gray-200">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-box text-gray-400 text-sm"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-gray-900 truncate">{{ $item->produk->nama ?? 'Produk' }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->kuantitas }} × Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-ui.card>

            {{-- Vendor Info --}}
            <x-ui.card>
                <div class="p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Vendor</h3>
                    <div class="flex items-center gap-3">
                        @if($transaksi->vendor && $transaksi->vendor->logo)
                            <img src="{{ asset('vendors_logo/' . $transaksi->vendor->logo) }}" alt="{{ $transaksi->vendor->name }}" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                        @else
                            <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                                <span class="text-sm font-bold text-primary-700">{{ strtoupper(substr($transaksi->vendor->name ?? 'V', 0, 1)) }}</span>
                            </div>
                        @endif
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $transaksi->vendor->name ?? '-' }}</div>
                            @if($transaksi->vendor && $transaksi->vendor->address)
                                <div class="text-xs text-gray-500 mt-0.5">{{ Str::limit($transaksi->vendor->address, 40) }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
@endsection
