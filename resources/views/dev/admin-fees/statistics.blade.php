@extends('dev.layouts.app')

@section('title', 'Statistik Biaya Admin')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Statistik Biaya Admin</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.admin-fees.index') }}" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M18 6l-12 12" />
                                <path d="M6 6l12 12" />
                            </svg>
                            Kembali ke Pengaturan
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date"
                                value="{{ $startDate->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="end_date"
                                value="{{ $endDate->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                        <path d="M21 21l-6 -6" />
                                    </svg>
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Total Transaksi</div>
                                    </div>
                                    <div class="h1 mb-3">{{ number_format($statistics['total_transactions']) }}</div>
                                    <div class="d-flex mb-2">
                                        <div>Transaksi biaya admin</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Total Nilai Lelang</div>
                                    </div>
                                    <div class="h1 mb-3">Rp
                                        {{ number_format($statistics['total_auction_amount'], 0, ',', '.') }}</div>
                                    <div class="d-flex mb-2">
                                        <div>Nilai total lelang</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Total Biaya Admin</div>
                                    </div>
                                    <div class="h1 mb-3">Rp
                                        {{ number_format($statistics['total_admin_fees'], 0, ',', '.') }}</div>
                                    <div class="d-flex mb-2">
                                        <div>Pendapatan admin</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Total Biaya Payment</div>
                                    </div>
                                    <div class="h1 mb-3">Rp
                                        {{ number_format($statistics['total_payment_fees'], 0, ',', '.') }}</div>
                                    <div class="d-flex mb-2">
                                        <div>Biaya payment gateway</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Total Admin Menerima</div>
                                    </div>
                                    <div class="h1 mb-3">Rp
                                        {{ number_format($statistics['total_admin_receives'], 0, ',', '.') }}</div>
                                    <div class="d-flex mb-2">
                                        <div>Total yang diterima admin</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Total Vendor Menerima</div>
                                    </div>
                                    <div class="h1 mb-3">Rp
                                        {{ number_format($statistics['total_vendor_receives'], 0, ',', '.') }}</div>
                                    <div class="d-flex mb-2">
                                        <div>Total yang diterima vendor</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Rata-rata Biaya Admin</div>
                                    </div>
                                    <div class="h1 mb-3">{{ $statistics['average_admin_fee_percentage'] }}%</div>
                                    <div class="d-flex mb-2">
                                        <div>Persentase rata-rata</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Average Transaction Amount -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Rata-rata Nilai Transaksi</div>
                                    </div>
                                    <div class="h1 mb-3">Rp
                                        {{ number_format($statistics['average_transaction_amount'], 0, ',', '.') }}</div>
                                    <div class="d-flex mb-2">
                                        <div>Nilai rata-rata per transaksi</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Periode Laporan</div>
                                    </div>
                                    <div class="h1 mb-3">{{ $startDate->format('d M Y') }} -
                                        {{ $endDate->format('d M Y') }}</div>
                                    <div class="d-flex mb-2">
                                        <div>Rentang waktu laporan</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
