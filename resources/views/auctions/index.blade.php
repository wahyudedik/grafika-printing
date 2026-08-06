@extends('layouts.vendor')

@section('title', 'Daftar Lelang')

@section('content')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Daftar Lelang</h2>
            <p class="text-sm text-gray-500 mt-1">Pilih lelang yang sesuai dengan kemampuan produksi Anda</p>
        </div>
        <a href="{{ route('vendor.auctions.my-bids') }}"
            class="inline-flex items-center gap-2 px-4 py-2 border border-blue-600 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-50 transition-colors">
            <i class="fas fa-users"></i> Penawaran Saya
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500 text-lg"></i>
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
                <p class="text-sm text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if ($auctions->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($auctions as $auction)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-3">
                            <h5 class="text-lg font-semibold text-gray-900">{{ $auction->title }}</h5>
                            @if ($auction->status === 'active')
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Aktif</span>
                            @else
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-800">{{ ucfirst($auction->status) }}</span>
                            @endif
                        </div>

                        <p class="text-sm text-gray-500 mb-4">{{ Str::limit($auction->description, 100) }}</p>

                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div>
                                <div class="text-xs text-gray-500">Kategori</div>
                                <div class="text-sm font-semibold text-gray-900">{{ $auction->category }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Jumlah</div>
                                <div class="text-sm font-semibold text-gray-900">{{ number_format($auction->quantity) }} pcs</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Budget</div>
                                <div class="text-sm font-semibold text-green-600">Rp {{ number_format($auction->budget) }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Deadline</div>
                                <div class="text-sm font-semibold text-gray-900">{{ $auction->deadline->format('d M Y') }}</div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mb-4 text-xs text-gray-500">
                            <span>Oleh: <span class="font-semibold text-gray-700">{{ $auction->user->name }}</span></span>
                            <span>{{ $auction->getBidCount() }} penawaran</span>
                        </div>

                        @if ($auction->getLowestBid())
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-blue-700">Penawaran Terendah:</span>
                                    <span class="text-sm font-bold text-blue-900">Rp {{ number_format($auction->getLowestBid()) }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="flex gap-2">
                            <a href="{{ route('vendor.auctions.show', $auction) }}"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700 transition-colors">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </a>
                            @if ($auction->isActive())
                                <a href="{{ route('vendor.auctions.bid', $auction) }}"
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-green-600 text-white rounded-lg text-xs font-medium hover:bg-green-700 transition-colors">
                                    <i class="fas fa-plus"></i> Beri Penawaran
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-center">
            {{ $auctions->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12">
            <x-ui.empty-state icon="fas fa-gavel" title="Belum ada lelang aktif" description="Saat ini belum ada lelang yang tersedia. Silakan kembali lagi nanti." />
        </div>
    @endif
@endsection
