<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Grafika Printing</title>
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
        }

        @media (max-width: 576px) {
            .nav-link-title {
                font-size: 0.875rem;
            }

            .navbar-brand {
                font-size: 1.1rem;
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
                    Grafika Printing
                </a>
            </h1>
            <div class="navbar-nav flex-row order-md-last">
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
                        <a class="dropdown-item" href="{{ route('admin.profile') }}">Profile</a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">Logout</button>
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
                                <span class="nav-link-title">Home</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('admin.dashboard') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                        <line x1="16" y1="2" x2="16" y2="6" />
                                        <line x1="8" y1="2" x2="8" y2="6" />
                                        <line x1="3" y1="10" x2="21" y2="10" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Dashboard</span>
                                <span class="nav-link-title d-sm-none">Dash</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('admin.vendors.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 21h18" />
                                        <path d="M5 21v-16l8 -4v16" />
                                        <path d="M19 21v-11l-6 -4" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Vendors</span>
                                <span class="nav-link-title d-sm-none">Vendor</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('admin.users.index') }}">
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
                                <span class="nav-link-title d-none d-sm-inline">Users</span>
                                <span class="nav-link-title d-sm-none">User</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown {{ request()->routeIs('admin.auctions.*') ? 'active' : '' }}">
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
                                <a class="dropdown-item" href="{{ route('admin.auctions.index') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M7 8l-4 4l4 4" />
                                        <path d="M17 8l4 4l-4 4" />
                                        <path d="M14 4l-4 16" />
                                    </svg>
                                    Daftar Lelang
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.auctions.statistics') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                        <path d="M12 7v5l3 3" />
                                    </svg>
                                    Statistik
                                </a>
                            </div>
                        </li>
                        <li class="nav-item dropdown {{ request()->routeIs('admin.pulse.*') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle hover-shadow-sm" href="#navbar-pulse"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                aria-expanded="false">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 19c-4.3 0 -8 -3.7 -8 -8s3.7 -8 8 -8s8 3.7 8 8s-3.7 8 -8 8z" />
                                        <path d="M15 13l-3 -3l-3 3" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Statistik Server</span>
                                <span class="nav-link-title d-sm-none">Stats</span>
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('admin.analytics.pulse') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <rect x="3" y="4" width="18" height="18" rx="2"
                                            ry="2" />
                                        <line x1="16" y1="2" x2="16" y2="6" />
                                        <line x1="8" y1="2" x2="8" y2="6" />
                                        <line x1="3" y1="10" x2="21" y2="10" />
                                    </svg>
                                    Dashboard
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.analytics.pulse.statistics') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 19c-4.3 0 -8 -3.7 -8 -8s3.7 -8 8 -8s8 3.7 8 8s-3.7 8 -8 8z" />
                                        <path d="M15 13l-3 -3l-3 3" />
                                    </svg>
                                    Server Statistics
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.analytics.pulse.performance') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 13a3 3 0 1 0 0 -6a3 3 0 0 0 0 6z" />
                                        <path d="M12 1v6" />
                                        <path d="M12 17v6" />
                                    </svg>
                                    Performance
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.analytics.pulse.activity') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                    </svg>
                                    User Activity
                                </a>
                            </div>
                        </li>

                        <li
                            class="nav-item {{ request()->routeIs('admin.analytics.vendor-revenue.*') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('admin.analytics.vendor-revenue') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                        <path d="M12 7v5l3 3" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Data Pendapatan Vendor</span>
                                <span class="nav-link-title d-sm-none">Revenue</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('admin.admin-fees.*') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('admin.admin-fees.index') }}">
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
                                <span class="nav-link-title d-none d-sm-inline">Biaya Admin</span>
                                <span class="nav-link-title d-sm-none">Admin Fee</span>
                            </a>
                        </li>
                        <!-- Financial Management -->
                        <li
                            class="nav-item dropdown {{ request()->routeIs('admin.withdrawals.*') || request()->routeIs('admin.payments.*') || request()->routeIs('admin.wallets.*') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle hover-shadow-sm" href="#navbar-financial"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                aria-expanded="false">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                        <path d="M12 6v6l4 2" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Financial</span>
                                <span class="nav-link-title d-sm-none">Finance</span>
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('admin.withdrawals.index') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M7 7h10v3l-4 5l-4 -5z" />
                                    </svg>
                                    Withdrawals
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.payments.index') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                        <path
                                            d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                    </svg>
                                    Payments
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.wallets.index') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M17 8v-3a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1v12a1 1 0 0 0 1 1h10a1 1 0 0 0 1 -1v-3" />
                                    </svg>
                                    Wallets
                                </a>
                            </div>
                        </li>

                        <!-- Shipping & Delivery -->
                        <li
                            class="nav-item dropdown {{ request()->routeIs('admin.shipping.*') || request()->routeIs('admin.delivery.*') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle hover-shadow-sm" href="#navbar-shipping"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                aria-expanded="false">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M5 17h-2v-6l2 -5h9l4 5v6h-2m-4 0h-6m-2 -5h4m-4 -3h3" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Shipping</span>
                                <span class="nav-link-title d-sm-none">Ship</span>
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('admin.shipping.index') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M5 17h-2v-6l2 -5h9l4 5v6h-2m-4 0h-6m-2 -5h4m-4 -3h3" />
                                    </svg>
                                    Shipping Tracking
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.delivery.index') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 12l2 2l4 -4" />
                                        <path d="M21 12c-1 0 -3 -1 -3 -3s2 -3 3 -3s3 1 3 3s-2 3 -3 3" />
                                        <path d="M3 12c1 0 3 -1 3 -3s-2 -3 -3 -3s-3 1 -3 3s2 3 3 3" />
                                    </svg>
                                    Delivery Confirmations
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.shipping.invoices') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                        <path
                                            d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                    </svg>
                                    Shipping Invoices
                                </a>
                            </div>
                        </li>

                        <!-- Transactions & Orders -->
                        <li
                            class="nav-item dropdown {{ request()->routeIs('admin.admin-fees.*') || request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle hover-shadow-sm" href="#navbar-transactions"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                aria-expanded="false">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                        <path
                                            d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Transactions</span>
                                <span class="nav-link-title d-sm-none">Txn</span>
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('admin.admin-fees.transactions') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                    </svg>
                                    Admin Fee Transactions
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.payments.index') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 3h2l.4 2m7.6 5l8.5 -8.5a1.5 1.5 0 0 0 -4 -4l-8.5 8.5v4" />
                                        <path d="M14 6l7 7l-4 4l-7 -7l4 -4" />
                                    </svg>
                                    Payment Management
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.admin-fees.statistics') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />
                                        <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />
                                    </svg>
                                    Fee Statistics
                                </a>
                            </div>
                        </li>

                        <!-- Audit & Security -->
                        <li class="nav-item dropdown {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle hover-shadow-sm" href="#navbar-audit"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                aria-expanded="false">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 12l2 2l4 -4" />
                                        <path d="M21 12c-1 0 -3 -1 -3 -3s2 -3 3 -3s3 1 3 3s-2 3 -3 3" />
                                        <path d="M3 12c1 0 3 -1 3 -3s-2 -3 -3 -3s-3 1 -3 3s2 3 3 3" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">Audit & Security</span>
                                <span class="nav-link-title d-sm-none">Audit</span>
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('admin.audit-logs.index') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                    </svg>
                                    Audit Logs
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.audit-logs.high-risk') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 9v2m0 4v.01" />
                                        <path
                                            d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.84 2.75" />
                                    </svg>
                                    High Risk Transactions
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.audit-logs.financial') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z" />
                                        <path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" />
                                        <path d="M8 11v-4a4 4 0 1 1 8 0v4" />
                                    </svg>
                                    Financial Logs
                                </a>
                            </div>
                        </li>

                        <!-- CMS Management -->
                        <li class="nav-item {{ request()->routeIs('admin.cms.*') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('admin.cms.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 12l2 2l4 -4" />
                                        <path d="M21 12c-1 0 -3 -1 -3 -3s2 -3 3 -3s3 1 3 3s-2 3 -3 3" />
                                        <path d="M3 12c1 0 3 -1 3 -3s-2 -3 -3 -3s-3 1 -3 3s2 3 3 3" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">CMS Management</span>
                                <span class="nav-link-title d-sm-none">CMS</span>
                            </a>
                        </li>

                        <!-- User Lelang Management -->
                        <li class="nav-item {{ request()->routeIs('admin.user-lelang.*') ? 'active' : '' }}">
                            <a class="nav-link hover-shadow-sm" href="{{ route('admin.user-lelang.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                        <path d="M12 11l0 3" />
                                        <path d="M10.5 13.5l3 0" />
                                    </svg>
                                </span>
                                <span class="nav-link-title d-none d-sm-inline">User Lelang</span>
                                <span class="nav-link-title d-sm-none">Lelang</span>
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
                            @yield('title', 'Dashboard')
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
                            <a href="#" class="link-secondary">Documentation</a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#" class="link-secondary">License</a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#" class="link-secondary">Source code</a>
                        </li>
                    </ul>
                </div>
                <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                    <ul class="list-inline list-inline-dots mb-0">
                        <li class="list-inline-item">
                            Copyright © {{ date('Y') }}
                            <a href="#" class="link-secondary">Grafika Printing</a>.
                            All rights reserved.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0/dist/js/tabler.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('dev.components.alert')
    <script src="{{ asset('js/file-upload-validation.js') }}"></script>
    @stack('scripts')
</body>

</html>
