@extends('layouts.vendor')

@section('title', 'Detail Pelanggan')
@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Detail Pelanggan</h3>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pelanggan</label>
                        <p class="text-sm text-gray-900">{{ $pelanggan->kode }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan</label>
                        <p class="text-sm text-gray-900">{{ $pelanggan->nama }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <p class="text-sm text-gray-900">{{ $pelanggan->email ?: '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <p class="text-sm text-gray-900">{{ $pelanggan->no_telp ?: '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <p class="text-sm text-gray-900">{{ $pelanggan->alamat ?: '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Transaksi Terakhir</label>
                        <p class="text-sm text-gray-900">
                            @if ($pelanggan->transaksi_terakhir)
                                {{ $pelanggan->transaksi_terakhir->format('d M Y H:i') }}
                            @else
                                <span class="text-gray-400">Belum ada transaksi</span>
                            @endif
                        </p>
                    </div>
                </div>

                @if ($pelanggan->transaksi->count() > 0)
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-base font-semibold text-gray-900 mb-3">Riwayat Transaksi</h4>
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Transaksi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($pelanggan->transaksi->take(5) as $transaksi)
                                        <tr>
                                            <td class="px-6 py-3 text-sm text-gray-900">{{ $transaksi->kode }}</td>
                                            <td class="px-6 py-3 text-sm text-gray-500">{{ $transaksi->created_at->format('d M Y') }}</td>
                                            <td class="px-6 py-3 text-sm text-gray-900">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
                                            <td class="px-6 py-3">
                                                @if ($transaksi->status == 'completed')
                                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Selesai</span>
                                                @elseif($transaksi->status == 'pending')
                                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">Pending</span>
                                                @elseif($transaksi->status == 'cancelled')
                                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Dibatalkan</span>
                                                @else
                                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">{{ $transaksi->status }}</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-3 text-right">
                                                <a href="#" class="text-sm text-primary hover:underline">Detail</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($pelanggan->transaksi->count() > 5)
                            <div class="text-center mt-4">
                                <a href="#" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                                    Lihat Semua Transaksi
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('vendor.customers.edit', $pelanggan->id) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition">
                    <i class="fas fa-edit"></i>
                    Edit
                </a>
                <a href="{{ route('vendor.customers.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>
            </div>
        </div>
    </div>
@endsection
