@extends('dev.layouts.app')

@section('title', 'Detail Lelang')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $auction->title }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Detail lelang dan semua penawaran</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.auctions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:text-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
            <a href="{{ route('admin.auctions.edit', $auction) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-amber-500 border border-transparent rounded-lg hover:bg-amber-600 transition-colors">
                <i class="fas fa-edit"></i>
                Edit Lelang
            </a>
            @if($auction->status === 'pending')
                <form action="{{ route('admin.auctions.approve', $auction) }}" method="POST" class="inline" x-data>
                    @csrf
                    <button type="submit" @click="return confirm('Setujui lelang ini?')" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-500 border border-transparent rounded-lg hover:bg-emerald-600 transition-colors">
                        <i class="fas fa-check"></i>
                        Setujui Lelang
                    </button>
                </form>
                <div x-data="{ open: false }">
                    <button type="button" @click="open = true" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-red-500 border border-transparent rounded-lg hover:bg-red-600 transition-colors">
                        <i class="fas fa-times"></i>
                        Tolak Lelang
                    </button>
                    {{-- Reject Modal --}}
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" x-cloak @click.away="open = false">
                        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
                        <div class="flex min-h-full items-center justify-center p-4">
                            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-md">
                                <form action="{{ route('admin.auctions.reject', $auction) }}" method="POST">
                                    @csrf
                                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tolak Lelang</h3>
                                    </div>
                                    <div class="px-6 py-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                                        <textarea name="rejection_reason" rows="4" required placeholder="Masukkan alasan penolakan lelang..." class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                                    </div>
                                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                                        <button type="button" @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:text-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">Batal</button>
                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 transition-colors">Tolak Lelang</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if($auction->status === 'active')
                <form action="{{ route('admin.auctions.close', $auction) }}" method="POST" class="inline" x-data>
                    @csrf
                    <button type="submit" @click="return confirm('Tutup lelang ini?')" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-amber-500 border border-transparent rounded-lg hover:bg-amber-600 transition-colors">
                        <i class="fas fa-times-circle"></i>
                        Tutup Lelang
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Auction Details --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Detail Lelang</h2>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($auction->status === 'pending') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300
                                @elseif($auction->status === 'active') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300
                                @elseif($auction->status === 'closed') bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300
                                @elseif($auction->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                {{ ucfirst($auction->status) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Kategori</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $auction->category }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Oleh</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $auction->user->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $auction->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Budget Maksimal</p>
                            <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($auction->budget) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Jumlah Produksi</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($auction->quantity) }} pcs</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Deadline</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $auction->deadline->format('d M Y H:i') }}</p>
                            <p class="text-xs @if($auction->deadline->isFuture()) text-gray-500 dark:text-gray-400 @else text-red-600 dark:text-red-400 @endif">
                                @if($auction->deadline->isFuture())
                                    {{ $auction->deadline->diffForHumans() }}
                                @else
                                    Sudah lewat
                                @endif
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Deskripsi</p>
                            <div class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg p-3 whitespace-pre-wrap">{{ $auction->description }}</div>
                        </div>
                        @if($auction->specifications)
                            <div class="md:col-span-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Spesifikasi</p>
                                <div class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                    <pre class="whitespace-pre-wrap font-sans text-sm">{{ $auction->specifications }}</pre>
                                </div>
                            </div>
                        @endif
                        @if($auction->file_path)
                            <div class="md:col-span-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">File Lampiran</p>
                                <a href="{{ asset('storage/' . $auction->file_path) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary-700 bg-primary-50 border border-primary-300 rounded-lg hover:bg-primary-100 dark:text-primary-300 dark:bg-primary-900/30 dark:border-primary-700 dark:hover:bg-primary-900/50 transition-colors">
                                    <i class="fas fa-download"></i>
                                    Download File
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Bids Summary --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Ringkasan Penawaran</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Total Penawaran</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $bids->count() }}</span>
                    </div>
                    @if($bids->count() > 0)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Penawaran Terendah</span>
                            <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($bids->min('bid_amount')) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Penawaran Tertinggi</span>
                            <span class="text-sm font-bold text-red-600 dark:text-red-400">Rp {{ number_format($bids->max('bid_amount')) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Rata-rata</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">Rp {{ number_format($bids->avg('bid_amount')) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Winner Info --}}
            @if($auction->winnerVendor)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Pemenang</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                                @if($auction->winnerVendor->logo)
                                    <img src="{{ asset('storage/' . $auction->winnerVendor->logo) }}" alt="{{ $auction->winnerVendor->name }}" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <span class="text-sm font-bold text-primary-700 dark:text-primary-300">{{ substr($auction->winnerVendor->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $auction->winnerVendor->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $auction->winnerVendor->email }}</p>
                                <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($auction->winning_bid) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Bids List --}}
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Penawaran</h2>
        </div>
        <div class="p-6">
            @if($bids->count() > 0)
                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Vendor</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Harga Penawaran</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Pesan</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Status</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Tanggal</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($bids as $bid)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
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
                    @foreach($bids as $bid)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4" x-data="{ open: false }">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
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
        <div x-data="{ open: false }" x-on:open-bid-{{ $bid->id }}.window="open = true">
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
