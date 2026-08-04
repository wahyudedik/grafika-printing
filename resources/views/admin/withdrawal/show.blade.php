@extends('dev.layouts.app')

@section('title', 'Detail Penarikan')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col-auto">
            <div class="page-pretitle">Admin Panel</div>
            <h2 class="page-title">Detail Penarikan</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-outline-primary">
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
                        <h3 class="card-title">Penarikan #{{ $withdrawal->withdrawal_code }}</h3>
                        <div class="card-actions">
                            @if($withdrawal->status === 'pending')
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                    Setujui
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    Tolak
                                </button>
                            </div>
                            @elseif($withdrawal->status === 'approved')
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#completeModal">
                                Selesaikan
                            </button>
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
                                <div class="text-muted small">Vendor</div>
                                <div>{{ $withdrawal->vendor->name ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Jumlah</div>
                                <div class="h4 mb-0 text-success">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="text-muted small">Biaya Admin</div>
                                <div>Rp {{ number_format($withdrawal->fee ?? 0, 0, ',', '.') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Yang Diterima</div>
                                <div class="h5 mb-0 text-primary">Rp {{ number_format($withdrawal->net_amount ?? ($withdrawal->amount - ($withdrawal->fee ?? 0)), 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
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
                            <div class="col-md-6">
                                <div class="text-muted small">Bank / Penyedia</div>
                                <div>{{ $withdrawal->bank_name ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="text-muted small">Nomor Rekening</div>
                                <div>{{ $withdrawal->account_number }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Nama Pemilik</div>
                                <div>{{ $withdrawal->account_name }}</div>
                            </div>
                        </div>

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
                        <h3 class="card-title">Informasi Tambahan</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted small">Tanggal Pengajuan</div>
                            <div>{{ $withdrawal->created_at->format('d M Y H:i') }}</div>
                        </div>
                        @if($withdrawal->processedBy)
                        <div class="mb-3">
                            <div class="text-muted small">Diproses Oleh</div>
                            <div>{{ $withdrawal->processedBy->name }}</div>
                        </div>
                        @endif
                        @if($withdrawal->processed_at)
                        <div class="mb-3">
                            <div class="text-muted small">Tanggal Diproses</div>
                            <div>{{ $withdrawal->processed_at->format('d M Y H:i') }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal modal-blur fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.withdrawals.approve', $withdrawal) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Setujui Penarikan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menyetujui penarikan ini?</p>
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" name="admin_notes" rows="3" placeholder="Catatan untuk vendor"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal modal-blur fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.withdrawals.reject', $withdrawal) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Penarikan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menolak penarikan ini?</p>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="admin_notes" rows="3" required placeholder="Masukkan alasan penolakan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Complete Modal -->
<div class="modal modal-blur fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.withdrawals.complete', $withdrawal) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Selesaikan Penarikan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menandai penarikan ini sebagai selesai?</p>
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" name="admin_notes" rows="3" placeholder="Catatan penyelesaian"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Selesaikan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
