<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon.png') }}">
    <title>Grafika Printing - Smart Printing Management System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-500 to-blue-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                <div class="sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left">
                    <h1 class="text-4xl font-bold text-white sm:text-5xl md:text-6xl">
                        <span class="block">Grafika</span>
                        <span class="block text-yellow-300">Printing System</span>
                    </h1>
                    <p class="mt-3 text-base text-gray-100 sm:mt-5 sm:text-xl lg:text-lg xl:text-xl">
                        Streamline your printing business with our comprehensive POS system. Manage orders, track
                        inventory, and boost productivity.
                    </p>
                    <div class="mt-8 sm:max-w-lg sm:mx-auto sm:text-center lg:text-left">
                        <div class="space-y-4 sm:space-y-0 sm:space-x-4 flex flex-col sm:flex-row">
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-medium rounded-full text-white bg-[#0066CC] hover:bg-[#0052A3] transform hover:scale-105 transition duration-300 shadow-lg">
                                Get Started
                            </a>
                        </div>
                    </div>
                </div>
                <div class="mt-12 lg:mt-0 lg:col-span-6">
                    <div class="bg-white rounded-2xl shadow-2xl p-6 transform hover:rotate-2 transition duration-300">
                        <img src="{{ asset('dist/img/hero.jpg') }}" alt="POS System Preview" class="rounded-xl">
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 w-full">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                <path fill="#ffffff" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
            </svg>
        </div>
    </div>

    <!-- Features Section -->
    <div class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-4xl font-extrabold text-gray-900">
                    Features That Power Your Printing Business
                </h2>
            </div>
            <div class="mt-16 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Feature 1 -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8 transform hover:-translate-y-2 transition duration-300 shadow-lg">
                    <div class="text-[#0066CC] text-3xl mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Order Management</h3>
                    <p class="mt-4 text-gray-600 text-lg">Track and manage printing orders efficiently with real-time status updates.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl p-8 transform hover:-translate-y-2 transition duration-300 shadow-lg">
                    <div class="text-[#FFD700] text-3xl mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Inventory Control</h3>
                    <p class="mt-4 text-gray-600 text-lg">Monitor paper stock, ink levels, and supplies with automated alerts.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-2xl p-8 transform hover:-translate-y-2 transition duration-300 shadow-lg">
                    <div class="text-[#FF69B4] text-3xl mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Reports & Analytics</h3>
                    <p class="mt-4 text-gray-600 text-lg">Generate detailed reports on sales, popular products, and business performance.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-blue-400 relative">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:py-24 lg:px-8">
            <h2 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl text-center">
                <span class="block">Ready to get started?</span>
                <span class="block mt-2">Choose your theme and start today.</span>
            </h2>
            <div class="mt-12 flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                <a href="{{ route('login') }}"
                    class="inline-flex items-center justify-center px-8 py-4 text-lg font-medium rounded-full text-[#0066CC] bg-white hover:bg-gray-50 transform hover:scale-105 transition duration-300 shadow-lg">
                    get started
                </a>
            </div>
        </div>
        <div class="absolute top-0 w-full transform rotate-180">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                <path fill="#ffffff" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
            </svg>
        </div>
    </div>

    <footer class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-600 text-lg">
                © {{ date('Y') }} Grafika Printing. All rights reserved.
            </p>
        </div>
    </footer>
</body>

</html>