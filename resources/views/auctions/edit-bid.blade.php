@extends('layouts.vendor')

@section('title', 'Edit Penawaran')

@section('content')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Penawaran</h2>
            <p class="text-sm text-gray-500 mt-1">Perbarui penawaran Anda untuk lelang ini</p>
        </div>
        <a href="{{ route('vendor.auctions.show', $bid->auction) }}"
            class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Form Edit Penawaran</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('vendor.auctions.update-bid', $bid) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Penawaran <span class="text-red-500">*</span></label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-l-lg">Rp</span>
                                    <input type="number"
                                        class="flex-1 px-3 py-2.5 border border-gray-300 rounded-r-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('bid_amount') border-red-500 @enderror"
                                        name="bid_amount" value="{{ old('bid_amount', $bid->bid_amount) }}"
                                        placeholder="Masukkan harga penawaran" min="0" step="1000" required>
                                </div>
                                @error('bid_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Budget maksimal: <strong>Rp {{ number_format($bid->auction->budget) }}</strong></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pesan (Opsional)</label>
                                <textarea name="message" rows="4"
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('message') border-red-500 @enderror"
                                    placeholder="Tambahkan pesan atau catatan untuk pemilik lelang...">{{ old('message', $bid->message) }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Maksimal 1000 karakter</p>
                            </div>

                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                                    <div>
                                        <h4 class="text-sm font-semibold text-amber-800">Perhatian!</h4>
                                        <ul class="mt-1 text-sm text-amber-700 list-disc list-inside">
                                            <li>Mengubah penawaran akan memperbarui waktu penawaran</li>
                                            <li>Pastikan harga yang Anda berikan sudah termasuk semua biaya produksi</li>
                                            <li>Penawaran dapat diedit selama lelang masih aktif</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3 mt-6">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 text-white rounded-lg text-sm font-medium hover:bg-amber-700 transition-colors">
                                <i class="fas fa-save"></i> Update Penawaran
                            </button>
                            <a href="{{ route('vendor.auctions.show', $bid->auction) }}"
                                class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Detail Lelang</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs text-gray-500">Judul</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ $bid->auction->title }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Kategori</dt>
                            <dd class="text-sm text-gray-900">{{ $bid->auction->category }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Jumlah Produksi</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ number_format($bid->auction->quantity) }} pcs</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Budget Maksimal</dt>
                            <dd class="text-sm font-bold text-green-600">Rp {{ number_format($bid->auction->budget) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Deadline</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ $bid->auction->deadline->format('d M Y H:i') }}</dd>
                            <dd class="text-xs text-gray-500">{{ $bid->auction->deadline->diffForHumans() }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Penawaran Saat Ini</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs text-gray-500">Harga</dt>
                            <dd class="text-sm font-bold text-green-600">Rp {{ number_format($bid->bid_amount) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Status</dt>
                            <dd>
                                @if ($bid->status === 'accepted')
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Diterima</span>
                                @elseif($bid->status === 'rejected')
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">Ditolak</span>
                                @else
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Menunggu</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Dikirim</dt>
                            <dd class="text-sm text-gray-900">{{ $bid->created_at->format('d M Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection
