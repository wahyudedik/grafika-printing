@extends('dev.layouts.app')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Manajemen</p>
                <h1 class="text-2xl font-bold text-gray-900">Payment Management</h1>
            </div>
            <x-ui.button variant="outline-info" onclick="bulkCheckStatus()">
                <i class="fas fa-sync-alt mr-2"></i> Bulk Check Status
            </x-ui.button>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm font-medium text-gray-500">Pending Payments</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['pending_payments'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Total: Rp {{ number_format($stats['total_amount_pending'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm font-medium text-gray-500">Paid Payments</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['paid_payments'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Total: Rp {{ number_format($stats['total_amount_paid'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm font-medium text-gray-500">Failed Payments</p>
            <p class="text-3xl font-bold text-red-600 mt-2">{{ $stats['failed_payments'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm font-medium text-gray-500">Stuck Payments</p>
            <p class="text-3xl font-bold text-amber-600 mt-2">{{ $stats['stuck_payments'] }}</p>
        </div>
    </div>

    {{-- Stuck Payments --}}
    @if ($stuckPayments->count() > 0)
    <div class="bg-white rounded-xl shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Stuck Payments (24+ hours)</h3>
                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-800">{{ $stuckPayments->count() }} payments</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auction ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($stuckPayments as $auction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('admin.auctions.show', $auction) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">#{{ $auction->id }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ $auction->user->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ $auction->winnerVendor->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">Rp {{ number_format($auction->winning_bid, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-500">{{ $auction->created_at->diffForHumans() }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <x-ui.button variant="outline-primary" size="sm" onclick="createNewPaymentLink({{ $auction->id }})">
                                Create New Link
                            </x-ui.button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Expired Payments --}}
    @if ($expiredPayments->count() > 0)
    <div class="bg-white rounded-xl shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Expired Payments</h3>
                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">{{ $expiredPayments->count() }} payments</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expired At</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($expiredPayments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">#{{ $payment->id }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ $payment->auction->user->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-500">{{ $payment->expires_at->diffForHumans() }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <x-ui.button variant="outline-primary" size="sm" onclick="checkPaymentStatus({{ $payment->id }})">
                                Check Status
                            </x-ui.button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Failed Payments --}}
    @if ($failedPayments->count() > 0)
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Failed Payments</h3>
                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">{{ $failedPayments->count() }} payments</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Failed At</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($failedPayments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">#{{ $payment->id }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ $payment->auction->user->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-500">{{ $payment->updated_at->diffForHumans() }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <x-ui.button variant="outline-primary" size="sm" onclick="checkPaymentStatus({{ $payment->id }})">
                                Check Status
                            </x-ui.button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
    <script>
        function checkPaymentStatus(paymentId) {
            fetch(`/admin/payments/${paymentId}/check-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Payment status updated successfully');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error checking payment status: ' + error.message);
                });
        }

        function createNewPaymentLink(auctionId) {
            if (confirm('Are you sure you want to create a new payment link for this auction?')) {
                fetch(`/admin/payments/create-link/${auctionId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('New payment link created successfully');
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Error creating payment link: ' + error.message);
                    });
            }
        }

        function bulkCheckStatus() {
            if (confirm('This will check the status of all pending payments. Continue?')) {
                alert('Bulk check functionality will be implemented');
            }
        }
    </script>
@endpush
