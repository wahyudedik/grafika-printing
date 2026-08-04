@extends('layouts.vendor')

@section('title', 'Penarikan Dana')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col-auto">
            <div class="page-pretitle">Vendor Panel</div>
            <h2 class="page-title">Penarikan Dana</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('vendor.withdrawal.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 5v14"/>
                    <path d="M5 12h14"/>
                </svg>
                Ajukan Penarikan
            </a>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Wallet Summary -->
        <div class="row row-deck row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small">Saldo Tersedia</div>
                                <div class="h3 mb-0 mt-1">Rp {{ number_format($wallet->available_balance ?? 0, 0, ',', '.') }}</div>
                            </div>
                            <div class="avatar avatar-lg bg-success-lt">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M17 9v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h3" />
                                    <path d="M9 12h6" />
                                    <path d="M13 15v6" />
                                    <path d="M19 15v2a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-6" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small">Total Ditarik</div>
                                <div class="h3 mb-0 mt-1">Rp {{ number_format($wallet->total_withdrawn ?? 0, 0, ',', '.') }}</div>
                            </div>
                            <div class="avatar avatar-lg bg-primary-lt">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 5v14"/>
                                    <path d="M5 12l7 7"/>
                                    <path d="M19 12l-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small">Menunggu Proses</div>
                                <div class="h3 mb-0 mt-1">{{ $withdrawals->where('status', 'pending')->count() }}</div>
                            </div>
                            <div class="avatar avatar-lg bg-warning-lt">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
                                    <path d="M12 8l0 4"/>
                                    <path d="M12 16l.01 0"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small">Minimum Penarikan</div>
                                <div class="h3 mb-0 mt-1">Rp {{ number_format($minWithdrawal, 0, ',', '.') }}</div>
                            </div>
                            <div class="avatar avatar-lg bg-info-lt">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
                                    <path d="M12 8l0 4"/>
                                    <path d="M12 16l.01 0"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Withdrawal List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Riwayat Penarikan</h3>
                <div class="card-actions">
                    <a href="{{ route('vendor.withdrawal.history') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
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
                                    <span class="fw-bold text-success">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</span>
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
                                    <a href="{{ route('vendor.withdrawal.show', $withdrawal) }}" class="btn btn-sm btn-outline-primary">
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
                        <a href="{{ route('vendor.withdrawal.create') }}" class="btn btn-primary">
                            Ajukan Penarikan
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
