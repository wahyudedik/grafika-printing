<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dasbor') - Grafika Printing</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-gray-50 font-sans text-gray-900 antialiased"
    x-data="{ sidebarOpen: false }"
    x-init="$store.sidebar = { collapsed: localStorage.getItem('sidebarCollapsed') === 'true' }; $watch('$store.sidebar.collapsed', v => localStorage.setItem('sidebarCollapsed', v))">

    @php
        $vendorName = 'Dasbor';
        if (auth()->check()) {
            $vendorUser = optional(auth()->user())->vendorUser->first();
            if ($vendorUser) {
                $vendorName = $vendorUser->name ?? ($vendorUser->nama_vendor ?? 'Dasbor');
            }
        }

        // Icon SVGs
        $iconHome = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/></svg>';
        $iconGrid = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>';
        $iconCalculator = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>';
        $iconPrinter = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>';
        $iconUsers = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>';
        $iconReceipt = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>';
        $iconBox = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>';
        $iconArchive = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>';
        $iconWallet = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>';
        $iconBank = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11m16-11v11M8 10v11m4-11v11m4-11v11"/></svg>';
        $iconSwitch = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>';
        $iconTruck = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4zM9.5 4.5l2-2h5l2 2M4 10h16v7H4V10z"/></svg>';
        $iconLightning = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>';
        $iconClipboard = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>';
        $iconUserGroup = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
        $iconLink = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>';
        $iconChart = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';
        $iconDoc = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';

        $vendorMenus = [
            [
                'items' => [
                    ['label' => 'Beranda', 'url' => route('welcome'), 'route' => 'welcome', 'icon' => $iconHome],
                    ['label' => 'Dasbor',  'url' => route('vendor.dashboard'), 'route' => 'vendor.dashboard', 'icon' => $iconGrid],
                ]
            ],
            [
                'label' => 'Penjualan',
                'items' => [
                    ['label' => 'POS',       'url' => route('vendor.pos.index'), 'route' => 'vendor.pos.*', 'icon' => $iconCalculator],
                    ['label' => 'Cetak',     'url' => route('vendor.pos.printer.settings'), 'route' => 'vendor.pos.printer.*', 'icon' => $iconPrinter],
                    ['label' => 'Pelanggan', 'url' => route('vendor.customers.index'), 'route' => 'vendor.customers.*', 'icon' => $iconUsers],
                    ['label' => 'Transaksi', 'url' => route('vendor.transactions.index'), 'route' => 'vendor.transactions.*', 'icon' => $iconReceipt],
                ]
            ],
            [
                'label' => 'Produk & Inventori',
                'items' => [
                    [
                        'label' => 'Produk',
                        'route' => 'vendor.products.*|vendor.specifications.*|vendor.categories.*',
                        'icon' => $iconBox,
                        'children' => [
                            ['label' => 'Spesifikasi',  'url' => route('vendor.specifications.index'), 'route' => 'vendor.specifications.*'],
                            ['label' => 'Produk',       'url' => route('vendor.products.index'), 'route' => 'vendor.products.*'],
                            ['label' => 'Kategori',     'url' => route('vendor.categories.index'), 'route' => 'vendor.categories.*'],
                        ]
                    ],
                    [
                        'label' => 'Bahan & Alat',
                        'route' => 'vendor.materials.*|vendor.tools.*',
                        'icon' => $iconArchive,
                        'children' => [
                            ['label' => 'Bahan', 'url' => route('vendor.materials.index'), 'route' => 'vendor.materials.*'],
                            ['label' => 'Alat',  'url' => route('vendor.tools.index'), 'route' => 'vendor.tools.*'],
                        ]
                    ],
                ]
            ],
            [
                'label' => 'Keuangan',
                'items' => [
                    ['label' => 'Dompet',          'url' => route('vendor.wallet.index'), 'route' => 'vendor.wallet.*', 'icon' => $iconWallet],
                    ['label' => 'Bank',            'url' => route('vendor.bank-accounts.index'), 'route' => 'vendor.bank-accounts.*', 'icon' => $iconBank],
                    ['label' => 'Transfer Manual',  'url' => route('vendor.manual-transfers.index'), 'route' => 'vendor.manual-transfers.*', 'icon' => $iconSwitch],
                    ['label' => 'Ongkir',          'url' => route('vendor.shipping.calculator'), 'route' => 'vendor.shipping.*', 'icon' => $iconTruck],
                ]
            ],
            [
                'label' => 'Lelang',
                'items' => [
                    ['label' => 'Daftar Lelang',   'url' => route('vendor.auctions.index'), 'route' => 'vendor.auctions.index', 'icon' => $iconLightning],
                    ['label' => 'Penawaran Saya',  'url' => route('vendor.auctions.my-bids'), 'route' => 'vendor.auctions.my-bids', 'icon' => $iconDoc],
                ]
            ],
            [
                'label' => 'Lainnya',
                'items' => [
                    ['label' => 'Tracking',   'url' => route('vendor.tracking.index'), 'route' => 'vendor.tracking.*', 'icon' => $iconClipboard],
                    ['label' => 'Pengguna',   'url' => route('vendor.users.index'), 'route' => 'vendor.users.*', 'icon' => $iconUserGroup],
                    [
                        'label' => 'Linktree',
                        'route' => 'vendor.linktree.*',
                        'icon' => $iconLink,
                        'children' => [
                            ['label' => 'Semua Linktree', 'url' => route('vendor.linktree.index'), 'route' => 'vendor.linktree.index'],
                            ['label' => 'Buat Baru',      'url' => route('vendor.linktree.create'), 'route' => 'vendor.linktree.create'],
                        ]
                    ],
                    [
                        'label' => 'Laporan',
                        'route' => 'vendor.laporan.*',
                        'icon' => $iconChart,
                        'children' => [
                            ['label' => 'Penjualan Harian',  'url' => route('vendor.laporan.penjualan-harian'), 'route' => 'vendor.laporan.penjualan-harian'],
                            ['label' => 'Penjualan Bulanan', 'url' => route('vendor.laporan.penjualan-bulanan'), 'route' => 'vendor.laporan.penjualan-bulanan'],
                            ['label' => 'Penjualan Tahunan', 'url' => route('vendor.laporan.penjualan-tahunan'), 'route' => 'vendor.laporan.penjualan-tahunan'],
                        ]
                    ],
                    ['label' => 'Audit Log', 'url' => route('vendor.audit-logs.index'), 'route' => 'vendor.audit-logs.*', 'icon' => $iconDoc],
                ]
            ],
        ];
    @endphp

    {{-- ========== SIDEBAR ========== --}}
    <x-sidebar :menus="$vendorMenus" :brandName="$vendorName" />

    {{-- ========== SIDEBAR OVERLAY (mobile) ========== --}}
    <div x-show="sidebarOpen"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden"
        x-cloak>
    </div>

    {{-- ========== MAIN AREA ========== --}}
    <div class="lg:pl-64 transition-all duration-300" :class="$store.sidebar.collapsed ? 'lg:pl-[72px]' : 'lg:pl-64'">

        {{-- ========== TOP BAR ========== --}}
        <header class="sticky top-0 z-40 flex items-center h-16 px-4 bg-white border-b border-gray-200 sm:px-6 lg:px-8 print:hidden">
            {{-- Hamburger (mobile) --}}
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 -ml-2 text-gray-500 rounded-lg lg:hidden hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div class="flex items-center gap-2 ml-auto">
                {{-- Notifications --}}
                <div class="relative" x-data="{ notifDropdown: false }" @click.away="notifDropdown = false">
                    <button @click="notifDropdown = !notifDropdown" class="relative p-2 text-gray-500 rounded-lg hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3H6a4 4 0 0 0 2-3v-3a7 7 0 0 1 4-6"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v1a3 3 0 0 0 6 0v-1"/>
                        </svg>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute -top-0.5 -right-0.5 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
                                {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>
                    <div x-show="notifDropdown" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50" x-cloak>
                        <div class="px-4 py-2 text-sm font-semibold text-gray-900 border-b border-gray-100">Notifikasi</div>
                        <div class="px-4 py-6 text-center text-sm text-gray-500">Belum ada notifikasi baru</div>
                    </div>
                </div>

                {{-- User Dropdown --}}
                <div class="relative" x-data="{ userDropdown: false }" @click.away="userDropdown = false">
                    <button @click="userDropdown = !userDropdown" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <span class="flex items-center justify-center w-8 h-8 text-xs font-semibold text-white rounded-full bg-primary-600">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </span>
                        <div class="hidden md:block text-left">
                            <div class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-gray-500">{{ auth()->user()->usertype }}</div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="userDropdown" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50" x-cloak>
                        <a href="{{ route('vendor.profile') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profil
                        </a>
                        <div class="my-1 border-t border-gray-100"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center w-full gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- ========== MAIN CONTENT ========== --}}
        <main class="min-h-[calc(100vh-4rem)]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                {{-- Page Header --}}
                <div class="mb-6 print:hidden">
                    <h1 class="text-2xl font-bold text-gray-900">
                        @yield('title', 'Dasbor')
                    </h1>
                </div>

                {{-- Breadcrumbs --}}
                @yield('breadcrumbs')

                {{-- Page Content --}}
                @yield('content')
            </div>
        </main>

        {{-- ========== FOOTER ========== --}}
        <footer class="border-t border-gray-200 bg-white print:hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <a href="{{ route('welcome') }}" class="hover:text-gray-700 transition-colors">Beranda</a>
                        <a href="{{ route('vendor.dashboard') }}" class="hover:text-gray-700 transition-colors">Dasbor</a>
                    </div>
                    <div class="text-sm text-gray-500">
                        &copy; {{ date('Y') }} <a href="#" class="hover:text-gray-700">Grafika Printing</a>. Hak cipta dilindungi.
                    </div>
                </div>
            </div>
        </footer>
    </div>

    @include('components.alert')
    @stack('scripts')
</body>

</html>
