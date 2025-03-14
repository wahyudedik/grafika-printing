<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $transaksi->kode }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
            background-color: white;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            background-color: white;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid black;
        }

        .header h1 {
            color: black;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .invoice-info {
            margin-bottom: 15px;
            padding: 8px;
            border: 1px solid black;
        }

        .invoice-info p {
            margin: 3px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            padding: 6px 8px;
            border: 1px solid black;
            font-size: 11px;
        }

        th {
            background-color: white;
            color: black;
            font-weight: bold;
        }

        .total {
            text-align: right;
            font-weight: bold;
            font-size: 14px;
            padding: 8px;
            border: 1px solid black;
            margin-bottom: 15px;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid black;
            color: black;
            font-size: 11px;
        }

        .footer p {
            margin: 3px 0;
        }

        @media print {
            body {
                background-color: white;
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        @if ($transaksi->vendor)
            <div class="header">
                <h1>{{ $transaksi->vendor->name }}</h1>
                <p>{{ $transaksi->vendor->address }}</p>
            </div>
        @endif
        <div class="invoice-info">
            <p><strong>Invoice #:</strong> {{ $transaksi->kode }}</p>
            <p><strong>Date:</strong> {{ $transaksi->tanggal_dibuat->format('d/m/Y') }}</p>
            <p><strong>Customer:</strong> {{ $transaksi->pelanggan->nama }}</p>
            <p><strong>Payment Method:</strong> {{ ucfirst($transaksi->payment_method) }}</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Specifications</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaksi->transaksiItem as $item)
                    <tr>
                        <td>{{ $item->produk->nama_produk ?? 'Product Not Found' }}</td>
                        <td>
                            @foreach ($item->transaksiItemSpecifications as $spec)
                                <strong>{{ $spec->spesifikasiProduk->spesifikasi->nama_spesifikasi ?? 'Specification' }}:</strong>
                                @if ($spec->input_type === 'select')
                                    {{ $spec->bahan->nama_bahan ?? 'Material Not Found' }}
                                @else
                                    {{ $spec->value }} {{ $spec->spesifikasiProduk->spesifikasi->satuan ?? '' }}
                                @endif
                                (Rp {{ number_format($spec->price, 0, ',', '.') }})
                                <br>
                            @endforeach
                        </td>
                        <td>{{ $item->kuantitas }}</td>
                        <td>Rp {{ number_format($item->harga_satuan * $item->kuantitas, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="total">
            <p>Total: Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>
        </div>
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>Estimated Completion: {{ \Carbon\Carbon::parse($transaksi->estimasi_selesai)->format('d/m/Y H:i') }}</p>
            <p>{{ $transaksi->vendor->name }} | {{ $transaksi->vendor->phone }}</p>
        </div>
    </div>
</body>

</html>
