@extends('dev.layouts.app')

@section('title', 'Shipping Details')
@section('content')
<div class="space-y-6" x-data="{ tracking: null, showTrackModal: false }">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Shipping Details</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Detail pengiriman #{{ $shippingInvoice->kode }}</p>
        </div>
        <x.ui.button type="button" variant="outline" href="{{ route('admin.shipping.index') }}">
            <i class="fas fa-arrow-left mr-1"></i> Back to List
        </x.ui.button>
    </div>

    {{-- Shipping & Vendor Info --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Shipping Information --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Shipping Information</h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">ID</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $shippingInvoice->id }}</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Code</span>
                    <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $shippingInvoice->kode }}</code>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</span>
                    @if($shippingInvoice->shipping_status == 'delivered')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Delivered</span>
                    @elseif($shippingInvoice->shipping_status == 'failed')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">Failed</span>
                    @elseif($shippingInvoice->shipping_status == 'shipped')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400">Shipped</span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">Pending</span>
                    @endif
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Service</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $shippingInvoice->service ?? 'N/A' }}</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Cost</span>
                    @if($shippingInvoice->shipping_cost)
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Rp {{ number_format($shippingInvoice->shipping_cost, 0, ',', '.') }}</span>
                    @else
                        <span class="text-sm text-gray-400">N/A</span>
                    @endif
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Resi</span>
                    @if($shippingInvoice->waybill_number)
                        <div class="flex items-center gap-2">
                            <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $shippingInvoice->waybill_number }}</code>
                            <x.ui.button type="button" variant="outline-info" size="xs" @click="fetch(`/admin/shipping/{{ $shippingInvoice->id }}/track`).then(r => r.json()).then(data => { if(data.success) { tracking = data.data; showTrackModal = true; } else { alert(data.message); } }).catch(() => alert('Failed to track shipment'))">
                                <i class="fas fa-map-marker-alt mr-1"></i> Track
                            </x.ui.button>
                        </div>
                    @else
                        <span class="text-sm text-gray-400">N/A</span>
                    @endif
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $shippingInvoice->created_at->format('d M Y H:i:s') }}</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Updated At</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $shippingInvoice->updated_at->format('d M Y H:i:s') }}</span>
                </div>
            </div>
        </div>

        {{-- Vendor Information --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Vendor Information</h3>
            </div>
            @if($shippingInvoice->vendor)
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $shippingInvoice->vendor->name }}</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $shippingInvoice->vendor->email }}</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $shippingInvoice->vendor->phone }}</span>
                </div>
                <div class="px-6 py-3">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">Address</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $shippingInvoice->vendor->address }}</p>
                </div>
            </div>
            @else
            <div class="p-6 text-center text-gray-500 dark:text-gray-400 text-sm">No vendor information available</div>
            @endif
        </div>
    </div>

    {{-- Transaction Information --}}
    @if($shippingInvoice->transaction)
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Transaction Information</h3>
        </div>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <div class="px-6 py-3 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Transaction Code</span>
                <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $shippingInvoice->transaction->kode_transaksi }}</code>
            </div>
            <div class="px-6 py-3 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</span>
                @if($shippingInvoice->transaction->status == 'completed')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Completed</span>
                @elseif($shippingInvoice->transaction->status == 'pending')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">Pending</span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400">{{ ucfirst($shippingInvoice->transaction->status) }}</span>
                @endif
            </div>
            <div class="px-6 py-3 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Amount</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">Rp {{ number_format($shippingInvoice->transaction->total, 0, ',', '.') }}</span>
            </div>
            <div class="px-6 py-3 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Method</span>
                <span class="text-sm text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $shippingInvoice->transaction->payment_method)) }}</span>
            </div>
            <div class="px-6 py-3 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</span>
                <span class="text-sm text-gray-900 dark:text-white">{{ $shippingInvoice->transaction->created_at->format('d M Y H:i:s') }}</span>
            </div>
        </div>
    </div>
    @endif

    {{-- Delivery Confirmation --}}
    @if($shippingInvoice->deliveryConfirmation)
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delivery Confirmation</h3>
        </div>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <div class="px-6 py-3 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Confirmation Code</span>
                <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $shippingInvoice->deliveryConfirmation->confirmation_code }}</code>
            </div>
            <div class="px-6 py-3 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</span>
                @if($shippingInvoice->deliveryConfirmation->status == 'confirmed')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Confirmed</span>
                @elseif($shippingInvoice->deliveryConfirmation->status == 'rejected')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">Rejected</span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">Pending</span>
                @endif
            </div>
            <div class="px-6 py-3 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Customer Name</span>
                <span class="text-sm text-gray-900 dark:text-white">{{ $shippingInvoice->deliveryConfirmation->customer_name ?? 'N/A' }}</span>
            </div>
            <div class="px-6 py-3 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Customer Phone</span>
                <span class="text-sm text-gray-900 dark:text-white">{{ $shippingInvoice->deliveryConfirmation->customer_phone ?? 'N/A' }}</span>
            </div>
            <div class="px-6 py-3">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">Delivery Address</span>
                <p class="text-sm text-gray-900 dark:text-white">{{ $shippingInvoice->deliveryConfirmation->delivery_address ?? 'N/A' }}</p>
            </div>
            @if($shippingInvoice->deliveryConfirmation->admin_notes)
            <div class="px-6 py-3">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">Admin Notes</span>
                <p class="text-sm text-gray-900 dark:text-white">{{ $shippingInvoice->deliveryConfirmation->admin_notes }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Update Status Form --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Update Status</h3>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('admin.shipping.update-status', $shippingInvoice->id) }}">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                            <option value="pending" {{ $shippingInvoice->shipping_status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $shippingInvoice->shipping_status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $shippingInvoice->shipping_status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $shippingInvoice->shipping_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="failed" {{ $shippingInvoice->shipping_status == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                        <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" rows="2" placeholder="Enter notes about the status update..."></textarea>
                    </div>
                </div>
                <div class="mt-4">
                    <x.ui.button type="submit" variant="primary" size="sm">
                        <i class="fas fa-save mr-1"></i> Update Status
                    </x.ui.button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tracking Modal --}}
    <div x-show="showTrackModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="fixed inset-0 bg-black/50" @click="showTrackModal = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-2xl w-full max-h-[80vh] overflow-y-auto p-6" @click.away="showTrackModal = false">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tracking Result</h3>
                <button @click="showTrackModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                <pre class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap overflow-x-auto" x-text="JSON.stringify(tracking, null, 2)"></pre>
            </div>
        </div>
    </div>
</div>
@endsection
