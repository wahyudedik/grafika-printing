@extends('layouts.vendor')

@section('title', 'Void Transaksi ' . $transaksi->kode)
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-ban text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Void Transaksi</h3>
                        <p class="text-sm text-gray-500">Batalkan transaksi {{ $transaksi->kode }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                {{-- Info Transaksi --}}
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Detail Transaksi</h4>
                    <dl class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <dt class="text-gray-500">Kode</dt>
                            <dd class="font-medium text-gray-900">{{ $transaksi->kode }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Pelanggan</dt>
                            <dd class="font-medium text-gray-900">{{ $transaksi->pelanggan->nama ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Total Harga</dt>
                            <dd class="font-medium text-gray-900">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Status</dt>
                            <dd>
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                    {{ ucfirst($transaksi->status) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Metode Bayar</dt>
                            <dd class="font-medium text-gray-900">{{ ucfirst($transaksi->payment_method) }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Tanggal</dt>
                            <dd class="font-medium text-gray-900">{{ $transaksi->tanggal_dibuat->format('d/m/Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Items yang akan di-restore stoknya --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mt-0.5">
                            <i class="fas fa-undo text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-blue-900">Pengembalian Stok</h4>
                            <p class="text-sm text-blue-700 mt-1">
                                Stok bahan berikut akan dikembalikan secara otomatis:
                            </p>
                            <ul class="mt-2 space-y-1 text-sm text-blue-700">
                                @foreach ($transaksi->transaksiItem as $item)
                                    @foreach ($item->transaksiItemSpecifications as $spec)
                                        @if ($spec->bahan)
                                            <li class="flex items-center gap-2">
                                                <i class="fas fa-check text-blue-500 text-xs"></i>
                                                {{ $spec->bahan->nama_bahan }}
                                                —
                                                @php
                                                    $qty = $spec->input_type === 'number'
                                                        ? (float) $spec->value * $item->kuantitas
                                                        : $item->kuantitas;
                                                @endphp
                                                +{{ number_format($qty, 2, ',', '.') }} {{ $spec->bahan->satuan ?? 'pcs' }}
                                            </li>
                                        @endif
                                    @endforeach
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Info Refund --}}
                @if ($refundInfo)
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center mt-0.5">
                                <i class="fas fa-money-bill-wave text-amber-600 text-sm"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-amber-900">Proses Refund</h4>
                                <p class="text-sm text-amber-700 mt-1">
                                    Transaksi ini dibayar via <strong>{{ $refundInfo['payment_method'] }}</strong>.
                                    Sejumlah <strong>Rp {{ number_format($refundInfo['amount'], 0, ',', '.') }}</strong>
                                    akan dikembalikan melalui Xendit. Proses refund biasanya memakan waktu 1-7 hari kerja.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Form Void --}}
                <form action="{{ route('vendor.transactions.confirm-void', $transaksi->id) }}" method="POST" id="void-form">
                    @csrf

                    <div class="space-y-4">
                        {{-- Jenis Alasan --}}
                        <div>
                            <label for="reason_type" class="block text-sm font-medium text-gray-700 mb-1">
                                Jenis Alasan <span class="text-red-500">*</span>
                            </label>
                            <select name="reason_type" id="reason_type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('reason_type') border-red-500 @enderror"
                                required>
                                <option value="">— Pilih Alasan —</option>
                                <option value="customer_request" {{ old('reason_type') === 'customer_request' ? 'selected' : '' }}>Permintaan Pelanggan</option>
                                <option value="stock_issue" {{ old('reason_type') === 'stock_issue' ? 'selected' : '' }}>Masalah Stok</option>
                                <option value="pricing_error" {{ old('reason_type') === 'pricing_error' ? 'selected' : '' }}>Kesalahan Harga</option>
                                <option value="quality_issue" {{ old('reason_type') === 'quality_issue' ? 'selected' : '' }}>Masalah Kualitas</option>
                                <option value="system_error" {{ old('reason_type') === 'system_error' ? 'selected' : '' }}>Kesalahan Sistem</option>
                                <option value="other" {{ old('reason_type') === 'other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('reason_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Alasan Detail --}}
                        <div>
                            <label for="void_reason" class="block text-sm font-medium text-gray-700 mb-1">
                                Alasan Void <span class="text-red-500">*</span>
                            </label>
                            <textarea name="void_reason" id="void_reason" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('void_reason') border-red-500 @enderror"
                                placeholder="Jelaskan alasan pembatalan transaksi ini..."
                                required>{{ old('void_reason') }}</textarea>
                            @error('void_reason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Peringatan --}}
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mt-6">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mt-0.5">
                                <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-red-900">Peringatan</h4>
                                <p class="text-sm text-red-700 mt-1">
                                    Tindakan ini <strong>TIDAK DAPAT DIBATALKAN</strong>. Transaksi akan ditandai sebagai "Dibatalkan"
                                    dan stok akan dikembalikan. Semua data transaksi tetap tersimpan untuk keperluan audit.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                        <a href="{{ route('vendor.transactions.show', $transaksi->id) }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </a>
                        <button type="button" onclick="confirmVoid()"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors inline-flex items-center gap-2">
                            <i class="fas fa-ban"></i>
                            Void Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmVoid() {
                const reasonType = document.getElementById('reason_type').value;
                const voidReason = document.getElementById('void_reason').value;

                if (!reasonType) {
                    Swal.fire({
                        title: 'Jenis Alasan Wajib',
                        text: 'Silakan pilih jenis alasan void terlebih dahulu.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                if (!voidReason || voidReason.length < 5) {
                    Swal.fire({
                        title: 'Alasan Wajib Diisi',
                        text: 'Alasan void minimal 5 karakter.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Yakin Ingin Void?',
                    text: "Transaksi akan dibatalkan dan stok akan dikembalikan. Tindakan ini tidak dapat dibatalkan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Void!',
                    cancelButtonText: 'Batal',
                    inputValidator: undefined,
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Sedang Memproses',
                            text: 'Mohon tunggu...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        document.getElementById('void-form').submit();
                    }
                });
            }
        </script>
    @endpush
@endsection
