<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Gagal - {{ $payment->external_id }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-8">
        <div class="max-w-md mx-auto px-4">
            <!-- Failure Card -->
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <!-- Failure Icon -->
                <div class="mb-6">
                    <div class="mx-auto w-20 h-20 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-times text-red-600 text-3xl"></i>
                    </div>
                </div>

                <!-- Failure Message -->
                <h1 class="text-2xl font-bold text-gray-900 mb-4">
                    Pembayaran Gagal
                </h1>

                <p class="text-gray-600 mb-6">
                    Maaf, pembayaran Anda tidak dapat diproses. Silakan coba lagi.
                </p>

                <!-- Payment Details -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                    <h3 class="font-semibold text-gray-800 mb-3">Detail Pembayaran:</h3>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">ID Pembayaran:</span>
                            <span class="font-mono text-gray-800">{{ $payment->external_id }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-600">Jumlah:</span>
                            <span class="font-semibold text-gray-800">{{ $payment->formatted_amount }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                {{ $payment->status_text }}
                            </span>
                        </div>

                        @if ($payment->failure_reason)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Alasan:</span>
                                <span class="text-red-600 text-right">{{ $payment->failure_reason }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    @if ($payment->checkout_url && $payment->status === 'pending')
                        <a href="{{ $payment->checkout_url }}"
                            class="block w-full bg-blue-600 text-white py-3 px-6 rounded-md hover:bg-blue-700 transition duration-200">
                            <i class="fas fa-redo mr-2"></i>
                            Coba Lagi
                        </a>
                    @endif

                    <a href="{{ route('user.dashboard') }}"
                        class="block w-full bg-gray-600 text-white py-3 px-6 rounded-md hover:bg-gray-700 transition duration-200">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>

            <!-- Help Info -->
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-2 mt-1"></i>
                    <div>
                        <h4 class="font-semibold text-yellow-800 mb-2">Butuh Bantuan?</h4>
                        <p class="text-sm text-yellow-700">
                            Jika Anda mengalami masalah berkelanjutan, silakan hubungi tim support atau coba metode
                            pembayaran yang berbeda.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
