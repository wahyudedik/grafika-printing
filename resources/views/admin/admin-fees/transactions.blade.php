@extends('dev.layouts.app')

@section('title', 'Transaksi Biaya Admin')

@section('content')
    <div x-data="{ openDropdown: false, activeModal: null }">
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pengaturan</p>
                    <h1 class="text-2xl font-bold text-gray-900">Transaksi Biaya Admin</h1>
                </div>
                <a href="{{ route('admin.admin-fees.index') }}" class="inline-flex items-center justify-center border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold py-2 px-4 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Transaksi Biaya Admin</h3>
                    <div class="relative" @click.away="openDropdown = false">
                        <button @click="openDropdown = !openDropdown" class="inline-flex items-center justify-center border border-blue-300 text-blue-700 hover:bg-blue-50 font-semibold py-2 px-4 rounded-lg transition">
                            <i class="fas fa-filter mr-1"></i>Filter
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div x-show="openDropdown" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" href="{{ request()->fullUrlWithQuery(['status' => '']) }}">Semua Status</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}">Pending</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" href="{{ request()->fullUrlWithQuery(['status' => 'paid']) }}">Paid</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" href="{{ request()->fullUrlWithQuery(['status' => 'failed']) }}">Failed</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" href="{{ request()->fullUrlWithQuery(['status' => 'refunded']) }}">Refunded</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                @if ($transactions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Transaksi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lelang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Lelang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya Admin</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($transactions as $transaction)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-500">#{{ $transaction->id }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($transaction->auction)
                                                <div class="text-sm font-medium text-gray-900">{{ Str::limit($transaction->auction->title, 30) }}</div>
                                                <div class="text-xs text-gray-500">{{ $transaction->auction->kode }}</div>
                                            @else
                                                <span class="text-sm text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($transaction->vendor)
                                                <div class="text-sm font-medium text-gray-900">{{ $transaction->vendor->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $transaction->vendor->email }}</div>
                                            @else
                                                <span class="text-sm text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($transaction->user)
                                                <div class="text-sm font-medium text-gray-900">{{ $transaction->user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $transaction->user->email }}</div>
                                            @else
                                                <span class="text-sm text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-500">Rp {{ number_format($transaction->auction_amount, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-amber-600">Rp {{ number_format($transaction->admin_fee_amount, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-medium text-gray-900">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-{{ $transaction->status_color }}-100 text-{{ $transaction->status_color }}-800">
                                                {{ $transaction->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-xs text-gray-500">{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <button @click="activeModal = {{ $transaction->id }}" class="inline-flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-100 py-1 px-2 rounded-lg transition">
                                                <i class="fas fa-eye text-sm"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-center mt-6">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
                        <p class="text-lg font-medium text-gray-900">Belum ada transaksi biaya admin</p>
                        <p class="text-sm text-gray-500 mt-1">Transaksi akan muncul setelah ada lelang yang menggunakan biaya admin.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Transaction Detail Modals (Alpine.js) -->
        @foreach ($transactions as $transaction)
            <div x-show="activeModal === {{ $transaction->id }}" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="activeModal === {{ $transaction->id }}" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 transition-opacity bg-gray-500/75" @click="activeModal = null"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                    <div x-show="activeModal === {{ $transaction->id }}" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-bottom transition-all transform bg-white shadow-xl rounded-xl sm:align-middle"
                        @click.outside="activeModal = null">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Detail Transaksi #{{ $transaction->id }}</h3>
                            <button type="button" @click="activeModal = null" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">ID Transaksi</label>
                                    <div class="text-sm text-gray-900">#{{ $transaction->id }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $transaction->status_color }}-100 text-{{ $transaction->status_color }}-800">
                                        {{ $transaction->status_label }}
                                    </span>
                                </div>
                            </div>
                            @if ($transaction->auction)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Lelang</label>
                                    <div class="text-sm text-gray-900 font-medium">{{ $transaction->auction->title }}</div>
                                    <div class="text-xs text-gray-500">{{ $transaction->auction->kode }}</div>
                                </div>
                            @endif
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Vendor</label>
                                    @if ($transaction->vendor)
                                        <div class="text-sm text-gray-900">{{ $transaction->vendor->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $transaction->vendor->email }}</div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">User</label>
                                    @if ($transaction->user)
                                        <div class="text-sm text-gray-900">{{ $transaction->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $transaction->user->email }}</div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Jumlah Lelang</label>
                                    <div class="text-sm text-gray-900">Rp {{ number_format($transaction->auction_amount, 0, ',', '.') }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Biaya Admin</label>
                                    <div class="text-sm text-amber-600">Rp {{ number_format($transaction->admin_fee_amount, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Biaya Payment Gateway</label>
                                    <div class="text-sm text-gray-900">Rp {{ number_format($transaction->payment_gateway_fee, 0, ',', '.') }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Total Pembayaran</label>
                                    <div class="text-sm text-gray-900 font-bold">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Vendor Menerima</label>
                                    <div class="text-sm text-green-600">Rp {{ number_format($transaction->vendor_receives, 0, ',', '.') }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Admin Menerima</label>
                                    <div class="text-sm text-blue-600">Rp {{ number_format($transaction->admin_receives, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Dibuat</label>
                                <div class="text-sm text-gray-900">{{ $transaction->created_at->format('d F Y H:i:s') }}</div>
                            </div>
                            @if ($transaction->updated_at != $transaction->created_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Terakhir Diperbarui</label>
                                    <div class="text-sm text-gray-900">{{ $transaction->updated_at->format('d F Y H:i:s') }}</div>
                                </div>
                            @endif
                        </div>
                        <div class="flex justify-end mt-6 pt-4 border-t border-gray-200">
                            <button @click="activeModal = null" type="button" class="inline-flex items-center justify-center border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold py-2 px-4 rounded-lg transition">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
