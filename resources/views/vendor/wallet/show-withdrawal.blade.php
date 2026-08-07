@extends('layouts.vendor')

@section('title', 'Detail Penarikan Wallet')

@section('content')
<x-ui.breadcrumb :items="[['label' => 'Wallet Dashboard', 'url' => route('vendor.wallet.index')], ['label' => 'Riwayat Penarikan', 'url' => route('vendor.wallet.withdrawals')], ['label' => 'Detail']]" />

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <p class="text-xs font-medium text-primary-600 uppercase tracking-wider mb-1">Vendor Panel</p>
        <h1 class="text-2xl font-bold text-gray-900">Detail Penarikan Wallet</h1>
    </div>
    <x.ui.button href="{{ route('vendor.wallet.withdrawals') }}" variant="outline" icon="fa-arrow-left">
        Kembali
    </x.ui.button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main Detail --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Detail Penarikan #{{ $withdrawal->withdrawal_code }}</h3>
                @if($withdrawal->status === 'pending')
                <form id="cancel-wallet-withdrawal-{{ $withdrawal->id }}" action="{{ route('vendor.wallet.cancel-withdrawal', $withdrawal) }}" method="POST" class="inline"
                      x-data @submit.prevent="confirmFormSubmit('cancel-wallet-withdrawal-{{ $withdrawal->id }}', { title: 'Batalkan Penarikan?', text: 'Apakah Anda yakin ingin membatalkan penarikan ini?', confirmText: 'Ya, Batalkan', confirmColor: '#d33' })">
                    @csrf
                    <x.ui.button type="submit" variant="outline-danger" size="xs">
                        Batalkan
                    </x.ui.button>
                </form>
                @endif
            </div>
            <div class="p-6 space-y-6">
                {{-- Withdrawal Code & Status --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Penarikan</div>
                        <div class="mt-1 text-sm font-semibold text-gray-900">{{ $withdrawal->withdrawal_code }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</div>
                        <div class="mt-1">
                            @if($withdrawal->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Menunggu</span>
                            @elseif($withdrawal->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Disetujui</span>
                            @elseif($withdrawal->status === 'processing')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Diproses</span>
                            @elseif($withdrawal->status === 'completed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-600 text-white">Selesai</span>
                            @elseif($withdrawal->status === 'rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
                            @elseif($withdrawal->status === 'cancelled')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Dibatalkan</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Amount & Fee --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Penarikan</div>
                        <div class="mt-1 text-xl font-bold text-green-600">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya Admin</div>
                        <div class="mt-1 text-lg font-semibold text-gray-900">Rp {{ number_format($withdrawal->fee ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>

                {{-- Net Amount & Method --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Yang Diterima</div>
                        <div class="mt-1 text-xl font-bold text-primary-600">Rp {{ number_format($withdrawal->net_amount ?? ($withdrawal->amount - ($withdrawal->fee ?? 0)), 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</div>
                        <div class="mt-1">
                            @if($withdrawal->method === 'bank_transfer')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Transfer Bank</span>
                            @elseif($withdrawal->method === 'e_wallet')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">E-Wallet</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Tunai</span>
                            @endif
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- Bank & Account --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Bank / Penyedia</div>
                        <div class="mt-1 text-sm text-gray-900">{{ $withdrawal->bank_name ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Rekening</div>
                        <div class="mt-1 text-sm text-gray-900">{{ $withdrawal->account_number }}</div>
                    </div>
                </div>

                {{-- Owner & Date --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pemilik</div>
                        <div class="mt-1 text-sm text-gray-900">{{ $withdrawal->account_name }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</div>
                        <div class="mt-1 text-sm text-gray-900">{{ $withdrawal->created_at->format('d M Y H:i') }}</div>
                    </div>
                </div>

                {{-- Notes --}}
                @if($withdrawal->notes)
                <div>
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</div>
                    <div class="mt-1 text-sm text-gray-700">{{ $withdrawal->notes }}</div>
                </div>
                @endif

                {{-- Admin Notes --}}
                @if($withdrawal->admin_notes)
                <div>
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan Admin</div>
                    <div class="mt-1 p-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700">
                        {{ $withdrawal->admin_notes }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Sidebar: Info --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900">Informasi</h3>
            </div>
            <div class="p-6">
                <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Status Penarikan</div>
                @if($withdrawal->status === 'pending')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Menunggu Persetujuan</span>
                    <p class="text-sm text-gray-500 mt-3">Penarikan Anda sedang menunggu persetujuan dari admin.</p>
                @elseif($withdrawal->status === 'approved')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800">Disetujui</span>
                    <p class="text-sm text-gray-500 mt-3">Penarikan Anda telah disetujui dan akan segera diproses.</p>
                @elseif($withdrawal->status === 'processing')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">Sedang Diproses</span>
                    <p class="text-sm text-gray-500 mt-3">Penarikan Anda sedang diproses oleh tim kami.</p>
                @elseif($withdrawal->status === 'completed')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-600 text-white">Selesai</span>
                    <p class="text-sm text-gray-500 mt-3">Penarikan Anda telah berhasil diproses.</p>
                @elseif($withdrawal->status === 'rejected')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-red-100 text-red-800">Ditolak</span>
                    <p class="text-sm text-gray-500 mt-3">Penarikan Anda ditolak. Silakan hubungi admin.</p>
                @elseif($withdrawal->status === 'cancelled')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800">Dibatalkan</span>
                    <p class="text-sm text-gray-500 mt-3">Penarikan ini telah Anda batalkan.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
