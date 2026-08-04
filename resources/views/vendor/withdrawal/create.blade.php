@extends('layouts.vendor')

@section('title', 'Ajukan Penarikan Dana')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col-auto">
            <div class="page-pretitle">Vendor Panel</div>
            <h2 class="page-title">Ajukan Penarikan Dana</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('vendor.withdrawal.index') }}" class="btn btn-outline-primary">
                Kembali
            </a>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Form Penarikan</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('vendor.withdrawal.store') }}" method="POST" id="withdrawalForm">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Jumlah Penarikan (Rp)</label>
                                <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                       name="amount" id="amount"
                                       value="{{ old('amount') }}"
                                       min="{{ $minWithdrawal }}"
                                       max="{{ $wallet->available_balance ?? 0 }}"
                                       required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-hint">Minimum: Rp {{ number_format($minWithdrawal, 0, ',', '.') }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Metode Penarikan</label>
                                <select class="form-select @error('method') is-invalid @enderror" name="method" id="method" required>
                                    <option value="">Pilih Metode</option>
                                    <option value="bank_transfer" {{ old('method') === 'bank_transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                    <option value="e_wallet" {{ old('method') === 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                                    <option value="cash" {{ old('method') === 'cash' ? 'selected' : '' }}>Tunai</option>
                                </select>
                                @error('method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3" id="bankNameGroup">
                                <label class="form-label">Nama Bank</label>
                                <input type="text" class="form-control @error('bank_name') is-invalid @enderror"
                                       name="bank_name" value="{{ old('bank_name') }}" placeholder="Contoh: BCA, Mandiri, BRI">
                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nomor Rekening / Akun</label>
                                <input type="text" class="form-control @error('account_number') is-invalid @enderror"
                                       name="account_number" value="{{ old('account_number') }}" required>
                                @error('account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Pemilik Rekening</label>
                                <input type="text" class="form-control @error('account_name') is-invalid @enderror"
                                       name="account_name" value="{{ old('account_name') }}" required>
                                @error('account_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Catatan (Opsional)</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror"
                                          name="notes" rows="3" placeholder="Tambahkan catatan jika diperlukan">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Jumlah Penarikan</span>
                                            <span id="displayAmount">Rp 0</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Biaya Admin</span>
                                            <span id="displayFee">Rp 0</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Yang Diterima</span>
                                            <span id="displayNet" class="text-success">Rp 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('vendor.withdrawal.index') }}" class="btn btn-outline-secondary me-2">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Ajukan Penarikan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Saldo</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted small">Saldo Tersedia</div>
                            <div class="h4 mb-0 text-success">Rp {{ number_format($wallet->available_balance ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Total Pendapatan</div>
                            <div class="h5 mb-0">Rp {{ number_format($wallet->total_earned ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Total Ditarik</div>
                            <div class="h5 mb-0">Rp {{ number_format($wallet->total_withdrawn ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <hr>
                        <div class="text-muted small">
                            <p class="mb-1">• Penarikan akan diproses dalam 1-3 hari kerja</p>
                            <p class="mb-1">• Minimum penarikan: Rp {{ number_format($minWithdrawal, 0, ',', '.') }}</p>
                            <p class="mb-0">• Biaya admin akan dipotong dari jumlah penarikan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('amount');
        const methodSelect = document.getElementById('method');
        const bankNameGroup = document.getElementById('bankNameGroup');
        const displayAmount = document.getElementById('displayAmount');
        const displayFee = document.getElementById('displayFee');
        const displayNet = document.getElementById('displayNet');

        function formatCurrency(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        }

        function calculateFee() {
            const amount = parseFloat(amountInput.value) || 0;
            const method = methodSelect.value;

            if (amount > 0 && method) {
                fetch('{{ route("vendor.withdrawal.calculate-fee") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ amount: amount, method: method })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayAmount.textContent = formatCurrency(data.amount);
                        displayFee.textContent = formatCurrency(data.fee);
                        displayNet.textContent = formatCurrency(data.net_amount);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }

        // Show/hide bank name field based on method
        methodSelect.addEventListener('change', function() {
            if (this.value === 'bank_transfer') {
                bankNameGroup.style.display = 'block';
            } else {
                bankNameGroup.style.display = 'none';
            }
            calculateFee();
        });

        // Calculate fee on amount change
        amountInput.addEventListener('input', calculateFee);

        // Initial state
        if (methodSelect.value !== 'bank_transfer') {
            bankNameGroup.style.display = 'none';
        }
    });
</script>
@endpush
@endsection
