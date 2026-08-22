@extends('layouts.vendor')

@section('title', 'Order Tracking - Vendor')

@section('content')
<x-ui.breadcrumb :items="[['label' => 'Order Tracking']]" />

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="text-sm text-gray-500 font-medium">Vendor Panel</div>
        <h2 class="text-2xl font-bold text-gray-900">Order Tracking</h2>
    </div>
</div>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Pesanan</h3>
            </div>
            <div class="p-5">
                @if($orderTrackings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Kode Pesanan</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Lelang</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Pembeli</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Status</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Resi</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">COD</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Tanggal</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orderTrackings as $tracking)
                            @php
                                $transaksi = $tracking->auction->transaksi ?? null;
                                $isCod = $transaksi && $transaksi->is_cod;
                                $isPaid = $isCod && in_array($transaksi->shipping_payment_status ?? '', ['paid_cash', 'paid_app']);
                                $hasShippingData = $transaksi && ($transaksi->kurir || $transaksi->ongkir > 0 || $transaksi->alamat_pengiriman);
                            @endphp
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors" x-data="{ open: false, openShipping: false, openCOD: false }">
                                <td class="py-3 px-4 font-medium">{{ $tracking->order_code ?? $tracking->id }}</td>
                                <td class="py-3 px-4">{{ Str::limit($tracking->auction->title ?? '-', 30) }}</td>
                                <td class="py-3 px-4">{{ $tracking->user->name ?? '-' }}</td>
                                <td class="py-3 px-4">
                                    @php
                                        $statusMap = [
                                            'payment_received' => ['Pembayaran Diterima', 'bg-blue-100 text-blue-800'],
                                            'order_accepted' => ['Pesanan Diterima', 'bg-green-100 text-green-800'],
                                            'production_started' => ['Proses Cetak Dimulai', 'bg-amber-100 text-amber-800'],
                                            'production_completed' => ['Cetak Selesai', 'bg-orange-100 text-orange-800'],
                                            'quality_check' => ['Quality Check', 'bg-purple-100 text-purple-800'],
                                            'packaging' => ['Dikemas', 'bg-indigo-100 text-indigo-800'],
                                            'shipped' => ['Dikirim', 'bg-teal-100 text-teal-800'],
                                            'delivered' => ['Diterima', 'bg-emerald-100 text-emerald-800'],
                                            'completed' => ['Selesai', 'bg-green-500 text-white'],
                                            'mediation' => ['Mediasi', 'bg-red-100 text-red-800'],
                                        ];
                                        $s = $statusMap[$tracking->status] ?? [$tracking->status, 'bg-gray-100 text-gray-800'];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $s[1] }}">{{ $s[0] }}</span>
                                </td>
                                <td class="py-3 px-4">{{ $tracking->tracking_number ?? '-' }}</td>
                                <td class="py-3 px-4">
                                    @if($isCod)
                                        @if($isPaid)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i> Dibayar
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-money-bill-wave mr-1"></i> Belum Dibayar
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-gray-500">{{ $tracking->created_at->format('d M Y') }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex gap-2">
                                        <x.ui.button @click="open = !open" variant="primary" size="sm">Update</x.ui.button>
                                        <x.ui.button @click="openShipping = !openShipping" variant="secondary" size="sm">
                                            <i class="fas fa-truck mr-1"></i> Pengiriman
                                        </x.ui.button>

                                        {{-- Tombol Konfirmasi COD --}}
                                        @if($isCod && !$isPaid)
                                            <x.ui.button @click="openCOD = !openCOD" variant="success" size="sm">
                                                <i class="fas fa-money-bill-wave mr-1"></i> Konfirmasi COD
                                            </x.ui.button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            {{-- Update Status Modal --}}
                            <tr x-show="open" x-transition @keydown.escape.window="open = false">
                                <td colspan="8" class="p-4 bg-gray-50">
                                    <form action="{{ route('vendor.tracking.update', $tracking) }}" method="POST" class="space-y-3">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                                                <select class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" name="status" required>
                                                    <option value="payment_received" {{ $tracking->status === 'payment_received' ? 'selected' : '' }}>Pembayaran Diterima</option>
                                                    <option value="order_accepted" {{ $tracking->status === 'order_accepted' ? 'selected' : '' }}>Pesanan Diterima</option>
                                                    <option value="production_started" {{ $tracking->status === 'production_started' ? 'selected' : '' }}>Proses Cetak Dimulai</option>
                                                    <option value="production_completed" {{ $tracking->status === 'production_completed' ? 'selected' : '' }}>Cetak Selesai</option>
                                                    <option value="quality_check" {{ $tracking->status === 'quality_check' ? 'selected' : '' }}>Quality Check</option>
                                                    <option value="packaging" {{ $tracking->status === 'packaging' ? 'selected' : '' }}>Dikemas</option>
                                                    <option value="shipped" {{ $tracking->status === 'shipped' ? 'selected' : '' }}>Dikirim</option>
                                                    <option value="delivered" {{ $tracking->status === 'delivered' ? 'selected' : '' }}>Diterima</option>
                                                    <option value="completed" {{ $tracking->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Resi</label>
                                                <input type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" name="tracking_number" value="{{ $tracking->tracking_number }}" placeholder="Nomor resi">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Estimasi</label>
                                                <input type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" name="estimated_delivery" value="{{ $tracking->estimated_delivery?->format('Y-m-d') }}">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Deskripsi</label>
                                                <input type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" name="status_description" placeholder="Deskripsi singkat">
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <input type="text" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm" name="notes" placeholder="Catatan tambahan" value="{{ $tracking->notes }}">
                                            <x.ui.button type="submit" variant="primary" size="sm">Simpan</x.ui.button>
                                            <x.ui.button @click="open = false" variant="outline" size="sm">Batal</x.ui.button>
                                        </div>
                                    </form>
                                </td>
                            </tr>

                            {{-- Shipping Data Section --}}
                            <tr x-show="openShipping" x-transition @keydown.escape.window="openShipping = false">
                                <td colspan="8" class="p-4 bg-blue-50 border-l-4 border-blue-400"
                                    x-data="shippingForm({{ $tracking->id }}, {{ $transaksi ? json_encode($transaksi) : 'null' }})">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-semibold text-blue-900">
                                            <i class="fas fa-truck mr-1"></i> Data Pengiriman
                                            @if($hasShippingData)
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Sudah Diisi
                                                </span>
                                            @endif
                                        </h4>
                                        <button @click="openShipping = false" class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>

                                    {{-- Rincian Transaksi --}}
                                    @if($transaksi)
                                    <div class="mb-3 p-3 bg-white rounded-lg border border-blue-200">
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                                            <div>
                                                <span class="text-gray-500">Total Harga:</span>
                                                <p class="font-semibold">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Ongkir Saat Ini:</span>
                                                <p class="font-semibold">Rp {{ number_format($transaksi->ongkir ?? 0, 0, ',', '.') }}</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Kurir:</span>
                                                <p class="font-semibold">{{ strtoupper($transaksi->kurir ?? '-') }}</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">COD:</span>
                                                <p class="font-semibold {{ $isCod ? 'text-green-600' : 'text-gray-400' }}">
                                                    {{ $isCod ? 'YA' : 'TIDAK' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Form Data Pengiriman --}}
                                    <form @submit.prevent="saveShipping()">
                                        @csrf
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                            {{-- Toggle COD --}}
                                            <div class="flex items-center gap-3">
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" x-model="form.is_cod" class="sr-only peer">
                                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                                </label>
                                                <span class="text-sm font-medium text-gray-700">COD (Bayar di Tempat)</span>
                                            </div>

                                            {{-- Kurir --}}
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Kurir</label>
                                                <select x-model="form.kurir" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required>
                                                    <option value="">Pilih kurir</option>
                                                    <option value="jne">JNE</option>
                                                    <option value="tiki">TIKI</option>
                                                    <option value="pos">POS Indonesia</option>
                                                    <option value="jnt">J&T Express</option>
                                                    <option value="sicepat">SiCepat</option>
                                                    <option value="ninja">Ninja Xpress</option>
                                                    <option value="lion">Lion Parcel</option>
                                                </select>
                                            </div>

                                            {{-- Ongkir --}}
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Ongkos Kirim (Rp)</label>
                                                <input type="number" x-model="form.ongkir" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" min="0" required placeholder="0">
                                            </div>

                                            {{-- Alamat Pengiriman --}}
                                            <div class="sm:col-span-2 lg:col-span-1">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Alamat Pengiriman</label>
                                                <textarea x-model="form.alamat" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" rows="2" required placeholder="Alamat lengkap pengiriman"></textarea>
                                            </div>
                                        </div>

                                        {{-- Hitung Ongkir Section --}}
                                        <div class="mt-3 p-3 bg-white rounded-lg border border-blue-200">
                                            <div class="flex items-center gap-2 mb-2">
                                                <i class="fas fa-calculator text-blue-500"></i>
                                                <span class="text-xs font-medium text-gray-700">Hitung Ongkir via RajaOngkir</span>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                                                <div>
                                                    <input type="text" x-model="calc.origin" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Kota asal (ID)">
                                                </div>
                                                <div>
                                                    <input type="text" x-model="calc.destination" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Kota tujuan (ID)">
                                                </div>
                                                <div>
                                                    <input type="number" x-model="calc.weight" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Berat (gram)" min="1">
                                                </div>
                                                <div class="flex gap-2">
                                                    <button type="button" @click="hitungOngkir()" :disabled="calc.loading || !calc.origin || !calc.destination || !calc.weight || !form.kurir"
                                                        class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                                        <template x-if="calc.loading">
                                                            <svg class="animate-spin h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                        </template>
                                                        <i class="fas fa-calculator mr-1" x-show="!calc.loading"></i>
                                                        Hitung
                                                    </button>
                                                </div>
                                            </div>
                                            {{-- Hasil perhitungan --}}
                                            <template x-if="calc.results.length > 0">
                                                <div class="mt-2 space-y-1">
                                                    <template x-for="(r, i) in calc.results" :key="i">
                                                        <div class="flex items-center justify-between p-2 bg-blue-50 rounded text-xs cursor-pointer hover:bg-blue-100 transition"
                                                            @click="form.ongkir = r.cost; calc.results = []">
                                                            <span x-text="r.service + ' (' + r.etd + ')'"></span>
                                                            <span class="font-bold text-blue-700" x-text="'Rp ' + formatNumber(r.cost)"></span>
                                                        </div>
                                                    </template>
                                                    <p class="text-xs text-gray-500">Klik untuk menggunakan harga ini</p>
                                                </div>
                                            </template>
                                            <template x-if="calc.error">
                                                <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded text-xs text-red-600">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                                    <span x-text="calc.error"></span>
                                                </div>
                                            </template>
                                        </div>

                                        {{-- Action Buttons --}}
                                        <div class="flex gap-2 mt-3">
                                            <button type="submit" :disabled="saving"
                                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition disabled:opacity-50">
                                                <template x-if="saving">
                                                    <svg class="animate-spin h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                </template>
                                                <i class="fas fa-save mr-1" x-show="!saving"></i>
                                                Simpan Data Pengiriman
                                            </button>
                                            <button type="button" @click="openShipping = false" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                                                Batal
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>

                            {{-- COD Confirmation Modal --}}
                            @if($isCod && !$isPaid && $transaksi)
                            <tr x-show="openCOD" x-transition @keydown.escape.window="openCOD = false">
                                <td colspan="8" class="p-4 bg-green-50 border-l-4 border-green-400">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-semibold text-green-900">
                                            <i class="fas fa-money-bill-wave mr-1"></i> Konfirmasi Pembayaran COD
                                        </h4>
                                        <button @click="openCOD = false" class="text-green-600 hover:text-green-800">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>

                                    {{-- Rincian Transaksi --}}
                                    <div class="mb-3 p-3 bg-white rounded-lg border border-green-200">
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                                            <div>
                                                <span class="text-gray-500">Total Harga:</span>
                                                <p class="font-semibold">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Ongkir:</span>
                                                <p class="font-semibold">Rp {{ number_format($transaksi->ongkir ?? 0, 0, ',', '.') }}</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Kurir:</span>
                                                <p class="font-semibold">{{ strtoupper($transaksi->kurir ?? '-') }}</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Yang Harus Diterima:</span>
                                                <p class="font-bold text-green-700">Rp {{ number_format($transaksi->ongkir ?? 0, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Form Konfirmasi COD --}}
                                    <form id="codForm-{{ $transaksi->id }}" onsubmit="submitCODPayment(event, {{ $transaksi->id }})">
                                        @csrf
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                                                <select name="payment_method" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required>
                                                    <option value="cash">Cash (Tunai)</option>
                                                    <option value="app">Aplikasi (Transfer)</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah Dibayar</label>
                                                <input type="number" name="amount_paid"
                                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                                       value="{{ $transaksi->ongkir ?? 0 }}" min="0" required>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Bukti Pembayaran (Opsional)</label>
                                                <input type="file" name="payment_proof" accept="image/*,.pdf"
                                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                            </div>
                                        </div>
                                        <div class="flex gap-2 mt-3">
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                                                <i class="fas fa-check mr-1"></i> Konfirmasi Pembayaran
                                            </button>
                                            <button type="button" @click="openCOD = false" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                                                Batal
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-center">{{ $orderTrackings->links() }}</div>
                @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-lg font-medium text-gray-900">Belum ada pesanan</p>
                    <p class="text-sm text-gray-500 mt-1">Pesanan dari lelang akan muncul di sini</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function shippingForm(orderTrackingId, transaksi) {
    return {
        form: {
            kurir: transaksi?.kurir || '',
            ongkir: transaksi?.ongkir || 0,
            alamat: transaksi?.alamat_pengiriman || '',
            is_cod: transaksi?.is_cod || false,
        },
        calc: {
            origin: '',
            destination: '',
            weight: '',
            loading: false,
            error: null,
            results: [],
        },
        saving: false,
        async saveShipping() {
            this.saving = true;
            try {
                const response = await fetch(`{{ url('vendor/tracking') }}/${orderTrackingId}/shipping-data`, {
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
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message || 'Data pengiriman berhasil disimpan.',
                        icon: 'success',
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan saat menyimpan data.',
                        icon: 'error',
                        confirmButtonColor: '#dc2626'
                    });
                }
            } catch (error) {
                console.error('Save shipping error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan jaringan. Silakan coba lagi.',
                    icon: 'error',
                    confirmButtonColor: '#dc2626'
                });
            } finally {
                this.saving = false;
            }
        },
        async hitungOngkir() {
            this.calc.loading = true;
            this.calc.error = null;
            this.calc.results = [];
            try {
                const response = await fetch('{{ route("vendor.shipping.calculate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        origin: this.calc.origin,
                        destination: this.calc.destination,
                        weight: this.calc.weight,
                        courier: this.form.kurir
                    })
                });
                const data = await response.json();
                if (data.success && data.data) {
                    this.calc.results = data.data;
                } else {
                    this.calc.error = data.message || 'Gagal menghitung ongkir';
                }
            } catch (e) {
                this.calc.error = 'Terjadi kesalahan saat menghitung ongkir';
            } finally {
                this.calc.loading = false;
            }
        },
        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }
    };
}

function submitCODPayment(event, transaksiId) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);

    Swal.fire({
        title: 'Konfirmasi Pembayaran COD?',
        text: 'Pastikan jumlah pembayaran sudah benar.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Konfirmasi',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang memproses pembayaran COD',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(`{{ url('shipping/payment') }}/${transaksiId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message || 'Pembayaran COD berhasil dikonfirmasi.',
                        icon: 'success',
                        confirmButtonColor: '#16a34a'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan saat memproses pembayaran.',
                        icon: 'error',
                        confirmButtonColor: '#dc2626'
                    });
                }
            })
            .catch(error => {
                console.error('COD Payment Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan jaringan. Silakan coba lagi.',
                    icon: 'error',
                    confirmButtonColor: '#dc2626'
                });
            });
        }
    });
}
</script>
@endpush
