<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thermal Print - {{ $transaksi->kode }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <style>
        @page {
            size: {{ $printerSettings->getCssWidth() }} auto;
            margin: 0;
            padding: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: {{ $printerSettings->font_size }}px;
            width: {{ $printerSettings->getCssWidth() }};
            margin: 0;
            padding: {{ $printerSettings->margin }};
            background: #fff;
            color: #000;
            line-height: 1.3;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .header {
            text-align: center;
            margin-bottom: 8px;
        }

        .header .vendor-logo {
            max-width: 60mm;
            max-height: 20mm;
            margin: 0 auto 4px;
            display: block;
        }

        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin: 0 0 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header p {
            font-size: 9px;
            margin: 1px 0;
            line-height: 1.2;
        }

        .divider {
            border: none;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .info {
            font-size: 9px;
            margin-bottom: 8px;
        }

        .info p {
            margin: 1px 0;
        }

        .info strong {
            display: inline-block;
            width: 80px;
        }

        .items {
            font-size: 9px;
        }

        .item-row {
            margin-bottom: 6px;
            page-break-inside: avoid;
        }

        .item-title {
            font-weight: bold;
            margin-bottom: 2px;
            font-size: 10px;
        }

        .item-spec {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1px;
            font-size: 8px;
        }

        .item-total {
            display: flex;
            justify-content: space-between;
            margin-top: 2px;
            font-weight: bold;
            font-size: 9px;
        }

        .total-section {
            margin-top: 8px;
            font-size: 10px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .total-row.grand-total {
            font-weight: bold;
            font-size: 12px;
            border-top: 1px dashed #000;
            padding-top: 4px;
            margin-top: 4px;
        }

        .footer {
            text-align: center;
            font-size: 8px;
            margin-top: 8px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }

        .footer p {
            margin: 2px 0;
        }

        /* Print specific styles */
        @media print {
            body {
                width: {{ $printerSettings->getCssWidth() }};
                margin: 0;
                padding: 1mm;
            }

            .no-print {
                display: none !important;
            }

            .receipt-content {
                padding: 0;
            }
        }

        /* Screen preview styles */
        @media screen {
            body {
                max-width: 400px;
                margin: 10px auto;
                padding: 10px;
                border: 1px solid #ccc;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
        }
    </style>
</head>

<body>
    <!-- PRINT CONTROLS (hidden when printing) -->
    <div class="no-print" style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px; margin-bottom: 20px;">
        <div style="margin-bottom: 15px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"
                style="color: #007bff;">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
            </svg>
        </div>
        <h3 style="margin: 0 0 5px; color: #333;">Cetak Struk Thermal</h3>
        <p style="color: #666; margin: 0 0 15px; font-size: 14px;">
            Paper: {{ $printerSettings->paper_width }} | Font: {{ $printerSettings->font_size }}px
        </p>
        <div>
            <button onclick="window.print()"
                style="padding: 12px 30px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: bold;">
                🖨️ Print Sekarang
            </button>
            <button onclick="selectPrinter()"
                style="padding: 12px 30px; background: #007bff; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; margin-left: 10px;">
                📋 Pilih Printer
            </button>
            <button onclick="window.close()"
                style="padding: 12px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; margin-left: 10px;">
                ✕ Tutup
            </button>
        </div>
    </div>

    <!-- THERMAL RECEIPT CONTENT -->
    <div class="receipt-content">
        <!-- HEADER -->
        <div class="header">
            @php
                $vendor = $transaksi->vendor;
                $vendorName = $vendor->name ?? ($vendor->nama_vendor ?? 'Bamboo Digital Printing');
                $vendorAddress = $vendor->address ?? ($vendor->alamat ?? 'Pesantren Peterongan Jombang');
                $vendorPhone = $vendor->phone ?? ($vendor->telepon ?? '081-515-876-755');
                $vendorEmail = $vendor->email ?? 'infografikaprint@gmail.com';
            @endphp

            @if($vendor && $vendor->logo && file_exists(public_path('vendors_logo/' . $vendor->logo)))
                <img src="{{ asset('vendors_logo/' . $vendor->logo) }}" alt="{{ $vendorName }}" class="vendor-logo">
            @endif
            <h1>{{ $vendorName }}</h1>
            <p>{{ $vendorAddress }}</p>
            <p>{{ $vendorPhone }}</p>
            @if($vendorEmail)
                <p>{{ $vendorEmail }}</p>
            @endif
        </div>

        <hr class="divider">

        <!-- TRANSACTION INFO -->
        <div class="info">
            <p><strong>No. Invoice</strong> {{ $transaksi->kode }}</p>
            <p><strong>Tanggal</strong> {{ $transaksi->tanggal_dibuat->format('d/m/Y H:i') }}</p>
            <p><strong>Customer</strong> {{ $transaksi->pelanggan->nama ?? '-' }}</p>
            <p><strong>Pembayaran</strong> {{ ucfirst($transaksi->payment_method ?? 'Cash') }}</p>
            @if($transaksi->status)
                <p><strong>Status</strong> {{ ucfirst($transaksi->status) }}</p>
            @endif
        </div>

        <hr class="divider">

        <!-- ITEMS -->
        <div class="items">
            @foreach ($transaksi->transaksiItem as $item)
                <div class="item-row">
                    <div class="item-title">{{ $item->produk->nama_produk ?? 'Produk' }}</div>

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
                        <span>Subtotal:</span>
                        <span>Rp {{ number_format($item->harga_satuan * $item->kuantitas, 0, ',', '.') }}</span>
                    </div>

                    @if (!$loop->last)
                        <hr class="divider" style="margin: 4px 0;">
                    @endif
                </div>
            @endforeach
        </div>

        <hr class="divider">

        <!-- TOTALS -->
        @php
            $ongkir = (float) ($transaksi->ongkir ?? 0);
            $subtotalBarang = (float) $transaksi->total_harga - $ongkir;
        @endphp
        <div class="total-section">
            <div class="total-row">
                <span>Subtotal Barang:</span>
                <span>Rp {{ number_format($subtotalBarang > 0 ? $subtotalBarang : $transaksi->total_harga, 0, ',', '.') }}</span>
            </div>
            @if ($ongkir > 0)
                <div class="total-row">
                    <span>Ongkir ({{ $transaksi->kurir ?? '-' }}):</span>
                    <span>Rp {{ number_format($ongkir, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>Dibayar:</span>
                <span>Rp {{ number_format($transaksi->terbayar, 0, ',', '.') }}</span>
            </div>
            @if (($transaksi->kembali ?? 0) > 0)
                <div class="total-row">
                    <span>Kembali:</span>
                    <span>Rp {{ number_format($transaksi->kembali, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>

        <!-- SHIPPING INFO (if COD) -->
        @if ($ongkir > 0 && ($transaksi->is_cod ?? false))
            <hr class="divider">
            <div style="font-size: 8px;">
                <p style="font-weight: bold;">📦 Info Pengiriman:</p>
                @if ($transaksi->alamat_pengiriman)
                    <p>Alamat: {{ $transaksi->alamat_pengiriman }}</p>
                @endif
                @if ($transaksi->kurir)
                    <p>Kurir: {{ $transaksi->kurir }}</p>
                @endif
                @if ($transaksi->no_resi)
                    <p>No. Resi: {{ $transaksi->no_resi }}</p>
                @endif
                <p style="font-weight: bold; margin-top: 4px;">⚠ COD - Bayar di Tempat</p>
            </div>
        @endif

        <hr class="divider">

        <!-- BARCODE AREA -->
        <div style="text-align: center; margin: 8px 0;">
            <p style="font-size: 7px; letter-spacing: 2px;">{{ $transaksi->kode }}</p>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <p style="font-weight: bold;">Terima kasih telah berbelanja!</p>
            @if($transaksi->estimasi_selesai)
                <p>Estimasi Selesai:</p>
                <p>{{ \Carbon\Carbon::parse($transaksi->estimasi_selesai)->format('d/m/Y H:i') }}</p>
            @endif
            <p style="margin-top: 4px;">---</p>
            <p>{{ $vendorName }}</p>
        </div>
    </div>

    <script>
        /**
         * Select printer using WebUSB API (for direct thermal printer connection)
         */
        async function selectPrinter() {
            if ('usb' in navigator) {
                try {
                    const device = await navigator.usb.requestDevice({ filters: [] });
                    showToast('Printer dipilih: ' + device.productName, 'success');

                    // Store selected device for printing
                    window.selectedPrinter = device;

                    // Now print
                    await printToUsbPrinter(device);
                } catch (error) {
                    console.error('WebUSB Error:', error);
                    if (error.name === 'NotFoundError') {
                        // User cancelled, fallback to browser print
                        window.print();
                    } else {
                        showToast('Gagal: ' + error.message + '. Menggunakan browser print...', 'error');
                        window.print();
                    }
                }
            } else {
                // WebUSB not supported, use browser print
                showToast('WebUSB tidak tersedia. Menggunakan browser print...', 'info');
                window.print();
            }
        }

        /**
         * Print to USB printer using ESC/POS commands
         */
        async function printToUsbPrinter(device) {
            try {
                await device.open();

                if (device.configuration === null) {
                    await device.selectConfiguration(1);
                }

                await device.claimInterface(0);

                const receiptText = generateReceiptText();
                const encoder = new TextEncoder();
                const data = encoder.encode(receiptText);

                await device.transferOut(1, data);

                // Send cut command
                const cutCommand = new Uint8Array([0x1D, 0x56, 0x00]);
                await device.transferOut(1, cutCommand);

                await device.close();

                showToast('Berhasil dikirim ke printer!', 'success');

                @if($printerSettings->auto_close_window)
                    setTimeout(() => window.close(), 2000);
                @endif

            } catch (error) {
                console.error('Print Error:', error);
                showToast('Gagal print ke USB: ' + error.message, 'error');
                // Fallback
                window.print();
            }
        }

        /**
         * Generate ESC/POS receipt text
         */
        function generateReceiptText() {
            const ESC = '\x1B';
            const GS = '\x1D';
            let text = '';

            // Initialize
            text += ESC + '@';

            // Center + Bold
            text += ESC + 'a' + '\x01';
            text += ESC + 'E' + '\x01';
            text += '{{ addslashes($vendorName) }}\n';

            // Bold off
            text += ESC + 'E' + '\x00';
            text += '{{ addslashes($vendorAddress) }}\n';
            text += '{{ addslashes($vendorPhone) }}\n';

            // Left align
            text += ESC + 'a' + '\x00';
            text += '================================\n';

            // Info
            text += 'No: {{ $transaksi->kode }}\n';
            text += 'Tgl: {{ $transaksi->tanggal_dibuat->format("d/m/Y H:i") }}\n';
            text += 'Cust: {{ addslashes($transaksi->pelanggan->nama ?? "-") }}\n';
            text += 'Bayar: {{ ucfirst($transaksi->payment_method ?? "Cash") }}\n';
            text += '--------------------------------\n';

            // Items
            @foreach ($transaksi->transaksiItem as $item)
                text += '{{ addslashes($item->produk->nama_produk ?? "Produk") }}\n';
                text += '  {{ $item->kuantitas }} pcs  Rp {{ number_format($item->harga_satuan * $item->kuantitas, 0, ",", ".") }}\n';
                @if (!$loop->last)
                    text += '  --------------------------------\n';
                @endif
            @endforeach

            text += '================================\n';
            text += 'SUBTOTAL:  Rp {{ number_format($subtotalBarang > 0 ? $subtotalBarang : $transaksi->total_harga, 0, ",", ".") }}\n';
            @if ($ongkir > 0)
                text += 'ONGKIR:    Rp {{ number_format($ongkir, 0, ",", ".") }}\n';
            @endif

            // Bold total
            text += ESC + 'E' + '\x01';
            text += 'TOTAL:     Rp {{ number_format($transaksi->total_harga, 0, ",", ".") }}\n';
            text += ESC + 'E' + '\x00';

            text += 'DIBAYAR:   Rp {{ number_format($transaksi->terbayar, 0, ",", ".") }}\n';
            @if (($transaksi->kembali ?? 0) > 0)
                text += 'KEMBALI:   Rp {{ number_format($transaksi->kembali, 0, ",", ".") }}\n';
            @endif

            text += '================================\n';

            // Footer
            text += ESC + 'a' + '\x01';
            text += '\nTerima kasih telah berbelanja!\n';
            @if($transaksi->estimasi_selesai)
                text += 'Estimasi: {{ \Carbon\Carbon::parse($transaksi->estimasi_selesai)->format("d/m/Y H:i") }}\n';
            @endif
            text += '\n================================\n';

            // Feed + cut
            text += ESC + 'd' + '\x03';
            text += GS + 'V' + '\x00';

            return text;
        }

        /**
         * Toast notification
         */
        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed; top: 20px; right: 20px; padding: 12px 24px;
                border-radius: 8px; color: white; font-size: 14px; z-index: 9999;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#007bff'};
            `;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        // Auto close after printing
        window.addEventListener('afterprint', function() {
            @if($printerSettings->auto_close_window)
                setTimeout(function() {
                    window.close();
                }, 1500);
            @endif
        });
    </script>
</body>

</html>
