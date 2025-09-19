@extends('layouts.user')

@section('title', 'Pembayaran Lelang')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pembayaran Lelang</h3>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Detail Lelang</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5>{{ $auction->title }}</h5>
                                                <p class="text-muted">{{ $auction->description }}</p>

                                                <div class="mb-3">
                                                    <strong>Kategori:</strong> {{ $auction->category }}<br>
                                                    <strong>Jumlah:</strong> {{ $auction->quantity }} pcs<br>
                                                    <strong>Budget:</strong> Rp
                                                    {{ number_format($auction->budget, 0, ',', '.') }}<br>
                                                    <strong>Deadline:</strong>
                                                    {{ \Carbon\Carbon::parse($auction->deadline)->format('d M Y') }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="alert alert-info">
                                                    <h6><strong>Pemenang Lelang</strong></h6>
                                                    <p class="mb-1"><strong>Vendor:</strong>
                                                        {{ $auction->winnerVendor->name ?? 'N/A' }}</p>
                                                    <p class="mb-1"><strong>Harga Penawaran:</strong> Rp
                                                        {{ number_format($auction->winning_bid, 0, ',', '.') }}</p>
                                                    <p class="mb-0"><strong>Status:</strong> <span
                                                            class="badge bg-warning">Menunggu Pembayaran</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Pembayaran</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Jumlah yang harus dibayar:</label>
                                            <h3 class="text-primary">Rp
                                                {{ number_format($auction->winning_bid, 0, ',', '.') }}</h3>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Metode Pembayaran:</label>
                                            <select class="form-select" id="payment_type">
                                                <option value="payment_link">Payment Link (Bank Transfer)</option>
                                                <option value="xenpayment">XenPayment (E-Wallet)</option>
                                            </select>
                                        </div>

                                        <button type="button" class="btn btn-primary w-100" id="create_payment_btn">
                                            <i class="fas fa-credit-card me-2"></i>
                                            Buat Link Pembayaran
                                        </button>

                                        <div class="mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Pembayaran akan otomatis diverifikasi setelah transfer berhasil.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="payment_content">
                        <!-- Payment content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const createPaymentBtn = document.getElementById('create_payment_btn');
            const paymentTypeSelect = document.getElementById('payment_type');
            const paymentModal = document.getElementById('paymentModal');
            const paymentContent = document.getElementById('payment_content');

            createPaymentBtn.addEventListener('click', function() {
                const paymentType = paymentTypeSelect.value;

                if (!paymentType) {
                    alert('Pilih metode pembayaran terlebih dahulu');
                    return;
                }

                // Show loading state
                createPaymentBtn.disabled = true;
                createPaymentBtn.innerHTML =
                    '<i class="fas fa-spinner fa-spin me-2"></i>Membuat Pembayaran...';

                // Create payment
                console.log('Creating payment with type:', paymentType);
                console.log('Auction ID:', {{ $auction->id }});
                console.log('Payment route:', '{{ route('xendit.payment.create', $auction->id) }}');
                console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]').getAttribute(
                    'content'));

                fetch(`{{ route('xendit.payment.create', $auction->id) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify({
                            payment_type: paymentType,
                            customer: {
                                given_names: '{{ auth()->user()->name }}',
                                email: '{{ auth()->user()->email }}'
                            }
                        })
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response headers:', response.headers);

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        return response.json();
                    })
                    .then(data => {
                        console.log('Payment response:', data);

                        if (data.success) {
                            if (data.checkout_url) {
                                console.log('Redirecting to:', data.checkout_url);
                                // Redirect to payment page
                                window.location.href = data.checkout_url;
                            } else if (data.xenpayment_id) {
                                // Show XenPayment widget
                                showXenPaymentWidget(data.xenpayment_id);
                            } else {
                                console.error('No checkout_url or xenpayment_id in response');
                                alert('Error: Tidak ada URL pembayaran yang dihasilkan');
                            }
                        } else {
                            console.error('Payment creation failed:', data);
                            alert('Error: ' + (data.error || 'Gagal membuat pembayaran'));
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        console.error('Error details:', {
                            name: error.name,
                            message: error.message,
                            stack: error.stack
                        });
                        alert('Terjadi kesalahan saat membuat pembayaran: ' + error.message);
                    })
                    .finally(() => {
                        createPaymentBtn.disabled = false;
                        createPaymentBtn.innerHTML =
                            '<i class="fas fa-credit-card me-2"></i>Buat Link Pembayaran';
                    });
            });

            function showXenPaymentWidget(xenPaymentId) {
                paymentContent.innerHTML = `
                    <div class="text-center">
                        <h5>XenPayment Widget</h5>
                        <div id="xenpayment-widget" class="min-h-[400px] border rounded p-4">
                            <p class="text-muted">Loading payment widget...</p>
                        </div>
                    </div>
                `;

                paymentModal.show();

                // Load XenPayment widget
                if (typeof Xendit !== 'undefined') {
                    const xendit = new Xendit({
                        publicKey: '{{ config('services.xendit.public_key') }}'
                    });

                    const xenPayment = xendit.createXenPayment({
                        id: xenPaymentId,
                        onSuccess: function(result) {
                            console.log('Payment successful:', result);
                            paymentModal.hide();
                            location.reload();
                        },
                        onError: function(error) {
                            console.error('Payment error:', error);
                            alert('Terjadi kesalahan pada pembayaran');
                        }
                    });

                    xenPayment.mount('#xenpayment-widget');
                } else {
                    paymentContent.innerHTML = `
                        <div class="text-center">
                            <h5>XenPayment Widget</h5>
                            <p class="text-muted">Widget tidak dapat dimuat. Silakan coba lagi.</p>
                        </div>
                    `;
                }
            }
        });
    </script>

    <!-- Load Xendit SDK for XenPayment -->
    <script src="https://js.xendit.co/v1/xendit.min.js"></script>
@endsection
