@extends('layouts.vendor')

@section('title', 'Detail Lelang')

@section('content')
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $auction->title }}</h2>
            <p class="text-sm text-gray-500 mt-1">Detail lelang dan penawaran</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('vendor.auctions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
            @if ($auction->isActive() && !$myBid)
                <a href="{{ route('vendor.auctions.bid', $auction) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus"></i>
                    Beri Penawaran
                </a>
            @endif
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-3">
            <i class="fas fa-check-circle text-green-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if (session('info'))
        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg flex items-center gap-3">
            <i class="fas fa-info-circle text-blue-500"></i>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-8 space-y-6">
            {{-- Auction Info Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Lelang</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Judul Lelang</p>
                            <p class="mt-1 font-semibold text-gray-900">{{ $auction->title }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Kategori</p>
                            <p class="mt-1 font-semibold text-gray-900">{{ $auction->category }}</p>
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
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Status</p>
                            <div class="mt-1">
                                @if ($auction->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-800">{{ ucfirst($auction->status) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Deskripsi</p>
                            <p class="mt-1 text-sm text-gray-600">{{ $auction->description }}</p>
                        </div>
                        @if ($auction->specifications)
                            <div class="sm:col-span-2">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Spesifikasi Khusus</p>
                                <p class="mt-1 text-sm text-gray-600">{{ $auction->specifications }}</p>
                            </div>
                        @endif
                        @if ($auction->file_path)
                            <div class="sm:col-span-2">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">File Lampiran</p>
                                <div class="mt-2">
                                    <a href="{{ asset('storage/auction_files/' . $auction->file_path) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                                        <i class="fas fa-download text-xs"></i>
                                        Download File
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- My Bid Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Penawaran Saya</h3>
                </div>
                <div class="p-6">
                    @if ($myBid)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Harga Penawaran</p>
                                <p class="mt-1 font-semibold text-green-600">Rp {{ number_format($myBid->bid_amount) }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Status</p>
                                <div class="mt-1">
                                    @if ($myBid->status === 'accepted')
                                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Diterima</span>
                                    @elseif ($myBid->status === 'rejected')
                                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">Ditolak</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Menunggu</span>
                                    @endif
                                </div>
                            </div>
                            @if ($myBid->message)
                                <div class="sm:col-span-2">
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pesan</p>
                                    <p class="mt-1 text-sm text-gray-600">{{ $myBid->message }}</p>
                                </div>
                            @endif
                            <div class="sm:col-span-2">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Dikirim pada</p>
                                <p class="mt-1 text-sm text-gray-600">{{ $myBid->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>

                        @if ($auction->isActive() && $myBid->status === 'pending')
                            <div class="flex items-center gap-2 mt-5 pt-5 border-t border-gray-200">
                                <a href="{{ route('vendor.auctions.edit-bid', $myBid) }}" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition-colors">
                                    <i class="fas fa-edit text-xs"></i>
                                    Edit Penawaran
                                </a>
                                <form action="{{ route('vendor.auctions.destroy-bid', $myBid) }}" method="POST" class="inline" x-data x-on:submit.prevent="if(await $dispatch('show-delete-confirmation')) { $el.submit(); }">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                        <i class="fas fa-trash text-xs"></i>
                                        Hapus Penawaran
                                    </button>
                                </form>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <div class="flex justify-center mb-4">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-plus text-2xl text-gray-400"></i>
                                </div>
                            </div>
                            <p class="text-sm font-medium text-gray-900">Belum ada penawaran</p>
                            <p class="text-sm text-gray-500 mt-1">Anda belum memberikan penawaran untuk lelang ini.</p>
                            @if ($auction->isActive())
                                <div class="mt-4">
                                    <a href="{{ route('vendor.auctions.bid', $auction) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-plus"></i>
                                        Beri Penawaran
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-4 space-y-6">
            {{-- Owner Info Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Pemilik</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-semibold text-white">{{ substr($auction->user->name, 0, 2) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $auction->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $auction->user->email }}</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-500">Bergabung: {{ $auction->user->created_at->format('M Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- Statistics Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Statistik Lelang</h3>
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
                        <div>
                            <p class="text-xs text-gray-500">Sisa Waktu</p>
                            <p class="mt-1 font-semibold text-gray-900">
                                @if ($auction->deadline > now())
                                    {{ $auction->deadline->diffForHumans() }}
                                @else
                                    <span class="text-red-600">Berakhir</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Dibuat</p>
                            <p class="mt-1 font-semibold text-gray-900">{{ $auction->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SweetAlert2 Delete Confirmation --}}
    <script>
        window.addEventListener('show-delete-confirmation', async () => {
            const result = await Swal.fire({
                title: 'Hapus Penawaran?',
                text: "Apakah Anda yakin ingin menghapus penawaran ini? Tindakan ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            });
            return result.isConfirmed;
        });
    </script>
@endsection
