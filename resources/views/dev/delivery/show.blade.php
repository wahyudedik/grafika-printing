@extends('dev.layouts.app')

@section('title', 'Delivery Confirmation Details')
@section('content')
    <div x-data="{ approveId: null, rejectId: null, rejectNotes: '' }" class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Delivery Confirmation Details</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Konfirmasi #{{ $deliveryConfirmation->confirmation_code }}</p>
            </div>
            <a href="{{ route('admin.delivery.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm font-medium">
                <i class="fas fa-arrow-left"></i>
                <span>Back to List</span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Confirmation Information --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Confirmation Information</h2>
                </div>
                <div class="p-5">
                    <dl class="space-y-3">
                        <div class="flex justify-between"><dt class="text-sm text-gray-500">ID</dt><dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $deliveryConfirmation->id }}</dd></div>
                        <div class="flex justify-between"><dt class="text-sm text-gray-500">Code</dt><dd><code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">{{ $deliveryConfirmation->confirmation_code }}</code></dd></div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Status</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $deliveryConfirmation->status == 'confirmed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' :
                                       ($deliveryConfirmation->status == 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300') }}">
                                    {{ ucfirst($deliveryConfirmation->status) }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between"><dt class="text-sm text-gray-500">Created At</dt><dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $deliveryConfirmation->created_at->format('d M Y H:i:s') }}</dd></div>
                        @if($deliveryConfirmation->confirmed_at)
                            <div class="flex justify-between"><dt class="text-sm text-gray-500">Confirmed At</dt><dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $deliveryConfirmation->confirmed_at->format('d M Y H:i:s') }}</dd></div>
                        @endif
                        @if($deliveryConfirmation->rejected_at)
                            <div class="flex justify-between"><dt class="text-sm text-gray-500">Rejected At</dt><dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $deliveryConfirmation->rejected_at->format('d M Y H:i:s') }}</dd></div>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Vendor Information --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Vendor Information</h2>
                </div>
                <div class="p-5">
                    @if($deliveryConfirmation->vendor)
                        <dl class="space-y-3">
                            <div class="flex justify-between"><dt class="text-sm text-gray-500">Name</dt><dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $deliveryConfirmation->vendor->name }}</dd></div>
                            <div class="flex justify-between"><dt class="text-sm text-gray-500">Email</dt><dd class="text-sm text-gray-900 dark:text-white">{{ $deliveryConfirmation->vendor->email }}</dd></div>
                            <div class="flex justify-between"><dt class="text-sm text-gray-500">Phone</dt><dd class="text-sm text-gray-900 dark:text-white">{{ $deliveryConfirmation->vendor->phone }}</dd></div>
                            <div class="flex justify-between items-start"><dt class="text-sm text-gray-500">Address</dt><dd class="text-sm text-gray-900 dark:text-white text-right max-w-[60%]">{{ $deliveryConfirmation->vendor->address }}</dd></div>
                        </dl>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No vendor information available</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Customer Information --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Customer Information</h2>
                </div>
                <div class="p-5">
                    <dl class="space-y-3">
                        <div class="flex justify-between"><dt class="text-sm text-gray-500">Name</dt><dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $deliveryConfirmation->customer_name ?? 'N/A' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-sm text-gray-500">Phone</dt><dd class="text-sm text-gray-900 dark:text-white">{{ $deliveryConfirmation->customer_phone ?? 'N/A' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-sm text-gray-500">Email</dt><dd class="text-sm text-gray-900 dark:text-white">{{ $deliveryConfirmation->customer_email ?? 'N/A' }}</dd></div>
                        <div class="flex justify-between items-start"><dt class="text-sm text-gray-500">Address</dt><dd class="text-sm text-gray-900 dark:text-white text-right max-w-[60%]">{{ $deliveryConfirmation->delivery_address ?? 'N/A' }}</dd></div>
                    </dl>
                </div>
            </div>

            {{-- Transaction Information --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Transaction Information</h2>
                </div>
                <div class="p-5">
                    @if($deliveryConfirmation->transaction)
                        <dl class="space-y-3">
                            <div class="flex justify-between"><dt class="text-sm text-gray-500">Code</dt><dd><code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">{{ $deliveryConfirmation->transaction->kode_transaksi }}</code></dd></div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Status</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $deliveryConfirmation->transaction->status == 'completed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' :
                                           ($deliveryConfirmation->transaction->status == 'pending' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300') }}">
                                        {{ ucfirst($deliveryConfirmation->transaction->status) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="flex justify-between"><dt class="text-sm text-gray-500">Total</dt><dd class="text-sm font-bold text-gray-900 dark:text-white">Rp {{ number_format($deliveryConfirmation->transaction->total, 0, ',', '.') }}</dd></div>
                            <div class="flex justify-between"><dt class="text-sm text-gray-500">Payment</dt><dd class="text-sm text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $deliveryConfirmation->transaction->payment_method)) }}</dd></div>
                            <div class="flex justify-between"><dt class="text-sm text-gray-500">Created At</dt><dd class="text-sm text-gray-900 dark:text-white">{{ $deliveryConfirmation->transaction->created_at->format('d M Y H:i:s') }}</dd></div>
                        </dl>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No transaction information available</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Shipping Information --}}
        @if($deliveryConfirmation->shippingInvoice)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Shipping Information</h2>
                </div>
                <div class="p-5">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="flex justify-between"><dt class="text-sm text-gray-500">Shipping Code</dt><dd><code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">{{ $deliveryConfirmation->shippingInvoice->kode }}</code></dd></div>
                        <div class="flex justify-between"><dt class="text-sm text-gray-500">Resi</dt><dd class="text-sm text-gray-900 dark:text-white">{{ $deliveryConfirmation->shippingInvoice->resi ?? 'N/A' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-sm text-gray-500">Service</dt><dd class="text-sm text-gray-900 dark:text-white">{{ $deliveryConfirmation->shippingInvoice->service ?? 'N/A' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-sm text-gray-500">Cost</dt><dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $deliveryConfirmation->shippingInvoice->cost ? 'Rp ' . number_format($deliveryConfirmation->shippingInvoice->cost, 0, ',', '.') : 'N/A' }}</dd></div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Status</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $deliveryConfirmation->shippingInvoice->status == 'delivered' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' :
                                       ($deliveryConfirmation->shippingInvoice->status == 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' :
                                       ($deliveryConfirmation->shippingInvoice->status == 'in_transit' ? 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $deliveryConfirmation->shippingInvoice->status)) }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        @endif

        {{-- Admin Notes --}}
        @if($deliveryConfirmation->admin_notes)
            <div class="bg-sky-50 dark:bg-sky-900/10 border border-sky-200 dark:border-sky-800 rounded-xl p-5">
                <h3 class="text-sm font-semibold text-sky-800 dark:text-sky-300 mb-2">
                    <i class="fas fa-info-circle mr-1"></i> Admin Notes
                </h3>
                <p class="text-sm text-sky-700 dark:text-sky-200">{{ $deliveryConfirmation->admin_notes }}</p>
            </div>
        @endif

        {{-- Action Buttons (SweetAlert2 kept per plan) --}}
        @if($deliveryConfirmation->status == 'pending')
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Actions</h2>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-3">
                        <button onclick="approveConfirmation({{ $deliveryConfirmation->id }})" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button onclick="rejectConfirmation({{ $deliveryConfirmation->id }})" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- SweetAlert2 for approve/reject (kept per plan) --}}
    <script>
        function approveConfirmation(id) {
            Swal.fire({
                title: 'Approve Delivery Confirmation',
                text: 'Are you sure you want to approve this delivery confirmation?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Approve',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/delivery/${id}/approve`;
                    form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="PATCH">';
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function rejectConfirmation(id) {
            Swal.fire({
                title: 'Reject Delivery Confirmation',
                input: 'textarea',
                inputLabel: 'Reason for rejection',
                inputPlaceholder: 'Enter reason for rejection...',
                inputValidator: (value) => { if (!value) return 'You need to provide a reason for rejection!'; },
                showCancelButton: true,
                confirmButtonText: 'Reject',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/delivery/${id}/reject`;
                    form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="PATCH"><input type="hidden" name="admin_notes" value="' + result.value.replace(/"/g, '"') + '">';
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endsection
