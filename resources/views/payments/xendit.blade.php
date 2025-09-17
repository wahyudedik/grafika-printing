@extends('layouts.app')

@section('title', 'Payment - Xendit')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Payment Details</h1>
                        <p class="text-gray-600 mt-1">{{ $payment->description }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-blue-600">
                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $payment->currency }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Status -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Status</h2>

                <div class="flex items-center space-x-4">
                    @if ($payment->status === 'pending')
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-yellow-400 rounded-full mr-2"></div>
                            <span class="text-yellow-600 font-medium">Pending Payment</span>
                        </div>
                    @elseif($payment->status === 'paid')
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-400 rounded-full mr-2"></div>
                            <span class="text-green-600 font-medium">Payment Successful</span>
                        </div>
                    @elseif($payment->status === 'expired')
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-400 rounded-full mr-2"></div>
                            <span class="text-red-600 font-medium">Payment Expired</span>
                        </div>
                    @else
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-gray-400 rounded-full mr-2"></div>
                            <span class="text-gray-600 font-medium">{{ ucfirst($payment->status) }}</span>
                        </div>
                    @endif

                    @if ($payment->expires_at)
                        <div class="text-sm text-gray-500">
                            Expires: {{ $payment->expires_at->format('d M Y H:i') }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Methods -->
            @if ($payment->status === 'pending')
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Choose Payment Method</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Bank Transfer -->
                        <div class="border rounded-lg p-4 hover:border-blue-500 transition-colors">
                            <h3 class="font-semibold text-gray-900 mb-2">Bank Transfer</h3>
                            <div class="space-y-2 text-sm text-gray-600">
                                <div>BCA, BNI, BRI, BSI</div>
                                <div>Mandiri, Permata</div>
                            </div>
                        </div>

                        <!-- E-Wallet -->
                        <div class="border rounded-lg p-4 hover:border-blue-500 transition-colors">
                            <h3 class="font-semibold text-gray-900 mb-2">E-Wallet</h3>
                            <div class="space-y-2 text-sm text-gray-600">
                                <div>OVO, DANA, LinkAja</div>
                                <div>ShopeePay</div>
                            </div>
                        </div>

                        <!-- Retail Outlet -->
                        <div class="border rounded-lg p-4 hover:border-blue-500 transition-colors">
                            <h3 class="font-semibold text-gray-900 mb-2">Retail Outlet</h3>
                            <div class="space-y-2 text-sm text-gray-600">
                                <div>Alfamart, Indomaret</div>
                                <div>Pay at store</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Payment Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                @if ($payment->status === 'pending')
                    @if ($payment->type === 'payment_link' && $payment->checkout_url)
                        <div class="space-y-4">
                            <a href="{{ $payment->checkout_url }}" target="_blank"
                                class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold text-center block hover:bg-blue-700 transition-colors">
                                Pay Now with Xendit
                            </a>

                            <div class="text-center">
                                <button onclick="checkPaymentStatus()"
                                    class="text-blue-600 hover:text-blue-800 font-medium">
                                    Check Payment Status
                                </button>
                            </div>
                        </div>
                    @elseif($payment->type === 'xenpayment')
                        <div class="space-y-4">
                            <div id="xenpayment-widget" class="min-h-[400px]">
                                <!-- XenPayment widget will be loaded here -->
                            </div>

                            <div class="text-center">
                                <button onclick="checkPaymentStatus()"
                                    class="text-blue-600 hover:text-blue-800 font-medium">
                                    Check Payment Status
                                </button>
                            </div>
                        </div>
                    @endif
                @elseif($payment->status === 'paid')
                    <div class="text-center">
                        <div class="text-green-600 text-lg font-semibold mb-4">
                            ✅ Payment Successful!
                        </div>
                        <p class="text-gray-600 mb-4">
                            Your payment has been processed successfully. You will receive a confirmation email shortly.
                        </p>
                        <a href="{{ route('auctions.show', $payment->external_id) }}"
                            class="bg-green-600 text-white py-2 px-6 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                            View Auction Details
                        </a>
                    </div>
                @elseif($payment->status === 'expired')
                    <div class="text-center">
                        <div class="text-red-600 text-lg font-semibold mb-4">
                            ❌ Payment Expired
                        </div>
                        <p class="text-gray-600 mb-4">
                            This payment link has expired. Please create a new payment to continue.
                        </p>
                        <button onclick="createNewPayment()"
                            class="bg-blue-600 text-white py-2 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                            Create New Payment
                        </button>
                    </div>
                @endif
            </div>

            <!-- Payment Details -->
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">External ID</label>
                        <p class="text-sm text-gray-900">{{ $payment->external_id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Payment Type</label>
                        <p class="text-sm text-gray-900">{{ ucfirst($payment->type) }}</p>
                    </div>

                    @if ($payment->payment_method)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                            <p class="text-sm text-gray-900">{{ $payment->payment_method }}</p>
                        </div>
                    @endif

                    @if ($payment->paid_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Paid At</label>
                            <p class="text-sm text-gray-900">{{ $payment->paid_at->format('d M Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($payment->type === 'xenpayment')
        <script src="https://js.xendit.co/v1/xendit.min.js"></script>
        <script>
            // Initialize XenPayment widget
            const xendit = new Xendit({
                publicKey: '{{ config('services.xendit.public_key') }}'
            });

            const xenPayment = xendit.createXenPayment({
                id: '{{ $payment->xendit_id }}',
                onSuccess: function(result) {
                    console.log('Payment successful:', result);
                    checkPaymentStatus();
                },
                onError: function(error) {
                    console.error('Payment error:', error);
                }
            });

            xenPayment.mount('#xenpayment-widget');
        </script>
    @endif

    <script>
        function checkPaymentStatus() {
            fetch('{{ route('xendit.payment.status', $payment->id) }}', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'paid') {
                        location.reload();
                    } else {
                        alert('Payment is still pending. Please try again in a moment.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to check payment status. Please try again.');
                });
        }

        function createNewPayment() {
            // This would redirect to create a new payment
            window.location.href = '{{ route('auctions.show', $payment->external_id) }}';
        }
    </script>
@endsection
