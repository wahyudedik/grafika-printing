@extends('layouts.vendor')

@section('title', 'Kalkulator Ongkir')

@section('content')
<x-ui.breadcrumb :items="[['label' => 'Kalkulator Ongkir']]" />

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Kalkulator Ongkos Kirim</h1>
        <p class="text-sm text-gray-500 mt-1">Hitung estimasi ongkos kirim via RajaOngkir</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Form Kalkulasi --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Hitung Ongkir</h2>

        <form id="shippingForm" x-data="shippingCalculator()" @submit.prevent="calculate()">
            <div class="space-y-4">
                {{-- Kota Asal --}}
                <div>
                    <label for="origin" class="block text-sm font-medium text-gray-700 mb-1">Kota Asal</label>
                    <select id="origin" x-model="form.origin" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <option value="">Pilih kota asal</option>
                        @foreach($cities as $city)
                            <option value="{{ $city['city_id'] }}">{{ $city['city_name'] }}, {{ $city['province'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Kota Tujuan --}}
                <div>
                    <label for="destination" class="block text-sm font-medium text-gray-700 mb-1">Kota Tujuan</label>
                    <select id="destination" x-model="form.destination" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <option value="">Pilih kota tujuan</option>
                        @foreach($cities as $city)
                            <option value="{{ $city['city_id'] }}">{{ $city['city_name'] }}, {{ $city['province'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Berat (gram) --}}
                <div>
                    <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">Berat (gram)</label>
                    <input type="number" id="weight" x-model="form.weight" min="1" max="30000" required
                        placeholder="Contoh: 1000"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" />
                </div>

                {{-- Kurir --}}
                <div>
                    <label for="courier" class="block text-sm font-medium text-gray-700 mb-1">Kurir</label>
                    <select id="courier" x-model="form.courier" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <option value="">Pilih kurir</option>
                        <option value="jne">JNE</option>
                        <option value="tiki">TIKI</option>
                        <option value="pos">POS Indonesia</option>
                    </select>
                </div>

                {{-- Tombol Hitung --}}
                <button type="submit" :disabled="loading" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <template x-if="loading">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Menghitung...
                        </span>
                    </template>
                    <template x-if="!loading">
                        <span><i class="fas fa-calculator mr-2"></i>Hitung Ongkir</span>
                    </template>
                </button>
            </div>
        </form>
    </div>

    {{-- Hasil --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Hasil Perhitungan</h2>

        <template x-if="error">
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span x-text="error"></span>
            </div>
        </template>

        <template x-if="results.length > 0">
            <div class="space-y-3">
                <template x-for="(result, index) in results" :key="index">
                    <div class="p-4 border border-gray-200 rounded-lg hover:border-primary-300 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-gray-900" x-text="result.service"></span>
                            <span class="text-sm text-gray-500" x-text="result.etd"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600" x-text="result.description"></span>
                            <span class="text-lg font-bold text-primary-600" x-text="'Rp ' + formatNumber(result.cost)"></span>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <template x-if="!loading && results.length === 0 && !error">
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-truck text-4xl text-gray-300 mb-3"></i>
                <p class="text-sm">Isi form dan klik "Hitung Ongkir" untuk melihat hasil</p>
            </div>
        </template>
    </div>
</div>

@push('scripts')
<script>
function shippingCalculator() {
    return {
        form: {
            origin: '',
            destination: '',
            weight: '',
            courier: ''
        },
        loading: false,
        error: null,
        results: [],
        async calculate() {
            this.loading = true;
            this.error = null;
            this.results = [];
            try {
                const response = await fetch('{{ route("vendor.shipping.calculate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });
                const data = await response.json();
                if (data.success) {
                    this.results = data.data;
                } else {
                    this.error = data.message || 'Gagal menghitung ongkir';
                }
            } catch (e) {
                this.error = 'Terjadi kesalahan saat menghitung ongkir';
            } finally {
                this.loading = false;
            }
        },
        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }
    };
}
</script>
@endpush
@endsection
