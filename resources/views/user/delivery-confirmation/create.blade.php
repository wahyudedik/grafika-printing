@extends('layouts.user')

@section('title', 'Konfirmasi Barang Diterima')

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Konfirmasi Barang Diterima</h1>
            <p class="text-sm text-gray-500 mt-1">Auction #{{ $auction->id }} - {{ $auction->title }}</p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-5">
                <form action="{{ route('user.delivery-confirmation.store', $auction) }}" method="POST" enctype="multipart/form-data" x-data="deliveryConfirm()">
                    @csrf

                    {{-- Auction Info --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-50 rounded-lg px-5 py-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Informasi Lelang</h3>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Judul</span>
                                    <span class="font-medium text-gray-900">{{ $auction->title }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Vendor</span>
                                    <span class="font-medium text-gray-900">{{ $auction->winnerVendor->name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Harga Lelang</span>
                                    <span class="font-medium text-gray-900">Rp {{ number_format($auction->winning_bid, 0, ',', '.') }}</span>
                                </div>
                                @if ($auction->admin_fee_amount > 0)
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-500">Admin Fee</span>
                                        <span class="font-medium text-yellow-600">Rp {{ number_format($auction->admin_fee_amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm pt-2 border-t border-gray-200">
                                        <span class="font-medium text-gray-900">Vendor Dapat</span>
                                        <span class="font-bold text-green-600">Rp {{ number_format($auction->winning_bid - $auction->admin_fee_amount, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="bg-blue-50 rounded-lg px-5 py-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Status Pembayaran</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 mb-3">Sudah Dibayar</span>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Tanggal Bayar</span>
                                    <span class="font-medium text-gray-900">{{ $auction->updated_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                            <div class="mt-3 bg-blue-100 rounded-lg px-3 py-2">
                                <p class="text-xs text-blue-800"><strong>Catatan:</strong> Vendor sudah mencetak dan mengirim barang. Ongkir sudah dibayar CASH saat barang diterima. Vendor akan dapat bayar setelah Anda konfirmasi barang diterima.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Delivery Status --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Status Barang <span class="text-red-500">*</span></label>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-4 border rounded-lg cursor-pointer transition-all"
                                :class="deliveryStatus === 'delivered' ? 'border-green-500 bg-green-50 ring-2 ring-green-200' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="delivery_status" value="delivered" x-model="deliveryStatus" required class="mt-0.5 text-green-600 focus:ring-green-500">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900"><i class="fas fa-check-circle text-green-500"></i> Barang Sudah Diterima dengan Baik</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Barang sudah sampai dan sesuai dengan pesanan</p>
                                </div>
                            </label>
                            <label class="flex items-start gap-3 p-4 border rounded-lg cursor-pointer transition-all"
                                :class="deliveryStatus === 'disputed' ? 'border-red-500 bg-red-50 ring-2 ring-red-200' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="delivery_status" value="disputed" x-model="deliveryStatus" required class="mt-0.5 text-red-600 focus:ring-red-500">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900"><i class="fas fa-times-circle text-red-500"></i> Ada Masalah dengan Barang</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Barang rusak, tidak sesuai, atau ada masalah lainnya</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Delivery Notes --}}
                    <div class="mb-6">
                        <label for="delivery_notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan Pengiriman</label>
                        <textarea id="delivery_notes" name="delivery_notes" rows="3" placeholder="Berikan detail tentang kondisi barang yang diterima..."
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                    </div>

                    {{-- Rating (only if delivered) --}}
                    <div class="mb-6" x-show="deliveryStatus === 'delivered'" x-transition x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating Vendor</label>
                        <div class="flex items-center gap-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" @click="userRating = {{ $i }}" class="text-3xl transition-colors focus:outline-none"
                                    :class="userRating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'">
                                    &#9733;
                                </button>
                            @endfor
                            <input type="hidden" name="user_rating" :value="userRating">
                        </div>
                    </div>

                    {{-- Feedback (only if delivered) --}}
                    <div class="mb-6" x-show="deliveryStatus === 'delivered'" x-transition x-cloak>
                        <label for="user_feedback" class="block text-sm font-medium text-gray-700 mb-1">Feedback untuk Vendor</label>
                        <textarea id="user_feedback" name="user_feedback" rows="3" placeholder="Bagikan pengalaman Anda dengan vendor ini..."
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                    </div>

                    {{-- Photos --}}
                    <div class="mb-6">
                        <label for="photos" class="block text-sm font-medium text-gray-700 mb-1">Foto Barang (Opsional)</label>
                        <input type="file" id="photos" name="photos[]" multiple accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 file:cursor-pointer">
                        <p class="text-xs text-gray-400 mt-1">Upload foto barang yang diterima (maksimal 5 foto)</p>
                    </div>

                    {{-- Dispute Reason (only if disputed) --}}
                    <div class="mb-6" x-show="deliveryStatus === 'disputed'" x-transition x-cloak>
                        <label for="dispute_reason" class="block text-sm font-medium text-gray-700 mb-1">Alasan Masalah <span class="text-red-500">*</span></label>
                        <textarea id="dispute_reason" name="dispute_reason" rows="3" placeholder="Jelaskan masalah yang ditemukan..."
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <a href="{{ route('user.auctions.show', $auction) }}" class="inline-flex items-center justify-center border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold py-2 px-4 rounded-lg transition">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                            <i class="fas fa-check mr-2"></i> Konfirmasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@once
<script>
    function deliveryConfirm() {
        return {
            deliveryStatus: '',
            userRating: 0
        }
    }
</script>
@endonce
