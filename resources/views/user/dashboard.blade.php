@extends('layouts.user')

@section('title', 'User Dashboard')

@section('content')
    <div class="row row-deck row-cards mb-4">
        <!-- Welcome Card -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="card-title">Selamat Datang, {{ auth()->user()->name }}!</h2>
                            <p class="text-muted">Anda telah berhasil login sebagai User di Grafika Printing.</p>
                        </div>
                        <div class="col-auto">
                            <div class="avatar avatar-lg"
                                style="background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiMyMTk2RjMiLz4KPHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4PSI4IiB5PSI4Ij4KPHBhdGggZD0iTTEyIDJDMTMuMSAyIDE0IDIuOSAxNCA0QzE0IDUuMSAxMy4xIDYgMTIgNkMxMC45IDYgMTAgNS4xIDEwIDRDMTAgMi45IDEwLjkgMiAxMiAyWk0yMSA5VjIySDNWOUgyMVoiIGZpbGw9IndoaXRlIi8+Cjwvc3ZnPgo8L3N2Zz4K')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Layanan Cetak</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">📄</div>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted">Akses layanan cetak dan printing yang tersedia</p>
                        <a href="#" class="btn btn-primary btn-sm">Lihat Layanan</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Pesanan Saya</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">📋</div>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted">Kelola pesanan dan transaksi Anda</p>
                        <a href="#" class="btn btn-outline-primary btn-sm">Lihat Pesanan</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Profil Saya</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">👤</div>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted">Kelola informasi profil dan akun Anda</p>
                        <a href="{{ route('user.profile.edit') }}" class="btn btn-outline-primary btn-sm">Edit Profil</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Cards -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Akun</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <div class="form-control-plaintext">{{ auth()->user()->name }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <div class="form-control-plaintext">{{ auth()->user()->email }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipe Akun</label>
                                <div class="form-control-plaintext">
                                    <span class="badge bg-blue-lt">{{ ucfirst(auth()->user()->usertype) }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status Verifikasi</label>
                                <div class="form-control-plaintext">
                                    @if (auth()->user()->email_verified_at)
                                        <span class="badge bg-green-lt">✓ Terverifikasi</span>
                                    @else
                                        <span class="badge bg-yellow-lt">⚠ Belum Terverifikasi</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Coming Soon -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Fitur yang Akan Datang</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <span class="avatar bg-blue-lt">🏆</span>
                                </div>
                                <div>
                                    <div class="font-weight-medium">Sistem Lelang</div>
                                    <div class="text-muted">Buat permintaan cetak dan terima penawaran dari vendor</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <span class="avatar bg-green-lt">💳</span>
                                </div>
                                <div>
                                    <div class="font-weight-medium">Pembayaran Online</div>
                                    <div class="text-muted">Integrasi dengan Midtrans untuk pembayaran yang aman</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <span class="avatar bg-yellow-lt">📦</span>
                                </div>
                                <div>
                                    <div class="font-weight-medium">Tracking Pesanan</div>
                                    <div class="text-muted">Lacak status pesanan dari pemesanan hingga pengiriman</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
