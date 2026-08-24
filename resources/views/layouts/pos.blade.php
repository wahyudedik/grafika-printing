<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Point of Sale') - Grafika Printing</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        /* POS-specific: no sidebar, full screen */
        body { overflow-x: hidden; }
    </style>
</head>

<body class="bg-gray-50 font-sans text-gray-900 antialiased min-h-screen">

    {{-- POS Top Bar --}}
    <header class="sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm" x-data="{ userDropdown: false }">
        <div class="max-w-full mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-14">
                {{-- Left: Back + Brand --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('vendor.dashboard') }}"
                       class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                       title="Kembali ke Dashboard">
                        <i class="fas fa-arrow-left"></i>
                        <span class="hidden sm:inline">Dashboard</span>
                    </a>
                    <div class="h-5 w-px bg-gray-200"></div>
                    <span class="text-sm font-semibold text-gray-900">
                        <i class="fas fa-cash-register mr-1.5 text-primary-600"></i>POS
                    </span>
                </div>

                {{-- Right: User Dropdown --}}
                <div class="relative" @click.away="userDropdown = false">
                    <button @click="userDropdown = !userDropdown" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="w-7 h-7 rounded-full bg-primary-600 flex items-center justify-center text-white text-xs font-semibold">
                            {{ substr(auth()->user()->name, 0, 2) }}
                        </div>
                        <span class="text-sm font-medium text-gray-700 hidden sm:inline">{{ auth()->user()->name }}</span>
                        <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div x-show="userDropdown" x-cloak x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                        <a href="{{ route('vendor.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-tachometer-alt w-4 text-center"></i> Dashboard
                        </a>
                        <a href="{{ route('vendor.profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-user w-4 text-center"></i> Profil
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt w-4 text-center"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="min-h-[calc(100vh-3.5rem)]">
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 class="mx-4 sm:mx-6 mt-4">
                <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-xl">
                    <div class="flex items-center gap-2 text-green-800"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span></div>
                    <button @click="show = false" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 class="mx-4 sm:mx-6 mt-4">
                <div class="flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-center gap-2 text-red-800"><i class="fas fa-exclamation-circle"></i><span>{{ session('error') }}</span></div>
                    <button @click="show = false" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    @include('components.alert')
    @stack('scripts')
</body>

</html>
