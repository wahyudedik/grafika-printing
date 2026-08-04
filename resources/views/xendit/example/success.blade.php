<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil - {{ $payment->external_id }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-8">
        <div class="max-w-md mx-auto px-4">
            <!-- Success Card -->
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <!-- Success Icon -->
                <div class="mb-6">
                    <div class="mx-auto w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600 text-3xl"></i>
                    </div>
                </div>

                <!-- Success Message -->
                <h1 class="text-2xl font-bold text-gray-900 mb-4">
                    Pembayaran Berhasil!
                </h1>

                <p class="text-gray-600 mb-6">
                    Terima kasih! Pembayaran Anda telah berhasil diproses.
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
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                {{ $payment->status_text }}
                            </span>
                        </div>

                        @if ($payment->payment_method)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Metode:</span>
                                <span class="text-gray-800">{{ $payment->payment_method }}</span>
                            </div>
                        @endif

                        @if ($payment->paid_at)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Waktu:</span>
                                <span class="text-gray-800">{{ $payment->paid_at->format('d M Y H:i:s') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="{{ route('xendit.example.show-payment', $payment->external_id) }}"
                        class="block w-full bg-blue-600 text-white py-3 px-6 rounded-md hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-receipt mr-2"></i>
                        Lihat Detail Pembayaran
                    </a>

                    <a href="{{ route('xendit.example.index') }}"
                        class="block w-full bg-gray-600 text-white py-3 px-6 rounded-md hover:bg-gray-700 transition duration-200">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Halaman Utama
                    </a>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Anda akan menerima email konfirmasi pembayaran
                </p>
            </div>
        </div>
    </div>
</body>

</html>
