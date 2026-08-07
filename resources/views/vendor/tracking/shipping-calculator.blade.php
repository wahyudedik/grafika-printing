@extends('layouts.vendor')

@section('content')
<div x-data="shippingCalc()" x-init="init()">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kalkulator Ongkir</h1>
            <p class="text-sm text-gray-500 mt-1">RajaOngkir Integration</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="mb-6 flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-lg">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="text-sm text-green-700 flex-1">{{ session('success') }}</span>
        <button @click="show = false" class="text-green-600 hover:text-green-800"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-lg">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="text-sm text-red-700 flex-1">{{ session('error') }}</span>
        <button @click="show = false" class="text-red-600 hover:text-red-800"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Calculator Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Hitung Ongkos Kirim
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Origin City --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kota Asal <span class="text-red-500">*</span></label>
                            <select x-model="form.origin" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                                <option value="">Pilih Kota Asal</option>
                                @foreach($cities as $city)
                                <option value="{{ $city['city_id'] }}" {{ $vendor->city ?? '' === $city['city_name'] ? 'selected' : '' }}>
                                    {{ $city['city_name'] }}, {{ $city['province'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Destination City --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kota Tujuan <span class="text-red-500">*</span></label>
                            <select x-model="form.destination" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                                <option value="">Pilih Kota Tujuan</option>
                                @foreach($cities as $city)
                                <option value="{{ $city['city_id'] }}">
                                    {{ $city['city_name'] }}, {{ $city['province'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Weight --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Berat (gram) <span class="text-red-500">*</span></label>
                            <input type="number" x-model="form.weight" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" min="1" max="30000" placeholder="Contoh: 1000" required>
                            <p class="text-xs text-gray-500 mt-1">Maksimal 30.000 gram (30 kg)</p>
                        </div>

                        {{-- Courier --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kurir <span class="text-red-500">*</span></label>
                            <select x-model="form.courier" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                                <option value="">Pilih Kurir</option>
                                <option value="jne">JNE</option>
                                <option value="tiki">TIKI</option>
                                <option value="pos">POS Indonesia</option>
                                <option value="jnt">J&T Express</option>
                                <option value="lion">Lion Parcel</option>
                                <option value="wahana">Wahana</option>
                                <option value="sap">SAP</option>
                                <option value="anteraja">AnterAja</option>
                            </select>
                        </div>

                        {{-- Service Type --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Layanan</label>
                            <select x-model="form.service" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Semua Layanan</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Pilih layanan spesifik (opsional)</p>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-3 mt-6">
                        <x.ui.button type="button" @click="calculate()" variant="primary" :disabled="calculating">
                            <svg x-show="!calculating" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <svg x-show="calculating" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                            <span x-text="calculating ? 'Menghitung...' : 'Hitung Ongkir'"></span>
                        </x.ui.button>
                        <x.ui.button type="button" @click="resetForm()" variant="outline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Reset
                        </x.ui.button>
                    </div>
                </div>
            </div>

            {{-- Results Section --}}
            <div x-show="showResults" x-transition class="mt-6 bg-white rounded-xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Hasil Perhitungan
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Kurir</th>
                                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Layanan</th>
                                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Estimasi</th>
                                <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Biaya</th>
                                <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(item, idx) in results" :key="idx">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-3">
                                        <div class="font-semibold text-gray-900" x-text="item.code ? item.code.toUpperCase() : ''"></div>
                                        <div class="text-xs text-gray-500" x-text="item.name || ''"></div>
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="font-semibold text-gray-900" x-text="item.description || '-'"></div>
                                    </td>
                                    <td class="px-6 py-3 text-gray-600" x-text="item.etd || '-'"></td>
                                    <td class="px-6 py-3 text-right font-bold text-gray-900">Rp <span x-text="item.price.toLocaleString('id-ID')"></span></td>
                                    <td class="px-6 py-3 text-right">
                                        <x.ui.button type="button" @click="selectShipping(item)" variant="outline-primary" size="xs">Pilih</x.ui.button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                {{-- Empty State --}}
                <div x-show="results.length === 0" class="px-6 py-12 text-center">
                    <p class="text-sm text-gray-500">Tidak ada hasil. Silakan masukkan data pengiriman dan klik "Hitung Ongkir".</p>
                </div>
            </div>

            {{-- Error Alert --}}
            <div x-show="errorMessage" x-transition class="mt-6 flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-lg">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                <span class="text-sm text-red-700" x-text="errorMessage"></span>
            </div>
        </div>

        {{-- Sidebar: Info --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900">Informasi</h3>
                </div>
                <div class="p-6 space-y-6">
                    {{-- Cara Menggunakan --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Cara Menggunakan</h4>
                        <ol class="space-y-2 text-sm text-gray-600 list-decimal list-inside">
                            <li>Pilih <strong>kota asal</strong> (lokasi vendor)</li>
                            <li>Pilih <strong>kota tujuan</strong> (lokasi pelanggan)</li>
                            <li>Masukkan <strong>berat barang</strong> dalam gram</li>
                            <li>Pilih <strong>kurir</strong> yang diinginkan</li>
                            <li>Klik <strong>"Hitung Ongkir"</strong></li>
                        </ol>
                    </div>

                    {{-- Kurir Tersedia --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Kurir Tersedia</h4>
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">JNE</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">TIKI</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">POS</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">J&T</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Lion</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Wahana</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">SAP</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">AnterAja</span>
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Catatan</h4>
                        <ul class="space-y-1 text-sm text-gray-600">
                            <li>• Harga belum termasuk asuransi</li>
                            <li>• Estimasi waktu dapat berubah</li>
                            <li>• Berat minimum 1 gram</li>
                            <li>• Berat maksimum 30 kg</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Success Toast --}}
    <div x-show="showSuccessToast" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2" class="fixed top-5 right-5 z-50 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg" x-cloak>
        <span x-text="successMessage"></span>
    </div>
</div>

<script>
    function shippingCalc() {
        return {
            calculating: false,
            showResults: false,
            errorMessage: null,
            showSuccessToast: false,
            successMessage: '',
            results: [],
            form: {
                origin: '',
                destination: '',
                weight: '',
                courier: '',
                service: ''
            },
            init() {
                // Set default origin if vendor city matches
            },
            async calculate() {
                if (this.calculating) return;

                const { origin, destination, weight, courier } = this.form;

                if (!origin || !destination || !weight || !courier) {
                    this.errorMessage = 'Mohon lengkapi semua field yang diperlukan (Kota Asal, Kota Tujuan, Berat, dan Kurir).';
                    return;
                }

                if (origin === destination) {
                    this.errorMessage = 'Kota asal dan tujuan tidak boleh sama.';
                    return;
                }

                if (parseInt(weight) < 1 || parseInt(weight) > 30000) {
                    this.errorMessage = 'Berat harus antara 1 hingga 30.000 gram.';
                    return;
                }

                this.calculating = true;
                this.errorMessage = null;
                this.showResults = false;

                try {
                    const response = await fetch('{{ route("vendor.shipping.calculate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.form)
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal menghitung ongkir. Status: ' + response.status);
                    }

                    if (data.success && data.data) {
                        this.results = [];
                        data.data.forEach(item => {
                            if (item.costs && item.costs.length > 0) {
                                item.costs.forEach(cost => {
                                    this.results.push({
                                        code: item.code,
                                        name: item.name,
                                        description: cost.description || '-',
                                        etd: cost.etd || '-',
                                        price: cost.cost && cost.cost[0] ? cost.cost[0].value : 0
                                    });
                                });
                            }
                        });
                        this.showResults = true;
                    } else {
                        throw new Error(data.message || 'Tidak ada data ongkir ditemukan.');
                    }
                } catch (error) {
                    console.error('Shipping calculation error:', error);
                    this.errorMessage = error.message || 'Terjadi kesalahan saat menghitung ongkir. Silakan coba lagi.';
                } finally {
                    this.calculating = false;
                }
            },
            selectShipping(item) {
                const data = { courier: item.code, service: item.description, cost: item.price, etd: item.etd };
                localStorage.setItem('selectedShipping', JSON.stringify(data));
                this.successMessage = `Pengiriman dipilih: ${item.code.toUpperCase()} ${item.description} - Rp ${item.price.toLocaleString('id-ID')}`;
                this.showSuccessToast = true;
                setTimeout(() => { this.showSuccessToast = false; }, 5000);
            },
            resetForm() {
                this.form = { origin: '', destination: '', weight: '', courier: '', service: '' };
                this.showResults = false;
                this.errorMessage = null;
                this.results = [];
                this.calculating = false;
            }
        }
    }
</script>
@endsection
