@extends('layouts.vendor')

@section('title', 'Kelola Rekening Bank')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Kelola Rekening Bank</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Kelola Rekening Bank</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-university me-2"></i>Detail Rekening Bank
                        </h5>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Primary Bank Account -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-star me-2"></i>Rekening Utama
                                            @if ($vendor->primary_bank_name)
                                                <span class="badge bg-success ms-2">Terisi</span>
                                            @else
                                                <span class="badge bg-warning ms-2">Belum Diisi</span>
                                            @endif
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if ($vendor->primary_bank_name)
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Nama Bank:</strong> {{ $vendor->primary_bank_name }}<br>
                                                    <strong>Nomor Rekening:</strong>
                                                    {{ $vendor->primary_account_number }}<br>
                                                    <strong>Nama Pemilik:</strong> {{ $vendor->primary_account_name }}
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Kode Bank:</strong> {{ $vendor->primary_bank_code ?? '-' }}<br>
                                                    <strong>Status Verifikasi:</strong>
                                                    @if ($vendor->bank_verified)
                                                        <span class="badge bg-success">Terverifikasi</span>
                                                    @else
                                                        <span class="badge bg-warning">Belum Diverifikasi</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <a href="{{ route('vendor.bank-accounts.edit', 'primary') }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                                <button class="btn btn-danger btn-sm" onclick="deleteAccount('primary')">
                                                    <i class="fas fa-trash me-1"></i>Hapus
                                                </button>
                                            </div>
                                        @else
                                            <p class="text-muted mb-3">Belum ada rekening utama yang terdaftar.</p>
                                            <a href="{{ route('vendor.bank-accounts.create') }}?type=primary"
                                                class="btn btn-primary">
                                                <i class="fas fa-plus me-1"></i>Tambah Rekening Utama
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Secondary Bank Account -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-university me-2"></i>Rekening Cadangan
                                            @if ($vendor->secondary_bank_name)
                                                <span class="badge bg-success ms-2">Terisi</span>
                                            @else
                                                <span class="badge bg-warning ms-2">Belum Diisi</span>
                                            @endif
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if ($vendor->secondary_bank_name)
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Nama Bank:</strong> {{ $vendor->secondary_bank_name }}<br>
                                                    <strong>Nomor Rekening:</strong>
                                                    {{ $vendor->secondary_account_number }}<br>
                                                    <strong>Nama Pemilik:</strong> {{ $vendor->secondary_account_name }}
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Kode Bank:</strong>
                                                    {{ $vendor->secondary_bank_code ?? '-' }}<br>
                                                    <strong>Status Verifikasi:</strong>
                                                    @if ($vendor->bank_verified)
                                                        <span class="badge bg-success">Terverifikasi</span>
                                                    @else
                                                        <span class="badge bg-warning">Belum Diverifikasi</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <a href="{{ route('vendor.bank-accounts.edit', 'secondary') }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                                <button class="btn btn-danger btn-sm" onclick="deleteAccount('secondary')">
                                                    <i class="fas fa-trash me-1"></i>Hapus
                                                </button>
                                            </div>
                                        @else
                                            <p class="text-muted mb-3">Belum ada rekening cadangan yang terdaftar.</p>
                                            <a href="{{ route('vendor.bank-accounts.create') }}?type=secondary"
                                                class="btn btn-info">
                                                <i class="fas fa-plus me-1"></i>Tambah Rekening Cadangan
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- E-Wallet Account -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0">
                                            <i class="fas fa-mobile-alt me-2"></i>E-Wallet
                                            @if ($vendor->ewallet_provider)
                                                <span class="badge bg-success ms-2">Terisi</span>
                                            @else
                                                <span class="badge bg-warning ms-2">Belum Diisi</span>
                                            @endif
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if ($vendor->ewallet_provider)
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Provider:</strong> {{ $vendor->ewallet_provider }}<br>
                                                    <strong>Nomor E-Wallet:</strong> {{ $vendor->ewallet_number }}<br>
                                                    <strong>Nama Pemilik:</strong> {{ $vendor->ewallet_name }}
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Status Verifikasi:</strong>
                                                    @if ($vendor->bank_verified)
                                                        <span class="badge bg-success">Terverifikasi</span>
                                                    @else
                                                        <span class="badge bg-warning">Belum Diverifikasi</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <a href="{{ route('vendor.bank-accounts.edit', 'ewallet') }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                                <button class="btn btn-danger btn-sm" onclick="deleteAccount('ewallet')">
                                                    <i class="fas fa-trash me-1"></i>Hapus
                                                </button>
                                            </div>
                                        @else
                                            <p class="text-muted mb-3">Belum ada e-wallet yang terdaftar.</p>
                                            <a href="{{ route('vendor.bank-accounts.create') }}?type=ewallet"
                                                class="btn btn-warning">
                                                <i class="fas fa-plus me-1"></i>Tambah E-Wallet
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Notes -->
                        @if ($vendor->bank_notes)
                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-secondary">
                                        <div class="card-header bg-secondary text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-sticky-note me-2"></i>Catatan Rekening
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-0">{{ $vendor->bank_notes }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Verification Status -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-info-circle me-2"></i>Status Verifikasi Rekening
                                    </h6>
                                    <p class="mb-0">
                                        @if ($vendor->hasVerifiedBankAccount())
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            Rekening Anda telah diverifikasi oleh admin pada
                                            {{ $vendor->bank_verified_at->format('d M Y H:i') }}.
                                        @else
                                            <i class="fas fa-clock text-warning me-2"></i>
                                            Rekening Anda belum diverifikasi. Silakan tunggu verifikasi dari admin atau
                                            hubungi admin untuk proses verifikasi.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus detail rekening ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function deleteAccount(type) {
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            const form = document.getElementById('deleteForm');
            form.action = `{{ route('vendor.bank-accounts.destroy', '') }}/${type}`;
            modal.show();
        }
    </script>
@endsection
