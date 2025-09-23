<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thermal Print - {{ $transaksi->kode }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 80mm;
            margin: 0;
            padding: 5mm;
            background: #fff;
            color: #000;
            line-height: 1.2;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }

        .header p {
            font-size: 10px;
            margin: 2px 0;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .info {
            font-size: 10px;
            margin-bottom: 10px;
        }

        .info p {
            margin: 1px 0;
        }

        .items {
            font-size: 10px;
        }

        .item-row {
            margin-bottom: 8px;
        }

        .item-title {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .item-spec {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 9px;
        }

        .item-total {
            display: flex;
            justify-content: space-between;
            margin-top: 3px;
            font-weight: bold;
        }

        .total-section {
            margin-top: 10px;
            font-size: 11px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }

        /* Print specific styles */
        @media print {
            body {
                width: 80mm;
                margin: 0;
                padding: 2mm;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()"
            style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            🖨️ Print Thermal Receipt
        </button>
        <button onclick="window.close()"
            style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            ❌ Close
        </button>
    </div>

    <!-- THERMAL RECEIPT CONTENT -->
    <div class="receipt-content">
        <!-- HEADER -->
        <div class="header">
            @php
                $vendorName =
                    $transaksi->vendor->name ?? ($transaksi->vendor->nama_vendor ?? 'Bamboo Digital Printing');
                $vendorAddress =
                    $transaksi->vendor->address ?? ($transaksi->vendor->alamat ?? 'Pesantren Peterongan Jombang');
                $vendorPhone = $transaksi->vendor->phone ?? ($transaksi->vendor->telepon ?? '081-515-876-755');
                $vendorEmail = $transaksi->vendor->email ?? 'infografikaprint@gmail.com';
            @endphp

            <h1>{{ $vendorName }}</h1>
            <p>{{ $vendorAddress }}</p>
            <p>{{ $vendorPhone }}</p>
            <p>{{ $vendorEmail }}</p>
        </div>

        <div class="divider"></div>

        <!-- TRANSACTION INFO -->
        <div class="info">
            <p><strong>Invoice:</strong> {{ $transaksi->kode }}</p>
            <p><strong>Tanggal:</strong> {{ $transaksi->tanggal_dibuat->format('d/m/Y H:i') }}</p>
            <p><strong>Customer:</strong> {{ $transaksi->pelanggan->nama }}</p>
            <p><strong>Pembayaran:</strong> {{ ucfirst($transaksi->payment_method ?? 'Cash') }}</p>
            <p><strong>Status:</strong> {{ ucfirst($transaksi->status ?? 'Processing') }}</p>
        </div>

        <div class="divider"></div>

        <!-- ITEMS -->
        <div class="items">
            @foreach ($transaksi->transaksiItem as $item)
                <div class="item-row">
                    <div class="item-title">{{ $item->produk->nama_produk }}</div>

                    <div class="item-spec">
                        <span>Qty:</span>
                        <span>{{ $item->kuantitas }} pcs</span>
                    </div>

                    @if ($item->transaksiItemSpecifications && count($item->transaksiItemSpecifications) > 0)
                        @foreach ($item->transaksiItemSpecifications as $spec)
                            @if ($spec->spesifikasiProduk && $spec->spesifikasiProduk->spesifikasi)
                                <div class="item-spec">
                                    <span>{{ $spec->spesifikasiProduk->spesifikasi->nama_spesifikasi }}:</span>
                                    <span>
                                        @php
                                            $totalHarga = $spec->price ?? 0;
                                            $nilai = $spec->nilai_spesifikasi ?? null;

                                            if (empty($nilai) && isset($spec->value)) {
                                                $nilai = $spec->value;
                                            }

                                            if ($spec->input_type == 'select' && $spec->bahan) {
                                                echo $spec->bahan->nama_bahan .
                                                    ' x ' .
                                                    $item->kuantitas .
                                                    ' = Rp ' .
                                                    number_format($totalHarga, 0, ',', '.');
                                            } elseif ($nilai && $spec->spesifikasiProduk->spesifikasi) {
                                                $hargaSatuan = $totalHarga / ($nilai > 0 ? $nilai : 1);
                                                echo number_format($nilai, 2, ',', '.') .
                                                    ' ' .
                                                    ($spec->spesifikasiProduk->spesifikasi->satuan ?? '') .
                                                    ' x Rp ' .
                                                    number_format($hargaSatuan, 0, ',', '.') .
                                                    ' = Rp ' .
                                                    number_format($totalHarga, 0, ',', '.');
                                            } else {
                                                echo 'Rp ' . number_format($totalHarga, 0, ',', '.');
                                            }
                                        @endphp
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    @endif

                    <div class="item-total">
                        <span>Total Item:</span>
                        <span>Rp {{ number_format($item->harga_satuan * $item->kuantitas, 0, ',', '.') }}</span>
                    </div>

                    @if (!$loop->last)
                        <div class="divider"></div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="divider"></div>

        <!-- TOTALS -->
        <div class="total-section">
            <div class="total-row">
                <span>Total:</span>
                <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>Terbayar:</span>
                <span>Rp {{ number_format($transaksi->terbayar, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>Kembali:</span>
                <span>Rp {{ number_format($transaksi->kembali, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- FOOTER -->
        <div class="footer">
            <p>Terima kasih telah berbelanja!</p>
            <p>Estimasi Selesai: {{ \Carbon\Carbon::parse($transaksi->estimasi_selesai)->format('d/m/Y H:i') }}</p>
            <p>---</p>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            // Small delay to ensure content is loaded
            setTimeout(function() {
                window.print();
            }, 500);
        };

        // Close window after printing
        window.onafterprint = function() {
            setTimeout(function() {
                window.close();
            }, 1000);
        };
    </script>
</body>

</html>
