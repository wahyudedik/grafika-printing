{{-- Enhanced Shipping Form Modal --}}
<div x-data="shippingCalculator()" x-show="showModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" x-cloak @keydown.escape.window="showModal = false">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/50 transition-opacity" @click="showModal = false"></div>

    {{-- Modal --}}
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" @click.away="showModal = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h5 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4 4 4-4m-4-5v9M20 7l-4-4-4 4m4-4v9"/></svg>
                    Kalkulator Ongkir
                </h5>
                <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-4">
                {{-- API Status --}}
                <div class="flex items-center gap-2 p-3 rounded-lg mb-4"
                     :class="apiStatus === 'success' ? 'bg-green-50 text-green-700' : (apiStatus === 'warning' ? 'bg-yellow-50 text-yellow-700' : 'bg-blue-50 text-blue-700')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <template x-if="apiStatus === 'success'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></template>
                        <template x-if="apiStatus === 'warning'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></template>
                        <template x-if="apiStatus === 'info'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></template>
                    </svg>
                    <span x-text="apiMessage" class="text-sm"></span>
                </div>

                <form @submit.prevent="calculateShipping()">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Origin --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <svg class="w-4 h-4 inline text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                Kota Asal
                            </label>
                            <select x-model="form.origin" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                                <option value="">Pilih Kota Asal</option>
                                <option value="151" selected>Jakarta Pusat, DKI Jakarta</option>
                                <option value="152">Jakarta Utara, DKI Jakarta</option>
                                <option value="153">Jakarta Selatan, DKI Jakarta</option>
                                <option value="154">Jakarta Barat, DKI Jakarta</option>
                                <option value="155">Jakarta Timur, DKI Jakarta</option>
                                <option value="501">Bandung, Jawa Barat</option>
                                <option value="502">Bogor, Jawa Barat</option>
                                <option value="503">Depok, Jawa Barat</option>
                                <option value="504">Tangerang, Banten</option>
                                <option value="505">Bekasi, Jawa Barat</option>
                                <option value="601">Surabaya, Jawa Timur</option>
                                <option value="602">Malang, Jawa Timur</option>
                                <option value="701">Yogyakarta, DI Yogyakarta</option>
                                <option value="801">Semarang, Jawa Tengah</option>
                                <option value="901">Denpasar, Bali</option>
                                <option value="1001">Medan, Sumatera Utara</option>
                                <option value="1101">Palembang, Sumatera Selatan</option>
                                <option value="1201">Makassar, Sulawesi Selatan</option>
                            </select>
                        </div>

                        {{-- Destination --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <svg class="w-4 h-4 inline text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                Kota Tujuan
                            </label>
                            <select x-model="form.destination" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                                <option value="">Pilih Kota Tujuan</option>
                                <option value="151">Jakarta Pusat, DKI Jakarta</option>
                                <option value="152">Jakarta Utara, DKI Jakarta</option>
                                <option value="153">Jakarta Selatan, DKI Jakarta</option>
                                <option value="154">Jakarta Barat, DKI Jakarta</option>
                                <option value="155">Jakarta Timur, DKI Jakarta</option>
                                <option value="501">Bandung, Jawa Barat</option>
                                <option value="502">Bogor, Jawa Barat</option>
                                <option value="503">Depok, Jawa Barat</option>
                                <option value="504">Tangerang, Banten</option>
                                <option value="505">Bekasi, Jawa Barat</option>
                                <option value="601">Surabaya, Jawa Timur</option>
                                <option value="602">Malang, Jawa Timur</option>
                                <option value="701">Yogyakarta, DI Yogyakarta</option>
                                <option value="801">Semarang, Jawa Tengah</option>
                                <option value="901">Denpasar, Bali</option>
                                <option value="1001">Medan, Sumatera Utara</option>
                                <option value="1101">Palembang, Sumatera Selatan</option>
                                <option value="1201">Makassar, Sulawesi Selatan</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                        {{-- Weight --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <svg class="w-4 h-4 inline text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                                Berat (gram)
                            </label>
                            <div class="flex">
                                <input type="number" x-model="form.weight" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" min="1" max="30000" value="1000" required>
                                <span class="inline-flex items-center px-3 text-sm text-gray-500 bg-gray-50 border border-l-0 border-gray-300 rounded-r-lg">gram</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Maksimal 30kg (30,000 gram)</p>
                        </div>

                        {{-- Courier --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <svg class="w-4 h-4 inline text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4 4 4-4m-4-5v9M20 7l-4-4-4 4m4-4v9"/></svg>
                                Kurir
                            </label>
                            <select x-model="form.courier" @change="updateServices()" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                                <option value="">Pilih Kurir</option>
                                <option value="jne">JNE</option>
                                <option value="tiki">TIKI</option>
                                <option value="pos">POS Indonesia</option>
                                <option value="jnt">J&T Express</option>
                                <option value="sicepat">SiCepat</option>
                                <option value="anteraja">AnterAja</option>
                                <option value="lion">Lion Parcel</option>
                                <option value="ninja">Ninja Xpress</option>
                            </select>
                        </div>

                        {{-- Service Type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <svg class="w-4 h-4 inline text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                Layanan
                            </label>
                            <select x-model="form.service" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                                <option value="">Pilih Layanan</option>
                                <template x-for="service in availableServices" :key="service">
                                    <option :value="service" x-text="service"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <svg class="w-4 h-4 inline text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Alamat Lengkap Tujuan
                        </label>
                        <textarea x-model="form.address" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" rows="3" placeholder="Masukkan alamat lengkap tujuan pengiriman..." required></textarea>
                    </div>

                    {{-- Calculate Button --}}
                    <div class="text-center mt-4">
                        <button type="submit" :disabled="calculating" class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <svg x-show="!calculating" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            <svg x-show="calculating" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                            <span x-text="calculating ? 'Menghitung...' : 'Hitung Ongkir'"></span>
                        </button>
                    </div>
                </form>

                {{-- Results --}}
                <div x-show="showResults" x-transition class="mt-6">
                    <div class="bg-white border border-primary-200 rounded-xl overflow-hidden">
                        <div class="bg-primary-600 text-white px-6 py-3">
                            <h5 class="text-sm font-semibold flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Hasil Perhitungan Ongkir
                            </h5>
                        </div>
                        <div class="p-6">
                            <template x-if="results.length > 0">
                                <div class="space-y-3">
                                    <template x-for="(item, idx) in results" :key="idx">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900" x-text="item.service"></div>
                                                <div class="text-xs text-gray-500">Estimasi: <span x-text="item.etd"></span></div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm font-bold text-primary-600">Rp <span x-text="item.cost.toLocaleString('id-ID')"></span></div>
                                                <button type="button" @click="selectShipping(item)" class="text-xs font-medium text-primary-700 hover:text-primary-800">Pilih</button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="errorMessage">
                                <div class="flex items-center gap-2 p-3 bg-red-50 text-red-700 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-sm" x-text="errorMessage"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Manual Input Fallback --}}
                <div x-show="showManualInput" x-transition class="mt-4 bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <div class="px-6 py-3 border-b border-gray-100">
                        <h6 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Input Manual
                        </h6>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kurir</label>
                                <input type="text" x-model="manual.courier" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Contoh: JNE, TIKI, dll">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Ongkir (Rp)</label>
                                <input type="number" x-model="manual.cost" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" min="0" placeholder="0">
                            </div>
                        </div>
                        <button type="button" @click="useManualInput()" class="mt-3 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-700 bg-primary-50 border border-primary-200 rounded-lg hover:bg-primary-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Gunakan Input Manual
                        </button>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-200">
                <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Tutup
                </button>
                <button type="button" x-show="selectedShipping" @click="saveShipping()" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan & Kirim
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function shippingCalculator() {
        return {
            showModal: false,
            calculating: false,
            apiStatus: 'info',
            apiMessage: 'Menggunakan RajaOngkir API untuk perhitungan yang akurat',
            showResults: false,
            showManualInput: false,
            results: [],
            errorMessage: null,
            selectedShipping: null,
            availableServices: [],
            form: {
                origin: '151',
                destination: '',
                weight: 1000,
                courier: '',
                service: '',
                address: ''
            },
            manual: {
                courier: '',
                cost: 0
            },
            serviceTypes: {
                'jne': ['REG', 'OKE', 'YES'],
                'tiki': ['REG', 'ECO', 'ONS'],
                'pos': ['REG', 'KILAT', 'EXPRESS'],
                'jnt': ['REG', 'EZ', 'COCO'],
                'sicepat': ['REG', 'BEST', 'EXPRESS'],
                'anteraja': ['REG', 'EXPRESS'],
                'lion': ['REG', 'EXPRESS'],
                'ninja': ['REG', 'EXPRESS']
            },
            updateServices() {
                this.availableServices = this.serviceTypes[this.form.courier] || [];
                this.form.service = '';
            },
            async calculateShipping() {
                if (this.calculating) return;
                this.calculating = true;
                this.errorMessage = null;
                this.showResults = false;

                try {
                    const response = await fetch('{{ route("api.shipping.calculate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(this.form)
                    });
                    const data = await response.json();

                    if (data.success) {
                        const rajaongkir = data.data.rajaongkir;
                        this.results = [];
                        rajaongkir.results.forEach(result => {
                            result.costs.forEach(cost => {
                                this.results.push({
                                    service: cost.service,
                                    cost: cost.cost[0].value,
                                    etd: cost.cost[0].etd,
                                    courier: result.name
                                });
                            });
                        });
                        this.showResults = true;
                        this.apiStatus = 'success';
                        this.apiMessage = 'RajaOngkir API berhasil diakses';
                    } else {
                        if (data.fallback) {
                            this.apiStatus = 'warning';
                            this.apiMessage = data.message;
                            this.showManualInput = true;
                        } else {
                            this.errorMessage = data.message || 'Gagal menghitung ongkir';
                        }
                    }
                } catch (error) {
                    this.apiStatus = 'warning';
                    this.apiMessage = 'Terjadi kesalahan saat menghitung ongkir';
                    this.showManualInput = true;
                } finally {
                    this.calculating = false;
                }
            },
            useManualInput() {
                if (!this.manual.courier || !this.manual.cost) {
                    alert('Mohon isi kurir dan biaya ongkir');
                    return;
                }
                this.results = [{
                    service: 'Input Manual',
                    cost: parseInt(this.manual.cost),
                    etd: '-',
                    courier: this.manual.courier
                }];
                this.showResults = true;
            },
            selectShipping(item) {
                this.selectedShipping = item;
                this.form.courier = item.courier.toLowerCase();
                this.form.service = item.service;
                this.updateServices();
            },
            saveShipping() {
                if (this.selectedShipping) {
                    alert(`Dipilih: ${this.selectedShipping.service} - ${this.selectedShipping.courier} - Rp ${this.selectedShipping.cost.toLocaleString('id-ID')}`);
                }
            }
        }
    }
</script>
