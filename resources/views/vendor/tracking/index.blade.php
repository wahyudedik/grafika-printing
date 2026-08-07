@extends('layouts.vendor')

@section('title', 'Kelola Tracking Pesanan')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="trackingManager()">
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Kelola Tracking Pesanan</h3>
            <p class="text-sm text-gray-500 mt-1">Update status pesanan dari lelang</p>
        </div>
        <div class="p-5">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
                    <div class="flex items-center gap-2 text-green-800"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span></div>
                    <button @click="show = false" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Kode Transaksi</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Lelang</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Pelanggan</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Status</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Total</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Ongkir</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">No. Resi</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($auctions as $auction)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4">{{ $auction->transaksi->kode ?? $auction->kode }}</td>
                                <td class="py-3 px-4">
                                    <div class="font-medium">{{ $auction->title }}</div>
                                    <div class="text-xs text-gray-500">Kode: {{ $auction->kode }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-medium">{{ $auction->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $auction->user->email }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    @php
                                        $trackingStatus = $auction->transaksi->tracking_status ?? 'menunggu';
                                        $statusColors = [
                                            'menunggu' => 'bg-gray-100 text-gray-800',
                                            'diproses' => 'bg-blue-100 text-blue-800',
                                            'dicetak' => 'bg-amber-100 text-amber-800',
                                            'dikirim' => 'bg-primary-100 text-primary-800',
                                            'selesai' => 'bg-green-100 text-green-800',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$trackingStatus] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($trackingStatus) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-medium">Rp {{ number_format($auction->winning_bid) }}</td>
                                <td class="py-3 px-4">
                                    @if ($auction->shippingInvoice)
                                        Rp {{ number_format($auction->shippingInvoice->shipping_cost) }}
                                        @if ($auction->shippingInvoice->payment_status === 'pending')
                                            <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Belum Bayar</span>
                                        @elseif($auction->shippingInvoice->payment_status === 'paid')
                                            <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Lunas</span>
                                        @endif
                                    @else
                                        <x.ui.button @click="openCreateInvoice({{ $auction->id }})" variant="ghost" size="xs">Buat Invoice</x.ui.button>
                                    @endif
                                </td>
                                <td class="py-3 px-4">{{ $auction->shippingInvoice->waybill_number ?? '-' }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex gap-1">
                                        @if ($auction->shippingInvoice)
                                            <x.ui.button @click="openUpdateStatus({{ $auction->id }})" variant="primary" size="xs">Update</x.ui.button>
                                            <x.ui.button href="/dashboard/tracking/{{ $auction->id }}/track" variant="info" size="xs">Track</x.ui.button>
                                        @else
                                            <x.ui.button @click="openCreateInvoice({{ $auction->id }})" variant="success" size="xs">Setup Shipping</x.ui.button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12">
                                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <p class="text-lg font-medium text-gray-900">Belum ada pesanan dari lelang</p>
                                    <p class="text-sm text-gray-500 mt-1">Pesanan akan muncul di sini setelah ada lelang yang dimenangkan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create Shipping Invoice Modal --}}
    <div x-show="showCreateModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" x-cloak @keydown.escape.window="showCreateModal = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showCreateModal = false"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full p-6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Buat Shipping Invoice</h3>
                    <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
                </div>
                <form id="createShippingInvoiceForm" method="POST" @submit.prevent="submitCreateInvoice()">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kurir</label>
                            <select name="courier" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                                <option value="">Pilih Kurir</option>
                                <option value="jne">JNE</option>
                                <option value="tiki">TIKI</option>
                                <option value="pos">POS Indonesia</option>
                                <option value="jnt">J&T Express</option>
                                <option value="sicepat">SiCepat</option>
                                <option value="ninja">Ninja Xpress</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Layanan</label>
                            <select name="service" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                                <option value="">Pilih Layanan</option>
                                <option value="reg">Regular</option>
                                <option value="eco">Economy</option>
                                <option value="ons">Overnight</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Berat (gram)</label>
                            <input type="number" name="weight" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" min="1" required placeholder="1000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kota Asal</label>
                            <input type="text" name="origin_city" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required placeholder="Jakarta">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kota Tujuan</label>
                            <input type="text" name="destination_city" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required placeholder="Bandung">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Asal</label>
                            <textarea name="origin_address" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" rows="2" required placeholder="Alamat vendor"></textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Tujuan</label>
                        <textarea name="destination_address" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" rows="3" required placeholder="Alamat pengiriman user"></textarea>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="notes" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" rows="2" placeholder="Catatan pengiriman (opsional)"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <x.ui.button @click="showCreateModal = false" variant="outline">Batal</x.ui.button>
                        <x.ui.button type="submit" variant="primary" :disabled="creatingInvoice">
                            {{ creatingInvoice ? 'Membuat...' : 'Buat Invoice' }}
                        </x.ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Update Shipping Status Modal --}}
    <div x-show="showUpdateModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" x-cloak @keydown.escape.window="showUpdateModal = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showUpdateModal = false"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Update Status Pengiriman</h3>
                    <button @click="showUpdateModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
                </div>
                <form id="updateShippingStatusForm" method="POST" @submit.prevent="submitUpdateStatus()">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Pengiriman</label>
                            <select name="shipping_status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Resi</label>
                            <input type="text" name="waybill_number" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Masukkan nomor resi">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="notes" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" rows="3" placeholder="Catatan status pengiriman"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <x.ui.button type="button" @click="showUpdateModal = false" variant="outline" size="sm">Batal</x.ui.button>
                        <x.ui.button type="submit" variant="primary" size="sm" :disabled="updatingStatus">{{ updatingStatus ? 'Menyimpan...' : 'Update Status' }}</x.ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function trackingManager() {
    return {
        showCreateModal: false,
        showUpdateModal: false,
        creatingInvoice: false,
        updatingStatus: false,
        currentAuctionId: null,

        openCreateInvoice(auctionId) {
            this.currentAuctionId = auctionId;
            this.showCreateModal = true;
        },

        openUpdateStatus(auctionId) {
            this.currentAuctionId = auctionId;
            this.showUpdateModal = true;
        },

        async submitCreateInvoice() {
            this.creatingInvoice = true;
            try {
                const form = document.getElementById('createShippingInvoiceForm');
                const formData = new FormData(form);
                const response = await fetch(`/dashboard/tracking/${this.currentAuctionId}/shipping-invoice`, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
                });
                const data = await response.json();
                if (data.success) { alert('Shipping invoice berhasil dibuat!'); location.reload(); }
                else { alert('Error: ' + data.message); }
            } catch (e) { alert('Terjadi kesalahan saat membuat invoice'); }
            finally { this.creatingInvoice = false; }
        },

        async submitUpdateStatus() {
            this.updatingStatus = true;
            try {
                const form = document.getElementById('updateShippingStatusForm');
                const formData = new FormData(form);
                const response = await fetch(`/dashboard/tracking/${this.currentAuctionId}/shipping-status`, {
                    method: 'PUT',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
                });
                const data = await response.json();
                if (data.success) { alert('Status pengiriman berhasil diperbarui!'); location.reload(); }
                else { alert('Error: ' + data.message); }
            } catch (e) { alert('Terjadi kesalahan saat memperbarui status'); }
            finally { this.updatingStatus = false; }
        }
    }
}
</script>
@endsection
