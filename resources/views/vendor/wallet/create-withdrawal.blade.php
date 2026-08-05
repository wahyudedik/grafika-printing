@extends('layouts.vendor')

@section('title', 'Tarik Dana')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Permintaan Penarikan Dana</h3>
                    <div class="card-subtitle">Ajukan penarikan dana dari saldo wallet Anda</div>
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

                    <!-- Wallet Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Saldo Tersedia</h6>
                                    <div class="h2 text-success">Rp {{ number_format($wallet->available_balance) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Total Pendapatan</h6>
                                    <div class="h2 text-primary">Rp {{ number_format($wallet->total_earned) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('vendor.wallet.store-withdrawal') }}" data-loading>
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Jumlah Penarikan <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                            id="amount" name="amount" value="{{ old('amount') }}" min="10000"
                                            max="{{ $wallet->available_balance }}" step="1000" required>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">
                                        Minimum: Rp 10,000 | Maksimum: Rp {{ number_format($wallet->available_balance) }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="method" class="form-label">Metode Penarikan <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control @error('method') is-invalid @enderror" id="method"
                                        name="method" required>
                                        <option value="">Pilih Metode</option>
                                        <option value="bank_transfer"
                                            {{ old('method') === 'bank_transfer' ? 'selected' : '' }}>
                                            Transfer Bank
                                        </option>
                                        <option value="e_wallet" {{ old('method') === 'e_wallet' ? 'selected' : '' }}>
                                            E-Wallet
                                        </option>
                                        <option value="cash" {{ old('method') === 'cash' ? 'selected' : '' }}>
                                            Tunai
                                        </option>
                                    </select>
                                    @error('method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="account_number" class="form-label">Nomor Rekening/E-Wallet <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('account_number') is-invalid @enderror"
                                        id="account_number" name="account_number" value="{{ old('account_number') }}"
                                        placeholder="Masukkan nomor rekening atau e-wallet" required>
                                    @error('account_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="account_name" class="form-label">Nama Pemilik <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('account_name') is-invalid @enderror"
                                        id="account_name" name="account_name" value="{{ old('account_name') }}"
                                        placeholder="Nama pemilik rekening/e-wallet" required>
                                    @error('account_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3" id="bank_name_field" style="display: none;">
                                    <label for="bank_name" class="form-label">Nama Bank</label>
                                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror"
                                        id="bank_name" name="bank_name" value="{{ old('bank_name') }}"
                                        placeholder="Nama bank">
                                    @error('bank_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Catatan (Opsional)</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4"
                                        placeholder="Catatan tambahan untuk penarikan">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Fee Information -->
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Informasi Biaya</h6>
                                        <div class="small">
                                            <div class="d-flex justify-content-between">
                                                <span>Transfer Bank:</span>
                                                <span>1% (min Rp 5,000)</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>E-Wallet:</span>
                                                <span>0.5% (min Rp 3,000)</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Tunai:</span>
                                                <span>Gratis</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Preview -->
                                <div class="card" id="preview_card" style="display: none;">
                                    <div class="card-body">
                                        <h6 class="card-title">Preview Penarikan</h6>
                                        <div class="small">
                                            <div class="d-flex justify-content-between">
                                                <span>Jumlah:</span>
                                                <span id="preview_amount">Rp 0</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Biaya:</span>
                                                <span id="preview_fee">Rp 0</span>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between fw-bold">
                                                <span>Diterima:</span>
                                                <span id="preview_net">Rp 0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('vendor.wallet.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Ajukan Penarikan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const methodSelect = document.getElementById('method');
            const bankNameField = document.getElementById('bank_name_field');
            const previewCard = document.getElementById('preview_card');
            const amountInput = document.getElementById('amount');

            // Show/hide bank name field based on method
            methodSelect.addEventListener('change', function() {
                if (this.value === 'bank_transfer') {
                    bankNameField.style.display = 'block';
                } else {
                    bankNameField.style.display = 'none';
                }
                updatePreview();
            });

            // Update preview when amount changes
            amountInput.addEventListener('input', updatePreview);

            function updatePreview() {
                const amount = parseFloat(amountInput.value) || 0;
                const method = methodSelect.value;

                if (amount > 0 && method) {
                    let fee = 0;

                    switch (method) {
                        case 'bank_transfer':
                            fee = Math.min(5000, amount * 0.01);
                            break;
                        case 'e_wallet':
                            fee = Math.min(3000, amount * 0.005);
                            break;
                        case 'cash':
                            fee = 0;
                            break;
                    }

                    const netAmount = amount - fee;

                    document.getElementById('preview_amount').textContent = 'Rp ' + amount.toLocaleString();
                    document.getElementById('preview_fee').textContent = 'Rp ' + fee.toLocaleString();
                    document.getElementById('preview_net').textContent = 'Rp ' + netAmount.toLocaleString();

                    previewCard.style.display = 'block';
                } else {
                    previewCard.style.display = 'none';
                }
            }
        });
    </script>
@endsection
