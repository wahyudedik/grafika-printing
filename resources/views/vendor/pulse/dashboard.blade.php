<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Pulse Dashboard</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl border border-gray-200 p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Laravel Pulse Dashboard</h3>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-blue-900 mb-1">Real-time Monitoring</h4>
                <p class="text-sm text-blue-700">Dashboard monitoring real-time untuk melihat performa aplikasi, request, dan aktivitas sistem.</p>
            </div>
        </div>
    </div>
</body>

</html>
