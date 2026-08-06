@extends('dev.layouts.app')

@section('title', 'Penawaran Lelang')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $auction->title }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Semua penawaran untuk lelang ini</p>
        </div>
        <a href="{{ route('admin.auctions.show', $auction) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:text-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Detail
        </a>
    </div>

    {{-- Auction Info --}}
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Informasi Lelang</h2>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Status</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                        @if($auction->status === 'pending') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300
                        @elseif($auction->status === 'active') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300
                        @elseif($auction->status === 'closed') bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300
                        @elseif($auction->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                        {{ ucfirst($auction->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Budget</p>
                    <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($auction->budget) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Jumlah</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($auction->quantity) }} pcs</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Deadline</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $auction->deadline->format('d M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Bids List --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Penawaran ({{ $bids->count() }})</h2>
        </div>
        <div class="p-6">
            @if($bids->count() > 0)
                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">No</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Vendor</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Harga Penawaran</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Pesan</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Status</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Tanggal</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($bids as $index => $bid)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="py-3 px-4 text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                                                @if($bid->vendor->logo)
                                                    <img src="{{ asset('storage/' . $bid->vendor->logo) }}" alt="{{ $bid->vendor->name }}" class="w-8 h-8 rounded-full object-cover">
                                                @else
                                                    <span class="text-xs font-bold text-primary-700 dark:text-primary-300">{{ substr($bid->vendor->name, 0, 1) }}</span>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900 dark:text-white">{{ $bid->vendor->name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $bid->vendor->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($bid->bid_amount) }}</p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                            @if($bid->bid_amount <= $auction->budget) bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 @else bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 @endif">
                                            @if($bid->bid_amount <= $auction->budget) Dalam Budget @else Melebihi Budget @endif
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($bid->message)
                                            <p class="text-sm text-gray-700 dark:text-gray-300 truncate max-w-[200px]" title="{{ $bid->message }}">{{ $bid->message }}</p>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($bid->status === 'accepted') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300
                                            @elseif($bid->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                            @else bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 @endif">
                                            {{ ucfirst($bid->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="text-sm text-gray-900 dark:text-white">{{ $bid->created_at->format('d M Y') }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $bid->created_at->format('H:i') }}</p>
                                    </td>
                                    <td class="py-3 px-4 text-center" x-data="{ open: false }">
                                        <button type="button" @click="open = true" class="inline-flex items-center justify-center w-8 h-8 text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden space-y-3">
                    @foreach($bids as $index => $bid)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4" x-data="{ open: false }">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">#{{ $index + 1 }}</span>
                                    <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                                        @if($bid->vendor->logo)
                                            <img src="{{ asset('storage/' . $bid->vendor->logo) }}" alt="{{ $bid->vendor->name }}" class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <span class="text-xs font-bold text-primary-700 dark:text-primary-300">{{ substr($bid->vendor->name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $bid->vendor->name }}</p>
                                    </div>
                                </div>
                                <button @click="open = true" class="text-primary-600 dark:text-primary-400"><i class="fas fa-eye"></i></button>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($bid->bid_amount) }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    @if($bid->status === 'accepted') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300
                                    @elseif($bid->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                    @else bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 @endif">
                                    {{ ucfirst($bid->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $bid->created_at->format('d M Y H:i') }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-gavel text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">Belum ada penawaran</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada vendor yang memberikan penawaran untuk lelang ini.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Bid Detail Modals --}}
    @foreach($bids as $bid)
        <div x-data="{ open: false }">
            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" x-cloak @click.away="open = false">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
                <div class="flex min-h-full items-center justify-center p-4">
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Detail Penawaran</h3>
                            <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="px-6 py-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Vendor</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $bid->vendor->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $bid->vendor->email }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Harga Penawaran</p>
                                    <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($bid->bid_amount) }}</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Pesan</p>
                                <div class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg p-3">{{ $bid->message ?: 'Tidak ada pesan' }}</div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($bid->status === 'accepted') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300
                                        @elseif($bid->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                        @else bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 @endif">
                                        {{ ucfirst($bid->status) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tanggal Penawaran</p>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $bid->created_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                            <button @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:text-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
