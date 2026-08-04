@extends('dev.layouts.app')

@section('title', 'Edit User Lelang - ' . ($profile->user->name ?? 'Unknown'))

@section('content')
<div class="row row-deck row-cards">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Edit User Lelang</h3>
            </div>
            <form action="{{ route('admin.user-lelang.update', $profile) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <!-- User Info (readonly) -->
                    <div class="mb-3">
                        <label class="form-label">User</label>
                        <div class="form-control-plaintext fw-bold">
                            {{ $profile->user->name ?? '-' }} ({{ $profile->user->email ?? '-' }})
                        </div>
                    </div>

                    <!-- Company Info -->
                    <div class="mb-3">
                        <label for="company_name" class="form-label">Nama Perusahaan</label>
                        <input type="text" name="company_name" id="company_name"
                            class="form-control @error('company_name') is-invalid @enderror"
                            value="{{ old('company_name', $profile->company_name) }}" placeholder="PT. contoh">
                        @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Contact Info -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="phone_number" class="form-label">Nomor Telepon</label>
                            <input type="text" name="phone_number" id="phone_number"
                                class="form-control @error('phone_number') is-invalid @enderror"
                                value="{{ old('phone_number', $profile->phone_number) }}" placeholder="08123456789">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="postal_code" class="form-label">Kode Pos</label>
                            <input type="text" name="postal_code" id="postal_code"
                                class="form-control @error('postal_code') is-invalid @enderror"
                                value="{{ old('postal_code', $profile->postal_code) }}" placeholder="12345" maxlength="10">
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror"
                            rows="2" placeholder="Alamat lengkap...">{{ old('address', $profile->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="city" class="form-label">Kota</label>
                            <input type="text" name="city" id="city"
                                class="form-control @error('city') is-invalid @enderror"
                                value="{{ old('city', $profile->city) }}" placeholder="Jakarta">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="province" class="form-label">Provinsi</label>
                            <input type="text" name="province" id="province"
                                class="form-control @error('province') is-invalid @enderror"
                                value="{{ old('province', $profile->province) }}" placeholder="DKI Jakarta">
                            @error('province')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Status & Notes -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status', $profile->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="pending" {{ old('status', $profile->status) === 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="suspended" {{ old('status', $profile->status) === 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan Admin</label>
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror"
                            rows="3" placeholder="Catatan internal...">{{ old('notes', $profile->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.user-lelang.show', $profile) }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Statistik</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted small">Terdaftar Sejak</label>
                    <div class="fw-bold">{{ $profile->created_at->format('d M Y H:i') }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Total Lelang Diikuti</label>
                    <div class="h3 mb-0">{{ $profile->total_auctions }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Total Menang</label>
                    <div class="h3 mb-0 text-success">{{ $profile->total_won }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Win Rate</label>
                    <div class="h3 mb-0 text-primary">{{ $profile->win_rate }}%</div>
                </div>
                <div>
                    <label class="form-label text-muted small">Total Belanja</label>
                    <div class="h3 mb-0 text-warning">Rp {{ number_format($profile->total_spent, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        @if($profile->is_verified)
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-success me-2" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M9 12l2 2l4 -4" />
                        </svg>
                        <div>
                            <div class="fw-bold text-success">Terverifikasi</div>
                            <small class="text-muted">
                                Diverifikasi pada {{ $profile->verified_at?->format('d M Y H:i') ?? '-' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
