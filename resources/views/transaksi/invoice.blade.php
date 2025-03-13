<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $transaksi->kode }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.5;
        }
        .invoice-header {
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
        }
        .logo-container {
            text-align: left;
        }
        .invoice-info {
            text-align: right;
        }
        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 5px;
        }
        .invoice-addresses {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }
        .invoice-addresses > div {
            width: 45%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
        }
        .total-row td {
            font-weight: bold;
            border-top: 2px solid #000;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #777;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <div class="logo-container">
            <h1>{{ $transaksi->vendor->name }}</h1>
            <p>{{ $transaksi->vendor->address }}</p>
            <p>{{ $transaksi->vendor->phone }}</p>
        </div>
        <div class="invoice-info">
            <h2>INVOICE</h2>
            <p><strong>No: {{ $transaksi->kode }}</strong></p>
            <p>Tanggal: {{ $transaksi->tanggal_dibuat->format('d/m/Y') }}</p>
            <p>Status: {{ ucfirst($transaksi->status) }}</p>
        </div>
    </div>

    <div class="invoice-addresses">
        <div>
            <h3>Ditagihkan Kepada:</h3>
            <p><strong>{{ $transaksi->pelanggan->nama }}</strong></p>
            <p>{{ $transaksi->pelanggan->alamat }}</p>
            <p>Telp: {{ $transaksi->pelanggan->telepon }}</p>
            <p>Email: {{ $transaksi->pelanggan->email }}</p>
        </div>
        <div>
            <h3>Metode Pembayaran:</h3>
            <p>{{ $transaksi->payment_method }}</p>
            <h3>Estimasi Selesai:</h3>
            <p>{{ $transaksi->estimasi_selesai->format('d/m/Y') }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Spesifikasi</th>
                <th class="text-right">Kuantitas</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi->transaksiItem as $item)
                <tr>
                    <td>{{ $item->produk->nama_produk }}</td>
                    <td>
                        @if($item->transaksiItemSpecifications->count() > 0)
                            <ul style="padding-left: 15px; margin: 0;">
                                @foreach($item->transaksiItemSpecifications as $spec)
                                    <li>
                                        {{ $spec->spesifikasiProduk->spesifikasi->nama_spesifikasi ?? 'Spesifikasi' }}: 
                                        {{ $spec->value }}
                                        @if($spec->bahan)
                                            ({{ $spec->bahan->nama_bahan }})
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </td>
                    <td class="text-right">{{ $item->kuantitas }}</td>
                    <td class="text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->kuantitas * $item->harga_satuan, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="text-right">Total:</td>
                <td class="text-right">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if($transaksi->catatan)
        <div>
            <h3>Catatan:</h3>
            <p>{{ $transaksi->catatan }}</p>
        </div>
    @endif

    <div class="footer">
        <p>Terima kasih atas kepercayaan Anda.</p>
        <p>{{ $transaksi->vendor->name }} | {{ $transaksi->vendor->email }} | {{ $transaksi->vendor->phone }}</p>
    </div>
</body>
</html>
