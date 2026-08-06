@extends('layouts.vendor')

@section('title', 'Point of Sale')

@section('content')
    {{-- Add CSRF token meta tag --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div x-data="posHome()">
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

        {{-- Navigation Kategori --}}
        <div class="px-4 mt-3">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-4">
                    <div class="flex flex-col lg:flex-row lg:items-center gap-3">
                        {{-- Category --}}
                        <div class="flex-1">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('vendor.pos.index') }}" data-no-loading
                                    class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium transition-colors {{ request()->routeIs('vendor.pos.index') && !request()->has('search') ? 'bg-primary text-white' : 'border border-primary text-primary hover:bg-primary/5' }}">
                                    <i class="fas fa-th-large mr-2"></i>All Products
                                </a>
                                @foreach ($categories as $category)
                                    @if ($category)
                                        <a href="{{ route('vendor.pos.category', ['slug' => $category->slug]) }}" data-no-loading
                                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium transition-colors {{ request()->is('*/pos/category/' . $category->slug) ? 'bg-primary text-white' : 'border border-primary text-primary hover:bg-primary/5' }}">
                                            <i class="fas fa-tag mr-2"></i>{{ $category->nama_kategori }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        {{-- Search --}}
                        <div class="flex-1">
                            <form action="{{ route('vendor.pos.search') }}" method="GET" class="flex gap-2" data-no-loading>
                                <div class="relative flex-1">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" name="search"
                                        class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-full focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                                        value="{{ request('search') }}" placeholder="Search products..." autocomplete="off">
                                    @if (request('search'))
                                        <a href="{{ route('vendor.pos.index') }}" data-no-loading
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-danger">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>

                        {{-- Cart --}}
                        <div class="lg:w-auto">
                            <a href="{{ route('vendor.pos.cart') }}"
                                class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-primary text-white rounded-full text-sm font-medium hover:bg-primary/90 transition-colors">
                                <i class="fas fa-shopping-cart mr-2"></i>Cart
                                <span class="ml-2 bg-white/20 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ count(session('cart', [])) }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Produk --}}
        <div class="px-4 mt-3 pb-4">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-4">
                    @if ($products->isEmpty())
                        <x-ui.empty-state icon="fas fa-box-open" title="No Products Found" :description="request('search') ? 'Try a different search term' : 'No products available in this category'">
                            <x-slot:actions>
                                <a href="{{ route('vendor.pos.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors">
                                    <i class="fas fa-arrow-left mr-2"></i>Back to All Products
                                </a>
                            </x-slot:actions>
                        </x-ui.empty-state>
                    @else
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach ($products as $product)
                                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow cursor-pointer"
                                    @click="openProductModal({{ json_encode($product->id) }}, {{ json_encode($product->nama_produk) }}, {{ json_encode($product->gambar[0] ?? 'images/no-image.jpg') }}, {{ json_encode($product->deskripsi ?? '') }})">
                                    {{-- Product Image --}}
                                    <div class="h-40 overflow-hidden flex items-center justify-center bg-gray-50">
                                        <img src="{{ asset($product->gambar[0] ?? 'images/no-image.jpg') }}"
                                            alt="{{ $product->nama_produk }}"
                                            class="w-full h-full object-cover">
                                    </div>

                                    <div class="p-3">
                                        <h5 class="font-bold text-gray-800 text-sm mb-1 truncate">{{ $product->nama_produk }}</h5>
                                        <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ Str::limit(strip_tags($product->deskripsi), 50) }}</p>
                                        <button class="w-full inline-flex items-center justify-center px-3 py-2 border border-primary text-primary rounded-lg text-xs font-medium hover:bg-primary/5 transition-colors">
                                            <i class="fas fa-shopping-cart mr-1"></i>Order Now
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Single Product Modal (Alpine.js) --}}
        <div x-show="showModal" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="product-modal-title" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity"
                @click="closeModal()"
                x-show="showModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
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
                    @click.outside="closeModal()">

                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <h5 class="text-lg font-semibold text-gray-800" id="product-modal-title" x-text="productName"></h5>
                        <button type="button" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors"
                            @click="closeModal()">
                            <i class="fas fa-times text-gray-500"></i>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <form :action="addToCartUrl" method="POST" data-no-loading>
                        @csrf
                        <div class="px-6 py-4">
                            <input type="hidden" name="product_id" :value="productId">

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                <input type="number" name="quantity" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                                    value="1" min="1">
                            </div>

                            @foreach ($products->first()->spesifikasiProduk ?? [] as $spec)
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ $spec->spesifikasi->nama_spesifikasi }}
                                        @if ($spec->wajib_diisi)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>

                                    @if ($spec->spesifikasi->tipe_input === 'select')
                                        <select name="specifications[{{ $spec->id }}]"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                                            {{ $spec->wajib_diisi ? 'required' : '' }}>
                                            @foreach ($spec->bahans as $bahan)
                                                <option value="{{ $bahan->id }}">
                                                    {{ $bahan->nama_bahan }} -
                                                    @if ($bahan->wholesalePrice->count() > 0)
                                                        @foreach ($bahan->wholesalePrice as $price)
                                                            {{ $price->min_quantity }}-{{ $price->max_quantity }}
                                                            pcs: Rp {{ number_format($price->harga) }}
                                                        @endforeach
                                                    @else
                                                        Rp {{ number_format($bahan->hpp) }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    @elseif ($spec->spesifikasi->tipe_input === 'number')
                                        <div class="flex">
                                            <input type="text"
                                                name="specifications[{{ $spec->id }}]"
                                                class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition decimal-input"
                                                {{ $spec->wajib_diisi ? 'required' : '' }}
                                                pattern="[0-9]*[.,]?[0-9]+"
                                                inputmode="decimal">
                                            @if ($spec->spesifikasi->satuan)
                                                <span class="inline-flex items-center px-4 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-sm text-gray-600">{{ $spec->spesifikasi->satuan }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="flex">
                                            <input type="{{ $spec->spesifikasi->tipe_input }}"
                                                name="specifications[{{ $spec->id }}]"
                                                class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                                                {{ $spec->wajib_diisi ? 'required' : '' }}>
                                            @if ($spec->spesifikasi->satuan)
                                                <span class="inline-flex items-center px-4 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-sm text-gray-600">{{ $spec->spesifikasi->satuan }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            {{-- Price Details --}}
                            <div class="mb-4">
                                <div :id="'priceDetails' + productId" class="mt-3"></div>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200">
                            <button type="button"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors"
                                @click="closeModal()">Close</button>
                            <button type="button"
                                class="inline-flex items-center px-4 py-2 bg-info text-white rounded-lg font-medium hover:bg-info/90 transition-colors"
                                :id="'cekHarga' + productId"
                                @click="checkPrice($event)">
                                <i class="fas fa-calculator mr-2"></i>Cek Harga
                            </button>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors">
                                <i class="fas fa-cart-plus mr-2"></i>Add to Cart
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function posHome() {
            return {
                showModal: false,
                productId: null,
                productName: '',
                productImage: '',
                productDescription: '',
                addToCartUrl: '{{ route("vendor.pos.addToCart") }}',

                openProductModal(id, name, image, description) {
                    this.productId = id;
                    this.productName = name;
                    this.productImage = image;
                    this.productDescription = description;
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
                },

                checkPrice(event) {
                    const button = event.currentTarget;
                    const form = button.closest('form');
                    const formData = new FormData(form);
                    const data = {
                        product_id: formData.get('product_id'),
                        quantity: formData.get('quantity'),
                        specifications: {}
                    };

                    // Show loading state
                    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Calculating...';
                    button.disabled = true;

                    // Clear previous price details
                    const priceDetails = document.querySelector(`#priceDetails${this.productId}`);
                    if (priceDetails) {
                        priceDetails.innerHTML = '<div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-700">Calculating price...</div>';
                    }

                    formData.forEach((value, key) => {
                        if (key.startsWith('specifications')) {
                            const matches = key.match(/\[(.*?)\]/);
                            if (matches) {
                                const specId = matches[1];
                                const numValue = parseFloat(value);
                                data.specifications[specId] = isNaN(numValue) ? value : numValue;
                            }
                        }
                    });

                    fetch(`{{ route('vendor.pos.checkPrice') }}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(data)
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
                        .then(data => {
                            if (data.error) {
                                priceDetails.innerHTML = `
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-700">
                                        <strong>Error:</strong> ${data.error}
                                    </div>
                                `;
                            } else {
                                priceDetails.innerHTML = `
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                        <h6 class="mb-3 font-bold text-gray-800"><i class="fas fa-receipt mr-2"></i>Rincian Harga:</h6>
                                        <div class="flex justify-between mb-2 text-sm">
                                            <span class="text-gray-600">Quantity:</span>
                                            <span class="font-medium">${data.quantity}</span>
                                        </div>
                                        ${data.specifications.map(spec => `
                                            <div class="flex justify-between mb-2 text-sm">
                                                <span class="text-gray-600">${spec.name}:</span>
                                                <span class="font-medium">Rp ${spec.price}</span>
                                            </div>
                                        `).join('')}
                                        <hr class="my-2 border-gray-200">
                                        <div class="flex justify-between">
                                            <span class="font-bold text-gray-800">Total Harga:</span>
                                            <span class="font-bold text-primary">Rp ${data.totalPrice}</span>
                                        </div>
                                    </div>
                                `;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            priceDetails.innerHTML = `
                                <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-700">
                                    <strong>Error:</strong> Failed to calculate price. Please try again.
                                </div>
                            `;
                        })
                        .finally(() => {
                            button.innerHTML = '<i class="fas fa-calculator mr-2"></i>Cek Harga';
                            button.disabled = false;
                        });
                },

                init() {
                    // Handle decimal inputs
                    this.$nextTick(() => {
                        document.querySelectorAll('.decimal-input').forEach(input => {
                            input.addEventListener('input', function(e) {
                                this.value = this.value.replace(/,/g, '.');
                                this.value = this.value.replace(/[^0-9.]/g, '');
                                const parts = this.value.split('.');
                                if (parts.length > 2) {
                                    this.value = parts[0] + '.' + parts.slice(1).join('');
                                }
                            });
                        });
                    });
                }
            }
        }

        // Form validation before submission
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const requiredFields = this.querySelectorAll('[required]');
                    let isValid = true;

                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.classList.add('border-danger');
                        } else {
                            field.classList.remove('border-danger');
                        }
                    });

                    if (!isValid) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Please fill in all required fields',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });
            });
        });
    </script>
@endsection
