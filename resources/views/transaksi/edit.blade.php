@extends('layouts.vendor')

@section('title', 'Edit Transaksi')
@section('content')
    <div class="max-w-6xl mx-auto">
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
                    <div>
                        <h4 class="text-sm font-semibold text-red-800">Error!</h4>
                        <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('vendor.transactions.update', $transaksi->id) }}" method="POST"
            class="bg-white rounded-xl shadow-sm" id="transaction-form" onsubmit="showLoading('Memperbarui transaksi...')">
            @csrf
            @method('PUT')
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Edit Transaksi: {{ $transaksi->kode }}</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Customer Section --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Pelanggan <span class="text-red-500">*</span>
                        </label>
                        <select name="pelanggan_id" required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pelanggan_id') border-red-500 @enderror">
                            <option value="">Pilih Pelanggan</option>
                            @foreach ($pelanggans as $pelanggan)
                                <option value="{{ $pelanggan->id }}"
                                    {{ old('pelanggan_id', $transaksi->pelanggan_id) == $pelanggan->id ? 'selected' : '' }}>
                                    {{ $pelanggan->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('pelanggan_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Payment Method Section --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Metode Pembayaran <span class="text-red-500">*</span>
                        </label>
                        <select name="payment_method" required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('payment_method') border-red-500 @enderror">
                            <option value="">Pilih Metode Pembayaran</option>
                            @foreach ($paymentMethods as $value => $label)
                                <option value="{{ $value }}"
                                    {{ old('payment_method', $transaksi->payment_method) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status Section --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}"
                                    {{ old('status', $transaksi->status) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Estimated Date Section --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Estimasi Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="estimasi_selesai" required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('estimasi_selesai') border-red-500 @enderror"
                            value="{{ old('estimasi_selesai', $transaksi->estimasi_selesai->format('Y-m-d')) }}">
                        @error('estimasi_selesai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Notes Section --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="catatan" rows="3"
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('catatan') border-red-500 @enderror"
                            placeholder="Masukkan catatan transaksi">{{ old('catatan', $transaksi->catatan) }}</textarea>
                        @error('catatan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Transaction Items Section --}}
                <div class="mt-8">
                    <h4 class="text-base font-semibold text-gray-900">Item Transaksi</h4>
                    <p class="text-sm text-gray-500 mt-1">Edit produk yang dibeli dalam transaksi ini</p>

                    <div id="items-container" class="mt-4 space-y-4">
                        {{-- Existing Items --}}
                        @foreach ($transaksi->transaksiItem as $index => $item)
                            <div class="border border-gray-200 rounded-xl p-4 relative item-row"
                                id="item-row-existing-{{ $item->id }}">
                                <button type="button"
                                    class="absolute top-3 right-3 p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    onclick="removeItemRow('item-row-existing-{{ $item->id }}')">
                                    <i class="fas fa-times"></i>
                                </button>

                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                    <div class="md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Produk <span class="text-red-500">*</span></label>
                                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 product-select"
                                            name="items[{{ $index }}][produk_id]" required
                                            onchange="loadProductSpecifications('item-row-existing-{{ $item->id }}', this.value, true)">
                                            <option value="">Pilih Produk</option>
                                            @foreach ($produks as $produk)
                                                <option value="{{ $produk->id }}" data-product='@json($produk)'
                                                    {{ $item->produk_id == $produk->id ? 'selected' : '' }}>
                                                    {{ $produk->nama_produk }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Kuantitas <span class="text-red-500">*</span></label>
                                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 item-quantity"
                                            name="items[{{ $index }}][kuantitas]" min="1" value="{{ $item->kuantitas }}" required
                                            onchange="calculateSubtotal('item-row-existing-{{ $item->id }}')">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan <span class="text-red-500">*</span></label>
                                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 item-price"
                                            name="items[{{ $index }}][harga_satuan]" min="0" step="0.01"
                                            value="{{ $item->harga_satuan }}" required
                                            onchange="calculateSubtotal('item-row-existing-{{ $item->id }}')">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                                        <div class="flex">
                                            <span class="inline-flex items-center px-3 border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-l-lg">Rp</span>
                                            <input type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-r-lg text-sm bg-gray-50 item-subtotal" readonly
                                                value="{{ number_format($item->kuantitas * $item->harga_satuan, 0, ',', '.') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <div id="item-row-existing-{{ $item->id }}-specifications" class="specifications-container">
                                        @if ($item->transaksiItemSpecifications->count() > 0)
                                            <div class="mt-3 bg-gray-50 rounded-lg p-4">
                                                <h5 class="text-sm font-semibold text-gray-900 mb-3">Spesifikasi Produk</h5>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    @foreach ($item->transaksiItemSpecifications as $spec)
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                                {{ $spec->spesifikasiProduk->spesifikasi->nama_spesifikasi ?? 'Spesifikasi' }}
                                                            </label>
                                                            <input type="text"
                                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                                name="items[{{ $index }}][specifications][{{ $spec->spesifikasi_produk_id }}][value]"
                                                                value="{{ $spec->value }}"
                                                                {{ $spec->spesifikasiProduk->wajib_diisi ? 'required' : '' }}>

                                                            <input type="hidden"
                                                                name="items[{{ $index }}][specifications][{{ $spec->spesifikasi_produk_id }}][input_type]"
                                                                value="{{ $spec->input_type }}">
                                                            <input type="hidden"
                                                                name="items[{{ $index }}][specifications][{{ $spec->spesifikasi_produk_id }}][price]"
                                                                value="{{ $spec->price }}">

                                                            @if ($spec->bahan)
                                                                <div class="mt-2">
                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Bahan</label>
                                                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                                        name="items[{{ $index }}][specifications][{{ $spec->spesifikasi_produk_id }}][bahan_id]">
                                                                        <option value="">Pilih Bahan</option>
                                                                        @foreach ($spec->spesifikasiProduk->bahanSpesifikasiProduk as $bahan)
                                                                            <option value="{{ $bahan->id }}"
                                                                                {{ $spec->bahan_id == $bahan->id ? 'selected' : '' }}>
                                                                                {{ $bahan->nama_bahan }} ({{ $bahan->satuan }})
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div id="new-items-container" class="mt-4 space-y-4">
                        {{-- New items will be added here --}}
                    </div>

                    <div class="mt-3">
                        <button type="button" id="add-item-row"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-blue-600 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-50 transition-colors">
                            <i class="fas fa-plus"></i> Tambah Item Baru
                        </button>
                    </div>
                </div>

                {{-- Total Section --}}
                <div class="mt-6 border-t border-gray-200 pt-4">
                    <div class="flex justify-end">
                        <div class="text-right">
                            <span class="text-sm text-gray-500">Total:</span>
                            <div class="text-xl font-bold text-gray-900" id="total-display">
                                Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                            </div>
                            <input type="hidden" name="total_harga" id="total-input" value="{{ $transaksi->total_harga }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-200 px-6 py-4 flex items-center justify-end gap-3">
                <a href="{{ route('vendor.transactions.show', $transaksi->id) }}"
                    class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times mr-1"></i> Batal
                </a>
                <button type="submit"
                    class="px-4 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const newItemsContainer = document.getElementById('new-items-container');
                const addItemButton = document.getElementById('add-item-row');
                let itemRowCount = {{ count($transaksi->transaksiItem) }};

                const products = @json($produks);

                addItemButton.addEventListener('click', function() {
                    addItemRow();
                });

                function addItemRow() {
                    const rowId = `item-row-new-${itemRowCount}`;

                    let productOptions = '';
                    products.forEach(product => {
                        productOptions +=
                            `<option value="${product.id}" data-product='${JSON.stringify(product)}'>${product.nama_produk}</option>`;
                    });

                    const html = `
                    <div class="border border-gray-200 rounded-xl p-4 relative item-row" id="${rowId}">
                        <button type="button"
                            class="absolute top-3 right-3 p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                            onclick="removeItemRow('${rowId}')">
                            <i class="fas fa-times"></i>
                        </button>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            <div class="md:col-span-6">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Produk <span class="text-red-500">*</span></label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 product-select"
                                    name="items[${itemRowCount}][produk_id]" required
                                    onchange="loadProductSpecifications('${rowId}', this.value)">
                                    <option value="">Pilih Produk</option>
                                    ${productOptions}
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kuantitas <span class="text-red-500">*</span></label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 item-quantity"
                                    name="items[${itemRowCount}][kuantitas]" min="1" value="1" required
                                    onchange="calculateSubtotal('${rowId}')">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan <span class="text-red-500">*</span></label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 item-price"
                                    name="items[${itemRowCount}][harga_satuan]" min="0" step="0.01" value="0" required
                                    onchange="calculateSubtotal('${rowId}')">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-l-lg">Rp</span>
                                    <input type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-r-lg text-sm bg-gray-50 item-subtotal" readonly value="0">
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div id="${rowId}-specifications" class="specifications-container"></div>
                        </div>
                    </div>
                    `;

                    newItemsContainer.insertAdjacentHTML('beforeend', html);
                    itemRowCount++;
                    calculateTotal();
                }

                // Initialize existing items
                document.querySelectorAll('.item-row').forEach(row => {
                    const productSelect = row.querySelector('.product-select');
                    if (productSelect && productSelect.value) {
                        calculateSubtotal(row.id);
                    }
                });

                window.removeItemRow = function(rowId) {
                    document.getElementById(rowId).remove();
                    calculateTotal();
                };

                window.loadProductSpecifications = function(rowId, productId, isExisting = false) {
                    const specContainer = document.getElementById(`${rowId}-specifications`);
                    specContainer.innerHTML = '';

                    if (!productId) return;

                    const product = products.find(p => p.id == productId);
                    if (!product || !product.spesifikasi_produk || product.spesifikasi_produk.length === 0) {
                        return;
                    }

                    let html = '<div class="mt-3 bg-gray-50 rounded-lg p-4"><h5 class="text-sm font-semibold text-gray-900 mb-3">Spesifikasi Produk</h5><div class="grid grid-cols-1 md:grid-cols-2 gap-4">';

                    product.spesifikasi_produk.forEach((spec, index) => {
                        const specName = spec.spesifikasi ? spec.spesifikasi.nama_spesifikasi : 'Spesifikasi';
                        const inputType = spec.spesifikasi ? spec.spesifikasi.tipe_input : 'text';
                        const required = spec.wajib_diisi ? 'required' : '';
                        const requiredMark = spec.wajib_diisi ? '<span class="text-red-500">*</span>' : '';

                        let itemIndex;
                        if (isExisting) {
                            const match = rowId.match(/item-row-existing-(\d+)/);
                            itemIndex = match ? match[1] : 0;
                        } else {
                            const match = rowId.match(/item-row-new-(\d+)/);
                            itemIndex = match ? match[1] : 0;
                        }

                        html += `<div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">${specName} ${requiredMark}</label>`;

                        if (inputType === 'select' || inputType === 'radio') {
                            if (inputType === 'select') {
                                html += `<select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    name="items[${itemIndex}][specifications][${spec.id}][value]" ${required}>
                                    <option value="">Pilih ${specName}</option>`;

                                if (spec.pilihan && spec.pilihan.length > 0) {
                                    spec.pilihan.forEach(option => {
                                        html += `<option value="${option}">${option}</option>`;
                                    });
                                }

                                html += `</select>`;
                            } else {
                                if (spec.pilihan && spec.pilihan.length > 0) {
                                    spec.pilihan.forEach((option, optIndex) => {
                                        html += `
                                        <div class="flex items-center gap-2 mt-1">
                                            <input type="radio" class="text-blue-600 focus:ring-blue-500"
                                                name="items[${itemIndex}][specifications][${spec.id}][value]"
                                                id="${rowId}-spec-${spec.id}-option-${optIndex}" value="${option}" ${optIndex === 0 && required ? 'checked' : ''}>
                                            <label class="text-sm text-gray-700" for="${rowId}-spec-${spec.id}-option-${optIndex}">${option}</label>
                                        </div>`;
                                    });
                                }
                            }

                            if (spec.bahan_spesifikasi_produk && spec.bahan_spesifikasi_produk.length > 0) {
                                html += `
                                <div class="mt-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Bahan</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        name="items[${itemIndex}][specifications][${spec.id}][bahan_id]">
                                        <option value="">Pilih Bahan</option>`;

                                spec.bahan_spesifikasi_produk.forEach(bahan => {
                                    html += `<option value="${bahan.id}">${bahan.nama_bahan} (${bahan.satuan})</option>`;
                                });

                                html += `</select></div>`;
                            }
                        } else if (inputType === 'number') {
                            html += `<input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                name="items[${itemIndex}][specifications][${spec.id}][value]" placeholder="Masukkan ${specName}" ${required}>`;
                        } else {
                            html += `<input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                name="items[${itemIndex}][specifications][${spec.id}][value]" placeholder="Masukkan ${specName}" ${required}>`;
                        }

                        html += `
                            <input type="hidden" name="items[${itemIndex}][specifications][${spec.id}][input_type]" value="${inputType}">
                            <input type="hidden" name="items[${itemIndex}][specifications][${spec.id}][price]" value="0">
                        </div>`;
                    });

                    html += '</div></div>';
                    specContainer.innerHTML = html;

                    if (!isExisting) {
                        const priceInput = document.querySelector(`#${rowId} .item-price`);
                        if (priceInput && product.harga) {
                            priceInput.value = product.harga;
                            calculateSubtotal(rowId);
                        }
                    }
                };

                window.calculateSubtotal = function(rowId) {
                    const row = document.getElementById(rowId);
                    const quantityInput = row.querySelector('.item-quantity');
                    const priceInput = row.querySelector('.item-price');
                    const subtotalInput = row.querySelector('.item-subtotal');

                    const quantity = parseFloat(quantityInput.value) || 0;
                    const price = parseFloat(priceInput.value) || 0;
                    const subtotal = quantity * price;

                    subtotalInput.value = subtotal.toLocaleString('id-ID');
                    calculateTotal();
                };

                window.calculateTotal = function() {
                    const subtotalInputs = document.querySelectorAll('.item-subtotal');
                    let total = 0;

                    subtotalInputs.forEach(input => {
                        const value = parseFloat(input.value.replace(/\./g, '').replace(',', '.')) || 0;
                        total += value;
                    });

                    document.getElementById('total-display').innerText = 'Rp ' + total.toLocaleString('id-ID');
                    document.getElementById('total-input').value = total;
                };

                const form = document.getElementById('transaction-form');
                form.addEventListener('submit', function(e) {
                    const itemRows = document.querySelectorAll('.item-row');

                    if (itemRows.length === 0) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Error!',
                            text: 'Transaksi harus memiliki minimal 1 item',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    return true;
                });

                calculateTotal();
            });
        </script>
    @endpush
@endsection
