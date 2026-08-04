@extends('layouts.vendor')

@section('title', 'Riwayat Penarikan Wallet')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col-auto">
            <div class="page-pretitle">Vendor Panel</div>
            <h2 class="page-title">Riwayat Penarikan Wallet</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('vendor.wallet.create-withdrawal') }}" class="btn btn-primary">
                Tarik Dana
            </a>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Semua Penarikan</h3>
            </div>
            <div class="card-body">
                @if($withdrawals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
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
                                    @endif
                                </td>
                                <td>
                                    {{ $withdrawal->created_at->format('d M Y H:i') }}
                                </td>
                                <td>
                                    <a href="{{ route('vendor.wallet.show-withdrawal', $withdrawal) }}" class="btn btn-sm btn-outline-primary">
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
                    <p class="empty-title">Belum ada penarikan</p>
                    <p class="empty-subtitle text-muted">Ajukan penarikan dana pertama Anda</p>
                    <div class="empty-action">
                        <a href="{{ route('vendor.wallet.create-withdrawal') }}" class="btn btn-primary">
                            Tarik Dana
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
