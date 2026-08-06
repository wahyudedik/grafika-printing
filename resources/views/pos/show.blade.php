@extends('layouts.vendor')

@section('title', 'Invoice ' . $transaksi->kode)

@section('content')
    <div x-data="invoicePreview()" class="px-4 py-4">
        <div class="flex justify-center">
            <div class="w-full max-w-3xl">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Invoice #{{ $transaksi->kode }}</h3>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('vendor.pos.invoice.print', $transaksi->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                                <i class="fas fa-download"></i>Download PDF
                            </a>
                            <button @click="printInvoice()" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                                <i class="fas fa-print"></i>Print
                            </button>
                            <button @click="openPreview()" class="inline-flex items-center gap-2 px-4 py-2 border border-blue-300 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-50 transition-colors">
                                <i class="fas fa-eye"></i>Preview
                            </button>
                            <a href="{{ route('vendor.pos.thermal-print', $transaksi->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 border border-amber-300 text-amber-700 rounded-lg text-sm font-medium hover:bg-amber-50 transition-colors">
                                <i class="fas fa-print"></i>Cetak Thermal
                            </a>
                            <a href="{{ route('vendor.pos.thermal-print-js', $transaksi->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 border border-green-300 text-green-700 rounded-lg text-sm font-medium hover:bg-green-50 transition-colors">
                                <i class="fas fa-print"></i>Thermal (WebUSB)
                            </a>
                            <a href="{{ route('vendor.pos.printer.settings') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                                <i class="fas fa-cog"></i>Printer Settings
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        <!-- Invoice yang akan diprint -->
                        <div id="invoice-container" class="invoice-print-container mx-auto">
                            <!-- Konten invoice tetap sama seperti sebelumnya -->
                            <div class="header">
                                @php
                                    $vendorName =
                                        $transaksi->vendor->name ??
                                        ($transaksi->vendor->nama_vendor ?? 'Bamboo Digital Printing');
                                    $vendorAddress =
                                        $transaksi->vendor->address ??
                                        ($transaksi->vendor->alamat ?? 'Pesantren Peterongan Jombang');
                                    $vendorPhone =
                                        $transaksi->vendor->phone ?? ($transaksi->vendor->telepon ?? '081-515-876-755');
                                    $vendorEmail = $transaksi->vendor->email ?? 'infografikaprint@gmail.com';
                                    $logoPath =
                                        $transaksi->vendor && $transaksi->vendor->logo
                                            ? asset('vendors_logo/' . $transaksi->vendor->logo)
                                            : asset('images/logo.png');
                                @endphp
                                <div class="flex items-center justify-center">
                                    <div class="text-center">
                                        <img src="{{ $logoPath }}" alt="Logo"
                                            style="height: 50px; margin-bottom: 10px;">
                                        <h2 style="margin: 0;">{{ $vendorName }}</h2>
                                        <p style="margin: 2px 0;">{{ $vendorAddress }}</p>
                                        <p style="margin: 2px 0;">{{ $vendorPhone }} | {{ $vendorEmail }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Sisa konten invoice tetap sama -->
                            <div class="info flex mb-5">
                                <div class="w-1/2">
                                    <p>No: {{ $transaksi->kode }}</p>
                                    <p>Cust: {{ $transaksi->pelanggan->nama }}</p>
                                    <p>Bayar: {{ ucfirst($transaksi->metode_pembayaran ?? 'Transfer') }}</p>
                                </div>
                                <div class="w-1/2">
                                    <p>Tanggal: {{ $transaksi->tanggal_dibuat->format('d/m/Y') }}</p>
                                    <p>Status: {{ ucfirst($transaksi->status ?? 'Processing') }}</p>
                                </div>
                            </div>

                            <div class="items">
                                @foreach ($transaksi->transaksiItem as $item)
                                    <div style="margin-bottom: 15px;">
                                        <!-- Nama Produk -->
                                        <div style="font-weight: bold; margin-bottom: 5px;">
                                            {{ $item->produk->nama_produk }}</div>

                                        <!-- Jumlah (quantity) -->
                                        <div
                                            style="display: flex; justify-content: space-between; margin-bottom: 5px; border-bottom: 1px dashed #ccc; padding-bottom: 3px;">
                                            <span style="color: #666;">Quantity</span>
                                            <span style="font-weight: 500;">{{ $item->kuantitas }} pcs</span>
                                        </div>

                                        <!-- Spesifikasi Produk -->
                                        @if ($item->transaksiItemSpecifications && count($item->transaksiItemSpecifications) > 0)
                                            <div class="item-details">
                                                @foreach ($item->transaksiItemSpecifications as $spec)
                                                    @if ($spec->spesifikasiProduk && $spec->spesifikasiProduk->spesifikasi)
                                                        <div
                                                            style="display: flex; justify-content: space-between; margin-bottom: 3px; border-bottom: 1px dashed #ccc; padding-bottom: 3px;">
                                                            <span style="color: #666;">
                                                                {{ $spec->spesifikasiProduk->spesifikasi->nama_spesifikasi }}
                                                            </span>
                                                            <span style="font-weight: 500;">
                                                                @php
                                                                    $hargaSatuan = 0;
                                                                    $totalHarga = $spec->price ?? 0;
                                                                    $nilai = $spec->nilai_spesifikasi ?? null;

                                                                    if (empty($nilai) && isset($spec->value)) {
                                                                        $nilai = $spec->value;
                                                                    }

                                                                    if ($spec->input_type == 'select' && $spec->bahan) {
                                                                        echo $spec->bahan->nama_bahan .
                                                                            ': ' .
                                                                            $item->kuantitas .
                                                                            ' x Rp ' .
                                                                            number_format(
                                                                                $totalHarga / $item->kuantitas,
                                                                                0,
                                                                                ',',
                                                                                '.',
                                                                            ) .
                                                                            ' = Rp ' .
                                                                            number_format($totalHarga, 0, ',', '.');
                                                                    } elseif (
                                                                        $nilai &&
                                                                        $spec->spesifikasiProduk->spesifikasi
                                                                    ) {
                                                                        if ($totalHarga > 0 && $nilai > 0) {
                                                                            $hargaSatuan = $totalHarga / $nilai;
                                                                        }

                                                                        echo number_format($nilai, 2, ',', '.') .
                                                                            ' ' .
                                                                            ($spec->spesifikasiProduk->spesifikasi
                                                                                ->satuan ??
                                                                                '') .
                                                                            ' x Rp ' .
                                                                            number_format($hargaSatuan, 0, ',', '.') .
                                                                            ' = Rp ' .
                                                                            number_format($totalHarga, 0, ',', '.');
                                                                    } else {
                                                                        echo 'Rp ' .
                                                                            number_format($totalHarga, 0, ',', '.');
                                                                    }
                                                                @endphp
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Total Harga Item -->
                                        <div
                                            style="display: flex; justify-content: space-between; margin-top: 5px; font-weight: bold;">
                                            <span>Total Item</span>
                                            <span style="color: #0d6efd;">
                                                Rp {{ number_format($item->harga_satuan * $item->kuantitas, 0, ',', '.') }}
                                            </span>
                                        </div>

                                        <!-- Garis pemisah antar item -->
                                        @if (!$loop->last)
                                            <div
                                                style="border-bottom: 1px solid #ccc; margin-top: 10px; margin-bottom: 10px;">
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="divider"></div>

                            <div class="total">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="width: 60%; text-align: left; padding-bottom: 5px;">Total</td>
                                        <td style="width: 40%; text-align: right; padding-bottom: 5px;">
                                            Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 60%; text-align: left; padding-bottom: 5px;">Terbayar</td>
                                        <td style="width: 40%; text-align: right; padding-bottom: 5px;">
                                            Rp {{ number_format($transaksi->terbayar, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 60%; text-align: left; padding-bottom: 5px;">Kembali</td>
                                        <td style="width: 40%; text-align: right; padding-bottom: 5px;">
                                            Rp {{ number_format($transaksi->kembali, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="footer">
                                <p>Terima kasih telah berbelanja!</p>
                                <p>Estimasi Selesai:
                                    {{ \Carbon\Carbon::parse($transaksi->estimasi_selesai)->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Preview Struk (Alpine.js) -->
    <div x-show="showPreview" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak @keydown.escape.window="closePreview()">
        <div class="fixed inset-0 bg-black/50" @click="closePreview()"></div>
        <div x-show="showPreview" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative bg-white rounded-xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h5 class="text-lg font-semibold text-gray-900">Preview Struk POS</h5>
                <button @click="closePreview()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="p-6 flex justify-center overflow-y-auto" style="max-height: 60vh;">
                <div id="preview-container" class="pos-receipt-preview"></div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50">
                <button @click="closePreview()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Tutup</button>
                <button @click="closePreview(); setTimeout(() => printInvoice(), 300)" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors">Print</button>
            </div>
        </div>
    </div>

    <style>
        /* Styles untuk tampilan invoice di halaman */
        .invoice-print-container {
            font-family: Arial, sans-serif;
            font-size: 12px;
            width: 7.5cm;
            padding: 10px;
            background: #fff;
            color: #000;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }

        .info {
            font-size: 10px;
            margin-bottom: 10px;
        }

        .info p {
            margin: 2px 0;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .items {
            font-size: 10px;
        }

        .item-details {
            margin-left: 10px;
            font-size: 9px;
        }

        .total {
            font-weight: bold;
            padding-top: 5px;
        }

        .total table {
            width: 100%;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 10px;
            padding-top: 5px;
        }

        /* Preview modal styles */
        .pos-receipt-preview {
            font-family: Arial, sans-serif;
            font-size: 12px;
            width: 7.5cm;
            padding: 10px;
            background: #fff;
            color: #000;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-height: 80vh;
            overflow-y: auto;
        }

        /* Styles khusus untuk print */
        @media print {
            @page {
                size: 80mm auto;
                /* Lebar 80mm (8cm) dan tinggi otomatis */
                margin: 0mm;
            }

            html,
            body {
                width: 75mm;
                /* 7.5cm */
                margin: 0;
                padding: 0;
            }

            body * {
                visibility: hidden;
            }

            .invoice-print-container,
            .invoice-print-container * {
                visibility: visible;
            }

            .invoice-print-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 75mm;
                /* 7.5cm */
                margin: 0;
                padding: 5mm;
                border: none;
                box-shadow: none;
            }

            .navbar,
            .header-menu,
            .footer-menu,
            .btn,
            .card-header,
            [x-show],
            .fixed {
                display: none !important;
            }

            /* Pastikan semua konten muat dalam lebar kertas */
            .items,
            .item-details,
            .total,
            .footer {
                width: 100%;
                max-width: 65mm;
                /* Sedikit lebih kecil dari lebar kertas */
            }

            /* Ukuran font yang lebih kecil untuk print */
            .header h2 {
                font-size: 14px;
                margin: 0;
            }

            .header p {
                font-size: 9px;
                margin: 1px 0;
            }

            .info {
                font-size: 9px;
            }

            .items {
                font-size: 9px;
            }

            .item-details {
                font-size: 8px;
            }

            .footer {
                font-size: 9px;
            }
        }
    </style>

    <script>
        function invoicePreview() {
            return {
                showPreview: false,

                openPreview() {
                    // Salin konten invoice ke dalam modal preview
                    const invoiceContent = document.getElementById('invoice-container').cloneNode(true);
                    const previewContainer = document.getElementById('preview-container');
                    previewContainer.innerHTML = '';
                    previewContainer.appendChild(invoiceContent);
                    this.showPreview = true;
                },

                closePreview() {
                    this.showPreview = false;
                },

                printInvoice() {
                    const style = document.createElement('style');
                    style.id = 'print-style';
                    style.innerHTML = `@page { size: 80mm auto; margin: 0; }`;
                    document.head.appendChild(style);

                    setTimeout(() => {
                        window.print();
                        setTimeout(() => {
                            const printStyle = document.getElementById('print-style');
                            if (printStyle) document.head.removeChild(printStyle);
                        }, 1000);
                    }, 100);
                }
            }
        }
    </script>
@endsection
