@extends('layouts.vendor')

@section('title', 'Tambah Transaksi')
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

        <form action="{{ route('vendor.transactions.store') }}" method="POST" class="bg-white rounded-xl shadow-sm"
            id="transaction-form" onsubmit="return validateForm()">
            @csrf
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Tambah Transaksi Baru</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Customer Section --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Pelanggan <span class="text-red-500">*</span>
                        </label>
                        <div class="flex">
                            <select name="pelanggan_id" id="pelanggan-select" required
                                class="flex-1 px-3 py-2.5 border border-gray-300 rounded-l-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pelanggan_id') border-red-500 @enderror">
                                <option value="">Pilih Pelanggan</option>
                                @foreach ($pelanggans as $pelanggan)
                                    <option value="{{ $pelanggan->id }}"
                                        {{ old('pelanggan_id') == $pelanggan->id ? 'selected' : '' }}>
                                        {{ $pelanggan->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <a href="{{ route('vendor.customers.create') }}" target="_blank"
                                class="inline-flex items-center px-3 border border-l-0 border-gray-300 rounded-r-lg bg-gray-50 text-gray-500 hover:bg-gray-100 transition-colors"
                                title="Tambah Pelanggan Baru">
                                <i class="fas fa-plus"></i>
                            </a>
                        </div>
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
                                <option value="{{ $value }}" {{ old('payment_method') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('payment_method')
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
                            value="{{ old('estimasi_selesai', date('Y-m-d', strtotime('+3 days'))) }}">
                        @error('estimasi_selesai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Notes Section --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="catatan" rows="3"
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('catatan') border-red-500 @enderror"
                            placeholder="Masukkan catatan transaksi">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Transaction Items Section --}}
                <div class="mt-8">
                    <h4 class="text-base font-semibold text-gray-900">Item Transaksi</h4>
                    <p class="text-sm text-gray-500 mt-1">Tambahkan produk yang dibeli dalam transaksi ini</p>

                    <div id="items-container" class="mt-4 space-y-4">
                        {{-- Dynamic rows will be added here --}}
                    </div>

                    <div class="mt-3">
                        <button type="button" id="add-item-row"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-blue-600 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-50 transition-colors">
                            <i class="fas fa-plus"></i> Tambah Item
                        </button>
                    </div>
                </div>

                {{-- Total Section --}}
                <div class="mt-6 border-t border-gray-200 pt-4">
                    <div class="flex justify-end">
                        <div class="text-right">
                            <span class="text-sm text-gray-500">Total:</span>
                            <div class="text-xl font-bold text-gray-900" id="total-display">Rp 0</div>
                            <input type="hidden" name="total_harga" id="total-input" value="0">
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-200 px-6 py-4 flex items-center justify-end gap-3">
                <a href="{{ route('vendor.transactions.index') }}"
                    class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times mr-1"></i> Batal
                </a>
                <button type="submit"
                    class="px-4 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const itemsContainer = document.getElementById('items-container');
                const addItemButton = document.getElementById('add-item-row');
                let itemRowCount = 0;

                // Products data for dropdown and specifications
                const products = @json($produks);

                // Add item row
                addItemButton.addEventListener('click', function() {
                    addItemRow();
                });

                function addItemRow() {
                    const rowId = `item-row-${itemRowCount}`;

                    let productOptions = '<option value="">Pilih Produk</option>';
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
                            <div id="${rowId}-specifications" class="specifications-container">
                                <!-- Specifications will be loaded here -->
                            </div>
                        </div>
                    </div>
                    `;

                    itemsContainer.insertAdjacentHTML('beforeend', html);
                    itemRowCount++;
                    calculateTotal();
                }

                // Add at least one item row by default
                addItemRow();

                // Make functions available globally
                window.removeItemRow = function(rowId) {
                    document.getElementById(rowId).remove();
                    calculateTotal();
                };

                window.loadProductSpecifications = function(rowId, productId) {
                    const specContainer = document.getElementById(`${rowId}-specifications`);
                    specContainer.innerHTML = '';

                    if (!productId) return;

                    const selectEl = document.querySelector(`#${rowId} .product-select`);
                    const selectedOption = selectEl.options[selectEl.selectedIndex];

                    if (!selectedOption) return;

                    let product;
                    try {
                        product = JSON.parse(selectedOption.dataset.product);
                    } catch (e) {
                        console.error("Error parsing product data:", e);
                        return;
                    }

                    if (!product || !product.spesifikasi_produk || product.spesifikasi_produk.length === 0) {
                        return;
                    }

                    let html = '<div class="mt-3 bg-gray-50 rounded-lg p-4"><h5 class="text-sm font-semibold text-gray-900 mb-3">Spesifikasi Produk</h5><div class="grid grid-cols-1 md:grid-cols-2 gap-4">';

                    product.spesifikasi_produk.forEach((spec, index) => {
                        if (!spec.spesifikasi) return;

                        const specName = spec.spesifikasi.nama_spesifikasi || 'Spesifikasi';
                        const inputType = spec.spesifikasi.tipe_input || 'text';
                        const required = spec.wajib_diisi ? 'required' : '';
                        const requiredMark = spec.wajib_diisi ? '<span class="text-red-500">*</span>' : '';
                        const itemIndex = rowId.split('-')[2];

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

                    const priceInput = document.querySelector(`#${rowId} .item-price`);
                    if (priceInput && product.harga) {
                        priceInput.value = product.harga;
                        calculateSubtotal(rowId);
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

                window.validateForm = function() {
                    const itemRows = document.querySelectorAll('.item-row');

                    if (itemRows.length === 0) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Transaksi harus memiliki minimal 1 item',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    let isValid = true;

                    itemRows.forEach(row => {
                        const requiredInputs = row.querySelectorAll('input[required], select[required]');
                        requiredInputs.forEach(input => {
                            if (!input.value) {
                                input.classList.add('border-red-500');
                                isValid = false;
                            } else {
                                input.classList.remove('border-red-500');
                            }
                        });
                    });

                    if (!isValid) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Mohon lengkapi semua field yang wajib diisi',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    Swal.fire({
                        title: 'Sedang Memproses',
                        text: 'Mohon tunggu...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    return true;
                };
            });
        </script>
    @endpush
@endsection
