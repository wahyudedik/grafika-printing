@extends('layouts.vendor')

@section('title', 'Detail Produk')
@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6">
            <div>
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Detail Produk</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <!-- Product gallery -->
                                @if (!empty($produk->gambar))
                                    <div x-data="productGallery({{ count($produk->gambar) }})">
                                        <div class="relative rounded-lg overflow-hidden bg-gray-100">
                                            @foreach ($produk->gambar as $index => $image)
                                                <img src="{{ asset($image) }}" alt="{{ $produk->nama_produk }}"
                                                    class="w-full rounded-lg {{ $index === 0 ? '' : 'hidden' }}"
                                                    :class="{ 'hidden': currentSlide !== {{ $index }} }"
                                                    style="max-height: 350px; object-fit: cover;">
                                            @endforeach
                                        </div>
                                        @if (count($produk->gambar) > 1)
                                            <div class="flex items-center justify-between mt-3">
                                                <button @click="prev()" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                                    <i class="fas fa-chevron-left"></i>
                                                </button>
                                                <div class="flex gap-2">
                                                    @foreach ($produk->gambar as $index => $image)
                                                        <button @click="currentSlide = {{ $index }}"
                                                            class="w-2 h-2 rounded-full transition-colors"
                                                            :class="currentSlide === {{ $index }} ? 'bg-primary' : 'bg-gray-300'">
                                                        </button>
                                                    @endforeach
                                                </div>
                                                <button @click="next()" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                                    <i class="fas fa-chevron-right"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex flex-col items-center justify-center py-12 bg-gray-50 rounded-lg">
                                        <i class="fas fa-image text-6xl text-gray-300 mb-4"></i>
                                        <p class="text-sm text-gray-500">Tidak ada gambar produk</p>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <div class="mb-3">
                                    <h2 class="text-xl font-bold text-gray-900">{{ $produk->nama_produk }}</h2>
                                    <span class="inline-block mt-1 px-2 py-1 text-xs font-medium rounded-full bg-primary/10 text-primary">{{ $produk->kategori->nama_kategori }}</span>
                                </div>

                                <div class="mb-3">
                                    <h4 class="text-sm font-semibold text-gray-900">Deskripsi</h4>
                                    <p class="text-sm text-gray-600">{{ $produk->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Specifications Section -->
                        <div class="mt-6">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Spesifikasi Produk</h4>
                            @if ($produk->spesifikasiProduk->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Spesifikasi</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe Input</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wajib Diisi</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pilihan</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bahan Digunakan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($produk->spesifikasiProduk as $spec)
                                                <tr>
                                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $spec->spesifikasi->nama_spesifikasi }}</td>
                                                    <td class="px-6 py-4 text-sm text-gray-500">{{ ucfirst($spec->spesifikasi->tipe_input) }}</td>
                                                    <td class="px-6 py-4">
                                                        @if ($spec->wajib_diisi)
                                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Ya</span>
                                                        @else
                                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-500">Tidak</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-500">
                                                        @if (!empty($spec->pilihan))
                                                            <ul class="list-disc list-inside">
                                                                @foreach ($spec->pilihan as $pilihan)
                                                                    <li>{{ $pilihan }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <span class="text-gray-400">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-500">
                                                        @if ($spec->bahanSpesifikasiProduk->count() > 0)
                                                            <ul class="list-disc list-inside">
                                                                @foreach ($spec->bahanSpesifikasiProduk as $bahan)
                                                                    <li>{{ $bahan->nama_bahan }} ({{ $bahan->satuan }})</li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <span class="text-gray-400">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <p class="text-sm text-blue-700">Produk ini belum memiliki spesifikasi.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Production Estimates Section -->
                        <div class="mt-6">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Estimasi Produksi</h4>
                            @if ($produk->estimasiProduk->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alat</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Persiapan</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Produksi per Unit</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($produk->estimasiProduk as $estimasi)
                                                <tr>
                                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $estimasi->alat->nama_alat }}</td>
                                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $estimasi->waktu_persiapan }} menit</td>
                                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $estimasi->waktu_produksi_per_unit }} menit</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <p class="text-sm text-blue-700">Produk ini belum memiliki estimasi produksi.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                        <a href="{{ route('vendor.products.edit', $produk->id) }}"
                            class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                            <i class="fas fa-edit"></i>
                            Edit
                        </a>
                        <a href="{{ route('vendor.products.index') }}"
                            class="inline-flex items-center gap-2 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
                            <i class="fas fa-arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function productGallery(totalSlides) {
                return {
                    currentSlide: 0,
                    total: totalSlides,
                    next() {
                        this.currentSlide = (this.currentSlide + 1) % this.total;
                    },
                    prev() {
                        this.currentSlide = (this.currentSlide - 1 + this.total) % this.total;
                    }
                }
            }
        </script>
    @endpush
@endsection
