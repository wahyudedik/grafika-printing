@extends('dev.layouts.app')

@section('title', 'Tambah Pengaturan Biaya Admin')

@section('content')
    <div class="row">
        <div class="col-12">
            <form action="{{ route('admin.admin-fees.store') }}" method="POST" class="card">
                @csrf
                <div class="card-header">
                    <h3 class="card-title">Tambah Pengaturan Biaya Admin</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Nama Pengaturan</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="name" value="{{ old('name') }}" placeholder="Contoh: Biaya Admin Lelang">
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
                                    <option value="auction" {{ old('category') == 'auction' ? 'selected' : '' }}>Lelang
                                    </option>
                                    <option value="payment" {{ old('category') == 'payment' ? 'selected' : '' }}>Pembayaran
                                    </option>
                                    <option value="transaction" {{ old('category') == 'transaction' ? 'selected' : '' }}>
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
                            placeholder="Deskripsi pengaturan biaya admin">{{ old('description') }}</textarea>
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
                                    <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Tetap (Rp)
                                    </option>
                                    <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>
                                        Persentase (%)</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Nilai</label>
                                <input type="number" class="form-control @error('value') is-invalid @enderror"
                                    name="value" value="{{ old('value') }}" step="0.01" min="0"
                                    placeholder="Masukkan nilai biaya">
                                <div class="form-hint" id="value-hint">
                                    Masukkan nilai biaya sesuai tipe yang dipilih
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
                                <input type="number" class="form-control @error('minimum_amount') is-invalid @enderror"
                                    name="minimum_amount" value="{{ old('minimum_amount') }}" step="0.01" min="0"
                                    placeholder="0">
                                <div class="form-hint">Biaya akan dikenakan jika jumlah lelang >= nilai ini</div>
                                @error('minimum_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jumlah Maksimum</label>
                                <input type="number" class="form-control @error('maximum_amount') is-invalid @enderror"
                                    name="maximum_amount" value="{{ old('maximum_amount') }}" step="0.01" min="0"
                                    placeholder="Tidak terbatas">
                                <div class="form-hint">Biaya akan dikenakan jika jumlah lelang <= nilai ini</div>
                                        @error('maximum_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Efektif Dari</label>
                                    <input type="datetime-local"
                                        class="form-control @error('effective_from') is-invalid @enderror"
                                        name="effective_from" value="{{ old('effective_from') }}">
                                    <div class="form-hint">Kosongkan untuk langsung aktif</div>
                                    @error('effective_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Efektif Sampai</label>
                                    <input type="datetime-local"
                                        class="form-control @error('effective_until') is-invalid @enderror"
                                        name="effective_until" value="{{ old('effective_until') }}">
                                    <div class="form-hint">Kosongkan untuk tidak ada batas waktu</div>
                                    @error('effective_until')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" name="is_active" value="1"
                                    {{ old('is_active', true) ? 'checked' : '' }}>
                                <span class="form-check-label">Aktifkan pengaturan ini</span>
                            </label>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                                Simpan
                            </button>
                            <a href="{{ route('admin.admin-fees.index') }}" class="btn btn-secondary ms-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M18 6l-12 12" />
                                    <path d="M6 6l12 12" />
                                </svg>
                                Batal
                            </a>
                        </div>
                    </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('type').addEventListener('change', function() {
            const valueInput = document.querySelector('input[name="value"]');
            const valueHint = document.getElementById('value-hint');

            if (this.value === 'fixed') {
                valueInput.placeholder = 'Masukkan jumlah dalam Rupiah';
                valueHint.textContent = 'Masukkan jumlah biaya tetap dalam Rupiah (contoh: 5000)';
            } else if (this.value === 'percentage') {
                valueInput.placeholder = 'Masukkan persentase';
                valueHint.textContent = 'Masukkan persentase biaya (contoh: 10 untuk 10%)';
            } else {
                valueInput.placeholder = 'Masukkan nilai biaya';
                valueHint.textContent = 'Masukkan nilai biaya sesuai tipe yang dipilih';
            }
        });
    </script>
@endsection
