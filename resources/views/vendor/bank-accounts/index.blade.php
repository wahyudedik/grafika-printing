@extends('layouts.vendor')

@section('title', 'Kelola Rekening Bank')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <x-ui.breadcrumb :items="[['label' => 'Kelola Rekening Bank']]" />

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-university text-primary-600"></i> Detail Rekening Bank
            </h3>
        </div>
        <div class="p-5">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
                    <div class="flex items-center gap-2 text-green-800">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center justify-between">
                    <div class="flex items-center gap-2 text-red-800">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
                </div>
            @endif

            {{-- Primary Bank Account --}}
            <div class="mb-6 border-2 border-primary-200 rounded-xl overflow-hidden">
                <div class="bg-primary-600 text-white px-5 py-3 flex items-center justify-between">
                    <h6 class="font-semibold flex items-center gap-2">
                        <i class="fas fa-star"></i> Rekening Utama
                    </h6>
                    @if ($vendor->primary_bank_name)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Terisi</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Belum Diisi</span>
                    @endif
                </div>
                <div class="p-5">
                    @if ($vendor->primary_bank_name)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <div><span class="font-semibold text-gray-700">Nama Bank:</span> {{ $vendor->primary_bank_name }}</div>
                                <div><span class="font-semibold text-gray-700">Nomor Rekening:</span> {{ $vendor->primary_account_number }}</div>
                                <div><span class="font-semibold text-gray-700">Nama Pemilik:</span> {{ $vendor->primary_account_name }}</div>
                            </div>
                            <div class="space-y-2">
                                <div><span class="font-semibold text-gray-700">Kode Bank:</span> {{ $vendor->primary_bank_code ?? '-' }}</div>
                                <div>
                                    <span class="font-semibold text-gray-700">Status Verifikasi:</span>
                                    @if ($vendor->bank_verified)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Terverifikasi</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Belum Diverifikasi</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex gap-2">
                            <x.ui.button href="{{ route('vendor.bank-accounts.edit', 'primary') }}" variant="primary" size="sm">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </x.ui.button>
                            <x.ui.button @click="deleteType = 'primary'; showDeleteModal = true" variant="danger" size="sm">
                                <i class="fas fa-trash mr-1"></i> Hapus
                            </x.ui.button>
                        </div>
                    @else
                        <p class="text-gray-500 mb-3">Belum ada rekening utama yang terdaftar.</p>
                        <x.ui.button href="{{ route('vendor.bank-accounts.create') }}?type=primary" variant="primary">
                            <i class="fas fa-plus mr-2"></i> Tambah Rekening Utama
                        </x.ui.button>
                    @endif
                </div>
            </div>

            {{-- Secondary Bank Account --}}
            <div class="mb-6 border-2 border-blue-200 rounded-xl overflow-hidden">
                <div class="bg-blue-500 text-white px-5 py-3 flex items-center justify-between">
                    <h6 class="font-semibold flex items-center gap-2">
                        <i class="fas fa-university"></i> Rekening Cadangan
                    </h6>
                    @if ($vendor->secondary_bank_name)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Terisi</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Belum Diisi</span>
                    @endif
                </div>
                <div class="p-5">
                    @if ($vendor->secondary_bank_name)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <div><span class="font-semibold text-gray-700">Nama Bank:</span> {{ $vendor->secondary_bank_name }}</div>
                                <div><span class="font-semibold text-gray-700">Nomor Rekening:</span> {{ $vendor->secondary_account_number }}</div>
                                <div><span class="font-semibold text-gray-700">Nama Pemilik:</span> {{ $vendor->secondary_account_name }}</div>
                            </div>
                            <div class="space-y-2">
                                <div><span class="font-semibold text-gray-700">Kode Bank:</span> {{ $vendor->secondary_bank_code ?? '-' }}</div>
                                <div>
                                    <span class="font-semibold text-gray-700">Status Verifikasi:</span>
                                    @if ($vendor->bank_verified)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Terverifikasi</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Belum Diverifikasi</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex gap-2">
                            <x.ui.button href="{{ route('vendor.bank-accounts.edit', 'secondary') }}" variant="info" size="sm">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </x.ui.button>
                            <x.ui.button @click="deleteType = 'secondary'; showDeleteModal = true" variant="danger" size="sm">
                                <i class="fas fa-trash mr-1"></i> Hapus
                            </x.ui.button>
                        </div>
                    @else
                        <p class="text-gray-500 mb-3">Belum ada rekening cadangan yang terdaftar.</p>
                        <x.ui.button href="{{ route('vendor.bank-accounts.create') }}?type=secondary" variant="info">
                            <i class="fas fa-plus mr-2"></i> Tambah Rekening Cadangan
                        </x.ui.button>
                    @endif
                </div>
            </div>

            {{-- E-Wallet Account --}}
            <div class="mb-6 border-2 border-amber-200 rounded-xl overflow-hidden">
                <div class="bg-amber-500 text-white px-5 py-3 flex items-center justify-between">
                    <h6 class="font-semibold flex items-center gap-2">
                        <i class="fas fa-mobile-alt"></i> E-Wallet
                    </h6>
                    @if ($vendor->ewallet_provider)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Terisi</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Belum Diisi</span>
                    @endif
                </div>
                <div class="p-5">
                    @if ($vendor->ewallet_provider)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <div><span class="font-semibold text-gray-700">Provider:</span> {{ $vendor->ewallet_provider }}</div>
                                <div><span class="font-semibold text-gray-700">Nomor E-Wallet:</span> {{ $vendor->ewallet_number }}</div>
                                <div><span class="font-semibold text-gray-700">Nama Pemilik:</span> {{ $vendor->ewallet_name }}</div>
                            </div>
                            <div class="space-y-2">
                                <div>
                                    <span class="font-semibold text-gray-700">Status Verifikasi:</span>
                                    @if ($vendor->bank_verified)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Terverifikasi</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Belum Diverifikasi</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex gap-2">
                            <x.ui.button href="{{ route('vendor.bank-accounts.edit', 'ewallet') }}" variant="warning" size="sm">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </x.ui.button>
                            <x.ui.button @click="deleteType = 'ewallet'; showDeleteModal = true" variant="danger" size="sm">
                                <i class="fas fa-trash mr-1"></i> Hapus
                            </x.ui.button>
                        </div>
                    @else
                        <p class="text-gray-500 mb-3">Belum ada e-wallet yang terdaftar.</p>
                        <x.ui.button href="{{ route('vendor.bank-accounts.create') }}?type=ewallet" variant="warning">
                            <i class="fas fa-plus mr-2"></i> Tambah E-Wallet
                        </x.ui.button>
                    @endif
                </div>
            </div>

            {{-- Bank Notes --}}
            @if ($vendor->bank_notes)
                <div class="mb-6 border border-gray-200 rounded-xl overflow-hidden">
                    <div class="bg-gray-500 text-white px-5 py-3">
                        <h6 class="font-semibold flex items-center gap-2">
                            <i class="fas fa-sticky-note"></i> Catatan Rekening
                        </h6>
                    </div>
                    <div class="p-5">
                        <p class="mb-0">{{ $vendor->bank_notes }}</p>
                    </div>
                </div>
            @endif

            {{-- Verification Status --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mt-6">
                <h6 class="font-semibold text-blue-900 flex items-center gap-2 mb-2">
                    <i class="fas fa-info-circle"></i> Status Verifikasi Rekening
                </h6>
                <p class="text-blue-800 mb-0">
                    @if ($vendor->hasVerifiedBankAccount())
                        <i class="fas fa-check-circle text-green-500 mr-1"></i>
                        Rekening Anda telah diverifikasi oleh admin pada {{ $vendor->bank_verified_at->format('d M Y H:i') }}.
                    @else
                        <i class="fas fa-clock text-amber-500 mr-1"></i>
                        Rekening Anda belum diverifikasi. Silakan tunggu verifikasi dari admin atau hubungi admin untuk proses verifikasi.
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div x-data="{ showDeleteModal: false, deleteType: '' }" x-cloak>
    <div x-show="showDeleteModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDeleteModal = false"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <div class="text-center">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Konfirmasi Hapus</h3>
                    <p class="text-gray-500 mb-6">Apakah Anda yakin ingin menghapus detail rekening ini?</p>
                    <div class="flex gap-3 justify-center">
                        <x.ui.button @click="showDeleteModal = false" variant="outline">Batal</x.ui.button>
                        <form :action="'{{ route('vendor.bank-accounts.destroy', '__ID__') }}'.replace('__ID__', deleteType)" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <x.ui.button type="submit" variant="danger">Hapus</x.ui.button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
