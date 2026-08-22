@extends('layouts.user')

@section('title', 'Dashboard Lelang')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6 space-y-6">

    {{-- Welcome Card with Profile Status --}}
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold">Dashboard Lelang <i class="fas fa-tags text-yellow-300"></i></h1>
                <p class="text-primary-100 mt-1">Kelola profil lelang, pantau lelang, dan lacak pesanan Anda.</p>
                @if($profile)
                    <div class="mt-3 flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $profile->is_active ? 'bg-green-100 text-green-800' : ($profile->status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                            {{ $profile->status_label }}
                        </span>
                        @if($profile->is_verified)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-check-circle mr-1"></i> Terverifikasi
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                <i class="fas fa-clock mr-1"></i> Menunggu Verifikasi
                            </span>
                        @endif
                    </div>
                @endif
            </div>
            <a href="{{ route('user.auctions.create') }}" class="inline-flex items-center justify-center bg-white text-primary-700 font-semibold shadow-sm border border-gray-300 hover:bg-gray-50 py-2 px-4 rounded-lg transition flex-shrink-0">
                <i class="fas fa-plus text-sm"></i>
                Buat Lelang Baru
            </a>
        </div>
    </div>

    {{-- Profile Warning (if no profile or not verified) --}}
    @if(!$profile)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
            <div>
                <p class="text-sm font-medium text-amber-800">Profil Lelang Belum Tersedia</p>
                <p class="text-sm text-amber-600 mt-1">Profil Anda akan dibuat otomatis saat pertama kali membuat lelang. Atau hubungi admin untuk membuat profil manual.</p>
            </div>
        </div>
    @elseif(!$profile->is_verified)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
            <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
            <div>
                <p class="text-sm font-medium text-blue-800">Profil Sedang Dalam Verifikasi</p>
                <p class="text-sm text-blue-600 mt-1">Profil Anda sedang menunggu verifikasi dari admin. Anda tetap bisa membuat lelang, namun beberapa fitur mungkin terbatas.</p>
            </div>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Total Lelang</p>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-bold text-gray-900">{{ $myAuctionsCount ?? 0 }}</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $activeAuctionsCount ?? 0 }} aktif
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Lelang Selesai</p>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-bold text-gray-900">{{ $completedAuctionsCount ?? 0 }}</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <i class="fas fa-check text-xs"></i>
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Pesanan Aktif</p>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-bold text-gray-900">{{ $pendingOrdersCount ?? 0 }}</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                    {{ $ordersCount ?? 0 }} total
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Total Pengeluaran</p>
            <div class="flex items-end">
                <span class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalSpent ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Quick Stats Row --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-primary-50 flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-primary-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Menunggu Persetujuan</p>
                    <p class="text-lg font-bold text-gray-900">{{ $pendingAuctionsCount ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center">
                    <i class="fas fa-coins text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Nilai Lelang Selesai</p>
                    <p class="text-lg font-bold text-gray-900">Rp {{ number_format($totalAuctionsValue ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-gavel text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Bid Diterima</p>
                    <p class="text-lg font-bold text-gray-900">{{ $totalBidsOnMyAuctions ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Auctions --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900">Lelang Terbaru</h3>
            <a href="{{ route('user.auctions.my') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">Lihat Semua</a>
        </div>
        @if(isset($recentAuctions) && $recentAuctions->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($recentAuctions as $auction)
                    @php
                        $statusStyles = [
                            'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'label' => 'Menunggu'],
                            'active' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Aktif'],
                            'closed' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => 'Ditutup'],
                            'completed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Selesai'],
                            'paid' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'label' => 'Dibayar'],
                            'waiting_payment' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Menunggu Pembayaran'],
                        ];
                        $style = $statusStyles[$auction->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => ucfirst($auction->status)];
                    @endphp
                    <a href="{{ route('user.auctions.show', $auction) }}" class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $auction->title }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $auction->category }} · {{ $auction->quantity }} pcs · Deadline: {{ $auction->deadline->format('d M Y') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3 ml-4">
                            <span class="text-sm font-semibold text-gray-900">Rp {{ number_format($auction->budget, 0, ',', '.') }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $style['bg'] }} {{ $style['text'] }}">
                                {{ $style['label'] }}
                            </span>
                            @if($auction->bids->count() > 0)
                                <span class="text-xs text-gray-500">{{ $auction->bids->count() }} bid</span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="px-4 py-12 text-center">
                <i class="fas fa-inbox text-3xl text-gray-300 mb-3"></i>
                <p class="text-sm text-gray-500">Belum ada lelang.</p>
                <a href="{{ route('user.auctions.create') }}" class="mt-3 inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-700">
                    <i class="fas fa-plus mr-1"></i> Buat Lelang Pertama
                </a>
            </div>
        @endif
    </div>

    {{-- Quick Actions --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('user.auctions.create') }}" class="bg-white rounded-xl border border-gray-200 p-4 hover:border-primary-300 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 rounded-lg bg-primary-50 flex items-center justify-center mb-3 group-hover:bg-primary-100 transition-colors">
                <i class="fas fa-plus text-primary-600"></i>
            </div>
            <p class="text-sm font-medium text-gray-900">Buat Lelang Baru</p>
            <p class="text-xs text-gray-500 mt-0.5">Kirim permintaan cetak</p>
        </a>

        <a href="{{ route('user.auctions.my') }}" class="bg-white rounded-xl border border-gray-200 p-4 hover:border-primary-300 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center mb-3 group-hover:bg-blue-100 transition-colors">
                <i class="fas fa-list text-blue-600"></i>
            </div>
            <p class="text-sm font-medium text-gray-900">Lelang Saya</p>
            <p class="text-xs text-gray-500 mt-0.5">Kelola semua lelang</p>
        </a>

        <a href="{{ route('user.orders.index') }}" class="bg-white rounded-xl border border-gray-200 p-4 hover:border-primary-300 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center mb-3 group-hover:bg-green-100 transition-colors">
                <i class="fas fa-truck text-green-600"></i>
            </div>
            <p class="text-sm font-medium text-gray-900">Pesanan Saya</p>
            <p class="text-xs text-gray-500 mt-0.5">Lacak pengiriman</p>
        </a>

        <a href="{{ route('user.profile.edit') }}" class="bg-white rounded-xl border border-gray-200 p-4 hover:border-primary-300 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center mb-3 group-hover:bg-amber-100 transition-colors">
                <i class="fas fa-user-cog text-amber-600"></i>
            </div>
            <p class="text-sm font-medium text-gray-900">Profil Saya</p>
            <p class="text-xs text-gray-500 mt-0.5">Edit informasi akun</p>
        </a>
    </div>

</div>
@endsection
