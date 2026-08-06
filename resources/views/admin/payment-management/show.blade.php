@extends('dev.layouts.app')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Payment Management</p>
                <h1 class="text-2xl font-bold text-gray-900">Payment Details</h1>
            </div>
            <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Payment Information</h3>
                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $payment->status === 'paid' ? 'bg-green-100 text-green-800' : ($payment->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                    {{ ucfirst($payment->status) }}
                </span>
            </div>
        </div>
        <div class="p-6 space-y-6">
            {{-- Payment Info --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Payment ID</label>
                    <p class="text-sm text-gray-900 font-medium">#{{ $payment->id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">External ID</label>
                    <p class="text-sm text-gray-900 font-mono">{{ $payment->external_id }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Amount</label>
                    <p class="text-sm text-gray-900 font-bold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $payment->status === 'paid' ? 'bg-green-100 text-green-800' : ($payment->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Created At</label>
                    <p class="text-sm text-gray-900">{{ $payment->created_at->format('d M Y H:i:s') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Expires At</label>
                    <p class="text-sm text-gray-900">
                        {{ $payment->expires_at ? $payment->expires_at->format('d M Y H:i:s') : 'N/A' }}
                        @if ($payment->expires_at && $payment->expires_at < now())
                            <span class="text-red-600 font-medium">(Expired)</span>
                        @endif
                    </p>
                </div>
            </div>

            @if ($payment->checkout_url)
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Payment URL</label>
                <a href="{{ $payment->checkout_url }}" target="_blank" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-300 rounded-lg hover:bg-blue-100">
                    <i class="fas fa-external-link-alt mr-1"></i> Open Payment Link
                </a>
            </div>
            @endif

            {{-- Auction Info --}}
            @if ($payment->auction)
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-base font-semibold text-gray-900 mb-4">Auction Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Auction ID</label>
                        <a href="{{ route('admin.auctions.show', $payment->auction) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">#{{ $payment->auction->id }}</a>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Auction Title</label>
                        <p class="text-sm text-gray-900">{{ $payment->auction->title }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">User</label>
                        <p class="text-sm text-gray-900">{{ $payment->auction->user->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Winning Vendor</label>
                        <p class="text-sm text-gray-900">{{ $payment->auction->winnerVendor->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Action Buttons --}}
            <div class="border-t border-gray-200 pt-6">
                <div class="flex items-center gap-3">
                    @if ($payment->status === 'pending')
                    <button type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700" onclick="checkPaymentStatus({{ $payment->id }})">
                        <i class="fas fa-sync-alt mr-2"></i> Check Status
                    </button>
                    @endif
                    <button type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-300 rounded-lg hover:bg-blue-100" onclick="resendNotification({{ $payment->id }})">
                        <i class="fas fa-paper-plane mr-2"></i> Resend Notification
                    </button>
                </div>
            </div>
        </div>
    </div>
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

        function resendNotification(paymentId) {
            fetch(`/admin/payments/${paymentId}/resend-notification`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Notification sent successfully');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error sending notification: ' + error.message);
                });
        }
    </script>
@endpush
