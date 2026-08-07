@extends('layouts.vendor')

@section('title', 'Detail Transaksi')
@section('content')
    <div class="bg-white rounded-xl shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h3 class="text-lg font-semibold text-gray-900">Detail Transaksi: {{ $transaksi->kode }}</h3>
            <div class="flex items-center gap-2">
                <a href="{{ route('vendor.transactions.edit', $transaksi->id) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('vendor.transactions.index') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Transaction Details --}}
                <div>
                    <h4 class="text-base font-semibold text-gray-900 mb-4">Info Transaksi</h4>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Kode Transaksi</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $transaksi->kode }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Tanggal</dt>
                                <dd class="text-sm text-gray-900">{{ $transaksi->tanggal_dibuat->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div class="flex justify-between items-center">
                                <dt class="text-sm text-gray-500">Status</dt>
                                <dd>
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'processing' => 'bg-blue-100 text-blue-800',
                                            'quality_check' => 'bg-purple-100 text-purple-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Pending',
                                            'processing' => 'Diproses',
                                            'quality_check' => 'QC',
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Dibatalkan',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $statusColors[$transaksi->status] }}">
                                        {{ $statusLabels[$transaksi->status] }}
                                    </span>
                                </dd>
                            </div>
                            <div class="flex justify-between items-center">
                                <dt class="text-sm text-gray-500">Progress</dt>
                                <dd class="flex items-center gap-2">
                                    <span class="text-xs font-medium text-gray-600">{{ $transaksi->progress_percentage }}%</span>
                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $transaksi->progress_percentage }}%"></div>
                                    </div>
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Metode Pembayaran</dt>
                                <dd class="text-sm text-gray-900">{{ $transaksi->payment_method }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Estimasi Selesai</dt>
                                <dd class="text-sm text-gray-900">{{ $transaksi->estimasi_selesai->format('d/m/Y') }}</dd>
                            </div>
                            <div class="border-t border-gray-200 pt-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-900">Total Harga</dt>
                                <dd class="text-sm font-bold text-gray-900">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Terbayar</dt>
                                <dd class="text-sm text-gray-900">Rp {{ number_format($transaksi->terbayar ?? $transaksi->total_harga, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Kembali</dt>
                                <dd class="text-sm text-gray-900">Rp {{ number_format($transaksi->kembali ?? 0, 0, ',', '.') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Customer Details --}}
                <div>
                    <h4 class="text-base font-semibold text-gray-900 mb-4">Info Pelanggan</h4>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Nama</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $transaksi->pelanggan->nama ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Email</dt>
                                <dd class="text-sm text-gray-900">{{ $transaksi->pelanggan->email ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Telepon</dt>
                                <dd class="text-sm text-gray-900">{{ $transaksi->pelanggan->telepon ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Alamat</dt>
                                <dd class="text-sm text-gray-900 text-right max-w-[200px]">{{ $transaksi->pelanggan->alamat ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Transaction Items --}}
            <div class="mt-8">
                <h4 class="text-base font-semibold text-gray-900 mb-4">Item Transaksi</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kuantitas</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($transaksi->transaksiItem as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $item->produk->nama_produk ?? 'Produk Tidak Ditemukan' }}</div>
                                            @if ($item->transaksiItemSpecifications->count() > 0)
                                                <div class="mt-1 space-y-0.5">
                                                    @foreach ($item->transaksiItemSpecifications as $spec)
                                                        <div class="text-xs text-gray-500">
                                                            <span class="font-medium">{{ $spec->spesifikasiProduk->spesifikasi->nama_spesifikasi ?? 'Spesifikasi' }}</span>:
                                                            {{ $spec->value }}
                                                            @if ($spec->bahan)
                                                                ({{ $spec->bahan->nama_bahan }})
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->kuantitas }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        Rp {{ number_format($item->kuantitas * $item->harga_satuan, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <th colspan="3" class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Total:</th>
                                <th class="px-6 py-3 text-sm font-bold text-gray-900">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Additional Notes --}}
            @if ($transaksi->catatan)
                <div class="mt-6">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h4 class="text-sm font-semibold text-gray-900 mb-2">Catatan</h4>
                        <p class="text-sm text-gray-700">{{ $transaksi->catatan }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="border-t border-gray-200 px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                @if ($transaksi->status != 'completed' && $transaksi->status != 'cancelled')
                    <form action="{{ route('vendor.transactions.update', $transaksi->id) }}" method="POST" id="status-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="pelanggan_id" value="{{ $transaksi->pelanggan_id }}">
                        <input type="hidden" name="payment_method" value="{{ $transaksi->payment_method }}">
                        <input type="hidden" name="estimasi_selesai" value="{{ $transaksi->estimasi_selesai->format('Y-m-d') }}">
                        <input type="hidden" name="catatan" value="{{ $transaksi->catatan }}">

                        @foreach ($transaksi->transaksiItem as $index => $item)
                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                            <input type="hidden" name="items[{{ $index }}][produk_id]" value="{{ $item->produk_id }}">
                            <input type="hidden" name="items[{{ $index }}][kuantitas]" value="{{ $item->kuantitas }}">
                            <input type="hidden" name="items[{{ $index }}][harga_satuan]" value="{{ $item->harga_satuan }}">
                        @endforeach

                        <div class="relative" x-data="{ openStatus: false }" @click.outside="openStatus = false">
                            <button type="button" @click="openStatus = !openStatus"
                                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                Ubah Status <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div x-show="openStatus" x-transition
                                class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                @if ($transaksi->status != 'processing')
                                    <button type="button" onclick="updateStatus('processing')"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Proses</button>
                                @endif
                                @if ($transaksi->status != 'quality_check')
                                    <button type="button" onclick="updateStatus('quality_check')"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Quality Check</button>
                                @endif
                                @if ($transaksi->status != 'completed')
                                    <button type="button" onclick="updateStatus('completed')"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Selesai</button>
                                @endif
                                @if ($transaksi->status != 'cancelled')
                                    <button type="button" onclick="updateStatus('cancelled')"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Batalkan</button>
                                @endif
                            </div>
                        </div>
                    </form>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <button type="button"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors"
                    onclick="confirmDelete({{ $transaksi->id }})">
                    <i class="fas fa-trash"></i> Hapus
                </button>
                <a href="{{ route('vendor.transactions.invoice', $transaksi->id) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-file-invoice"></i> Cetak Invoice
                </a>
            </div>
        </div>
    </div>

    {{-- Hidden delete form --}}
    <form id="delete-form" action="" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            function confirmDelete(id) {
                Swal.fire({
                    title: 'Anda yakin?',
                    text: "Data transaksi yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
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
                        const form = document.getElementById('delete-form');
                        form.action = '{{ route("vendor.transactions.destroy", $transaksi->id) }}';
                        form.submit();
                    }
                });
            }

            function updateStatus(status) {
                Swal.fire({
                    title: 'Ubah Status?',
                    text: `Apakah Anda yakin ingin mengubah status transaksi menjadi ${status}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, ubah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('status-form');
                        const statusInput = document.createElement('input');
                        statusInput.type = 'hidden';
                        statusInput.name = 'status';
                        statusInput.value = status;
                        form.appendChild(statusInput);

                        Swal.fire({
                            title: 'Sedang Memproses',
                            text: 'Mohon tunggu...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        form.submit();
                    }
                });
            }
        </script>
    @endpush
@endsection
