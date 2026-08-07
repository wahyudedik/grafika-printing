@extends('dev.layouts.app')

@section('title', 'Preview Biaya Admin')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <p class="text-sm text-gray-500">Pengaturan</p>
            <h1 class="text-2xl font-bold text-gray-900">Preview Biaya Admin</h1>
        </div>
        <x.ui.button href="{{ route('admin.admin-fees.index') }}" variant="outline">
            <i class="fas fa-chevron-left mr-1"></i>Kembali
        </x.ui.button>
    </div>

    <!-- Kalkulator Biaya Admin -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Kalkulator Biaya Admin</h3>
            <x.ui.button type="button" variant="primary" onclick="calculateFees()">
                <i class="fas fa-clock mr-1"></i>Hitung Biaya
            </x.ui.button>
        </div>
        <div class="p-5">
            <form id="previewForm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Lelang <span class="text-red-500">*</span></label>
                        <input type="number" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500" id="auction_amount" name="amount"
                            placeholder="Masukkan jumlah lelang" min="1" step="0.01" required>
                        <p class="mt-1 text-xs text-gray-500">Masukkan jumlah lelang dalam Rupiah</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                        <select class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500" id="payment_method" name="payment_method">
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="ewallet">E-Wallet</option>
                            <option value="qris">QRIS</option>
                            <option value="virtual_account">Virtual Account</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Results -->
    <div id="preview-results" class="hidden bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Hasil Perhitungan Biaya Admin</h3>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Jumlah Lelang</label>
                    <p class="text-sm text-gray-900" id="preview-auction-amount">-</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Metode Pembayaran</label>
                    <p class="text-sm text-gray-900" id="preview-payment-method">-</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Biaya Admin</label>
                    <p class="text-sm font-medium text-yellow-600" id="preview-admin-fee">-</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Biaya Payment Gateway</label>
                    <p class="text-sm font-medium text-cyan-600" id="preview-payment-gateway-fee">-</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Total Biaya</label>
                    <p class="text-sm font-medium text-primary-600" id="preview-total-fees">-</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Total Pembayaran</label>
                    <p class="text-sm font-bold text-green-600" id="preview-total-amount">-</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Vendor Menerima</label>
                    <p class="text-sm font-medium text-green-600" id="preview-vendor-receives">-</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Admin Menerima</label>
                    <p class="text-sm font-medium text-primary-600" id="preview-admin-receives">-</p>
                </div>
            </div>

            <!-- Fee Breakdown -->
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Rincian Biaya Admin</label>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Biaya</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nilai</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="fee-breakdown-table">
                            <!-- Will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loading-state" class="hidden bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-6">
        <div class="text-center">
            <div class="inline-block w-8 h-8 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin mb-3"></div>
            <p class="text-sm text-gray-500">Menghitung biaya admin...</p>
        </div>
    </div>

    <!-- Error State -->
    <div id="error-state" class="hidden bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
                <div>
                    <h4 class="text-sm font-semibold text-red-800">Error!</h4>
                    <p class="text-sm text-red-600" id="error-message">Terjadi kesalahan saat menghitung biaya admin.</p>
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
            document.getElementById('loading-state').classList.remove('hidden');
            document.getElementById('preview-results').classList.add('hidden');
            document.getElementById('error-state').classList.add('hidden');

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
                    document.getElementById('loading-state').classList.add('hidden');

                    if (data.error) {
                        document.getElementById('error-message').textContent = data.error;
                        document.getElementById('error-state').classList.remove('hidden');
                        return;
                    }

                    // Update preview results
                    document.getElementById('preview-auction-amount').textContent = 'Rp ' + data.auction_amount.toLocaleString('id-ID');
                    document.getElementById('preview-payment-method').textContent = getPaymentMethodLabel(data.payment_method || 'bank_transfer');
                    document.getElementById('preview-admin-fee').textContent = 'Rp ' + data.admin_fee.toLocaleString('id-ID');
                    document.getElementById('preview-payment-gateway-fee').textContent = 'Rp ' + data.payment_gateway_fee.toLocaleString('id-ID');
                    document.getElementById('preview-total-fees').textContent = 'Rp ' + data.total_fees.toLocaleString('id-ID');
                    document.getElementById('preview-total-amount').textContent = 'Rp ' + data.total_amount.toLocaleString('id-ID');
                    document.getElementById('preview-vendor-receives').textContent = 'Rp ' + data.vendor_receives.toLocaleString('id-ID');
                    document.getElementById('preview-admin-receives').textContent = 'Rp ' + data.admin_receives.toLocaleString('id-ID');

                    // Update fee breakdown table
                    updateFeeBreakdownTable(data.admin_fee_breakdown || []);

                    // Show results
                    document.getElementById('preview-results').classList.remove('hidden');
                })
                .catch(error => {
                    document.getElementById('loading-state').classList.add('hidden');
                    document.getElementById('error-message').textContent = 'Terjadi kesalahan: ' + error.message;
                    document.getElementById('error-state').classList.remove('hidden');
                });
        }

        function updateFeeBreakdownTable(breakdown) {
            const tbody = document.getElementById('fee-breakdown-table');
            tbody.innerHTML = '';

            if (breakdown && breakdown.length > 0) {
                breakdown.forEach(fee => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-4 py-2 text-sm text-gray-900">${fee.name}</td>
                        <td class="px-4 py-2"><span class="px-2 py-0.5 text-xs font-medium rounded-full ${fee.type === 'percentage' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}">${fee.type === 'percentage' ? 'Persentase' : 'Tetap'}</span></td>
                        <td class="px-4 py-2 text-sm text-gray-900">${fee.type === 'percentage' ? fee.value + '%' : 'Rp ' + fee.value.toLocaleString('id-ID')}</td>
                        <td class="px-4 py-2 text-sm font-medium text-gray-900">Rp ${fee.amount.toLocaleString('id-ID')}</td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada rincian biaya</td>';
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
