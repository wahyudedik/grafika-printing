@extends('dev.layouts.app')

@section('title', 'Shipping Tracking')
@section('content')
<div class="space-y-6" x-data="{ tracking: null, showTrackModal: false }">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Shipping Tracking</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitoring pengiriman semua vendor</p>
        </div>
        <a href="{{ route('admin.shipping.export', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-colors text-sm font-medium">
            <i class="fas fa-download"></i>
            Export CSV
        </a>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        {{-- Total --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-truck text-primary-600 dark:text-primary-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_shipments']) }}</p>
                </div>
            </div>
        </div>

        {{-- Pending --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600 dark:text-amber-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Pending</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($stats['pending_shipments']) }}</p>
                </div>
            </div>
        </div>

        {{-- In Transit --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-sky-100 dark:bg-sky-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shipping-fast text-sky-600 dark:text-sky-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">In Transit</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($stats['in_transit']) }}</p>
                </div>
            </div>
        </div>

        {{-- Delivered --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Delivered</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($stats['delivered']) }}</p>
                </div>
            </div>
        </div>

        {{-- Failed --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Failed</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($stats['failed']) }}</p>
                </div>
            </div>
        </div>

        {{-- Today --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-sky-100 dark:bg-sky-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-day text-sky-600 dark:text-sky-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Today</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($stats['today_shipments']) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vendor</label>
                    <select name="vendor_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Vendors</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date From</label>
                    <input type="date" name="date_from" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" value="{{ request('date_from') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date To</label>
                    <input type="date" name="date_to" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <input type="text" name="search" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Search by code, resi, transaction..." value="{{ request('search') }}">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors text-sm font-medium">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.shipping.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm font-medium">
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Shipping Table (Desktop) --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hidden md:block">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                        <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400">ID</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Code</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Vendor</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Transaction</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Resi</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Status</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Service</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Cost</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Date</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($shippingInvoices as $shipping)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $shipping->id }}</td>
                        <td class="px-4 py-3">
                            <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $shipping->kode }}</code>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                    <span class="text-xs font-semibold text-primary-600 dark:text-primary-400">{{ substr($shipping->vendor->name ?? 'V', 0, 1) }}</span>
                                </div>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $shipping->vendor->name ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $shipping->transaction->kode_transaksi ?? 'N/A' }}</code>
                        </td>
                        <td class="px-4 py-3">
                            @if($shipping->resi)
                                <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $shipping->resi }}</code>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($shipping->status == 'delivered')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Delivered</span>
                            @elseif($shipping->status == 'failed')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">Failed</span>
                            @elseif($shipping->status == 'in_transit')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400">In Transit</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $shipping->service ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            @if($shipping->cost)
                                <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($shipping->cost, 0, ',', '.') }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-gray-900 dark:text-white">{{ $shipping->created_at->format('d M Y') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $shipping->created_at->format('H:i:s') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.shipping.show', $shipping->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 border border-primary-300 dark:border-primary-700 text-primary-600 dark:text-primary-400 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors text-xs font-medium">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if($shipping->resi)
                                    <button @click="fetch(`/admin/shipping/{{ $shipping->id }}/track`).then(r => r.json()).then(data => { if(data.success) { tracking = data.data; showTrackModal = true; } else { alert(data.message); } }).catch(() => alert('Failed to track shipment'))" class="inline-flex items-center gap-1 px-3 py-1.5 border border-sky-300 dark:border-sky-700 text-sky-600 dark:text-sky-400 rounded-lg hover:bg-sky-50 dark:hover:bg-sky-900/20 transition-colors text-xs font-medium">
                                        <i class="fas fa-map-marker-alt"></i> Track
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-truck text-4xl text-gray-400 dark:text-gray-500 mb-3"></i>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">No shipping data found</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Try adjusting your filters or search criteria.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Shipping Cards (Mobile) --}}
    <div class="md:hidden space-y-3">
        @forelse($shippingInvoices as $shipping)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $shipping->kode }}</code>
                    @if($shipping->status == 'delivered')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 ml-2">Delivered</span>
                    @elseif($shipping->status == 'failed')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 ml-2">Failed</span>
                    @elseif($shipping->status == 'in_transit')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 ml-2">In Transit</span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 ml-2">Pending</span>
                    @endif
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $shipping->created_at->format('d M Y') }}</span>
            </div>
            <div class="space-y-1 text-sm">
                <p class="text-gray-900 dark:text-white font-medium">{{ $shipping->vendor->name ?? 'N/A' }}</p>
                <p class="text-gray-500 dark:text-gray-400">Resi: <code class="text-xs bg-gray-100 dark:bg-gray-700 px-1 rounded">{{ $shipping->resi ?? '-' }}</code></p>
                <p class="text-gray-500 dark:text-gray-400">Cost: <span class="font-medium text-gray-900 dark:text-white">{{ $shipping->cost ? 'Rp ' . number_format($shipping->cost, 0, ',', '.') : '-' }}</span></p>
            </div>
            <div class="flex gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.shipping.show', $shipping->id) }}" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-1.5 border border-primary-300 dark:border-primary-700 text-primary-600 dark:text-primary-400 rounded-lg text-xs font-medium">
                    <i class="fas fa-eye"></i> View
                </a>
                @if($shipping->resi)
                    <button @click="fetch(`/admin/shipping/{{ $shipping->id }}/track`).then(r => r.json()).then(data => { if(data.success) { tracking = data.data; showTrackModal = true; } else { alert(data.message); } }).catch(() => alert('Failed to track shipment'))" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-1.5 border border-sky-300 dark:border-sky-700 text-sky-600 dark:text-sky-400 rounded-lg text-xs font-medium">
                        <i class="fas fa-map-marker-alt"></i> Track
                    </button>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center">
            <i class="fas fa-truck text-4xl text-gray-400 dark:text-gray-500 mb-3"></i>
            <p class="text-gray-500 dark:text-gray-400 font-medium">No shipping data found</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="flex justify-center">
        {{ $shippingInvoices->links() }}
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
