@extends('layouts.user')

@section('title', 'Invoice #' . $transaksi->kode)

@section('content')
    {{-- Print Button — Hidden when printing --}}
    <div class="flex items-center justify-between mb-6 print:hidden">
        <nav class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('user.dashboard') }}" class="hover:text-primary-600 transition-colors">Beranda</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('user.transactions.index') }}" class="hover:text-primary-600 transition-colors">Riwayat Pesanan</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('user.transactions.show', $transaksi->id) }}" class="hover:text-primary-600 transition-colors">Detail</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 font-medium">Invoice</span>
        </nav>
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak Invoice
        </button>
    </div>

    {{-- Invoice Paper --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm max-w-3xl mx-auto">
        <div class="p-8 md:p-12">

            {{-- Invoice Header --}}
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6 mb-8 pb-6 border-b-2 border-gray-200">
                <div class="flex items-center gap-4">
                    @if($transaksi->vendor && $transaksi->vendor->logo)
                        <img src="{{ asset('vendors_logo/' . $transaksi->vendor->logo) }}" alt="{{ $transaksi->vendor->name }}" class="w-16 h-16 rounded-lg object-cover border border-gray-200">
                    @else
                        <div class="w-16 h-16 rounded-lg bg-primary-100 flex items-center justify-center">
                            <span class="text-2xl font-bold text-primary-700">{{ strtoupper(substr($transaksi->vendor->name ?? 'V', 0, 2)) }}</span>
                        </div>
                    @endif
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $transaksi->vendor->name ?? config('app.name') }}</h2>
                        @if($transaksi->vendor && $transaksi->vendor->address)
                            <p class="text-sm text-gray-500 mt-0.5">{{ $transaksi->vendor->address }}</p>
                        @endif
                        @if($transaksi->vendor && $transaksi->vendor->phone)
                            <p class="text-sm text-gray-500">{{ $transaksi->vendor->phone }}</p>
                        @endif
                    </div>
                </div>
                <div class="text-right md:text-right">
                    <h1 class="text-2xl font-bold text-primary-600 tracking-wide">INVOICE</h1>
                    <p class="text-sm text-gray-500 mt-1">No. {{ $transaksi->kode }}</p>
                    <p class="text-sm text-gray-500">Tanggal: {{ $transaksi->created_at->format('d M Y') }}</p>
                    @php
                        $statusLabel = match($transaksi->status) {
                            'pending' => ['label' => 'Menunggu', 'class' => 'bg-yellow-100 text-yellow-800'],
                            'processing' => ['label' => 'Diproses', 'class' => 'bg-blue-100 text-blue-800'],
                            'quality_check' => ['label' => 'Quality Check', 'class' => 'bg-purple-100 text-purple-800'],
                            'completed' => ['label' => 'Selesai', 'class' => 'bg-green-100 text-green-800'],
                            'cancelled' => ['label' => 'Dibatalkan', 'class' => 'bg-red-100 text-red-800'],
                            default => ['label' => ucfirst($transaksi->status), 'class' => 'bg-gray-100 text-gray-800'],
                        };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusLabel['class'] }} mt-2">
                        {{ $statusLabel['label'] }}
                    </span>
                </div>
            </div>

            {{-- Customer Info --}}
            <div class="mb-8">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Ditujukan Kepada</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="font-medium text-gray-900">{{ $transaksi->pelanggan->nama ?? '-' }}</p>
                    @if($transaksi->pelanggan && $transaksi->pelanggan->telepon)
                        <p class="text-sm text-gray-600 mt-0.5">{{ $transaksi->pelanggan->telepon }}</p>
                    @endif
                    @if($transaksi->pelanggan && $transaksi->pelanggan->alamat)
                        <p class="text-sm text-gray-600">{{ $transaksi->pelanggan->alamat }}</p>
                    @endif
                    @if($transaksi->customer_email)
                        <p class="text-sm text-gray-600">{{ $transaksi->customer_email }}</p>
                    @endif
                </div>
            </div>

            {{-- Items Table --}}
            <div class="mb-8">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="text-left py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="text-center py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-20">Qty</th>
                            <th class="text-right py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">Harga</th>
                            <th class="text-right py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-36">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($transaksi->transaksiItem as $item)
                            <tr>
                                <td class="py-3">
                                    <div class="font-medium text-gray-900">{{ $item->produk->nama ?? 'Produk' }}</div>
                                    @if($item->transaksiItemSpecifications && $item->transaksiItemSpecifications->count())
                                        <div class="mt-1 space-y-0.5">
                                            @foreach($item->transaksiItemSpecifications as $spec)
                                                <div class="text-xs text-gray-500">
                                                    {{ $spec->bahan->nama ?? $spec->nama ?? '' }}: {{ $spec->nilai ?? $spec->value ?? '' }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 text-center text-gray-700">{{ $item->kuantitas }}</td>
                                <td class="py-3 text-right text-gray-700">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                <td class="py-3 text-right font-medium text-gray-900">Rp {{ number_format($item->kuantitas * $item->harga_satuan, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Payment Summary --}}
            <div class="flex justify-end mb-8">
                <div class="w-full max-w-sm">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="text-gray-900">Rp {{ number_format($transaksi->total_harga - ($transaksi->ongkir ?? 0) - ($transaksi->admin_fee ?? 0), 0, ',', '.') }}</span>
                        </div>
                        @if($transaksi->ongkir > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Ongkos Kirim</span>
                                <span class="text-gray-900">Rp {{ number_format($transaksi->ongkir, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($transaksi->admin_fee > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Admin Fee</span>
                                <span class="text-gray-900">Rp {{ number_format($transaksi->admin_fee, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="border-t-2 border-gray-200 pt-2 mt-2">
                            <div class="flex justify-between">
                                <span class="text-base font-bold text-gray-900">Grand Total</span>
                                <span class="text-lg font-bold text-primary-600">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Info --}}
            <div class="bg-gray-50 rounded-lg p-4 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Metode Pembayaran</span>
                        <p class="font-medium text-gray-900 mt-0.5">{{ $transaksi->payment_method ? ucfirst(str_replace('_', ' ', $transaksi->payment_method)) : '-' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Status Pembayaran</span>
                        <p class="font-medium text-gray-900 mt-0.5">{{ $transaksi->payment_status ? ucfirst($transaksi->payment_status) : '-' }}</p>
                    </div>
                    @if($transaksi->paid_at)
                        <div>
                            <span class="text-gray-500">Tanggal Bayar</span>
                            <p class="font-medium text-gray-900 mt-0.5">{{ $transaksi->paid_at->format('d M Y, H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Footer --}}
            <div class="text-center text-xs text-gray-400 pt-6 border-t border-gray-200">
                <p>Invoice ini dicetak secara otomatis oleh {{ config('app.name') }}</p>
                <p class="mt-1">Dikeluarkan pada {{ $transaksi->created_at->format('d M Y, H:i') }} WIB</p>
            </div>
        </div>
    </div>

    {{-- Print Styles --}}
    <style>
        @media print {
            body {
                background: white !important;
            }
            .print\:hidden,
            nav, footer, header {
                display: none !important;
            }
            .bg-white.rounded-xl.border {
                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }
            .max-w-3xl {
                max-width: 100% !important;
            }
            .p-8, .md\:p-12 {
                padding: 1rem !important;
            }
        }
    </style>
@endsection
