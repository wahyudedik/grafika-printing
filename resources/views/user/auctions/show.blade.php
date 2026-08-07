@extends('layouts.user')

@section('title', 'Detail Lelang')

@section('breadcrumbs')
    <x-breadcrumbs :items="[
        ['label' => 'Dasbor', 'url' => route('user.dashboard')],
        ['label' => 'Lelang', 'url' => route('user.auctions.index')],
        ['label' => Str::limit($auction->title, 40)],
    ]" />
@endsection

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $auction->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">Oleh {{ $auction->user->name }} &bull; {{ $auction->created_at->diffForHumans() }}</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            @if ($auction->user_id === auth()->id())
                @if ($auction->status === 'paid' || $auction->status === 'completed')
                    <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed" title="Lelang sudah dibayar, tidak dapat diedit">
                        <i class="fas fa-lock mr-2"></i> Edit (Dikunci)
                    </span>
                @else
                    <x-ui.button :href="route('user.auctions.edit', $auction)" variant="outline">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </x-ui.button>
                @endif

                @if ($auction->status === 'paid' && !$auction->hasDeliveryConfirmation())
                    <x-ui.button :href="route('user.delivery-confirmation.create', $auction)" variant="success">
                        <i class="fas fa-check-circle mr-2"></i> Konfirmasi Barang
                    </x-ui.button>
                @elseif ($auction->hasDeliveryConfirmation())
                    @php $confirmation = $auction->deliveryConfirmation; @endphp
                    @php
                        $confColors = [
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'delivered' => 'bg-green-100 text-green-700',
                            'disputed' => 'bg-red-100 text-red-700',
                            'resolved' => 'bg-blue-100 text-blue-700',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $confColors[$confirmation->status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $confirmation->status_label }}
                    </span>
                @endif
            @endif
            <x-ui.button :href="route('user.auctions.index')" variant="outline">
                Kembali
            </x-ui.button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="mb-6 bg-green-50 border border-green-200 rounded-lg px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2"><i class="fas fa-check-circle text-green-600"></i><span class="text-sm text-green-800">{{ session('success') }}</span></div>
            <button @click="show = false" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 bg-red-50 border border-red-200 rounded-lg px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2"><i class="fas fa-exclamation-circle text-red-600"></i><span class="text-sm text-red-800">{{ session('error') }}</span></div>
            <button @click="show = false" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Status Indicator --}}
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3">
        <div class="flex items-center gap-2">
            <i class="fas fa-info-circle text-blue-600"></i>
            <div class="text-sm">
                <span class="font-medium text-blue-900">Status Lelang:</span>
                @php
                    $statusBadge = match($auction->status) {
                        'active' => 'bg-yellow-100 text-yellow-700',
                        'waiting_payment' => 'bg-yellow-100 text-yellow-700',
                        'paid' => 'bg-green-100 text-green-700',
                        'completed' => 'bg-green-100 text-green-700',
                        default => 'bg-gray-100 text-gray-700',
                    };
                    $statusLabel = match($auction->status) {
                        'active' => 'Aktif',
                        'waiting_payment' => 'Menunggu Pembayaran',
                        'paid' => 'Sudah Dibayar',
                        'completed' => 'Selesai',
                        default => ucfirst($auction->status),
                    };
                @endphp
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusBadge }}">{{ $statusLabel }}</span>
                @if ($auction->status === 'paid' || $auction->status === 'completed')
                    <span class="text-gray-500 ml-2"><i class="fas fa-lock mr-1"></i>Lelang sudah dibayar, tidak dapat diedit</span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Detail Permintaan --}}
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Detail Permintaan</h2>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-1">Deskripsi</h4>
                        <p class="text-sm text-gray-700">{{ $auction->description }}</p>
                    </div>
                    @if ($auction->specifications)
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Spesifikasi Teknis</h4>
                            <p class="text-sm text-gray-700">{{ $auction->specifications }}</p>
                        </div>
                    @endif
                    @if ($auction->file_path)
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-1">File Desain/Referensi</h4>
                            <x-ui.button :href="asset('storage/auction_files/' . $auction->file_path)" variant="outline-info" size="sm" target="_blank">
                                <i class="fas fa-download mr-1"></i> Download File
                            </x-ui.button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Penawaran Vendor --}}
            @if ($auction->user_id === auth()->id() && $auction->status === 'active' && $auction->bids->count() > 0)
                <div class="bg-white rounded-xl border border-gray-200" x-data="{ selectedBid: null }">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="font-semibold text-gray-900">Penawaran dari Vendor</h2>
                    </div>
                    <div class="px-6 py-5">
                        <form method="POST" action="{{ route('user.auctions.close', $auction) }}">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                @foreach ($auction->bids->where('status', 'pending') as $bid)
                                    <div class="border rounded-lg p-4 cursor-pointer transition-all hover:shadow-sm"
                                        :class="selectedBid == {{ $bid->id }} ? 'border-primary-500 bg-primary-50 ring-2 ring-primary-200' : 'border-gray-200'"
                                        @click="selectedBid = {{ $bid->id }}">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <a href="{{ route('vendor.profile', $bid->vendor->id) }}" target="_blank" class="text-sm font-semibold text-primary-600 hover:text-primary-700">
                                                    {{ $bid->vendor->name }} <i class="fas fa-external-link-alt text-[10px] ml-0.5"></i>
                                                </a>
                                                <p class="text-xs text-gray-500">{{ $bid->vendor->email }}</p>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Rp {{ number_format($bid->bid_amount) }}</span>
                                        </div>
                                        @if ($bid->vendor->average_rating > 0)
                                            <div class="flex items-center gap-1 mb-2">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= floor($bid->vendor->average_rating))
                                                        <i class="fas fa-star text-yellow-400 text-[11px]"></i>
                                                    @elseif($i - 0.5 <= $bid->vendor->average_rating)
                                                        <i class="fas fa-star-half-alt text-yellow-400 text-[11px]"></i>
                                                    @else
                                                        <i class="far fa-star text-yellow-300 text-[11px]"></i>
                                                    @endif
                                                @endfor
                                                <span class="text-[11px] text-gray-500 ml-1">{{ number_format($bid->vendor->average_rating, 1) }} ({{ $bid->vendor->rating_count }})</span>
                                            </div>
                                        @else
                                            <p class="text-[11px] text-gray-400 mb-2">Belum ada rating</p>
                                        @endif
                                        @if ($bid->message)
                                            <p class="text-xs text-gray-600 mb-2">{{ $bid->message }}</p>
                                        @endif
                                        <label class="flex items-center gap-2 text-xs text-gray-700">
                                            <input type="radio" name="winner_bid_id" value="{{ $bid->id }}" class="text-primary-600 focus:ring-primary-500" :checked="selectedBid == {{ $bid->id }}">
                                            Pilih sebagai pemenang
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            @if ($auction->bids->where('status', 'pending')->count() > 0)
                                <div class="flex justify-end">
                                    <x-ui.button type="submit" variant="success">
                                        <i class="fas fa-trophy mr-2"></i> Tutup Lelang & Pilih Pemenang
                                    </x-ui.button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            @endif

            {{-- Payment Button --}}
            @if ($auction->status === 'waiting_payment')
                <div class="bg-white rounded-xl border border-gray-200 px-6 py-5">
                    <form action="{{ route('user.auctions.payment', $auction) }}" method="POST" class="flex justify-end">
                        @csrf
                        <x-ui.button type="submit" variant="primary" size="lg">
                            <i class="fas fa-credit-card mr-2"></i> Bayar Sekarang
                        </x-ui.button>
                    </form>
                </div>
            @endif

            {{-- Pemenang --}}
            @if ($auction->status === 'closed' && $auction->winnerVendor)
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="font-semibold text-gray-900">Pemenang Lelang</h2>
                    </div>
                    <div class="px-6 py-5">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-green-800 mb-2">🏆 Lelang telah ditutup!</h4>
                            <p class="text-sm text-green-700"><strong>Pemenang:</strong> {{ $auction->winnerVendor->name }}</p>
                            <p class="text-sm text-green-700"><strong>Harga Menang:</strong> Rp {{ number_format($auction->winning_bid) }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Informasi Lelang --}}
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Informasi Lelang</h3>
                </div>
                <div class="px-5 py-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Kategori</span>
                            <span class="font-medium text-gray-900">{{ $auction->category }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Jumlah</span>
                            <span class="font-medium text-gray-900">{{ number_format($auction->quantity) }} pcs</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Budget</span>
                            <span class="font-medium text-gray-900">Rp {{ number_format($auction->budget) }}</span>
                        </div>
                        @if ($auction->fees_calculated && $auction->admin_fee_amount > 0)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Biaya Admin</span>
                                <span class="font-medium text-yellow-600">+ Rp {{ number_format($auction->admin_fee_amount) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Payment Gateway</span>
                                <span class="font-medium text-yellow-600">+ Rp {{ number_format($auction->payment_gateway_fee) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm pt-2 border-t border-gray-100">
                                <span class="font-semibold text-gray-900">Total Pembayaran</span>
                                <span class="font-bold text-primary-600">Rp {{ number_format($auction->total_amount_with_fees) }}</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Deadline</span>
                            <span class="font-medium text-gray-900">{{ $auction->deadline->format('d M Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Status</span>
                            @php
                                $sBadge = match($auction->status) {
                                    'active' => 'bg-green-100 text-green-700',
                                    'waiting_payment' => 'bg-yellow-100 text-yellow-700',
                                    'paid' => 'bg-blue-100 text-blue-700',
                                    'closed' => 'bg-blue-100 text-blue-700',
                                    default => 'bg-red-100 text-red-700',
                                };
                                $sLabel = match($auction->status) {
                                    'waiting_payment' => 'Menunggu Pembayaran',
                                    'paid' => 'Terbayar',
                                    default => ucfirst($auction->status),
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $sBadge }}">{{ $sLabel }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Penawaran</span>
                            <span class="font-medium text-gray-900">{{ $auction->getBidCount() }} vendor</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Waktu Tersisa --}}
            @if ($auction->isActive())
                <div class="bg-white rounded-xl border border-gray-200 px-5 py-4 text-center">
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Waktu Tersisa</h4>
                    <p class="text-3xl font-bold text-primary-600">{{ $auction->deadline->diffInDays(now()) }} hari</p>
                    <p class="text-xs text-gray-400 mt-1">Berakhir pada {{ $auction->deadline->format('d M Y H:i') }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection
