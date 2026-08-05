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
        /* Navbar horizontal scroll for mobile */
        @media (max-width: 768px) {
            .navbar-collapse {
                max-height: 70vh;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }

            .navbar-nav {
                flex-direction: column;
                width: 100%;
                padding-bottom: 0.5rem;
            }

            .navbar-nav .nav-item {
                width: 100%;
                margin-bottom: 2px;
            }

            .navbar-nav .nav-link {
                padding: 0.6rem 0.75rem;
                text-align: left;
                border-radius: 0.375rem;
                font-size: 0.9rem;
            }

            .navbar-nav .nav-link:hover {
                background-color: rgba(0, 0, 0, 0.05);
            }

            .navbar-nav .dropdown-menu {
                position: static !important;
                transform: none !important;
                box-shadow: none;
                border: none;
                background-color: rgba(0, 0, 0, 0.03);
                margin-left: 1rem;
                margin-bottom: 0.25rem;
                border-radius: 0.375rem;
            }

            .navbar-nav .dropdown-menu .dropdown-item {
                padding: 0.5rem 0.75rem;
                font-size: 0.85rem;
            }

            /* Page header responsive */
            .page-header {
                flex-wrap: wrap;
            }

            .page-header .col-auto {
                margin-top: 0.5rem;
            }

            .page-header .btn-list {
                flex-wrap: wrap;
                gap: 0.25rem;
            }

            .page-header .btn-list .btn {
                font-size: 0.8rem;
                padding: 0.35rem 0.6rem;
            }

            /* Mobile card/table improvements */
            .page-body .container-xl {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .table-responsive {
                font-size: 0.82rem;
            }

            .btn {
                font-size: 0.85rem;
                padding: 0.4rem 0.7rem;
            }

            .page-header h2 {
                font-size: 1.15rem;
            }

            .page-pretitle {
                font-size: 0.75rem;
            }

            /* Card improvements */
            .card-body {
                padding: 0.875rem;
            }

            .card-header {
                padding: 0.75rem 0.875rem;
            }

            .card-title {
                font-size: 0.95rem;
            }
        }

        @media (max-width: 576px) {
            .nav-link-title {
                font-size: 0.78rem;
            }

            .navbar-brand {
                font-size: 0.95rem;
            }

            .navbar-brand img {
                width: 28px;
                height: 28px;
            }

            /* Stack columns on very small screens */
            .row > .col-sm-6,
            .row > .col-md-4,
            .row > .col-lg-3,
            .row > .col-lg-4,
            .row > .col-lg-8 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .card-body {
                padding: 0.625rem;
            }

            .stat-card {
                margin-bottom: 0.75rem;
            }

            /* User dropdown */
            .navbar-nav .d-none.d-xl-block {
                display: none !important;
            }

            /* Page header stack on mobile */
            .page-header .row {
                flex-direction: column;
            }

            .page-header .col {
                width: 100%;
            }

            .page-header .col-auto {
                width: 100%;
                margin-top: 0.5rem;
            }

            .page-header .btn-list {
                width: 100%;
            }

            .page-header .btn-list .btn {
                flex: 1;
                justify-content: center;
            }

            /* Form improvements */
            .input-group-text {
                font-size: 0.8rem;
                padding: 0.35rem 0.5rem;
            }

            /* Alert improvements */
            .alert {
                font-size: 0.85rem;
                padding: 0.6rem 0.75rem;
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

            .dropdown-item {
                min-height: 40px;
                display: flex;
                align-items: center;
            }
        }

        /* Navbar collapse transition */
        .navbar-collapse {
            transition: max-height 0.3s ease-in-out;
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
                        $vendorName = 'Dasbor';
                        if (auth()->check()) {
                            $vendor = optional(auth()->user())->vendorUser->first();
                            if ($vendor) {
                                $vendorName = $vendor->name ?? ($vendor->nama_vendor ?? 'Dasbor');
                            }
                        }
                    @endphp
                    {{ $vendorName }}
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
                        <a class="dropdown-item" href="{{ route('vendor.profile') }}">Profil</a>
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
                        <li class="nav-item {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('vendor.dashboard') }}">
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
                        <li class="nav-item {{ request()->routeIs('vendor.pos.*') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('vendor.pos.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 3h18v18H3V3z" />
                                        <path d="M7 7h10v10H7V7z" />
                                        <path d="M9 9h6v6H9V9z" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">POS</span>
                                <span class="nav-link-title d-sm-none">POS</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('vendor.pos.printer.*') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('vendor.pos.printer.settings') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2H5a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                                        <path d="M17 9v-4a2 2 0 0 0 -2 -2H5a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2a2 2 0 0 0 2 -2v-4" />
                                        <path d="M6 17v2" />
                                        <path d="M6 13v2" />
                                        <path d="M18 17v2" />
                                        <path d="M13 13v2" />
                                        <path d="M7 7h10" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Cetak</span>
                                <span class="nav-link-title d-sm-none">Cetak</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('vendor.users.*') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('vendor.users.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                        <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Pengguna</span>
                                <span class="nav-link-title d-sm-none">Pengguna</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('vendor.manual-transfers.*') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('vendor.manual-transfers.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M5 12l5 -5l4 4" />
                                        <path d="M14 16l4 -4l-5 -5" />
                                        <path d="M4 4v16" />
                                        <path d="M20 4v16" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Transfer Manual</span>
                                <span class="nav-link-title d-sm-none">TM</span>
                            </a>
                        </li>
                        <li
                            class="nav-item dropdown {{ request()->routeIs('vendor.materials.*') || request()->routeIs('vendor.tools.*') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle hover-shadow-sm" href="#navbar-extra"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                aria-expanded="false">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 3h18v18H3V3z" />
                                        <path d="M7 7h10v2H7V7z" />
                                        <path d="M7 11h10v2H7v-2z" />
                                        <path d="M7 15h10v2H7v-2z" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Bahan & Alat</span>
                                <span class="nav-link-title d-sm-none">Bahan</span>
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('vendor.materials.index') }}">
                                    Bahan
                                </a>
                                <a class="dropdown-item" href="{{ route('vendor.tools.index') }}">
                                    Alat
                                </a>
                            </div>
                        </li>
                        <li
                            class="nav-item dropdown {{ request()->routeIs('vendor.products.*') ||
                            request()->routeIs('vendor.specifications.*') ||
                            request()->routeIs('vendor.categories.*')
                                ? 'active'
                                : '' }}">
                            <a class="nav-link dropdown-toggle hover-shadow-sm" href="#navbar-extra"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                aria-expanded="false">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M15 3v4a1 1 0 0 0 1 1h4" />
                                        <path
                                            d="M18 17h-7a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h4l5 5v7a2 2 0 0 1 -2 2z" />
                                        <path d="M16 17v2a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h2" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Produk</span>
                                <span class="nav-link-title d-sm-none">Produk</span>
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('vendor.specifications.index') }}">
                                    Spesifikasi
                                </a>
                                <a class="dropdown-item" href="{{ route('vendor.products.index') }}">
                                    Produk
                                </a>
                                <a class="dropdown-item" href="{{ route('vendor.categories.index') }}">
                                    Kategori Produk
                                </a>
                            </div>
                        </li>
                        <li class="nav-item {{ request()->routeIs('vendor.customers.*') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('vendor.customers.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                        <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Pelanggan</span>
                                <span class="nav-link-title d-sm-none">Plng</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('vendor.transactions.*') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('vendor.transactions.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                                        <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Transaksi</span>
                                <span class="nav-link-title d-sm-none">Trans</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown {{ request()->routeIs('vendor.auctions.*') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle hover-shadow-sm" href="#navbar-auctions"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                aria-expanded="false">
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
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('vendor.auctions.index') }}">
                                    Daftar Lelang
                                </a>
                                <a class="dropdown-item" href="{{ route('vendor.auctions.my-bids') }}">
                                    Penawaran Saya
                                </a>
                            </div>
                        </li>
                        <li class="nav-item {{ request()->routeIs('vendor.tracking.*') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('vendor.tracking.index') }}">
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
                        <li class="nav-item {{ request()->routeIs('vendor.wallet.*') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('vendor.wallet.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M17 8v-2a2 2 0 0 0 -2 -2h-4a2 2 0 0 0 -2 2v2a2 2 0 0 0 2 2h4a2 2 0 0 0 2 -2z" />
                                        <path d="M12 8v13" />
                                        <path d="M19 12v7a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-7" />
                                        <path d="M7 12v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Dompet</span>
                                <span class="nav-link-title d-sm-none">Dompet</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('vendor.bank-accounts.*') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('vendor.bank-accounts.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 21h18" />
                                        <path d="M3 10h18" />
                                        <path d="M5 6l7 -3l7 3" />
                                        <path d="M4 10v11" />
                                        <path d="M20 10v11" />
                                        <path d="M8 10v11" />
                                        <path d="M12 10v11" />
                                        <path d="M16 10v11" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Rekening Bank</span>
                                <span class="nav-link-title d-sm-none">Bank</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('vendor.shipping.*') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('vendor.shipping.calculator') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M18 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M6 17l-2 -4h12l-2 4" />
                                        <path d="M6 12l-2 -2l2 -1l2 2l2 -1l2 2l2 -1l2 2" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Kalkulator Ongkir</span>
                                <span class="nav-link-title d-sm-none">Ongkir</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('vendor.audit-logs.*') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('vendor.audit-logs.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                        <path
                                            d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        <path d="M12 11l0 6" />
                                        <path d="M9 14l6 0" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Audit Log</span>
                                <span class="nav-link-title d-sm-none">Log</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown {{ request()->routeIs('vendor.linktree.*') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle hover-shadow-sm" href="#navbar-linktree"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                aria-expanded="false">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 12m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0" />
                                        <path d="M12 7l5 5" />
                                        <path d="M12 12l5 -5" />
                                        <path d="M17 12h4" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Linktree</span>
                                <span class="nav-link-title d-sm-none">Link</span>
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('vendor.linktree.index') }}">
                                    Semua Linktree
                                </a>
                                <a class="dropdown-item" href="{{ route('vendor.linktree.create') }}">
                                    Buat Baru
                                </a>
                            </div>
                        </li>
                        <li class="nav-item dropdown {{ request()->routeIs('vendor.laporan.*') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle hover-shadow-sm" href="#navbar-reports"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                aria-expanded="false">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        <line x1="9" y1="9" x2="15" y2="9" />
                                        <line x1="9" y1="13" x2="15" y2="13" />
                                        <line x1="9" y1="17" x2="15" y2="17" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Laporan</span>
                                <span class="nav-link-title d-sm-none">Laporan</span>
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('vendor.laporan.penjualan-harian') }}">
                                    Laporan Penjualan Harian
                                </a>
                                <a class="dropdown-item" href="{{ route('vendor.laporan.penjualan-bulanan') }}">
                                    Laporan Penjualan Bulanan
                                </a>
                                <a class="dropdown-item" href="{{ route('vendor.laporan.penjualan-tahunan') }}">
                                    Laporan Penjualan Tahunan
                                </a>
                            </div>
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
                            <a href="{{ route('vendor.dashboard') }}" class="link-secondary">Dasbor</a>
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
    @include('components.alert')
    @stack('scripts')
</body>

</html>
