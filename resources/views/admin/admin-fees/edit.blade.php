@extends('dev.layouts.app')

@section('title', 'Edit Pengaturan Biaya Admin')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Pengaturan
                    </div>
                    <h2 class="page-title">
                        Edit Pengaturan Biaya Admin
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
                        <a href="{{ route('admin.admin-fees.show', $adminFee) }}" class="btn btn-outline-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path
                                    d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                            </svg>
                            Lihat Detail
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
                    <form action="{{ route('admin.admin-fees.update', $adminFee) }}" method="POST" class="card">
                        @csrf
                        @method('PUT')
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Pengaturan Biaya Admin</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Nama Pengaturan</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            name="name" value="{{ old('name', $adminFee->name) }}"
                                            placeholder="Contoh: Biaya Admin 10%">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Kategori</label>
                                        <select class="form-select @error('category') is-invalid @enderror" name="category">
                                            <option value="">Pilih Kategori</option>
                                            <option value="auction"
                                                {{ old('category', $adminFee->category) == 'auction' ? 'selected' : '' }}>
                                                Lelang</option>
                                            <option value="payment"
                                                {{ old('category', $adminFee->category) == 'payment' ? 'selected' : '' }}>
                                                Pembayaran</option>
                                            <option value="transaction"
                                                {{ old('category', $adminFee->category) == 'transaction' ? 'selected' : '' }}>
                                                Transaksi</option>
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3"
                                    placeholder="Deskripsi pengaturan biaya admin">{{ old('description', $adminFee->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Tipe Biaya</label>
                                        <select class="form-select @error('type') is-invalid @enderror" name="type"
                                            id="type">
                                            <option value="">Pilih Tipe</option>
                                            <option value="fixed"
                                                {{ old('type', $adminFee->type) == 'fixed' ? 'selected' : '' }}>Biaya Tetap
                                                (Rupiah)</option>
                                            <option value="percentage"
                                                {{ old('type', $adminFee->type) == 'percentage' ? 'selected' : '' }}>Biaya
                                                Persentase (%)</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Nilai Biaya</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control @error('value') is-invalid @enderror"
                                                name="value" value="{{ old('value', $adminFee->value) }}" step="0.01"
                                                min="0" placeholder="Masukkan nilai biaya">
                                            <span class="input-group-text" id="value-unit">
                                                {{ $adminFee->type === 'percentage' ? '%' : 'Rp' }}
                                            </span>
                                        </div>
                                        @error('value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Jumlah Minimum</label>
                                        <input type="number"
                                            class="form-control @error('minimum_amount') is-invalid @enderror"
                                            name="minimum_amount"
                                            value="{{ old('minimum_amount', $adminFee->minimum_amount) }}" step="0.01"
                                            min="0" placeholder="Jumlah minimum untuk menerapkan biaya">
                                        @error('minimum_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Jumlah Maksimum</label>
                                        <input type="number"
                                            class="form-control @error('maximum_amount') is-invalid @enderror"
                                            name="maximum_amount"
                                            value="{{ old('maximum_amount', $adminFee->maximum_amount) }}" step="0.01"
                                            min="0" placeholder="Jumlah maksimum untuk menerapkan biaya">
                                        @error('maximum_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Berlaku Dari</label>
                                        <input type="date"
                                            class="form-control @error('effective_from') is-invalid @enderror"
                                            name="effective_from"
                                            value="{{ old('effective_from', $adminFee->effective_from?->format('Y-m-d')) }}">
                                        @error('effective_from')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Berlaku Sampai</label>
                                        <input type="date"
                                            class="form-control @error('effective_until') is-invalid @enderror"
                                            name="effective_until"
                                            value="{{ old('effective_until', $adminFee->effective_until?->format('Y-m-d')) }}">
                                        @error('effective_until')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                        {{ old('is_active', $adminFee->is_active) ? 'checked' : '' }} id="is_active">
                                    <label class="form-check-label" for="is_active">
                                        Aktifkan pengaturan ini
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row align-items-center">
                                <div class="col">
                                    <a href="{{ route('admin.admin-fees.show', $adminFee) }}"
                                        class="btn btn-outline-secondary">
                                        Batal
                                    </a>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M5 12l5 5l10 -10" />
                                        </svg>
                                        Update Pengaturan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const valueUnit = document.getElementById('value-unit');
            const valueInput = document.querySelector('input[name="value"]');

            typeSelect.addEventListener('change', function() {
                if (this.value === 'percentage') {
                    valueUnit.textContent = '%';
                    valueInput.placeholder = 'Masukkan persentase (contoh: 10)';
                    valueInput.max = '100';
                } else if (this.value === 'fixed') {
                    valueUnit.textContent = 'Rp';
                    valueInput.placeholder = 'Masukkan jumlah dalam Rupiah';
                    valueInput.removeAttribute('max');
                }
            });

            // Trigger change event on page load if type is already selected
            if (typeSelect.value) {
                typeSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endpush
