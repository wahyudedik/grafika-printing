<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice Pengiriman</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #2196F3;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .content {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 0 0 8px 8px;
        }

        .invoice-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .payment-button {
            display: inline-block;
            background: #4CAF50;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }

        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>📦 Invoice Pengiriman</h1>
        <p>Pesanan Anda siap dikirim!</p>
    </div>

    <div class="content">
        <h2>Halo {{ $transaksi->pelanggan->nama }}!</h2>

        <p>Pesanan Anda dengan kode <strong>{{ $transaksi->kode }}</strong> telah siap dikirim.
            Untuk melanjutkan proses pengiriman, Anda perlu membayar biaya pengiriman terlebih dahulu.</p>

        <div class="invoice-details">
            <h3>Detail Pesanan</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Kode Transaksi:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $transaksi->kode }}</td>
                </tr>
                @php
                    $ongkir = (float) ($transaksi->ongkir ?? 0);
                    $subtotalBarang = (float) $transaksi->total_harga - $ongkir;
                @endphp
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Subtotal Barang:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Rp
                        {{ number_format($subtotalBarang > 0 ? $subtotalBarang : $transaksi->total_harga, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Biaya Pengiriman ({{ $transaksi->kurir ?? '-' }}):</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Rp
                        {{ number_format($ongkir, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Total Pesanan:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Rp
                        {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Kurir:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $transaksi->kurir }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Alamat Tujuan:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $transaksi->alamat_pengiriman }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px;"><strong>Status:</strong></td>
                    <td style="padding: 8px;">
                        <span style="background: #ffc107; color: #000; padding: 4px 8px; border-radius: 4px;">
                            Menunggu Pembayaran Pengiriman
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="warning">
            <h4>⚠️ Penting!</h4>
            <p>Anda memiliki 2 opsi pembayaran:</p>
            <ol>
                <li><strong>Pembayaran Online:</strong> Klik tombol di bawah untuk membayar via aplikasi</li>
                <li><strong>Cash on Delivery (COD):</strong> Bayar langsung ke kurir saat barang sampai</li>
            </ol>
        </div>

        <div style="text-align: center;">
            <a href="{{ $paymentLink['invoice_url'] }}" class="payment-button">
                💳 Bayar Sekarang - Rp {{ number_format($transaksi->ongkir, 0, ',', '.') }}
            </a>
        </div>

        <div style="background: #e3f2fd; padding: 15px; border-radius: 4px; margin: 20px 0;">
            <h4>📋 Informasi Tambahan</h4>
            <ul>
                <li>Pembayaran online akan mempercepat proses pengiriman</li>
                <li>Jika memilih COD, pastikan Anda siap menerima barang saat kurir datang</li>
                <li>Nomor resi akan dikirim setelah pembayaran dikonfirmasi</li>
                <li>Anda dapat melacak pengiriman melalui aplikasi</li>
            </ul>
        </div>

        <p>Jika Anda memiliki pertanyaan, silakan hubungi vendor atau tim support kami.</p>
    </div>

    <div class="footer">
        <p>Email ini dikirim otomatis oleh sistem Grafika Printing</p>
        <p>Jangan balas email ini. Untuk bantuan, hubungi support kami.</p>
    </div>
</body>

</html>
