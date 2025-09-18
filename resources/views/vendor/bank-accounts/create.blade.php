@extends('layouts.vendor')

@section('title', 'Tambah Detail Rekening')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('vendor.bank-accounts.index') }}">Kelola Rekening
                                    Bank</a></li>
                            <li class="breadcrumb-item active">Tambah Detail Rekening</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Tambah Detail Rekening</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-plus me-2"></i>Form Detail Rekening
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('vendor.bank-accounts.store') }}" method="POST">
                            @csrf

                            <!-- Account Type -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="account_type" class="form-label">Jenis Rekening <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('account_type') is-invalid @enderror"
                                        id="account_type" name="account_type" required>
                                        <option value="">Pilih Jenis Rekening</option>
                                        <option value="primary" {{ old('account_type') == 'primary' ? 'selected' : '' }}>
                                            Rekening Utama</option>
                                        <option value="secondary"
                                            {{ old('account_type') == 'secondary' ? 'selected' : '' }}>Rekening Cadangan
                                        </option>
                                        <option value="ewallet" {{ old('account_type') == 'ewallet' ? 'selected' : '' }}>
                                            E-Wallet</option>
                                    </select>
                                    @error('account_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Bank Account Fields -->
                            <div id="bank-fields" style="display: none;">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="bank_name" class="form-label">Nama Bank <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('bank_name') is-invalid @enderror" id="bank_name"
                                            name="bank_name">
                                            <option value="">Pilih Bank</option>
                                        </select>
                                        @error('bank_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="bank_code" class="form-label">Kode Bank</label>
                                        <input type="text" class="form-control @error('bank_code') is-invalid @enderror"
                                            id="bank_code" name="bank_code" value="{{ old('bank_code') }}" readonly>
                                        @error('bank_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="account_number" class="form-label">Nomor Rekening <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('account_number') is-invalid @enderror"
                                            id="account_number" name="account_number" value="{{ old('account_number') }}"
                                            placeholder="Masukkan nomor rekening">
                                        @error('account_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="account_name" class="form-label">Nama Pemilik Rekening <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('account_name') is-invalid @enderror"
                                            id="account_name" name="account_name" value="{{ old('account_name') }}"
                                            placeholder="Masukkan nama pemilik rekening">
                                        @error('account_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- E-Wallet Fields -->
                            <div id="ewallet-fields" style="display: none;">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="ewallet_provider" class="form-label">Provider E-Wallet <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('ewallet_provider') is-invalid @enderror"
                                            id="ewallet_provider" name="ewallet_provider">
                                            <option value="">Pilih Provider</option>
                                        </select>
                                        @error('ewallet_provider')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="ewallet_number" class="form-label">Nomor E-Wallet <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('ewallet_number') is-invalid @enderror"
                                            id="ewallet_number" name="ewallet_number" value="{{ old('ewallet_number') }}"
                                            placeholder="Masukkan nomor e-wallet">
                                        @error('ewallet_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="ewallet_name" class="form-label">Nama Pemilik E-Wallet <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('ewallet_name') is-invalid @enderror"
                                            id="ewallet_name" name="ewallet_name" value="{{ old('ewallet_name') }}"
                                            placeholder="Masukkan nama pemilik e-wallet">
                                        @error('ewallet_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Bank Notes -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="bank_notes" class="form-label">Catatan Rekening</label>
                                    <textarea class="form-control @error('bank_notes') is-invalid @enderror" id="bank_notes" name="bank_notes"
                                        rows="3" placeholder="Masukkan catatan tambahan (opsional)">{{ old('bank_notes') }}</textarea>
                                    @error('bank_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>Simpan
                                        </button>
                                        <a href="{{ route('vendor.bank-accounts.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-1"></i>Batal
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const accountTypeSelect = document.getElementById('account_type');
            const bankFields = document.getElementById('bank-fields');
            const ewalletFields = document.getElementById('ewallet-fields');
            const bankNameSelect = document.getElementById('bank_name');
            const bankCodeInput = document.getElementById('bank_code');
            const ewalletProviderSelect = document.getElementById('ewallet_provider');

            // Load banks and e-wallet providers
            loadBanks();
            loadEwalletProviders();

            // Handle account type change
            accountTypeSelect.addEventListener('change', function() {
                if (this.value === 'primary' || this.value === 'secondary') {
                    bankFields.style.display = 'block';
                    ewalletFields.style.display = 'none';
                } else if (this.value === 'ewallet') {
                    bankFields.style.display = 'none';
                    ewalletFields.style.display = 'block';
                } else {
                    bankFields.style.display = 'none';
                    ewalletFields.style.display = 'none';
                }
            });

            // Handle bank selection
            bankNameSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                bankCodeInput.value = selectedOption.dataset.code || '';
            });

            // Set initial state based on URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const type = urlParams.get('type');
            if (type) {
                accountTypeSelect.value = type;
                accountTypeSelect.dispatchEvent(new Event('change'));
            }

            function loadBanks() {
                fetch('/api/vendor/banks')
                    .then(response => response.json())
                    .then(banks => {
                        banks.forEach(bank => {
                            const option = document.createElement('option');
                            option.value = bank.name;
                            option.textContent = bank.name;
                            option.dataset.code = bank.code;
                            bankNameSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error loading banks:', error));
            }

            function loadEwalletProviders() {
                fetch('/api/vendor/ewallet-providers')
                    .then(response => response.json())
                    .then(providers => {
                        providers.forEach(provider => {
                            const option = document.createElement('option');
                            option.value = provider.name;
                            option.textContent = provider.name;
                            ewalletProviderSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error loading e-wallet providers:', error));
            }
        });
    </script>
@endsection
