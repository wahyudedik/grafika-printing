<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Auth' }} - {{ config('app.name', 'Grafika Printing') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('styles')
</head>

<body class="min-h-screen font-sans antialiased">

    <div class="flex min-h-screen">

        {{-- ======================== --}}
        {{-- Left Panel: Branding --}}
        {{-- ======================== --}}
        <div class="hidden lg:flex flex-1 auth-brand-gradient relative overflow-hidden items-center justify-center p-12">
            {{-- Background glow effects --}}
            <div class="absolute -top-1/2 -right-1/2 w-full h-full bg-[radial-gradient(circle,rgba(102,126,234,0.15)_0%,transparent_70%)]" style="animation: pulse-glow 8s ease-in-out infinite;"></div>
            <div class="absolute -bottom-1/3 -left-1/3 w-4/5 h-4/5 bg-[radial-gradient(circle,rgba(118,75,162,0.12)_0%,transparent_70%)]" style="animation: pulse-glow 10s ease-in-out infinite reverse;"></div>

            {{-- Content --}}
            <div class="relative z-10 text-center max-w-md">
                {{-- Logo --}}
                <div class="inline-flex items-center gap-3 mb-8">
                    <div class="relative">
                        <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-lg shadow-black/30">
                            <img src="{{ asset('logo.png') }}" alt="Grafika Printing" width="40" height="40" class="rounded-lg">
                        </div>
                        <div class="absolute -bottom-1 left-1 right-1 h-1 rounded-b-lg" style="background: linear-gradient(to right, #00d4ff, #e040fb, #ffeb3b, #212121);"></div>
                    </div>
                    <span class="text-xl font-extrabold text-white tracking-tight">GRAFIKA PRINTING</span>
                </div>

                {{-- Tagline --}}
                <p class="text-white/70 text-lg leading-relaxed mb-8 font-light">
                    Platform percetakan digital terlengkap di Indonesia.
                    Solusi cetak online untuk bisnis Anda.
                </p>

                {{-- Feature Cards --}}
                <div class="flex flex-col gap-3 text-left">
                    {{-- Multi-Tenant POS --}}
                    <div class="flex items-center gap-3.5 p-3.5 bg-white/5 border border-white/8 rounded-xl transition-all hover:bg-white/10 hover:translate-x-1">
                        <div class="w-10 h-10 rounded-lg bg-[rgba(102,126,234,0.2)] flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-[#667eea]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zm14 4H2v5a2 2 0 002 2h12a2 2 0 002-2V8zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-white m-0">Multi-Tenant POS</h4>
                            <p class="text-xs text-white/50 m-0">Sistem point of sale untuk vendor percetakan</p>
                        </div>
                    </div>

                    {{-- Lelang & Tender --}}
                    <div class="flex items-center gap-3.5 p-3.5 bg-white/5 border border-white/8 rounded-xl transition-all hover:bg-white/10 hover:translate-x-1">
                        <div class="w-10 h-10 rounded-lg bg-[rgba(118,75,162,0.2)] flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-[#764ba2]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-white m-0">Lelang & Tender</h4>
                            <p class="text-xs text-white/50 m-0">Sistem lelang proyek percetakan</p>
                        </div>
                    </div>

                    {{-- Pembayaran Aman --}}
                    <div class="flex items-center gap-3.5 p-3.5 bg-white/5 border border-white/8 rounded-xl transition-all hover:bg-white/10 hover:translate-x-1">
                        <div class="w-10 h-10 rounded-lg bg-[rgba(0,212,255,0.2)] flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-[#00d4ff]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-white m-0">Pembayaran Aman</h4>
                            <p class="text-xs text-white/50 m-0">Escrow payment via Xendit</p>
                        </div>
                    </div>

                    {{-- Linktree Vendor --}}
                    <div class="flex items-center gap-3.5 p-3.5 bg-white/5 border border-white/8 rounded-xl transition-all hover:bg-white/10 hover:translate-x-1">
                        <div class="w-10 h-10 rounded-lg bg-[rgba(40,167,69,0.2)] flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-[#28a745]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 00-5.656 0l-3-3a2 2 0 112.828-2.828l3 3a2 2 0 010 2.828 1 1 0 101.414 1.414z" clip-rule="evenodd" transform="rotate(180 10 10)"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-white m-0">Linktree Vendor</h4>
                            <p class="text-xs text-white/50 m-0">Halaman linktree kustom untuk vendor</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CMYK decorative dots --}}
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                <div class="w-2 h-2 rounded-full bg-[#00d4ff] opacity-40"></div>
                <div class="w-2 h-2 rounded-full bg-[#e040fb] opacity-40"></div>
                <div class="w-2 h-2 rounded-full bg-[#ffeb3b] opacity-40"></div>
                <div class="w-2 h-2 rounded-full bg-white opacity-40"></div>
            </div>
        </div>

        {{-- ======================== --}}
        {{-- Right Panel: Form --}}
        {{-- ======================== --}}
        <div class="flex-1 flex flex-col justify-center items-center p-8 lg:p-12 bg-white">
            <div class="w-full max-w-md">

                {{-- Mobile Logo (visible on small screens) --}}
                <div class="lg:hidden text-center mb-8">
                    <a href="{{ route('welcome') }}" class="inline-flex items-center gap-3">
                        <img src="{{ asset('favicon.png') }}" alt="Grafika Printing" class="h-10 w-10 rounded-lg">
                        <span class="text-xl font-bold text-gray-900">Grafika Printing</span>
                    </a>
                </div>

                {{-- Form Content --}}
                @yield('content')

                {{-- Back to Home --}}
                <div class="text-center mt-8">
                    <a href="{{ route('welcome') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-primary-600 font-medium transition-colors">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                        </svg>
                        Kembali ke beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    @yield('scripts')
</body>

</html>
