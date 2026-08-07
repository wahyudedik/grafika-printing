<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pembayaran - {{ $payment->external_id }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">
    <div class="min-h-screen py-8">
        <div class="max-w-2xl mx-auto px-4">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">
                    <i class="fas fa-receipt text-blue-600"></i>
                    Detail Pembayaran
                </h1>
                <p class="text-gray-600">ID: {{ $payment->external_id }}</p>
            </div>

            <!-- Payment Details Card -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex justify-between items-start mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Informasi Pembayaran</h2>
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $payment->status_badge_class }}">
                        {{ $payment->status_text }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Amount -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-money-bill-wave text-blue-600 mr-2"></i>
                            <span class="font-medium text-gray-700">Jumlah</span>
                        </div>
                        <p class="text-2xl font-bold text-blue-600">{{ $payment->formatted_amount }}</p>
                    </div>

                    <!-- Status -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-info-circle text-gray-600 mr-2"></i>
                            <span class="font-medium text-gray-700">Status</span>
                        </div>
                        <p class="text-lg font-semibold text-gray-800">{{ $payment->status_text }}</p>
                    </div>

                    <!-- Customer Info -->
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-user text-green-600 mr-2"></i>
                            <span class="font-medium text-gray-700">Customer</span>
                        </div>
                        <p class="font-semibold text-gray-800">{{ $payment->customer_name }}</p>
                        <p class="text-sm text-gray-600">{{ $payment->customer_email }}</p>
                    </div>

                    <!-- Description -->
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-file-text text-purple-600 mr-2"></i>
                            <span class="font-medium text-gray-700">Deskripsi</span>
                        </div>
                        <p class="text-gray-800">{{ $payment->description }}</p>
                    </div>
                </div>

                <!-- Payment Method & Timing -->
                @if ($payment->payment_method)
                    <div class="mt-6 bg-yellow-50 p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-credit-card text-yellow-600 mr-2"></i>
                            <span class="font-medium text-gray-700">Metode Pembayaran</span>
                        </div>
                        <p class="font-semibold text-gray-800">{{ $payment->payment_method }}</p>
                    </div>
                @endif

                @if ($payment->paid_at)
                    <div class="mt-4 bg-green-50 p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            <span class="font-medium text-gray-700">Waktu Pembayaran</span>
                        </div>
                        <p class="font-semibold text-gray-800">{{ $payment->paid_at->format('d M Y H:i:s') }}</p>
                    </div>
                @endif

                @if ($payment->expires_at)
                    <div class="mt-4 bg-red-50 p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-clock text-red-600 mr-2"></i>
                            <span class="font-medium text-gray-700">Kadaluarsa</span>
                        </div>
                        <p class="font-semibold text-gray-800">{{ $payment->expires_at->format('d M Y H:i:s') }}</p>
                    </div>
                @endif

                @if ($payment->failure_reason)
                    <div class="mt-4 bg-red-50 p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                            <span class="font-medium text-gray-700">Alasan Gagal</span>
                        </div>
                        <p class="text-red-800">{{ $payment->failure_reason }}</p>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    @if ($payment->checkout_url && $payment->status === 'pending')
                        <a href="{{ $payment->checkout_url }}" target="_blank"
                            class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-md hover:bg-blue-700 transition duration-200 text-center">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            Lanjutkan Pembayaran
                        </a>
                    @endif

                    <a href="{{ route('xendit.example.index') }}"
                        class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-md hover:bg-gray-700 transition duration-200 text-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Halaman Utama
                    </a>
                </div>
            </div>

            <!-- Payment Status Info -->
            <div class="mt-6 bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    Informasi Status
                </h3>

                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600">External ID:</span>
                        <span
                            class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ $payment->external_id }}</span>
                    </div>

                    @if ($payment->xendit_id)
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600">Xendit ID:</span>
                            <span
                                class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ $payment->xendit_id }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                        <span class="text-gray-600">Dibuat:</span>
                        <span class="text-gray-800">{{ $payment->created_at->format('d M Y H:i:s') }}</span>
                    </div>

                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600">Terakhir Update:</span>
                        <span class="text-gray-800">{{ $payment->updated_at->format('d M Y H:i:s') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
