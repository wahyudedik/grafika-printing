@extends('dev.layouts.app')

@section('title', 'Invoice Pengiriman')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Invoice Pengiriman</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Daftar invoice pengiriman vendor</p>
        </div>
        <x.ui.button type="button" variant="outline" href="{{ route('admin.shipping.index') }}">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Shipping
        </x.ui.button>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        <form method="GET" action="{{ route('admin.shipping.invoices') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Pembayaran</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vendor</label>
                    <select name="vendor_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Semua Vendor</option>
                        @if(isset($vendors))
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div>
                    <x.ui.button type="submit" variant="primary" size="sm" class="w-full">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </x.ui.button>
                </div>
                <div>
                    <x.ui.button type="button" variant="outline-success" size="sm" class="w-full" href="{{ route('admin.shipping.export', request()->query()) }}">
                        <i class="fas fa-download mr-1"></i> Export
                    </x.ui.button>
                </div>
            </div>
        </form>
    </div>

    {{-- Table (Desktop) --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hidden md:block">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Invoice Pengiriman</h3>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $shippingInvoices->total() }} invoice</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                        <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Kode</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Transaksi</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Vendor</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Kurir</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400">No. Resi</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Ongkir</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Status Bayar</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Status Kirim</th>
                        <th class="w-12"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($shippingInvoices as $invoice)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400">{{ $invoice->kode }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($invoice->transaction)
                                <a href="{{ route('admin.shipping.show', $invoice->id) }}" class="text-primary-600 dark:text-primary-400 hover:underline text-sm">{{ $invoice->transaction->kode_transaksi ?? 'N/A' }}</a>
                            @else
                                <span class="text-gray-400 text-sm">N/A</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white text-sm">{{ $invoice->vendor->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-gray-500 dark:text-gray-400 text-sm">{{ $invoice->courier ?? '-' }}</span>
                            @if($invoice->service)
                                <br><span class="text-xs text-gray-400 dark:text-gray-500">{{ $invoice->service }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($invoice->waybill_number)
                                <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $invoice->waybill_number }}</code>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white text-sm">{{ $invoice->formatted_shipping_cost ?? ('Rp ' . number_format($invoice->shipping_cost ?? 0, 0, ',', '.')) }}</td>
                        <td class="px-4 py-3">
                            @php
                                $paymentBadges = [
                                    'pending' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
                                    'paid' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
                                    'cancelled' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                                ];
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $paymentBadges[$invoice->payment_status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">{{ ucfirst($invoice->payment_status ?? 'unknown') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $shippingBadges = [
                                    'pending' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                                    'processing' => 'bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400',
                                    'shipped' => 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400',
                                    'delivered' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
                                    'failed' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                                ];
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $shippingBadges[$invoice->shipping_status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">{{ ucfirst(str_replace('_', ' ', $invoice->shipping_status ?? 'unknown')) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <x.ui.button type="button" variant="outline-primary" size="icon-sm" href="{{ route('admin.shipping.show', $invoice->id) }}">
                                <i class="fas fa-eye text-xs"></i>
                            </x.ui.button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <x-ui.empty-state icon="fas fa-box-open" title="Tidak ada invoice pengiriman" description="Belum ada invoice pengiriman yang tersedia." />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($shippingInvoices->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-center">
            {{ $shippingInvoices->withQueryString()->links() }}
        </div>
        @endif
    </div>

    {{-- Cards (Mobile) --}}
    <div class="md:hidden space-y-3">
        @forelse($shippingInvoices as $invoice)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-start justify-between mb-3">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400">{{ $invoice->kode }}</span>
                @php
                    $shippingBadges = [
                        'pending' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                        'processing' => 'bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400',
                        'shipped' => 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400',
                        'delivered' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
                        'failed' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                    ];
                @endphp
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $shippingBadges[$invoice->shipping_status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">{{ ucfirst(str_replace('_', ' ', $invoice->shipping_status ?? 'unknown')) }}</span>
            </div>
            <div class="space-y-1 text-sm">
                <p class="text-gray-900 dark:text-white font-medium">{{ $invoice->vendor->name ?? 'N/A' }}</p>
                <p class="text-gray-500 dark:text-gray-400">Kurir: {{ $invoice->courier ?? '-' }} {{ $invoice->service ? '(' . $invoice->service . ')' : '' }}</p>
                <p class="text-gray-500 dark:text-gray-400">Ongkir: <span class="font-medium text-gray-900 dark:text-white">{{ $invoice->formatted_shipping_cost ?? ('Rp ' . number_format($invoice->shipping_cost ?? 0, 0, ',', '.')) }}</span></p>
            </div>
            <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                @php
                    $paymentBadges = [
                        'pending' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
                        'paid' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
                        'cancelled' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                    ];
                @endphp
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $paymentBadges[$invoice->payment_status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">{{ ucfirst($invoice->payment_status ?? 'unknown') }}</span>
                <x.ui.button type="button" variant="outline-primary" size="xs" class="ml-auto" href="{{ route('admin.shipping.show', $invoice->id) }}">
                    <i class="fas fa-eye mr-1"></i> View
                </x.ui.button>
            </div>
        </div>
        @empty
        <x-ui.empty-state icon="fas fa-box-open" title="Tidak ada invoice pengiriman" description="Belum ada invoice pengiriman yang tersedia." />
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="flex justify-center">
        {{ $shippingInvoices->withQueryString()->links() }}
    </div>
</div>
@endsection
