@extends('dev.layouts.app')

@section('title', 'Manajemen Penarikan')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col-auto">
            <div class="page-pretitle">Admin Panel</div>
            <h2 class="page-title">Manajemen Penarikan</h2>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-body">
                <form action="{{ route('admin.withdrawals.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">Semua Status</option>
                                @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Vendor</label>
                            <select class="form-select" name="vendor_id">
                                <option value="">Semua Vendor</option>
                                @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Withdrawal List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Penarikan</h3>
                <div class="card-actions">
                    <a href="{{ route('admin.withdrawals.statistics') }}" class="btn btn-sm btn-outline-primary">
                        Statistik
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($withdrawals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Vendor</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($withdrawals as $withdrawal)
                            <tr>
                                <td>
                                    <span class="font-weight-medium">{{ $withdrawal->withdrawal_code }}</span>
                                </td>
                                <td>
                                    {{ $withdrawal->vendor->name ?? '-' }}
                                </td>
                                <td>
                                    <span class="fw-bold">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    @if($withdrawal->method === 'bank_transfer')
                                        <span class="badge bg-blue-lt">Transfer Bank</span>
                                    @elseif($withdrawal->method === 'e_wallet')
                                        <span class="badge bg-purple-lt">E-Wallet</span>
                                    @else
                                        <span class="badge bg-green-lt">Tunai</span>
                                    @endif
                                </td>
                                <td>
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
                                    @elseif($withdrawal->status === 'failed')
                                        <span class="badge bg-danger">Gagal</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $withdrawal->created_at->format('d M Y H:i') }}
                                </td>
                                <td>
                                    <a href="{{ route('admin.withdrawals.show', $withdrawal) }}" class="btn btn-sm btn-outline-primary">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $withdrawals->links() }}
                </div>
                @else
                <div class="empty">
                    <p class="empty-title">Tidak ada penarikan</p>
                    <p class="empty-subtitle text-muted">Tidak ada penarikan yang sesuai dengan filter</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
