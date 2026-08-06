@extends('layouts.vendor')

@section('title', 'Detail Penarikan')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="text-sm text-gray-500 font-medium">Vendor Panel</div>
        <h2 class="text-2xl font-bold text-gray-900">Detail Penarikan</h2>
    </div>
    <a href="{{ route('vendor.withdrawal.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm">
        Kembali
    </a>
</div>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Detail Card --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Detail Penarikan #{{ $withdrawal->withdrawal_code }}</h3>
                    @if($withdrawal->status === 'pending')
                        <form action="{{ route('vendor.withdrawal.cancel', $withdrawal) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan penarikan ini?')">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">Batalkan</button>
                        </form>
                    @endif
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <div class="text-sm text-gray-500">Kode Penarikan</div>
                            <div class="font-bold">{{ $withdrawal->withdrawal_code }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Status</div>
                            @if($withdrawal->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Menunggu</span>
                            @elseif($withdrawal->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Disetujui</span>
                            @elseif($withdrawal->status === 'processing')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Diproses</span>
                            @elseif($withdrawal->status === 'completed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500 text-white">Selesai</span>
                            @elseif($withdrawal->status === 'rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
                            @elseif($withdrawal->status === 'cancelled')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Dibatalkan</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <div class="text-sm text-gray-500">Jumlah Penarikan</div>
                            <div class="text-xl font-bold text-green-600">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Biaya Admin</div>
                            <div class="text-lg font-semibold">Rp {{ number_format($withdrawal->fee ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <div class="text-sm text-gray-500">Yang Diterima</div>
                            <div class="text-xl font-bold text-primary-600">Rp {{ number_format($withdrawal->net_amount ?? ($withdrawal->amount - ($withdrawal->fee ?? 0)), 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Metode</div>
                            @if($withdrawal->method === 'bank_transfer')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Transfer Bank</span>
                            @elseif($withdrawal->method === 'e_wallet')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">E-Wallet</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Tunai</span>
                            @endif
                        </div>
                    </div>

                    <hr class="my-4 border-gray-200">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <div class="text-sm text-gray-500">Bank / Penyedia</div>
                            <div>{{ $withdrawal->bank_name ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Nomor Rekening</div>
                            <div>{{ $withdrawal->account_number }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <div class="text-sm text-gray-500">Nama Pemilik</div>
                            <div>{{ $withdrawal->account_name }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Tanggal Pengajuan</div>
                            <div>{{ $withdrawal->created_at->format('d M Y H:i') }}</div>
                        </div>
                    </div>

                    @if($withdrawal->notes)
                        <div class="mb-4">
                            <div class="text-sm text-gray-500">Catatan</div>
                            <div>{{ $withdrawal->notes }}</div>
                        </div>
                    @endif

                    @if($withdrawal->admin_notes)
                        <div class="mb-4">
                            <div class="text-sm text-gray-500">Catatan Admin</div>
                            <div class="bg-gray-50 rounded-lg p-3 mt-1">{{ $withdrawal->admin_notes }}</div>
                        </div>
                    @endif

                    @if($withdrawal->processed_at)
                        <div class="mb-4">
                            <div class="text-sm text-gray-500">Diproses Pada</div>
                            <div>{{ $withdrawal->processed_at->format('d M Y H:i') }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info Sidebar --}}
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi</h3>
                </div>
                <div class="p-5">
                    <div class="mb-4">
                        <div class="text-sm text-gray-500 mb-1">Status Penarikan</div>
                        @if($withdrawal->status === 'pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800">Menunggu Persetujuan</span>
                            <p class="text-sm text-gray-500 mt-2">Penarikan Anda sedang menunggu persetujuan dari admin.</p>
                        @elseif($withdrawal->status === 'approved')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Disetujui</span>
                            <p class="text-sm text-gray-500 mt-2">Penarikan Anda telah disetujui dan akan segera diproses.</p>
                        @elseif($withdrawal->status === 'processing')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">Sedang Diproses</span>
                            <p class="text-sm text-gray-500 mt-2">Penarikan Anda sedang diproses oleh tim kami.</p>
                        @elseif($withdrawal->status === 'completed')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500 text-white">Selesai</span>
                            <p class="text-sm text-gray-500 mt-2">Penarikan Anda telah berhasil diproses.</p>
                        @elseif($withdrawal->status === 'rejected')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">Ditolak</span>
                            <p class="text-sm text-gray-500 mt-2">Penarikan Anda ditolak. Silakan hubungi admin untuk informasi lebih lanjut.</p>
                        @elseif($withdrawal->status === 'cancelled')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">Dibatalkan</span>
                            <p class="text-sm text-gray-500 mt-2">Penarikan ini telah Anda batalkan.</p>
                        @endif
                    </div>

                    <hr class="my-4 border-gray-200">

                    <div class="text-sm text-gray-500 space-y-1">
                        <p>• Penarikan diproses dalam 1-3 hari kerja</p>
                        <p>• Anda akan menerima notifikasi setelah diproses</p>
                        <p>• Hubungi admin jika ada pertanyaan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
