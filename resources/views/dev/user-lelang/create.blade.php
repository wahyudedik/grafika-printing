@extends('dev.layouts.app')

@section('title', 'Tambah User Lelang Baru')

@section('content')
<div class="row row-deck row-cards">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Tambah User Lelang</h3>
            </div>
            <form action="{{ route('admin.user-lelang.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <!-- User Selection -->
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Pilih User <span class="text-danger">*</span></label>
                        <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                            <option value="">-- Pilih User --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($users->isEmpty())
                            <div class="text-muted small mt-1">Semua user sudah memiliki profil lelang.</div>
                        @endif
                    </div>

                    <!-- Company Info -->
                    <div class="mb-3">
                        <label for="company_name" class="form-label">Nama Perusahaan</label>
                        <input type="text" name="company_name" id="company_name"
                            class="form-control @error('company_name') is-invalid @enderror"
                            value="{{ old('company_name') }}" placeholder="PT. contoh">
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
                                value="{{ old('phone_number') }}" placeholder="08123456789">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="postal_code" class="form-label">Kode Pos</label>
                            <input type="text" name="postal_code" id="postal_code"
                                class="form-control @error('postal_code') is-invalid @enderror"
                                value="{{ old('postal_code') }}" placeholder="12345" maxlength="10">
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror"
                            rows="2" placeholder="Alamat lengkap...">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="city" class="form-label">Kota</label>
                            <input type="text" name="city" id="city"
                                class="form-control @error('city') is-invalid @enderror"
                                value="{{ old('city') }}" placeholder="Jakarta">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="province" class="form-label">Provinsi</label>
                            <input type="text" name="province" id="province"
                                class="form-control @error('province') is-invalid @enderror"
                                value="{{ old('province') }}" placeholder="DKI Jakarta">
                            @error('province')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Status & Notes -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan Admin</label>
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror"
                            rows="3" placeholder="Catatan internal...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary" {{ $users->isEmpty() ? 'disabled' : '' }}>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Simpan
                    </button>
                    <a href="{{ route('admin.user-lelang.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar Tips -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                        <path d="M12 16l0 -4" />
                        <path d="M12 8l.01 0" />
                    </svg>
                    Panduan
                </h3>
            </div>
            <div class="card-body">
                <h5>User Lelang</h5>
                <p class="text-muted small">
                    User Lelang adalah pengguna yang aktif mengikuti lelang di platform.
                    Mereka dapat membuat lelang, menawar produk, dan memenangkan lelang.
                </p>

                <h6 class="mt-3">Status:</h6>
                <ul class="text-muted small">
                    <li><span class="badge bg-success">Aktif</span> - Dapat mengikuti lelang</li>
                    <li><span class="badge bg-warning">Menunggu</span> - Menunggu verifikasi</li>
                    <li><span class="badge bg-danger">Ditangguhkan</span> - Tidak dapat mengikuti lelang</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
