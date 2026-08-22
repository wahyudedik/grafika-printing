<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - {{ $linktree->nama_toko ?? config('app.name') }}</title>
    @vite(['resources/css/app.css'])
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4 font-sans antialiased">
    <div class="bg-white rounded-2xl shadow-lg max-w-md w-full p-6 text-center">
        <!-- Success Icon -->
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check text-green-600 text-2xl"></i>
        </div>

        <h1 class="text-xl font-bold text-gray-900 mb-2">Pesanan Berhasil Dibuat!</h1>
        <p class="text-gray-600 text-sm mb-4">Terima kasih! Pesanan Anda telah tercatat. Silakan kirim bukti pembayaran via WhatsApp.</p>

        <!-- Order Summary -->
        <div class="bg-gray-50 rounded-xl p-4 mb-4 text-left">
            <div class="text-xs text-gray-500 mb-1">No. Order</div>
            <div class="text-sm font-mono font-semibold text-gray-900 mb-3">{{ $order->uuid }}</div>

            <div class="text-xs text-gray-500 mb-1">Produk</div>
            <div class="text-sm font-semibold text-gray-900 mb-2">{{ $order->produk->nama_produk ?? '-' }}</div>

            @if($order->selected_specs_text && $order->selected_specs_text !== '-')
            <div class="text-xs text-gray-500 mb-1">Spesifikasi</div>
            <div class="text-sm text-gray-700 mb-2">{{ $order->selected_specs_text }}</div>
            @endif

            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Jumlah: {{ $order->quantity }}</span>
                @if($order->total_price)
                <span class="font-semibold text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                @endif
            </div>

            @if($order->notes)
            <div class="mt-2 text-xs text-gray-500">Catatan: {{ $order->notes }}</div>
            @endif
        </div>

        <!-- WhatsApp Button -->
        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"
           class="block w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl transition mb-3">
            <i class="fab fa-whatsapp mr-2 text-lg"></i> Kirim Bukti via WhatsApp
        </a>

        <!-- Back to Linktree -->
        <a href="{{ url('/l/' . $linktree->custom_url) }}"
           class="block w-full border border-gray-300 text-gray-700 font-semibold py-3 rounded-xl hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Linktree
        </a>
    </div>
</body>
</html>
