@extends('layouts.vendor')

@section('title', 'Penawaran Saya')

@section('content')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Penawaran Saya</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola semua penawaran yang telah Anda berikan</p>
        </div>
        <a href="{{ route('vendor.auctions.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 border border-blue-600 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-50 transition-colors">
            <i class="fas fa-gavel"></i> Lihat Lelang
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

    @if ($bids->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($bids as $bid)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-3">
                            <h5 class="text-lg font-semibold text-gray-900">{{ $bid->auction->title }}</h5>
                            @if ($bid->status === 'accepted')
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Diterima</span>
                            @elseif($bid->status === 'rejected')
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">Ditolak</span>
                            @else
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Menunggu</span>
                            @endif
                        </div>

                        <p class="text-sm text-gray-500 mb-4">{{ Str::limit($bid->auction->description, 100) }}</p>

                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div>
                                <div class="text-xs text-gray-500">Kategori</div>
                                <div class="text-sm font-semibold text-gray-900">{{ $bid->auction->category }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Jumlah</div>
                                <div class="text-sm font-semibold text-gray-900">{{ number_format($bid->auction->quantity) }} pcs</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Penawaran Saya</div>
                                <div class="text-sm font-semibold text-green-600">Rp {{ number_format($bid->bid_amount) }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Budget Maks</div>
                                <div class="text-sm font-semibold text-gray-900">Rp {{ number_format($bid->auction->budget) }}</div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mb-3 text-xs text-gray-500">
                            <span>Oleh: <span class="font-semibold text-gray-700">{{ $bid->auction->user->name }}</span></span>
                            <span>{{ $bid->created_at->format('d M Y') }}</span>
                        </div>

                        @if ($bid->message)
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                                <p class="text-xs text-blue-800"><strong>Pesan:</strong> {{ $bid->message }}</p>
                            </div>
                        @endif

                        <div class="flex justify-between items-center mb-4 text-xs text-gray-500">
                            <span>Deadline: <span class="font-semibold text-gray-700">{{ $bid->auction->deadline->format('d M Y') }}</span></span>
                            <span>Status Lelang:
                                @if ($bid->auction->status === 'active')
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-800">{{ ucfirst($bid->auction->status) }}</span>
                                @endif
                            </span>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('vendor.auctions.show', $bid->auction) }}"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700 transition-colors">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </a>
                            @if ($bid->auction->isActive() && $bid->status === 'pending')
                                <a href="{{ route('vendor.auctions.edit-bid', $bid) }}"
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-amber-600 text-white rounded-lg text-xs font-medium hover:bg-amber-700 transition-colors">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @endif
                        </div>

                        @if ($bid->auction->isActive() && $bid->status === 'pending')
                            <div class="mt-2">
                                <form action="{{ route('vendor.auctions.destroy-bid', $bid) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus penawaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700 transition-colors">
                                        <i class="fas fa-trash"></i> Hapus Penawaran
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-center">
            {{ $bids->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12">
            <div class="flex flex-col items-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-plus-circle text-gray-400 text-3xl"></i>
                </div>
                <p class="text-lg font-medium text-gray-900">Belum ada penawaran</p>
                <p class="text-sm text-gray-500 mt-1 text-center max-w-md">Anda belum memberikan penawaran untuk lelang apapun. Mulai berikan penawaran untuk lelang yang sesuai dengan kemampuan produksi Anda.</p>
                <a href="{{ route('vendor.auctions.index') }}"
                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    <i class="fas fa-gavel"></i> Lihat Lelang
                </a>
            </div>
        </div>
    @endif
@endsection
