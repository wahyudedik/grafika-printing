<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $transaksi->kode }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            color: #1e293b;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .header h1 {
            color: #1e293b;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .invoice-info {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .invoice-info p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th,
        td {
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
        }

        th {
            background-color: #f5f5f5;
            color: #1e293b;
            font-weight: 600;
            text-align: left;
        }

        .total {
            text-align: right;
            font-weight: bold;
            font-size: 18px;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            background-color: #f9f9f9;
            margin-bottom: 30px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
            color: #64748b;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            background-color: #4a6cf7;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
            gap: 8px;
        }

        .btn:hover {
            background-color: #3a5bd9;
        }

        .btn-secondary {
            background-color: #64748b;
        }

        .btn-secondary:hover {
            background-color: #475569;
        }

        @media print {
            .actions {
                display: none;
            }

            body {
                background-color: white;
                padding: 0;
            }

            .container {
                box-shadow: none;
                max-width: 100%;
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ $transaksi->vendor->name }}</h1>
            <p>{{ $transaksi->vendor->address }}</p>
        </div>

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
                    <th>Quantity</th>
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

        <div class="actions">
            <a href="{{ route('pos.invoice.download', ['transaksi' => $transaksi->id]) }}" class="btn">
                <i class="fas fa-download"></i> Download PDF
            </a>
            <a href="{{ route('pos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to POS
            </a>
            <button onclick="window.print()" class="btn">
                <i class="fas fa-print"></i> Print
            </button>
        </div>

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>Estimated Completion: {{ \Carbon\Carbon::parse($transaksi->estimasi_selesai)->format('d/m/Y H:i') }}</p>
            <p>{{ $transaksi->vendor->name }} | {{ $transaksi->vendor->phone }}</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Show success message when page loads
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Invoice Generated',
                text: 'Your invoice has been generated successfully!',
                confirmButtonColor: '#3085d6'
            });
        });
    </script>
</body>

</html>
