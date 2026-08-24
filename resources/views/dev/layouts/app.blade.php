<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dasbor') - Admin Grafika Printing</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-gray-50 font-sans text-gray-900 antialiased"
    x-data
    x-init="$store.sidebar.collapsed = localStorage.getItem('sidebarCollapsed') === 'true'; $watch('$store.sidebar.collapsed', v => localStorage.setItem('sidebarCollapsed', v))">

    @php
        // Icon SVGs
        $iconHome = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/></svg>';
        $iconGrid = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>';
        $iconBuilding = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>';
        $iconUsers = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>';
        $iconUserGroup = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
        $iconPencil = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>';
        $iconLightning = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>';
        $iconChart = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>';
        $iconCash = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        $iconWallet = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>';
        $iconTruck = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4zM9.5 4.5l2-2h5l2 2M4 10h16v7H4V10z"/></svg>';
        $iconReceipt = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>';
        $iconShield = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>';
        $iconCog = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
        $iconChat = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';

        $adminMenus = [
            [
                'items' => [
                    ['label' => 'Beranda',  'url' => route('welcome'), 'route' => 'welcome', 'icon' => $iconHome],
                    ['label' => 'Dasbor',   'url' => route('admin.dashboard'), 'route' => 'admin.dashboard', 'icon' => $iconGrid],
                ]
            ],
            [
                'label' => 'Manajemen',
                'items' => [
                    ['label' => 'Vendor',      'url' => route('admin.vendors.index'), 'route' => 'admin.vendors.*', 'icon' => $iconBuilding],
                    ['label' => 'Pengguna',    'url' => route('admin.users.index'), 'route' => 'admin.users.*', 'icon' => $iconUsers],
                    ['label' => 'User Lelang', 'url' => route('admin.user-lelang.index'), 'route' => 'admin.user-lelang.*', 'icon' => $iconUserGroup],
                    ['label' => 'CMS',         'url' => route('admin.cms.index'), 'route' => 'admin.cms.*', 'icon' => $iconPencil],
                ]
            ],
            [
                'label' => 'Lelang & Mediasi',
                'items' => [
                    [
                        'label' => 'Lelang',
                        'route' => 'admin.auctions.*',
                        'icon' => $iconLightning,
                        'children' => [
                            ['label' => 'Daftar Lelang', 'url' => route('admin.auctions.index'), 'route' => 'admin.auctions.index'],
                            ['label' => 'Statistik',     'url' => route('admin.auctions.statistics'), 'route' => 'admin.auctions.statistics'],
                        ]
                    ],
                    ['label' => 'Mediasi', 'url' => route('admin.mediation.index'), 'route' => 'admin.mediation.*', 'icon' => $iconChat],
                ]
            ],
            [
                'label' => 'Keuangan',
                'items' => [
                    [
                        'label' => 'Admin Fee',
                        'route' => 'admin.admin-fees.*',
                        'icon' => $iconCash,
                        'children' => [
                            ['label' => 'Pengaturan',  'url' => route('admin.admin-fees.index'), 'route' => 'admin.admin-fees.index'],
                            ['label' => 'Transaksi',   'url' => route('admin.admin-fees.transactions'), 'route' => 'admin.admin-fees.transactions'],
                            ['label' => 'Statistik',   'url' => route('admin.admin-fees.statistics'), 'route' => 'admin.admin-fees.statistics'],
                        ]
                    ],
                    [
                        'label' => 'Keuangan',
                        'route' => 'admin.withdrawals.*|admin.payments.*|admin.wallets.*',
                        'icon' => $iconWallet,
                        'children' => [
                            ['label' => 'Penarikan',  'url' => route('admin.withdrawals.index'), 'route' => 'admin.withdrawals.*'],
                            ['label' => 'Pembayaran', 'url' => route('admin.payments.index'), 'route' => 'admin.payments.*'],
                            ['label' => 'Dompet',     'url' => route('admin.wallets.index'), 'route' => 'admin.wallets.*'],
                        ]
                    ],
                ]
            ],
            [
                'label' => 'Operasional',
                'items' => [
                    [
                        'label' => 'Pengiriman',
                        'route' => 'admin.shipping.*|admin.delivery.*',
                        'icon' => $iconTruck,
                        'children' => [
                            ['label' => 'Pelacakan Pengiriman',     'url' => route('admin.shipping.index'), 'route' => 'admin.shipping.index'],
                            ['label' => 'Konfirmasi Pengiriman',    'url' => route('admin.delivery.index'), 'route' => 'admin.delivery.index'],
                            ['label' => 'Invoice Pengiriman',       'url' => route('admin.shipping.invoices'), 'route' => 'admin.shipping.invoices'],
                        ]
                    ],
                    [
                        'label' => 'Audit & Keamanan',
                        'route' => 'admin.audit-logs.*',
                        'icon' => $iconShield,
                        'children' => [
                            ['label' => 'Log Audit',      'url' => route('admin.audit-logs.index'), 'route' => 'admin.audit-logs.index'],
                            ['label' => 'Risiko Tinggi',   'url' => route('admin.audit-logs.high-risk'), 'route' => 'admin.audit-logs.high-risk'],
                            ['label' => 'Keuangan',        'url' => route('admin.audit-logs.financial'), 'route' => 'admin.audit-logs.financial'],
                        ]
                    ],
                    [
                        'label' => 'Statistik Server',
                        'route' => 'admin.analytics.*',
                        'icon' => $iconChart,
                        'children' => [
                            ['label' => 'Dasbor',              'url' => route('admin.analytics.pulse'), 'route' => 'admin.analytics.pulse'],
                            ['label' => 'Statistik Server',    'url' => route('admin.analytics.pulse.statistics'), 'route' => 'admin.analytics.pulse.statistics'],
                            ['label' => 'Performa',            'url' => route('admin.analytics.pulse.performance'), 'route' => 'admin.analytics.pulse.performance'],
                            ['label' => 'Aktivitas Pengguna',  'url' => route('admin.analytics.pulse.activity'), 'route' => 'admin.analytics.pulse.activity'],
                            ['label' => 'Pendapatan Vendor',   'url' => route('admin.analytics.vendor-revenue'), 'route' => 'admin.analytics.vendor-revenue'],
                        ]
                    ],
                    ['label' => 'Konfigurasi Service', 'url' => route('admin.service-configs.index'), 'route' => 'admin.service-configs.*', 'icon' => $iconCog],
                ]
            ],
        ];
    @endphp

    {{-- ========== SIDEBAR ========== --}}
    <x-sidebar :menus="$adminMenus" brandName="Admin Panel" brandSubtitle="Grafika Printing" />

    {{-- ========== SIDEBAR OVERLAY (mobile) ========== --}}
    <div x-show="$store.sidebar.mobileOpen"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="$store.sidebar.mobileOpen = false"
        class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden"
        x-cloak>
    </div>

    {{-- ========== MAIN AREA ========== --}}
    <div class="lg:pl-64 transition-all duration-300" :class="$store.sidebar.collapsed ? 'lg:pl-[72px]' : 'lg:pl-64'">

        {{-- ========== TOP BAR ========== --}}
        <header class="sticky top-0 z-40 flex items-center h-16 px-4 bg-white border-b border-gray-200 sm:px-6 lg:px-8 print:hidden">
            {{-- Hamburger (mobile) --}}
            <button @click="$store.sidebar.mobileOpen = !$store.sidebar.mobileOpen" class="p-2 -ml-2 text-gray-500 rounded-lg lg:hidden hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div class="flex items-center gap-2 ml-auto">
                {{-- Notifications --}}
                @php
                    $unreadCount = auth()->user()->unreadNotifications()->count();
                    $dropdownNotifications = auth()->user()->notifications()->latest()->take(5)->get();
                @endphp
                <div class="relative" x-data="{ notifDropdown: false }" @click.away="notifDropdown = false">
                    <button @click="notifDropdown = !notifDropdown" class="relative p-2 text-gray-500 rounded-lg hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3H6a4 4 0 0 0 2-3v-3a7 7 0 0 1 4-6"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v1a3 3 0 0 0 6 0v-1"/>
                        </svg>
                        @if($unreadCount > 0)
                            <span class="absolute -top-0.5 -right-0.5 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </button>
                    <div x-show="notifDropdown" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50" x-cloak>
                        <div class="px-4 py-2 text-sm font-semibold text-gray-900 border-b border-gray-100">Notifikasi</div>
                        @forelse($dropdownNotifications as $notification)
                            @if(!$notification->read_at)
                                <form action="{{ route('admin.notifications.markAsRead', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0">
                                        <p class="text-sm text-gray-700">{{ $notification->data['message'] ?? 'Notifikasi baru' }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                    </button>
                                </form>
                            @else
                                <div class="px-4 py-3 opacity-60 border-b border-gray-50 last:border-0">
                                    <p class="text-sm text-gray-700">{{ $notification->data['message'] ?? 'Notifikasi baru' }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            @endif
                        @empty
                            <div class="px-4 py-6 text-center text-sm text-gray-500">Belum ada notifikasi baru</div>
                        @endforelse
                        @if($unreadCount > 0)
                            <a href="{{ route('admin.notifications.index') }}" class="block px-4 py-2.5 text-sm text-center text-red-600 hover:bg-gray-50 font-medium border-t border-gray-100">
                                Lihat Semua Notifikasi
                            </a>
                        @endif
                    </div>
                </div>

                {{-- User Dropdown --}}
                <div class="relative" x-data="{ userDropdown: false }" @click.away="userDropdown = false">
                    <button @click="userDropdown = !userDropdown" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <span class="flex items-center justify-center w-8 h-8 text-xs font-semibold text-white rounded-full bg-red-600">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </span>
                        <div class="hidden md:block text-left">
                            <div class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-gray-500">Superadmin</div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="userDropdown" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50" x-cloak>
                        <a href="{{ route('admin.profile') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
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
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700 transition-colors">Dasbor</a>
                        <a href="{{ route('admin.audit-logs.index') }}" class="hover:text-gray-700 transition-colors">Log Audit</a>
                        <a href="{{ config('app.url') }}" class="hover:text-gray-700 transition-colors" target="_blank">Website</a>
                    </div>
                    <div class="text-sm text-gray-500">
                        &copy; {{ date('Y') }} <a href="{{ route('welcome') }}" class="hover:text-gray-700">Grafika Printing</a>. Hak cipta dilindungi.
                    </div>
                </div>
            </div>
        </footer>
    </div>

    @include('dev.components.alert')
    <script src="{{ asset('js/file-upload-validation.js') }}"></script>
    @stack('scripts')
</body>

</html>
