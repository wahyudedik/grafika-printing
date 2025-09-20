@extends('dev.layouts.app')

@section('title', 'Preview Biaya Admin')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Pengaturan
                    </div>
                    <h2 class="page-title">
                        Preview Biaya Admin
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.admin-fees.index') }}" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 6l6 6l-6 6" />
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Kalkulator Biaya Admin</h3>
                            <div class="card-actions">
                                <button type="button" class="btn btn-primary" onclick="calculateFees()">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                        <path d="M12 7v5l3 3" />
                                    </svg>
                                    Hitung Biaya
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="previewForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label required">Jumlah Lelang</label>
                                            <input type="number" class="form-control" id="auction_amount" name="amount"
                                                placeholder="Masukkan jumlah lelang" min="1" step="0.01" required>
                                            <div class="form-hint">Masukkan jumlah lelang dalam Rupiah</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Metode Pembayaran</label>
                                            <select class="form-select" id="payment_method" name="payment_method">
                                                <option value="bank_transfer">Bank Transfer</option>
                                                <option value="credit_card">Credit Card</option>
                                                <option value="ewallet">E-Wallet</option>
                                                <option value="qris">QRIS</option>
                                                <option value="virtual_account">Virtual Account</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Preview Results -->
                <div class="col-12" id="preview-results" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Hasil Perhitungan Biaya Admin</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Jumlah Lelang</label>
                                        <div class="form-control-plaintext" id="preview-auction-amount">-</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Metode Pembayaran</label>
                                        <div class="form-control-plaintext" id="preview-payment-method">-</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Biaya Admin</label>
                                        <div class="form-control-plaintext text-warning" id="preview-admin-fee">-</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Biaya Payment Gateway</label>
                                        <div class="form-control-plaintext text-info" id="preview-payment-gateway-fee">-
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Total Biaya</label>
                                        <div class="form-control-plaintext text-primary" id="preview-total-fees">-</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Total Pembayaran</label>
                                        <div class="form-control-plaintext font-weight-bold text-success"
                                            id="preview-total-amount">-</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Vendor Menerima</label>
                                        <div class="form-control-plaintext text-success" id="preview-vendor-receives">-
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Admin Menerima</label>
                                        <div class="form-control-plaintext text-primary" id="preview-admin-receives">-
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Fee Breakdown -->
                            <div class="mb-3">
                                <label class="form-label">Rincian Biaya Admin</label>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Nama Biaya</th>
                                                <th>Tipe</th>
                                                <th>Nilai</th>
                                                <th>Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody id="fee-breakdown-table">
                                            <!-- Will be populated by JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div class="col-12" id="loading-state" style="display: none;">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <div class="mt-2">Menghitung biaya admin...</div>
                        </div>
                    </div>
                </div>

                <!-- Error State -->
                <div class="col-12" id="error-state" style="display: none;">
                    <div class="card">
                        <div class="card-body">
                            <div class="alert alert-danger" role="alert">
                                <h4 class="alert-heading">Error!</h4>
                                <p id="error-message">Terjadi kesalahan saat menghitung biaya admin.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function calculateFees() {
            const form = document.getElementById('previewForm');
            const formData = new FormData(form);

            // Show loading state
            document.getElementById('loading-state').style.display = 'block';
            document.getElementById('preview-results').style.display = 'none';
            document.getElementById('error-state').style.display = 'none';

            // Make AJAX request
            fetch('{{ route('admin.admin-fees.preview') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loading-state').style.display = 'none';

                    if (data.error) {
                        document.getElementById('error-message').textContent = data.error;
                        document.getElementById('error-state').style.display = 'block';
                        return;
                    }

                    // Update preview results
                    document.getElementById('preview-auction-amount').textContent = 'Rp ' + data.auction_amount
                        .toLocaleString('id-ID');
                    document.getElementById('preview-payment-method').textContent = getPaymentMethodLabel(data
                        .payment_method || 'bank_transfer');
                    document.getElementById('preview-admin-fee').textContent = 'Rp ' + data.admin_fee.toLocaleString(
                        'id-ID');
                    document.getElementById('preview-payment-gateway-fee').textContent = 'Rp ' + data
                        .payment_gateway_fee.toLocaleString('id-ID');
                    document.getElementById('preview-total-fees').textContent = 'Rp ' + data.total_fees.toLocaleString(
                        'id-ID');
                    document.getElementById('preview-total-amount').textContent = 'Rp ' + data.total_amount
                        .toLocaleString('id-ID');
                    document.getElementById('preview-vendor-receives').textContent = 'Rp ' + data.vendor_receives
                        .toLocaleString('id-ID');
                    document.getElementById('preview-admin-receives').textContent = 'Rp ' + data.admin_receives
                        .toLocaleString('id-ID');

                    // Update fee breakdown table
                    updateFeeBreakdownTable(data.admin_fee_breakdown || []);

                    // Show results
                    document.getElementById('preview-results').style.display = 'block';
                })
                .catch(error => {
                    document.getElementById('loading-state').style.display = 'none';
                    document.getElementById('error-message').textContent = 'Terjadi kesalahan: ' + error.message;
                    document.getElementById('error-state').style.display = 'block';
                });
        }

        function updateFeeBreakdownTable(breakdown) {
            const tbody = document.getElementById('fee-breakdown-table');
            tbody.innerHTML = '';

            if (breakdown && breakdown.length > 0) {
                breakdown.forEach(fee => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                <td>${fee.name}</td>
                <td><span class="badge bg-${fee.type === 'percentage' ? 'blue' : 'green'}">${fee.type === 'percentage' ? 'Persentase' : 'Tetap'}</span></td>
                <td>${fee.type === 'percentage' ? fee.value + '%' : 'Rp ' + fee.value.toLocaleString('id-ID')}</td>
                <td>Rp ${fee.amount.toLocaleString('id-ID')}</td>
            `;
                    tbody.appendChild(row);
                });
            } else {
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="4" class="text-center text-muted">Tidak ada rincian biaya</td>';
                tbody.appendChild(row);
            }
        }

        function getPaymentMethodLabel(method) {
            const labels = {
                'bank_transfer': 'Bank Transfer',
                'credit_card': 'Credit Card',
                'ewallet': 'E-Wallet',
                'qris': 'QRIS',
                'virtual_account': 'Virtual Account'
            };
            return labels[method] || method;
        }

        // Auto-calculate on input change
        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('auction_amount');
            const paymentSelect = document.getElementById('payment_method');

            amountInput.addEventListener('input', function() {
                if (this.value && this.value > 0) {
                    calculateFees();
                }
            });

            paymentSelect.addEventListener('change', function() {
                if (amountInput.value && amountInput.value > 0) {
                    calculateFees();
                }
            });
        });
    </script>
@endpush
