<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Grafika Printing - Vendor Panel')</title>
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

            .card {
                margin-bottom: 1rem;
            }

            .table-responsive {
                font-size: 0.875rem;
            }

            h1 {
                font-size: 1.5rem;
            }

            h2 {
                font-size: 1.25rem;
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
                <a href="{{ route('vendor.dashboard') }}" class="d-flex align-items-center text-decoration-none">
                    <img src="{{ asset('favicon.png') }}" alt="Grafika Printing" height="32" width="32" style="border-radius: 6px; margin-right: 8px;">
                    Vendor Panel
                </a>
            </h1>
            <div class="navbar-nav flex-row order-md-last">
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                        aria-label="Open user menu">
                        <span class="avatar avatar-sm">{{ substr(auth()->user()->name, 0, 2) }}</span>
                        <div class="d-none d-xl-block ps-2">
                            <div>{{ auth()->user()->name }}</div>
                            <div class="mt-1 small text-muted">Vendor</div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <a class="dropdown-item" href="{{ route('vendor.dashboard') }}">Dashboard</a>
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
                        <li class="nav-item {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('vendor.dashboard') }}">
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
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('vendor.pos.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('vendor.pos.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 5v14" />
                                        <path d="M5 12h14" />
                                    </svg>
                                </span>
                                <span class="nav-link-title">POS</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('vendor.produks.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('vendor.produks.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 3l8 4.5v9l-8 4.5l-8 -4.5v-9l8 -4.5" />
                                    </svg>
                                </span>
                                <span class="nav-link-title">Produk</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('vendor.wallet.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('vendor.wallet.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M17 9v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h3" />
                                        <path d="M9 12h6" />
                                        <path d="M13 15v6" />
                                        <path d="M19 15v2a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-6" />
                                    </svg>
                                </span>
                                <span class="nav-link-title">Wallet</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('vendor.linktree.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('vendor.linktree.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 12l2 2l4 -4" />
                                        <path d="M12 3a12 12 0 0 0 -3 18" />
                                        <path d="M15 3a12 12 0 0 1 3 18" />
                                        <path d="M12 3a12 12 0 0 0 0 18" />
                                    </svg>
                                </span>
                                <span class="nav-link-title">Linktree</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('vendor.audit-logs.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('vendor.audit-logs.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                    </svg>
                                </span>
                                <span class="nav-link-title">Audit Log</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <main class="page-main">
        <div class="page-body">
            <div class="container-xl">
                @yield('content')
            </div>
        </div>
    </main>

    <footer class="footer footer-transparent d-print-none">
        <div class="container-xl">
            <div class="row text-center align-items-center flex-row">
                <div class="col-lg-auto ms-lg-auto">
                    <ul class="list-inline list-inline-dots mb-0">
                        <li class="list-inline-item">
                            &copy; {{ date('Y') }} Grafika Printing. All rights reserved.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0/dist/js/tabler.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // CSRF token setup for AJAX
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Show success alert
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            // Show error alert
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            // Show warning alert
            @if(session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: '{{ session('warning') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
    @stack('scripts')
    <x-alert />
</body>

</html>
