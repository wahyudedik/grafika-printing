<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Grafika Printing - Smart Printing Management System</title>
    <!-- CSS files -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="{{ asset('dist/css/tabler.min.css?1738096684') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/tabler-flags.min.css?1738096684') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/tabler-socials.min.css?1738096684') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/tabler-payments.min.css?1738096684') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/tabler-vendors.min.css?1738096684') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/tabler-marketing.min.css?1738096684') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/demo.min.css?1738096684') }}" rel="stylesheet" />
    <style>
        @import url('https://rsms.me/inter/inter.css');
    </style>
</head>

<body>
    <script src="{{ asset('dist/js/demo-theme.min.js?1738096684') }}"></script>
    <div class="page">
        <!-- Navbar -->
        @include('dev.layouts.header')
        <div class="page-wrapper">
            <!-- Page body -->
            <div class="page-body">
                <div class="container-xl my-auto">
                    @yield('content')
                </div>
            </div>
        </div>
        @include('dev.layouts.footer')
    </div>
    <!-- Libs JS -->
    <!-- Tabler Core -->
    <script src="{{ asset('dist/js/tabler.min.js?1738096684') }}" defer></script>
    <script src="{{ asset('dist/js/demo.min.js?1738096684') }}" defer></script>
</body>

</html>