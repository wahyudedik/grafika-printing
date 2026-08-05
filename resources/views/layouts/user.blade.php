<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Grafika Printing</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard-improvements.css') }}">
    <style>
        /* Responsive navbar improvements */
        @media (max-width: 768px) {
            .navbar-nav {
                flex-direction: column;
                width: 100%;
            }

            .navbar-nav .nav-item {
                width: 100%;
                margin-bottom: 0.25rem;
            }

            .navbar-nav .nav-link {
                padding: 0.75rem 1rem;
                text-align: left;
                border-radius: 0.375rem;
            }

            .navbar-nav .nav-link:hover {
                background-color: rgba(0, 0, 0, 0.05);
            }

            .navbar-nav .dropdown-menu {
                position: static !important;
                transform: none !important;
                box-shadow: none;
                border: none;
                background-color: rgba(0, 0, 0, 0.02);
                margin-left: 1rem;
            }

            /* Mobile card/table improvements */
            .page-body .container-xl {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .table-responsive {
                font-size: 0.85rem;
            }

            .btn {
                font-size: 0.875rem;
                padding: 0.4rem 0.75rem;
            }

            .page-header h2 {
                font-size: 1.25rem;
            }
        }

        @media (max-width: 576px) {
            .nav-link-title {
                font-size: 0.8rem;
            }

            .navbar-brand {
                font-size: 1rem;
            }

            .row > .col-sm-6,
            .row > .col-md-4,
            .row > .col-lg-3 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .card-body {
                padding: 0.75rem;
            }
        }

        /* Touch-friendly improvements */
        @media (hover: none) and (pointer: coarse) {
            .nav-link {
                min-height: 44px;
                display: flex;
                align-items: center;
            }

            .btn {
                min-height: 44px;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <header class="navbar navbar-expand-md navbar-light d-print-none">
        <div class="container-xl">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                <a href="{{ route('welcome') }}" class="d-flex align-items-center text-decoration-none">
                    <img src="{{ asset('logo.png') }}" alt="Grafika Printing" height="32" width="32" style="border-radius: 6px; margin-right: 8px;">
                    @php
                        $appName = 'Grafika Printing';
                        if (auth()->check() && auth()->user()->usertype === 'user') {
                            $appName = 'Dasbor Pengguna';
                        }
                    @endphp
                    {{ $appName }}
                </a>
            </h1>

            <div class="navbar-nav flex-row order-md-last">
                <div class="d-none d-md-flex me-3">
                    <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1"
                        aria-label="Show notifications">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path
                                d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6">
                            </path>
                            <path d="M9 17v1a3 3 0 0 0 6 0v-1"></path>
                        </svg>
                        {{-- Notification badge: tampilkan saat ada notifikasi --}}
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="badge bg-red">{{ auth()->user()->unreadNotifications->count() }}</span>
                        @endif
                    </a>
                </div>

                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                        aria-label="Open user menu">
                        <span class="avatar avatar-sm">{{ substr(auth()->user()->name, 0, 2) }}</span>
                        <div class="d-none d-xl-block ps-2">
                            <div>{{ auth()->user()->name }}</div>
                            <div class="mt-1 small text-muted">{{ auth()->user()->usertype }}</div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <a class="dropdown-item" href="{{ route('user.profile.edit') }}">Profil</a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="navbar-expand-md">
        <div class="collapse navbar-collapse" id="navbar-menu">
            <div class="navbar navbar-light">
                <div class="container-xl">
                    <ul class="navbar-nav flex-wrap">
                        <li class="nav-item {{ request()->routeIs('welcome') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('welcome') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <polyline points="5 12 3 12 12 3 21 12 19 12" />
                                        <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                        <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                                    </svg>
                                </span>
                                <span class="nav-link-title">Beranda</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('user.dashboard') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <rect x="3" y="4" width="18" height="18" rx="2"
                                            ry="2" />
                                        <line x1="16" y1="2" x2="16" y2="6" />
                                        <line x1="8" y1="2" x2="8" y2="6" />
                                        <line x1="3" y1="10" x2="21" y2="10" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Dasbor</span>
                                <span class="nav-link-title d-sm-none">Dasbor</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('user.auctions.index') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('user.auctions.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M7 8l-4 4l4 4" />
                                        <path d="M17 8l4 4l-4 4" />
                                        <path d="M14 4l-4 16" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Lelang</span>
                                <span class="nav-link-title d-sm-none">Lelang</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('user.auctions.my') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('user.auctions.my') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                        <path d="M12 7v5l3 3" />
                                        <path d="M12 1v6" />
                                        <path d="M12 17v6" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Lelang Saya</span>
                                <span class="nav-link-title d-sm-none">Saya</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('user.orders.*') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('user.orders.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                        <path
                                            d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                        <path d="M9 12l2 2l4 -4" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Tracking Pesanan</span>
                                <span class="nav-link-title d-sm-none">Tracking</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('user.delivery-confirmation.*') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('user.delivery-confirmation.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 12l2 2l4 -4" />
                                        <path d="M5 7a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-10" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Konfirmasi Pengiriman</span>
                                <span class="nav-link-title d-sm-none">Konfirmasi</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('user.profile.*') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('user.profile.edit') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <circle cx="12" cy="12" r="3" />
                                        <path
                                            d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1 -2.83 0l-.06-.06a1.65 1.65 0 0 0 -1.82-.33 1.65 1.65 0 0 0 -1 1.51V21a2 2 0 0 1 -2 2 2 2 0 0 1 -2 -2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0 -1.82.33l-.06.06a2 2 0 0 1 -2.83 0 2 2 0 0 1 0 -2.83l.06-.06a1.65 1.65 0 0 0 .33 -1.82 1.65 1.65 0 0 0 -1.51 -1H3a2 2 0 0 1 -2 -2 2 2 0 0 1 2 -2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0 -.33 -1.82l-.06-.06a2 2 0 0 1 0 -2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1 -1.51V3a2 2 0 0 1 2 -2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82 -.33l.06 -.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06 .06a1.65 1.65 0 0 0 -.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1 -2 2h-.09a1.65 1.65 0 0 0 -1.51 1z" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Profil</span>
                                <span class="nav-link-title d-sm-none">Profil</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="page-wrapper">
        <div class="container-xl">
            <div class="page-header d-print-none">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="page-title">
                            @yield('title', 'Dasbor')
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-body">
            <div class="container-xl">
                @yield('content')
            </div>
        </div>
    </div>

    <footer class="footer footer-transparent d-print-none">
        <div class="container-xl">
            <div class="row text-center align-items-center flex-row-reverse">
                <div class="col-lg-auto ms-lg-auto">
                    <ul class="list-inline list-inline-dots mb-0">
                        <li class="list-inline-item">
                            <a href="{{ route('welcome') }}" class="link-secondary">Beranda</a>
                        </li>
                        <li class="list-inline-item">
                            <a href="{{ route('user.dashboard') }}" class="link-secondary">Dasbor</a>
                        </li>
                    </ul>
                </div>
                <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                    <ul class="list-inline list-inline-dots mb-0">
                        <li class="list-inline-item">
                            Copyright © {{ date('Y') }}
                            <a href="#" class="link-secondary">Grafika Printing</a>.
                            Hak cipta dilindungi.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0/dist/js/tabler.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('user.components.alert')
    @stack('scripts')
</body>

</html>
