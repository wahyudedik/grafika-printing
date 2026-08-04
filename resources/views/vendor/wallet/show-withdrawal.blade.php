@extends('layouts.vendor')

@section('title', 'Detail Penarikan Wallet')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col-auto">
            <div class="page-pretitle">Vendor Panel</div>
            <h2 class="page-title">Detail Penarikan Wallet</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('vendor.wallet.withdrawals') }}" class="btn btn-outline-primary">
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
                        <h3 class="card-title">Detail Penarikan #{{ $withdrawal->withdrawal_code }}</h3>
                        <div class="card-actions">
                            @if($withdrawal->status === 'pending')
                            <form action="{{ route('vendor.wallet.cancel-withdrawal', $withdrawal) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Apakah Anda yakin ingin membatalkan penarikan ini?')">
                                @csrf
                                @method('POST')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    Batalkan
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="text-muted small">Kode Penarikan</div>
                                <div class="fw-bold">{{ $withdrawal->withdrawal_code }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Status</div>
                                <div>
                                    @if($withdrawal->status === 'pending')
                                        <span class="badge bg-warning-lt">Menunggu</span>
                                    @elseif($withdrawal->status === 'approved')
                                        <span class="badge bg-success-lt">Disetujui</span>
                                    @elseif($withdrawal->status === 'processing')
                                        <span class="badge bg-info-lt">Diproses</span>
                                    @elseif($withdrawal->status === 'completed')
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif($withdrawal->status === 'rejected')
                                        <span class="badge bg-danger-lt">Ditolak</span>
                                    @elseif($withdrawal->status === 'cancelled')
                                        <span class="badge bg-secondary-lt">Dibatalkan</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="text-muted small">Jumlah Penarikan</div>
                                <div class="h4 mb-0 text-success">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Biaya Admin</div>
                                <div class="h5 mb-0">Rp {{ number_format($withdrawal->fee ?? 0, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="text-muted small">Yang Diterima</div>
                                <div class="h4 mb-0 text-primary">Rp {{ number_format($withdrawal->net_amount ?? ($withdrawal->amount - ($withdrawal->fee ?? 0)), 0, ',', '.') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Metode</div>
                                <div>
                                    @if($withdrawal->method === 'bank_transfer')
                                        <span class="badge bg-blue-lt">Transfer Bank</span>
                                    @elseif($withdrawal->method === 'e_wallet')
                                        <span class="badge bg-purple-lt">E-Wallet</span>
                                    @else
                                        <span class="badge bg-green-lt">Tunai</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="text-muted small">Bank / Penyedia</div>
                                <div>{{ $withdrawal->bank_name ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Nomor Rekening</div>
                                <div>{{ $withdrawal->account_number }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="text-muted small">Nama Pemilik</div>
                                <div>{{ $withdrawal->account_name }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Tanggal Pengajuan</div>
                                <div>{{ $withdrawal->created_at->format('d M Y H:i') }}</div>
                            </div>
                        </div>

                        @if($withdrawal->notes)
                        <div class="mb-3">
                            <div class="text-muted small">Catatan</div>
                            <div>{{ $withdrawal->notes }}</div>
                        </div>
                        @endif

                        @if($withdrawal->admin_notes)
                        <div class="mb-3">
                            <div class="text-muted small">Catatan Admin</div>
                            <div class="card bg-light">
                                <div class="card-body">{{ $withdrawal->admin_notes }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted small">Status Penarikan</div>
                            <div class="mt-1">
                                @if($withdrawal->status === 'pending')
                                    <span class="badge bg-warning-lt badge-lg">Menunggu Persetujuan</span>
                                    <p class="text-muted small mt-2">Penarikan Anda sedang menunggu persetujuan dari admin.</p>
                                @elseif($withdrawal->status === 'approved')
                                    <span class="badge bg-success-lt badge-lg">Disetujui</span>
                                    <p class="text-muted small mt-2">Penarikan Anda telah disetujui dan akan segera diproses.</p>
                                @elseif($withdrawal->status === 'processing')
                                    <span class="badge bg-info-lt badge-lg">Sedang Diproses</span>
                                    <p class="text-muted small mt-2">Penarikan Anda sedang diproses oleh tim kami.</p>
                                @elseif($withdrawal->status === 'completed')
                                    <span class="badge bg-success badge-lg">Selesai</span>
                                    <p class="text-muted small mt-2">Penarikan Anda telah berhasil diproses.</p>
                                @elseif($withdrawal->status === 'rejected')
                                    <span class="badge bg-danger-lt badge-lg">Ditolak</span>
                                    <p class="text-muted small mt-2">Penarikan Anda ditolak. Silakan hubungi admin.</p>
                                @elseif($withdrawal->status === 'cancelled')
                                    <span class="badge bg-secondary-lt badge-lg">Dibatalkan</span>
                                    <p class="text-muted small mt-2">Penarikan ini telah Anda batalkan.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
