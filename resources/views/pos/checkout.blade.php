@extends('layouts.vendor')

@section('title', 'Pembayaran')

@section('content')
    {{-- Add CSRF token meta tag --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Alpine.js x-data for modal --}}
    <div x-data="{ showModal: false, isSubmitting: false }">

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

        <div class="px-4 mt-4 pb-4">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <form id="checkoutForm" action="{{ route('vendor.pos.checkout') }}" method="POST" data-no-loading>
                        @csrf
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            {{-- Left Column: Customer Info --}}
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pelanggan</h4>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Pelanggan</label>
                                    <div class="flex gap-2">
                                        <select name="pelanggan_id" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                                            required id="customerSelect">
                                            <option value="">Pilih Pelanggan</option>
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->nama }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="inline-flex items-center px-4 py-3 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors"
                                            @click="showModal = true">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                                    <select name="payment_method" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                                        required id="paymentMethodSelect">
                                        <option value="cash">Cash</option>
                                        <option value="transfer">Bank Transfer</option>
                                        <option value="qris">QRIS</option>
                                    </select>
                                </div>

                                {{-- Payment Amount (Cash only) --}}
                                <div class="mb-4" id="paymentAmountContainer" style="display: none;">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Pembayaran</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-medium">Rp</span>
                                        <input type="number" name="payment_amount" id="paymentAmount"
                                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                                            placeholder="Masukkan jumlah pembayaran">
                                    </div>
                                    <div class="mt-2 text-sm text-gray-500">
                                        Total: Rp <span id="totalAmountDisplay">{{ number_format($totalAmount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="mt-2" id="changeContainer" style="display: none;">
                                        <span class="font-bold text-success">Kembalian: Rp <span id="changeAmount">0</span></span>
                                    </div>
                                </div>

                                {{-- Payment Shortcuts (Cash only) --}}
                                <div class="mb-4" id="paymentShortcutsContainer" style="display: none;">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Shortcut Pembayaran</label>
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button" class="px-3 py-1.5 border border-primary text-primary rounded-lg text-sm font-medium hover:bg-primary/5 transition-colors payment-shortcut"
                                            data-amount="{{ $totalAmount }}">Uang Pas</button>
                                        <button type="button" class="px-3 py-1.5 border border-primary text-primary rounded-lg text-sm font-medium hover:bg-primary/5 transition-colors payment-shortcut"
                                            data-amount="{{ $totalAmount + 10000 }}">+10rb</button>
                                        <button type="button" class="px-3 py-1.5 border border-primary text-primary rounded-lg text-sm font-medium hover:bg-primary/5 transition-colors payment-shortcut"
                                            data-amount="{{ $totalAmount + 50000 }}">+50rb</button>
                                        <button type="button" class="px-3 py-1.5 border border-primary text-primary rounded-lg text-sm font-medium hover:bg-primary/5 transition-colors payment-shortcut"
                                            data-amount="100000">100rb</button>
                                        <button type="button" class="px-3 py-1.5 border border-primary text-primary rounded-lg text-sm font-medium hover:bg-primary/5 transition-colors payment-shortcut"
                                            data-amount="200000">200rb</button>
                                        <button type="button" class="px-3 py-1.5 border border-primary text-primary rounded-lg text-sm font-medium hover:bg-primary/5 transition-colors payment-shortcut"
                                            data-amount="500000">500rb</button>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                                    <textarea name="catatan" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition resize-none"
                                        rows="3" placeholder="Tambahkan instruksi khusus atau catatan di sini..."></textarea>
                                </div>
                            </div>

                            {{-- Right Column: Order Summary --}}
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Pesanan</h4>
                                @foreach ($cartItems as $index => $item)
                                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-3">
                                        <h5 class="font-bold text-gray-800 mb-3">{{ $item['product_name'] }} (x{{ $item['quantity'] }})</h5>

                                        {{-- Specifications --}}
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
                                            <div class="flex justify-between items-center border-b border-gray-200 py-2">
                                                <span class="text-sm text-gray-500">{{ $spec['nama_spesifikasi'] }}</span>
                                                <span class="text-sm font-medium text-gray-700">
                                                    @if ($spec['input_type'] === 'select' && $bahan)
                                                        {{ $bahan->nama_bahan }}: {{ $item['quantity'] }} x Rp
                                                        {{ number_format($pricePerUnit, 0, ',', '.') }} = Rp
                                                        {{ number_format($spec['price'], 0, ',', '.') }}
                                                    @elseif ($spesifikasiProduk && $spesifikasiProduk->spesifikasi && $bahan)
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

                                        {{-- Production Details --}}
                                        @php
                                            $product = \App\Models\Vendor\Produk::with('estimasiProduk.alat')->find(
                                                $item['product_id'],
                                            );
                                            $estimatedTime = $product
                                                ? $product->getEstimatedProductionTime($item['quantity'])
                                                : 0;
                                        @endphp
                                        <div class="flex justify-between items-center border-b border-gray-200 py-2">
                                            <span class="text-sm text-gray-500">Estimasi Waktu</span>
                                            <span class="text-sm font-medium text-gray-700">{{ $estimatedTime }} menit</span>
                                        </div>

                                        <div class="flex justify-between items-center border-b border-gray-200 py-2">
                                            <span class="text-sm text-gray-500">Alat Produksi</span>
                                            <span class="text-sm font-medium text-gray-700">
                                                @if ($product && $product->estimasiProduk)
                                                    {{ $product->estimasiProduk->pluck('alat.nama_alat')->filter()->implode(', ') ?: 'Tidak ada alat' }}
                                                @else
                                                    Tidak ada alat
                                                @endif
                                            </span>
                                        </div>

                                        {{-- Item Total --}}
                                        <div class="flex justify-between items-center pt-3">
                                            <span class="font-bold text-gray-800">Subtotal</span>
                                            <span class="font-bold text-primary">
                                                Rp {{ number_format($item['total_price'], 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Hidden inputs for form submission --}}
                                    <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item['product_id'] }}">
                                    <input type="hidden" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] }}">
                                    @foreach ($item['specifications'] as $specId => $spec)
                                        <input type="hidden" name="items[{{ $index }}][specifications][{{ $specId }}][bahan_id]" value="{{ $spec['bahan_id'] }}">
                                        <input type="hidden" name="items[{{ $index }}][specifications][{{ $specId }}][value]" value="{{ $spec['value'] }}">
                                        <input type="hidden" name="items[{{ $index }}][specifications][{{ $specId }}][input_type]" value="{{ $spec['input_type'] }}">
                                        <input type="hidden" name="items[{{ $index }}][specifications][{{ $specId }}][price]" value="{{ $spec['price'] }}">
                                    @endforeach
                                @endforeach

                                {{-- Order Totals --}}
                                <div class="bg-white border border-gray-200 rounded-xl p-4">
                                    <div class="flex justify-between items-center border-b border-gray-100 py-2">
                                        <span class="text-sm text-gray-500">Total Item</span>
                                        <span class="text-sm font-medium text-gray-700">{{ count($cartItems) }} item</span>
                                    </div>
                                    <div class="flex justify-between items-center border-b border-gray-100 py-2">
                                        <span class="text-sm text-gray-500">Total Jumlah</span>
                                        <span class="text-sm font-medium text-gray-700">{{ collect($cartItems)->sum('quantity') }} pcs</span>
                                    </div>
                                    <div class="flex justify-between items-center border-b border-gray-100 py-2">
                                        <span class="text-sm text-gray-500">Total Waktu Produksi</span>
                                        <span class="text-sm font-medium text-gray-700">{{ $totalTime }} menit</span>
                                    </div>
                                    <div class="flex justify-between items-center pt-3">
                                        <h4 class="font-bold text-gray-800">Total Akhir</h4>
                                        <h4 class="font-bold text-primary">
                                            Rp {{ number_format($totalAmount, 0, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex justify-end gap-3 mt-6">
                            <a href="{{ route('vendor.pos.cart') }}" data-no-loading
                                class="inline-flex items-center px-4 py-2 border border-primary text-primary rounded-full text-sm font-medium hover:bg-primary/5 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Keranjang
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-full text-sm font-medium hover:bg-primary/90 transition-colors">
                                <i class="fas fa-check mr-2"></i>Selesaikan Pesanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- New Customer Modal (Alpine.js) --}}
        <div x-show="showModal" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity" @click="showModal = false"
                x-show="showModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"></div>

            {{-- Modal Panel --}}
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full transform transition-all"
                    x-show="showModal" x-cloak
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    @click.outside="showModal = false">

                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <h5 class="text-lg font-semibold text-gray-800" id="modal-title">Tambah Pelanggan Baru</h5>
                        <button type="button" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors"
                            @click="showModal = false">
                            <i class="fas fa-times text-gray-500"></i>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <form action="{{ route('vendor.customers.store') }}" method="POST" id="newCustomerForm" data-no-loading x-data="{ isSubmitting: false }">
                        @csrf
                        <div class="px-6 py-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                                <input type="text" name="nama" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                                <textarea name="alamat" required rows="2"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition resize-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No Telp</label>
                                <input type="tel" name="no_telp" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200">
                            <button type="button"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors"
                                @click="showModal = false">Tutup</button>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Payment method toggle
            const paymentMethodSelect = document.getElementById('paymentMethodSelect');
            const paymentAmountContainer = document.getElementById('paymentAmountContainer');
            const paymentShortcutsContainer = document.getElementById('paymentShortcutsContainer');
            const paymentAmount = document.getElementById('paymentAmount');
            const changeContainer = document.getElementById('changeContainer');
            const changeAmount = document.getElementById('changeAmount');
            const totalAmount = {{ $totalAmount }};

            function calculateChange() {
                const amount = parseFloat(paymentAmount.value) || 0;
                const change = amount - totalAmount;

                if (change >= 0) {
                    changeAmount.textContent = change.toLocaleString('id-ID');
                    changeContainer.style.display = 'block';
                } else {
                    changeContainer.style.display = 'none';
                }
            }

            paymentMethodSelect.addEventListener('change', function() {
                if (this.value === 'cash') {
                    paymentAmountContainer.style.display = 'block';
                    paymentShortcutsContainer.style.display = 'block';
                } else {
                    paymentAmountContainer.style.display = 'none';
                    paymentShortcutsContainer.style.display = 'none';
                }
            });

            paymentAmount.addEventListener('input', calculateChange);

            document.querySelectorAll('.payment-shortcut').forEach(button => {
                button.addEventListener('click', function() {
                    paymentAmount.value = this.dataset.amount;
                    calculateChange();
                });
            });

            paymentMethodSelect.dispatchEvent(new Event('change'));

            // Handle new customer form submission via AJAX
            const newCustomerForm = document.getElementById('newCustomerForm');
            if (newCustomerForm) {
                newCustomerForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalButtonText = submitButton.innerHTML;

                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
                    submitButton.disabled = true;

                    try {
                        const response = await fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            const customerSelect = document.getElementById('customerSelect');
                            const option = document.createElement('option');
                            option.value = data.customer.id;
                            option.text = data.customer.nama;
                            option.selected = true;
                            customerSelect.appendChild(option);

                            // Close modal via Alpine.js
                            const alpineRoot = document.querySelector('[x-data="{ showModal: false, isSubmitting: false }"]');
                            if (alpineRoot && alpineRoot.__x) {
                                alpineRoot.__x.$data.showModal = false;
                            } else {
                                // Fallback: dispatch custom event
                                document.dispatchEvent(new CustomEvent('close-modal'));
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Pelanggan berhasil ditambahkan',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                        } else {
                            throw new Error(data.message || 'Gagal menambahkan pelanggan');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: error.message || 'Gagal menambahkan pelanggan. Silakan coba lagi.',
                            confirmButtonColor: '#3085d6'
                        });
                    } finally {
                        submitButton.innerHTML = originalButtonText;
                        submitButton.disabled = false;
                    }
                });
            }

            // Handle checkout form submission
            const checkoutForm = document.getElementById('checkoutForm');
            if (checkoutForm) {
                checkoutForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const customerSelect = document.getElementById('customerSelect');
                    if (!customerSelect.value) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: 'Silakan pilih pelanggan terlebih dahulu',
                            confirmButtonColor: '#3085d6'
                        });
                        customerSelect.focus();
                        return;
                    }

                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalButtonText = submitButton.innerHTML;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
                    submitButton.disabled = true;

                    try {
                        const formData = new FormData(this);

                        const response = await fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pesanan Selesai!',
                                text: 'Pesanan Anda berhasil diproses.',
                                confirmButtonColor: '#3085d6'
                            }).then(() => {
                                window.open(data.invoiceUrl, '_blank');

                                fetch(data.downloadUrl)
                                    .then(response => response.blob())
                                    .then(blob => {
                                        const url = window.URL.createObjectURL(blob);
                                        const a = document.createElement('a');
                                        a.href = url;
                                        a.download = "invoice.pdf";
                                        document.body.appendChild(a);
                                        a.click();
                                        window.URL.revokeObjectURL(url);
                                    });

                                window.location.href = data.redirectUrl;
                            });
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan saat checkout. Silakan coba lagi.');
                        }
                    } catch (error) {
                        console.error('Checkout error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Checkout Gagal',
                            text: error.message || 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi nanti.',
                            confirmButtonColor: '#3085d6'
                        });
                    } finally {
                        submitButton.innerHTML = originalButtonText;
                        submitButton.disabled = false;
                    }
                });
            }
        });
    </script>
@endsection
