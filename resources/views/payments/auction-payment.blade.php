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
            const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));

            createPaymentBtn.addEventListener('click', function() {
                const paymentType = document.getElementById('payment_type').value;

                // Show loading
                createPaymentBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
                createPaymentBtn.disabled = true;

                // Create payment
                fetch(`{{ route('xendit.payment.create', $auction->id) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify({
                            payment_type: paymentType
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show payment options
                            let paymentContent = '';

                            if (data.checkout_url) {
                                paymentContent = `
                        <div class="text-center">
                            <h5>Pembayaran Berhasil Dibuat!</h5>
                            <p>Silakan klik tombol di bawah untuk melakukan pembayaran:</p>
                            <a href="${data.checkout_url}" target="_blank" class="btn btn-primary btn-lg">
                                <i class="fas fa-external-link-alt me-2"></i>
                                Bayar Sekarang
                            </a>
                            <div class="mt-3">
                                <small class="text-muted">
                                    Link pembayaran akan berlaku selama 24 jam.
                                </small>
                            </div>
                        </div>
                    `;
                            } else if (data.xenpayment_id) {
                                paymentContent = `
                        <div class="text-center">
                            <h5>Pembayaran XenPayment Dibuat!</h5>
                            <p>ID Pembayaran: <strong>${data.xenpayment_id}</strong></p>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Silakan gunakan ID pembayaran di atas untuk melakukan pembayaran melalui aplikasi e-wallet Anda.
                            </div>
                        </div>
                    `;
                            }

                            document.getElementById('payment_content').innerHTML = paymentContent;
                            paymentModal.show();
                        } else {
                            alert('Error: ' + (data.error || 'Gagal membuat pembayaran'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat membuat pembayaran');
                    })
                    .finally(() => {
                        // Reset button
                        createPaymentBtn.innerHTML =
                            '<i class="fas fa-credit-card me-2"></i>Buat Link Pembayaran';
                        createPaymentBtn.disabled = false;
                    });
            });
        });
    </script>
@endsection
