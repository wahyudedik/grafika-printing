@extends('dev.layouts.app')

@section('title', 'Delivery Confirmations')
@section('content')
    <div x-data="{ approveId: null, rejectId: null, rejectNotes: '' }" class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Delivery Confirmations</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola konfirmasi pengiriman barang</p>
            </div>
            <x.ui.button href="{{ route('admin.delivery.export', request()->query()) }}" variant="primary" size="sm">
                <i class="fas fa-download"></i>
                <span>Export CSV</span>
            </x.ui.button>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                        <i class="fas fa-check-double text-primary-600 dark:text-primary-400"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_confirmations']) }}</div>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Pending</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($stats['pending_confirmations']) }}</div>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Confirmed</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($stats['confirmed']) }}</div>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Rejected</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($stats['rejected']) }}</div>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center">
                        <i class="fas fa-clock text-sky-600 dark:text-sky-400"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Today</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($stats['today_confirmations']) }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
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
                        <input type="text" name="search" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Search by code, transaction..." value="{{ request('search') }}">
                    </div>
                    <div class="flex items-end gap-2">
                        <x.ui.button type="submit" variant="primary" size="sm">Filter</x.ui.button>
                        <x.ui.button href="{{ route('admin.delivery.index') }}" variant="outline" size="sm">Clear</x.ui.button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Delivery Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Code</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vendor</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Transaction</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Address</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($deliveryConfirmations as $confirmation)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $confirmation->id }}</td>
                                <td class="px-4 py-3">
                                    <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded text-gray-800 dark:text-gray-200">{{ $confirmation->confirmation_code }}</code>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                                            <span class="text-sm font-medium text-primary-700 dark:text-primary-300">{{ substr($confirmation->vendor->name ?? 'V', 0, 1) }}</span>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $confirmation->vendor->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded text-gray-800 dark:text-gray-200">{{ $confirmation->transaction->kode_transaksi ?? 'N/A' }}</code>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $confirmation->customer_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $confirmation->customer_phone ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $confirmation->status == 'confirmed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' :
                                           ($confirmation->status == 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300') }}">
                                        {{ ucfirst($confirmation->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-600 dark:text-gray-300 max-w-[200px] truncate" title="{{ $confirmation->delivery_address }}">
                                        {{ $confirmation->delivery_address ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900 dark:text-white">{{ $confirmation->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $confirmation->created_at->format('H:i:s') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1">
                                        <x.ui.button href="{{ route('admin.delivery.show', $confirmation->id) }}" variant="outline-primary" size="xs">View</x.ui.button>
                                        @if($confirmation->status == 'pending')
                                            <x.ui.button type="button" variant="outline-success" size="xs" @click="approveId = {{ $confirmation->id }}">Approve</x.ui.button>
                                            <x.ui.button type="button" variant="outline-danger" size="xs" @click="rejectId = {{ $confirmation->id }}; rejectNotes = ''">Reject</x.ui.button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                                            <i class="fas fa-check-double text-gray-400 text-lg"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">No delivery confirmations found</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Try adjusting your filters or search criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($deliveryConfirmations as $confirmation)
                    <div class="p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-medium text-primary-700 dark:text-primary-300">{{ substr($confirmation->vendor->name ?? 'V', 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $confirmation->vendor->name ?? 'N/A' }}</div>
                                    <code class="text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded">{{ $confirmation->confirmation_code }}</code>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $confirmation->status == 'confirmed' ? 'bg-emerald-100 text-emerald-800' :
                                   ($confirmation->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ ucfirst($confirmation->status) }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-gray-500">Customer:</span>
                                <span class="ml-1 text-gray-900 dark:text-white">{{ $confirmation->customer_name ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Date:</span>
                                <span class="ml-1 text-gray-900 dark:text-white">{{ $confirmation->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <x.ui.button href="{{ route('admin.delivery.show', $confirmation->id) }}" variant="outline-primary" size="xs" class="flex-1 text-center">View</x.ui.button>
                            @if($confirmation->status == 'pending')
                                <x.ui.button type="button" variant="outline-success" size="xs" class="flex-1" @click="approveId = {{ $confirmation->id }}">Approve</x.ui.button>
                                <x.ui.button type="button" variant="outline-danger" size="xs" class="flex-1" @click="rejectId = {{ $confirmation->id }}; rejectNotes = ''">Reject</x.ui.button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">No delivery confirmations found</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Try adjusting your filters or search criteria.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $deliveryConfirmations->links() }}
            </div>
        </div>

        {{-- Approve Confirm Modal (Alpine.js) --}}
        <div x-show="approveId" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="approveId" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="approveId = null"></div>
                <div x-show="approveId" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
                    <div class="p-6">
                        <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check text-emerald-600 dark:text-emerald-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Approve Delivery Confirmation</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Are you sure you want to approve this delivery confirmation?</p>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-750 rounded-b-xl flex justify-end gap-3">
                        <x.ui.button type="button" variant="outline" size="sm" @click="approveId = null">Cancel</x.ui.button>
                        <form x-show="approveId" :action="`/admin/delivery/${approveId}/approve`" method="POST">
                            @csrf
                            @method('PATCH')
                            <x.ui.button type="submit" variant="success" size="sm">Yes, Approve</x.ui.button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Reject Confirm Modal (Alpine.js) --}}
        <div x-show="rejectId" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="rejectId" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="rejectId = null"></div>
                <div x-show="rejectId" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
                    <div class="p-6">
                        <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-times text-red-600 dark:text-red-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Reject Delivery Confirmation</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Provide a reason for rejection:</p>
                        <textarea x-model="rejectNotes" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter reason for rejection..."></textarea>
                        <p x-show="!rejectNotes" class="text-xs text-red-500 mt-1">Reason is required</p>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-750 rounded-b-xl flex justify-end gap-3">
                        <x.ui.button type="button" variant="outline" size="sm" @click="rejectId = null">Cancel</x.ui.button>
                        <form x-show="rejectId && rejectNotes" :action="`/admin/delivery/${rejectId}/reject`" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="admin_notes" :value="rejectNotes">
                            <x.ui.button type="submit" variant="danger" size="sm">Reject</x.ui.button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
