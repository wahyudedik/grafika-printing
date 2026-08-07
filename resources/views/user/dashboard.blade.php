@extends('layouts.user')

@section('title', 'User Dashboard')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6 space-y-6" x-data="{ loading: false }" x-init="loading = false">

    {{-- Loading Skeleton (tampilkan jika $loading dari controller) --}}
    @if($loading ?? false)
        {{-- Welcome Skeleton --}}
        <div class="bg-gradient-to-r from-gray-200 to-gray-300 rounded-2xl p-6 sm:p-8 animate-pulse">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="h-8 w-64 bg-white/30 rounded mb-2"></div>
                    <div class="h-4 w-48 bg-white/20 rounded"></div>
                </div>
                <div class="h-10 w-40 bg-white/30 rounded-xl"></div>
            </div>
        </div>

        {{-- Stats Skeleton --}}
        <x-skeleton type="stats" :count="4" />

        {{-- Lists Skeleton --}}
        <div class="grid lg:grid-cols-2 gap-6">
            <x-skeleton type="list" :count="3" />
            <x-skeleton type="list" :count="3" />
        </div>

    @else
    {{-- Welcome Card --}}
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold">Selamat Datang, {{ auth()->user()->name }}! 👋</h1>
                <p class="text-primary-100 mt-1">Kelola lelang, pesanan, dan aktivitas Anda di Grafika Printing.</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <x-ui.button :href="route('user.lelang-dashboard')" variant="outline" size="md" class="!bg-white/20 !text-white !border-white/30 !font-semibold !shadow-sm">
                    <i class="fas fa-chart-line text-sm"></i>
                    Dashboard Lelang
                </x-ui.button>
                <x-ui.button :href="route('user.auctions.create')" variant="outline" size="md" class="!bg-white !text-primary-700 !font-semibold !shadow-sm">
                    <i class="fas fa-plus text-sm"></i>
                    Buat Lelang
                </x-ui.button>
            </div>
        </div>
    </div>

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
            <p class="text-sm text-gray-500 mb-1">Pesanan Aktif</p>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-bold text-gray-900">{{ $pendingOrdersCount ?? 0 }}</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    {{ $ordersCount ?? 0 }} total
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
            <p class="text-sm text-gray-500 mb-1">Total Pengeluaran</p>
            <div class="flex items-end">
                <span class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalSpent ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Recent Auctions & Orders --}}
    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Recent Auctions --}}
        @if(isset($recentAuctions) && $recentAuctions->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Lelang Terbaru</h3>
                <a href="{{ route('user.auctions.my') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">Lihat Semua</a>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($recentAuctions as $auction)
                    @php
                        $statusStyles = [
                            'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800'],
                            'active' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
                            'closed' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'],
                            'completed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
                            'paid' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-800'],
                            'waiting_payment' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800'],
                            'refunded' => ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
                            'disputed' => ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
                        ];
                        $style = $statusStyles[$auction->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'];
                    @endphp
                    <div class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition-colors">
                        <div class="flex-1 min-w-0 mr-3">
                            <a href="{{ route('user.auctions.show', $auction) }}" class="text-sm font-medium text-gray-900 hover:text-primary-600 transition-colors truncate block">
                                {{ Str::limit($auction->title, 35) }}
                            </a>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $auction->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $style['bg'] }} {{ $style['text'] }} flex-shrink-0">
                            {{ ucfirst($auction->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Recent Orders --}}
        @if(isset($recentOrders) && $recentOrders->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Pesanan Terbaru</h3>
                <a href="{{ route('user.orders.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">Lihat Semua</a>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($recentOrders as $order)
                    <div class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition-colors">
                        <div class="flex-1 min-w-0 mr-3">
                            <a href="{{ route('user.orders.show', $order) }}" class="text-sm font-medium text-gray-900 hover:text-primary-600 transition-colors truncate block">
                                {{ Str::limit($order->auction->title ?? 'N/A', 35) }}
                            </a>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $order->status_color ?? 'bg-gray-100 text-gray-800' }} flex-shrink-0">
                            {{ $order->status_label ?? ucfirst($order->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Quick Actions --}}
    <div class="grid sm:grid-cols-3 gap-4">
        <a href="{{ route('user.auctions.my') }}" class="bg-white rounded-xl border border-gray-200 p-5 hover:border-primary-300 hover:shadow-md transition-all group">
            <div class="text-3xl mb-3">🏆</div>
            <h3 class="text-sm font-semibold text-gray-900 group-hover:text-primary-600 transition-colors">Lelang Saya</h3>
            <p class="text-xs text-gray-500 mt-1">Lihat semua lelang yang telah Anda buat</p>
        </a>

        <a href="{{ route('user.orders.index') }}" class="bg-white rounded-xl border border-gray-200 p-5 hover:border-primary-300 hover:shadow-md transition-all group">
            <div class="text-3xl mb-3">📦</div>
            <h3 class="text-sm font-semibold text-gray-900 group-hover:text-primary-600 transition-colors">Tracking Pesanan</h3>
            <p class="text-xs text-gray-500 mt-1">Lacak status pesanan dari lelang yang Anda menangkan</p>
        </a>

        <a href="{{ route('user.profile.edit') }}" class="bg-white rounded-xl border border-gray-200 p-5 hover:border-primary-300 hover:shadow-md transition-all group">
            <div class="text-3xl mb-3">👤</div>
            <h3 class="text-sm font-semibold text-gray-900 group-hover:text-primary-600 transition-colors">Profil Saya</h3>
            <p class="text-xs text-gray-500 mt-1">Kelola informasi profil dan akun Anda</p>
        </a>
    </div>

    {{-- Account Info --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900">Informasi Akun</h3>
        </div>
        <div class="p-4">
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-gray-500">Nama Lengkap</label>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">{{ auth()->user()->name }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Email</label>
                    <p class="text-sm font-medium text-gray-900 mt-0.5">{{ auth()->user()->email }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Tipe Akun</label>
                    <p class="mt-0.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ ucfirst(auth()->user()->usertype) }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Status Verifikasi</label>
                    <p class="mt-0.5">
                        @if (auth()->user()->email_verified_at)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check text-xs mr-1"></i> Terverifikasi
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                <i class="fas fa-exclamation-triangle text-xs mr-1"></i> Belum Terverifikasi
                            </span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Features Info --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900">Fitur Tersedia</h3>
        </div>
        <div class="p-4">
            <div class="grid sm:grid-cols-3 gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-lg">🏆</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900">Sistem Lelang</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Buat permintaan cetak dan terima penawaran dari vendor</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-lg">💳</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900">Pembayaran Xendit</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Integrasi dengan Xendit untuk pembayaran yang aman dan mudah</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-lg">⭐</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900">Rating Vendor</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Beri rating dan review untuk vendor setelah pesanan selesai</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif {{-- End loading check --}}
</div>
@endsection
