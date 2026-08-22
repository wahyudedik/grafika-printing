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
    x-data
    x-init="$store.sidebar.collapsed = localStorage.getItem('user-sidebar-collapsed') === 'true'; $watch('$store.sidebar.collapsed', v => localStorage.setItem('user-sidebar-collapsed', v))">

    {{-- ========== SIDEBAR (MOBILE OVERLAY) ========== --}}
    <div x-show="$store.sidebar.mobileOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="$store.sidebar.mobileOpen = false" class="fixed inset-0 z-40 bg-gray-900/80 lg:hidden" x-cloak></div>

    {{-- ========== SIDEBAR NAVIGATION ========== --}}
    @php
        $iconHome = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/></svg>';
        $iconDashboard = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>';
        $iconAuction = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>';
        $iconClock = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        $iconTracking = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>';
        $iconConfirm = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        $iconOrderHistory = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>';
        $iconProfile = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>';

        $userMenus = [
            [
                'items' => [
                    ['label' => 'Beranda',            'url' => route('welcome'),                               'route' => 'welcome',                      'icon' => $iconHome],
                    ['label' => 'Dasbor',              'url' => route('user.dashboard'),                        'route' => 'user.dashboard',               'icon' => $iconDashboard],
                    ['label' => 'Lelang',              'url' => route('user.auctions.index'),                   'route' => 'user.auctions.index',          'icon' => $iconAuction],
                    ['label' => 'Lelang Saya',         'url' => route('user.auctions.my'),                      'route' => 'user.auctions.my',             'icon' => $iconClock],
                    ['label' => 'Tracking Pesanan',    'url' => route('user.orders.index'),                     'route' => 'user.orders.*',                'icon' => $iconTracking],
                    ['label' => 'Konfirmasi Pengiriman','url' => route('user.delivery-confirmation.index'),     'route' => 'user.delivery-confirmation.*', 'icon' => $iconConfirm],
                    ['label' => 'Riwayat Pesanan',      'url' => route('user.transactions.index'),               'route' => 'user.transactions.*',          'icon' => $iconOrderHistory],
                    ['label' => 'Profil',              'url' => route('user.profile.edit'),                     'route' => 'user.profile.*',               'icon' => $iconProfile],
                ]
            ],
        ];
    @endphp

    <x-sidebar :menus="$userMenus" brandName="Grafika" brandSubtitle="Dasbor Pengguna" />

    {{-- ========== TOP BAR ========== --}}
    <div class="lg:pl-64 transition-all duration-300" :class="$store.sidebar.collapsed ? 'lg:pl-[72px]' : 'lg:pl-64'">
        <header class="sticky top-0 z-30 flex items-center h-16 px-4 bg-white border-b border-gray-200 sm:px-6 lg:px-8 print:hidden">
            {{-- Mobile hamburger --}}
            <button @click="$store.sidebar.mobileOpen = !$store.sidebar.mobileOpen" class="p-2 -ml-2 text-gray-500 rounded-lg lg:hidden hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            {{-- Desktop collapse toggle --}}
            <button @click="$dispatch('toggle-sidebar')" class="hidden p-2 -ml-2 text-gray-500 rounded-lg lg:block hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <div class="flex items-center gap-2 ml-auto">
                {{-- Notifications --}}
                <div class="relative" x-data="{ notifDropdown: false }" @click.away="notifDropdown = false">
                    @php
                        $unreadCount = auth()->user()->unreadNotifications()->count();
                        $dropdownNotifications = auth()->user()->notifications()->latest()->take(5)->get();
                    @endphp
                    <button @click="notifDropdown = !notifDropdown" class="relative p-2 text-gray-500 rounded-lg hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
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
                                <form action="{{ route('user.notifications.markAsRead', $notification->id) }}" method="POST">
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
                            <a href="{{ route('user.notifications.index') }}" class="block px-4 py-2.5 text-sm text-center text-primary-600 hover:bg-gray-50 font-medium border-t border-gray-100">
                                Lihat Semua Notifikasi
                            </a>
                        @endif
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
                        <a href="{{ route('user.profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
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
                        <a href="{{ route('user.dashboard') }}" class="hover:text-gray-700 transition-colors">Dasbor</a>
                    </div>
                    <div class="text-sm text-gray-500">
                        &copy; {{ date('Y') }} <a href="{{ route('welcome') }}" class="hover:text-gray-700">Grafika Printing</a>. Hak cipta dilindungi.
                    </div>
                </div>
            </div>
        </footer>
    </div>

    @include('user.components.alert')
    @stack('scripts')
</body>

</html>
