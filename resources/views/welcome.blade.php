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
</head>

<body class="font-sans antialiased bg-gray-50">
    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                <div class="sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left">
                    <h1 class="text-4xl font-bold text-gray-900 sm:text-5xl md:text-6xl">
                        <span class="block">Grafika</span>
                        <span class="block text-blue-600">Printing System</span>
                    </h1>
                    <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-xl lg:text-lg xl:text-xl">
                        Streamline your printing business with our comprehensive POS system. Manage orders, track
                        inventory, and boost productivity.
                    </p>
                    <div class="mt-8 sm:max-w-lg sm:mx-auto sm:text-center lg:text-left">
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Get Started
                        </a>
                    </div>
                </div>
                <div class="mt-12 lg:mt-0 lg:col-span-6">
                    <div class="bg-white rounded-lg shadow-xl p-8">
                        <img src="{{ asset('dist/img/hero.jpg') }}" alt="POS System Preview" class="rounded-lg">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900">
                    Features That Power Your Printing Business
                </h2>
            </div>
            <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Feature 1 -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="text-blue-600 text-2xl mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Order Management</h3>
                    <p class="mt-2 text-gray-500">Track and manage printing orders efficiently with real-time status
                        updates.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="text-blue-600 text-2xl mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Inventory Control</h3>
                    <p class="mt-2 text-gray-500">Monitor paper stock, ink levels, and supplies with automated alerts.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="text-blue-600 text-2xl mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Reports & Analytics</h3>
                    <p class="mt-2 text-gray-500">Generate detailed reports on sales, popular products, and business
                        performance.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-blue-600">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
            <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                <span class="block">Ready to get started?</span>
                <span class="block">Start your free trial today.</span>
            </h2><br>
            <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
                <div class="inline-flex rounded-md shadow">
                    <a href="/app"
                        class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-gray-50">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-gray-50">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500">
                © {{ date('Y') }} Grafika Printing. All rights reserved.
            </p>
        </div>
    </footer>
</body>

</html>