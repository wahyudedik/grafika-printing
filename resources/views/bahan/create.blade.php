@extends('layouts.vendor')

@section('title', 'Tambah Bahan')
@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6">
            <div>
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <h4 class="text-sm font-medium text-red-800 mb-2">Error!</h4>
                        <ul class="list-disc list-inside text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('vendor.materials.store') }}" method="POST"
                    onsubmit="showLoading('Menambahkan bahan...')">
                    @csrf
                    <div class="bg-white rounded-xl shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Tambah Bahan Baru</h3>
                        </div>
                        <div class="px-6 py-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Nama Bahan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nama_bahan" value="{{ old('nama_bahan') }}"
                                        placeholder="Masukkan nama bahan"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm @error('nama_bahan') border-red-500 @enderror">
                                    @error('nama_bahan')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Satuan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="satuan" value="{{ old('satuan') }}"
                                        placeholder="Contoh: lembar, meter, kg"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm @error('satuan') border-red-500 @enderror">
                                    @error('satuan')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Harga Pokok Produksi (HPP) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex">
                                        <span class="inline-flex items-center px-3 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg text-sm text-gray-600">Rp</span>
                                        <input type="number" step="0.01" name="hpp" value="{{ old('hpp') }}"
                                            placeholder="Masukkan HPP bahan"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm @error('hpp') border-red-500 @enderror">
                                    </div>
                                    @error('hpp')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Stok <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="stok" value="{{ old('stok', 0) }}"
                                        placeholder="Masukkan jumlah stok"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm @error('stok') border-red-500 @enderror">
                                    @error('stok')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Wholesale Prices Section -->
                            <div class="mt-6">
                                <h4 class="text-sm font-semibold text-gray-900">Harga Grosir (Opsional)</h4>
                                <p class="text-sm text-gray-500 mb-3">Tambahkan tier harga grosir untuk pembelian dalam jumlah banyak</p>

                                <div id="wholesale-container">
                                    <!-- Dynamic rows will be added here -->
                                </div>

                                <div class="mt-3">
                                    <button type="button" class="inline-flex items-center gap-2 border border-primary text-primary px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/5 transition-colors" id="add-wholesale-row">
                                        <i class="fas fa-plus"></i>
                                        Tambah Tier Harga Grosir
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                            <button type="submit"
                                class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                                <i class="fas fa-save"></i>
                                Simpan
                            </button>
                            <a href="{{ route('vendor.materials.index') }}"
                                class="inline-flex items-center gap-2 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
                                <i class="fas fa-times"></i>
                                Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('wholesale-container');
                const addButton = document.getElementById('add-wholesale-row');
                let rowCount = 0;

                addButton.addEventListener('click', function() {
                    addWholesalePriceRow();
                });

                function addWholesalePriceRow(minQty = '', maxQty = '', price = '') {
                    const rowId = `wholesale-row-${rowCount}`;
                    const html = `
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mb-3 wholesale-row items-end" id="${rowId}">
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Qty</label>
                            <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm" name="wholesale_min_qty[]" value="${minQty}" placeholder="Min quantity" min="1">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Maximum Qty</label>
                            <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm" name="wholesale_max_qty[]" value="${maxQty}" placeholder="Max quantity or leave empty">
                            <p class="mt-1 text-xs text-gray-500">Kosongkan untuk unlimited</p>
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                            <input type="number" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm" name="wholesale_price[]" value="${price}" placeholder="Wholesale price">
                        </div>
                        <div class="md:col-span-2 flex items-end">
                            <button type="button" class="inline-flex items-center gap-1 border border-red-500 text-red-500 px-3 py-2 rounded-lg text-sm font-medium hover:bg-red-50 transition-colors remove-row" data-row="${rowId}">
                                <i class="fas fa-times"></i>
                                Hapus
                            </button>
                        </div>
                    </div>
                `;

                    container.insertAdjacentHTML('beforeend', html);
                    rowCount++;

                    document.querySelector(`#${rowId} .remove-row`).addEventListener('click', function() {
                        document.getElementById(rowId).remove();
                    });
                }
            });
        </script>
    @endpush
@endsection
