@extends('layouts.vendor')

@section('title', 'Beri Penawaran')

@section('content')
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Beri Penawaran</h2>
            <p class="text-sm text-gray-500 mt-1">Berikan penawaran terbaik Anda untuk lelang ini</p>
        </div>
        <a href="{{ route('vendor.auctions.show', $auction) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Main Content: Bid Form --}}
        <div class="lg:col-span-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Form Penawaran</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('vendor.auctions.store-bid', $auction) }}" method="POST" data-loading>
                        @csrf

                        <div class="space-y-5">
                            {{-- Bid Amount --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Harga Penawaran <span class="text-red-500">*</span>
                                </label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 bg-gray-50 border border-r-0 border-gray-300 text-gray-500 text-sm rounded-l-lg">Rp</span>
                                    <input type="number"
                                        class="flex-1 min-w-0 px-3 py-2 border border-gray-300 rounded-r-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('bid_amount') border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                        name="bid_amount" value="{{ old('bid_amount') }}"
                                        placeholder="Masukkan harga penawaran" min="0" step="1"
                                        required>
                                </div>
                                @error('bid_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">
                                    Budget maksimal: <strong class="text-gray-700">Rp {{ number_format($auction->budget) }}</strong>
                                </p>
                            </div>

                            {{-- Message --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pesan (Opsional)</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('message') border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                    name="message" rows="4"
                                    placeholder="Tambahkan pesan atau catatan untuk pemilik lelang...">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Maksimal 1000 karakter</p>
                            </div>

                            {{-- Attention Alert --}}
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-semibold text-blue-800">Perhatian!</h4>
                                        <div class="mt-1 text-sm text-blue-700">
                                            <ul class="list-disc list-inside space-y-1">
                                                <li>Pastikan harga yang Anda berikan sudah termasuk semua biaya produksi</li>
                                                <li>Anda hanya bisa memberikan satu penawaran per lelang</li>
                                                <li>Penawaran dapat diedit selama lelang masih aktif</li>
                                                <li>Pemilik lelang akan memilih pemenang secara manual</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Submit --}}
                            <div class="flex items-center gap-2 pt-2">
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-paper-plane"></i>
                                    Kirim Penawaran
                                </button>
                                <a href="{{ route('vendor.auctions.show', $auction) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    Batal
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-4 space-y-6">
            {{-- Auction Details Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Detail Lelang</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Judul</p>
                            <p class="mt-1 font-semibold text-gray-900">{{ $auction->title }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Kategori</p>
                            <p class="mt-1 text-sm text-gray-700">{{ $auction->category }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Jumlah Produksi</p>
                            <p class="mt-1 font-semibold text-gray-900">{{ number_format($auction->quantity) }} pcs</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Budget Maksimal</p>
                            <p class="mt-1 font-semibold text-green-600">Rp {{ number_format($auction->budget) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Deadline</p>
                            <p class="mt-1 font-semibold text-gray-900">{{ $auction->deadline->format('d M Y H:i') }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $auction->deadline->diffForHumans() }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pemilik</p>
                            <div class="mt-1 flex items-center gap-2">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-semibold text-white">{{ substr($auction->user->name, 0, 2) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $auction->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $auction->user->email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistics Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Statistik</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Total Penawaran</p>
                            <p class="mt-1 text-xl font-bold text-gray-900">{{ $auction->getBidCount() }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Penawaran Terendah</p>
                            <p class="mt-1 text-xl font-bold text-green-600">
                                @if ($auction->getLowestBid())
                                    Rp {{ number_format($auction->getLowestBid()) }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Vendor Profile Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Profile Vendor</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        @if (auth()->user()->vendorUser->first()->logo)
                            <img src="{{ asset('storage/' . auth()->user()->vendorUser->first()->logo) }}" alt="{{ auth()->user()->vendorUser->first()->name }}" class="w-12 h-12 rounded-full object-cover flex-shrink-0">
                        @else
                            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-semibold text-white">{{ substr(auth()->user()->vendorUser->first()->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div>
                            <p class="font-semibold text-gray-900">{{ auth()->user()->vendorUser->first()->name }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->vendorUser->first()->email }}</p>
                        </div>
                    </div>

                    {{-- Rating --}}
                    <div class="mb-4">
                        <div class="flex items-center gap-1.5">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= floor(auth()->user()->vendorUser->first()->average_rating))
                                    <i class="fas fa-star text-yellow-400 text-sm"></i>
                                @elseif($i - 0.5 <= auth()->user()->vendorUser->first()->average_rating)
                                    <i class="fas fa-star-half-alt text-yellow-400 text-sm"></i>
                                @else
                                    <i class="far fa-star text-yellow-400 text-sm"></i>
                                @endif
                            @endfor
                            <span class="text-sm font-bold text-gray-900">{{ number_format(auth()->user()->vendorUser->first()->average_rating, 1) }}</span>
                            <span class="text-xs text-gray-500">({{ auth()->user()->vendorUser->first()->rating_count }} rating)</span>
                        </div>
                    </div>

                    <a href="{{ route('vendor.public.profile', auth()->user()->vendorUser->first()->vendor_id) }}" target="_blank" class="flex items-center justify-center gap-2 w-full px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                        <i class="fas fa-external-link-alt text-xs"></i>
                        Lihat Profile Lengkap
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
