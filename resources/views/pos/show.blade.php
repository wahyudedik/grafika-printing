@extends('layouts.layouts_dashboard')

@section('title', 'Invoice ' . $transaksi->kode)

@section('content')
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">Invoice #{{ $transaksi->kode }}</h3>
                        <div>
                            <a href="{{ route('pos.invoice.download', $transaksi->id) }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download me-2"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                                    <path d="M7 11l5 5l5 -5"></path>
                                    <path d="M12 4l0 12"></path>
                                </svg>Download PDF
                            </a>
                            <a href="#" onclick="window.print()" class="btn btn-outline-secondary ms-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer me-2"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path
                                        d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2">
                                    </path>
                                    <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                                    <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z">
                                    </path>
                                </svg>Print
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Invoice yang akan diprint -->
                        <div class="invoice-print-container mx-auto">
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
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="text-center">
                                        <img src="{{ $logoPath }}" alt="Logo"
                                            style="height: 50px; margin-bottom: 10px;">
                                        <h2 style="margin: 0;">{{ $vendorName }}</h2>
                                        <p style="margin: 2px 0;">{{ $vendorAddress }}</p>
                                        <p style="margin: 2px 0;">{{ $vendorPhone }} | {{ $vendorEmail }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="info d-flex mb-5">
                                <div class="col-6">
                                    <p>No: {{ $transaksi->kode }}</p>
                                    <p>Cust: {{ $transaksi->pelanggan->nama }}</p>
                                    <p>Bayar: {{ ucfirst($transaksi->metode_pembayaran ?? 'Transfer') }}</p>
                                </div>
                                <div class="col-6">
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
                                                                    // Debug info
                                                                    // dd($spec->toArray());

                                                                    $hargaSatuan = 0;
                                                                    $totalHarga = $spec->price ?? 0;
                                                                    $nilai = $spec->nilai_spesifikasi ?? null;

                                                                    // Jika nilai_spesifikasi kosong, coba cek field value
                                                                    if (empty($nilai) && isset($spec->value)) {
                                                                        $nilai = $spec->value;
                                                                    }

                                                                    if ($spec->input_type == 'select' && $spec->bahan) {
                                                                        // Untuk tipe select (pilihan bahan)
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
                                                                        // Untuk tipe numerik (ukuran, dll)
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

                                        <!-- Estimasi Waktu Produksi -->
                                        {{-- @php
                                            $estimatedTime = 0;
                                            if (isset($item->produk) && $item->produk->estimasiProduk) {
                                                $estimatedTime = $item->produk->getEstimatedProductionTime(
                                                    $item->kuantitas,
                                                );
                                            }
                                        @endphp
                                        <div
                                            style="display: flex; justify-content: space-between; margin-bottom: 3px; border-bottom: 1px dashed #ccc; padding-bottom: 3px;">
                                            <span style="color: #666;">Estimasi Waktu Produksi</span>
                                            <span style="font-weight: 500;">{{ $estimatedTime }} menit</span>
                                        </div> --}}

                                        <!-- Alat Produksi -->
                                        {{-- <div
                                            style="display: flex; justify-content: space-between; margin-bottom: 3px; border-bottom: 1px dashed #ccc; padding-bottom: 3px;">
                                            <span style="color: #666;">Alat Produksi</span>
                                            <span style="font-weight: 500;">
                                                @if (isset($item->produk) && $item->produk->estimasiProduk)
                                                    {{ $item->produk->estimasiProduk->pluck('alat.nama_alat')->filter()->implode(', ') ?: 'Tidak ada alat' }}
                                                @else
                                                    Tidak ada alat
                                                @endif
                                            </span>
                                        </div> --}}

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

        /* Styles khusus untuk print */
        @media print {
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
                width: 7.5cm;
                margin: 0;
                padding: 0.5cm;
                border: none;
                box-shadow: none;
            }

            .navbar,
            .header-menu,
            .footer-menu,
            .btn,
            .card-header {
                display: none !important;
            }
        }
    </style>
@endsection
