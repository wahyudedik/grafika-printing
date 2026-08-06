@extends('layouts.vendor')

@section('title', 'Detail Bahan')
@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6">
            <div>
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Detail Bahan</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Bahan</label>
                                <div class="text-sm text-gray-900">{{ $bahan->nama_bahan }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Satuan</label>
                                <div class="text-sm text-gray-900">{{ $bahan->satuan }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Harga Pokok Produksi (HPP)</label>
                                <div class="text-sm text-gray-900">Rp {{ number_format((float) $bahan->hpp, 0, ',', '.') }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Stok</label>
                                <div>
                                    @if ($bahan->stok == 0)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Habis</span>
                                    @elseif($bahan->stok < 10)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">Rendah ({{ $bahan->stok }})</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">{{ $bahan->stok }}</span>
                                    @endif
                                </div>
                            </div>

                            @if ($bahan->vendor)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Vendor/Supplier</label>
                                    <div class="text-sm text-gray-900">{{ $bahan->vendor->name }}</div>
                                </div>
                            @endif
                        </div>

                        <!-- Wholesale Prices Section -->
                        @if ($bahan->wholesalePrices && $bahan->wholesalePrices->count() > 0)
                            <div class="mt-6">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">Harga Grosir</h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Quantity</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Quantity</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($bahan->wholesalePrices()->orderBy('min_quantity', 'asc')->get() as $price)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $price->min_quantity }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $price->max_quantity ?? 'Unlimited' }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format((float) $price->harga, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-info-circle text-blue-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-blue-800">Tidak ada harga grosir untuk bahan ini</h4>
                                        <p class="text-sm text-blue-600">Harga bahan akan menggunakan HPP standar untuk semua kuantitas.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                        <a href="{{ route('vendor.materials.edit', $bahan->id) }}"
                            class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                            <i class="fas fa-edit"></i>
                            Edit
                        </a>
                        <a href="{{ route('vendor.materials.index') }}"
                            class="inline-flex items-center gap-2 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
                            <i class="fas fa-arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
