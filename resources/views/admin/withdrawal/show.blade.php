@extends('dev.layouts.app')

@section('title', 'Detail Penarikan')

@section('content')
<div x-data="{ showApproveModal: false, showRejectModal: false, showCompleteModal: false }">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Admin Panel</p>
            <h2 class="text-2xl font-bold text-gray-900">Detail Penarikan</h2>
        </div>
        <a href="{{ route('admin.withdrawals.index') }}" class="inline-flex items-center justify-center border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold py-2 px-4 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Penarikan #{{ $withdrawal->withdrawal_code }}</h3>
                    <div class="flex items-center gap-2">
                        @if($withdrawal->status === 'pending')
                            <button @click="showApproveModal = true" class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-semibold text-sm py-1 px-3 rounded-lg transition">
                                <i class="fas fa-check text-xs mr-1.5"></i>
                                Setujui
                            </button>
                            <button @click="showRejectModal = true" class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-semibold text-sm py-1 px-3 rounded-lg transition">
                                <i class="fas fa-times text-xs mr-1.5"></i>
                                Tolak
                            </button>
                        @elseif($withdrawal->status === 'approved')
                            <button @click="showCompleteModal = true" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm py-1 px-3 rounded-lg transition">
                                <i class="fas fa-check-double text-xs mr-1.5"></i>
                                Selesaikan
                            </button>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Kode Penarikan</p>
                            <p class="mt-1 font-semibold text-gray-900">{{ $withdrawal->withdrawal_code }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Status</p>
                            <div class="mt-1">
                                @if($withdrawal->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Menunggu</span>
                                @elseif($withdrawal->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Disetujui</span>
                                @elseif($withdrawal->status === 'processing')
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Diproses</span>
                                @elseif($withdrawal->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-600 text-white">Selesai</span>
                                @elseif($withdrawal->status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">Ditolak</span>
                                @elseif($withdrawal->status === 'cancelled')
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Dibatalkan</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Vendor</p>
                            <p class="mt-1 text-sm text-gray-700">{{ $withdrawal->vendor->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Jumlah</p>
                            <p class="mt-1 text-xl font-bold text-green-600">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Biaya Admin</p>
                            <p class="mt-1 text-sm text-gray-700">Rp {{ number_format($withdrawal->fee ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Yang Diterima</p>
                            <p class="mt-1 text-lg font-semibold text-blue-600">Rp {{ number_format($withdrawal->net_amount ?? ($withdrawal->amount - ($withdrawal->fee ?? 0)), 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Metode</p>
                            <div class="mt-1">
                                @if($withdrawal->method === 'bank_transfer')
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Transfer Bank</span>
                                @elseif($withdrawal->method === 'e_wallet')
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-purple-100 text-purple-800">E-Wallet</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Tunai</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Bank / Penyedia</p>
                            <p class="mt-1 text-sm text-gray-700">{{ $withdrawal->bank_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Nomor Rekening</p>
                            <p class="mt-1 text-sm text-gray-700">{{ $withdrawal->account_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Nama Pemilik</p>
                            <p class="mt-1 text-sm text-gray-700">{{ $withdrawal->account_name }}</p>
                        </div>
                    </div>

                    @if($withdrawal->admin_notes)
                    <div class="mt-5 pt-5 border-t border-gray-200">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Catatan Admin</p>
                        <div class="mt-1 p-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700">{{ $withdrawal->admin_notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Tambahan</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tanggal Pengajuan</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $withdrawal->created_at->format('d M Y H:i') }}</p>
                    </div>
                    @if($withdrawal->processedBy)
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Diproses Oleh</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $withdrawal->processedBy->name }}</p>
                    </div>
                    @endif
                    @if($withdrawal->processed_at)
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tanggal Diproses</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $withdrawal->processed_at->format('d M Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Approve Modal --}}
    <div x-show="showApproveModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showApproveModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" @click.outside="showApproveModal = false">
                <form action="{{ route('admin.withdrawals.approve', $withdrawal) }}" method="POST">
                    @csrf
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <h5 class="text-lg font-semibold text-gray-900">Setujui Penarikan</h5>
                        <button type="button" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors" @click="showApproveModal = false">
                            <i class="fas fa-times text-gray-500"></i>
                        </button>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-gray-600">Apakah Anda yakin ingin menyetujui penarikan ini?</p>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" name="admin_notes" rows="3" placeholder="Catatan untuk vendor"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                        <button @click="showApproveModal = false" type="button" class="inline-flex items-center justify-center border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold py-2 px-4 rounded-lg transition">Batal</button>
                        <button type="submit" class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">Setujui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showRejectModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" @click.outside="showRejectModal = false">
                <form action="{{ route('admin.withdrawals.reject', $withdrawal) }}" method="POST">
                    @csrf
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <h5 class="text-lg font-semibold text-gray-900">Tolak Penarikan</h5>
                        <button type="button" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors" @click="showRejectModal = false">
                            <i class="fas fa-times text-gray-500"></i>
                        </button>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-gray-600">Apakah Anda yakin ingin menolak penarikan ini?</p>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan <span class="text-red-500">*</span></label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" name="admin_notes" rows="3" required placeholder="Masukkan alasan penolakan"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                        <button @click="showRejectModal = false" type="button" class="inline-flex items-center justify-center border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold py-2 px-4 rounded-lg transition">Batal</button>
                        <button type="submit" class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition">Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Complete Modal --}}
    <div x-show="showCompleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showCompleteModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" @click.outside="showCompleteModal = false">
                <form action="{{ route('admin.withdrawals.complete', $withdrawal) }}" method="POST">
                    @csrf
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <h5 class="text-lg font-semibold text-gray-900">Selesaikan Penarikan</h5>
                        <button type="button" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors" @click="showCompleteModal = false">
                            <i class="fas fa-times text-gray-500"></i>
                        </button>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-gray-600">Apakah Anda yakin ingin menandai penarikan ini sebagai selesai?</p>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" name="admin_notes" rows="3" placeholder="Catatan penyelesaian"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                        <button @click="showCompleteModal = false" type="button" class="inline-flex items-center justify-center border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold py-2 px-4 rounded-lg transition">Batal</button>
                        <button type="submit" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">Selesaikan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
