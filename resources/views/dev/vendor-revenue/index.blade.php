@extends('dev.layouts.app')

@section('title', 'Data Pendapatan Vendor')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Data Pendapatan Vendor</h2>
                    <div class="text-muted mt-1">Monitoring pendapatan dan penarikan dana vendor dari lelang</div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Summary Cards -->
            <div class="row row-deck row-cards mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Vendor</div>
                            </div>
                            <div class="h1 mb-3">{{ $summary['total_vendors'] }}</div>
                            <div class="d-flex mb-2">
                                <div>Vendor aktif: {{ $summary['total_vendors'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Pendapatan</div>
                            </div>
                            <div class="h1 mb-3">Rp {{ number_format($summary['total_earnings'], 0, ',', '.') }}</div>
                            <div class="d-flex mb-2">
                                <div>Dari {{ $summary['total_auctions_won'] }} lelang</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Ditarik</div>
                            </div>
                            <div class="h1 mb-3">Rp {{ number_format($summary['total_withdrawn'], 0, ',', '.') }}</div>
                            <div class="d-flex mb-2">
                                <div>Penarikan berhasil</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Pending Penarikan</div>
                            </div>
                            <div class="h1 mb-3">Rp {{ number_format($summary['total_pending_withdrawal'], 0, ',', '.') }}
                            </div>
                            <div class="d-flex mb-2">
                                <div>Menunggu persetujuan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vendor Revenue Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Pendapatan Vendor</h3>
                    <div class="card-actions">
                        <button class="btn btn-primary" onclick="refreshData()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                            </svg>
                            Refresh Data
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Vendor</th>
                                    <th>Total Pendapatan</th>
                                    <th>Saldo Saat Ini</th>
                                    <th>Total Ditarik</th>
                                    <th>Pending Penarikan</th>
                                    <th>Lelang Menang</th>
                                    <th>Terakhir Penarikan</th>
                                    <th class="w-1">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vendors as $vendor)
                                    <tr>
                                        <td>
                                            <div class="d-flex py-1 align-items-center">
                                                <span class="avatar me-2"
                                                    style="background-image: url({{ $vendor['logo'] ?? '/demo/avatars/1.jpg' }})"></span>
                                                <div class="flex-fill">
                                                    <div class="font-weight-medium">{{ $vendor['name'] }}</div>
                                                    <div class="text-muted">{{ $vendor['email'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="font-weight-medium">Rp
                                                {{ number_format($vendor['total_earnings'], 0, ',', '.') }}</div>
                                            <div class="text-muted small">{{ $vendor['wallet_transactions_count'] }}
                                                transaksi</div>
                                        </td>
                                        <td>
                                            <div class="font-weight-medium text-success">Rp
                                                {{ number_format($vendor['current_balance'], 0, ',', '.') }}</div>
                                        </td>
                                        <td>
                                            <div class="font-weight-medium">Rp
                                                {{ number_format($vendor['total_withdrawn'], 0, ',', '.') }}</div>
                                        </td>
                                        <td>
                                            @if ($vendor['pending_withdrawal'] > 0)
                                                <div class="font-weight-medium text-warning">Rp
                                                    {{ number_format($vendor['pending_withdrawal'], 0, ',', '.') }}</div>
                                            @else
                                                <div class="text-muted">-</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="font-weight-medium">{{ $vendor['total_auctions_won'] }}</div>
                                            <div class="text-muted small">Rp
                                                {{ number_format($vendor['total_auction_earnings'], 0, ',', '.') }}</div>
                                        </td>
                                        <td>
                                            @if ($vendor['last_withdrawal'])
                                                <div class="text-muted small">
                                                    {{ $vendor['last_withdrawal']->created_at->diffForHumans() }}</div>
                                                <div class="text-muted small">Rp
                                                    {{ number_format($vendor['last_withdrawal']->amount, 0, ',', '.') }}
                                                </div>
                                            @else
                                                <div class="text-muted">Belum pernah</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-list flex-nowrap">
                                                <a href="{{ route('admin.vendor-revenue.show', $vendor['id']) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                        <path
                                                            d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                    </svg>
                                                    Detail
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="empty">
                                                <div class="empty-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                    </svg>
                                                </div>
                                                <p class="empty-title">Tidak ada data vendor</p>
                                                <p class="empty-subtitle text-muted">Belum ada vendor yang terdaftar dalam
                                                    sistem.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function refreshData() {
            location.reload();
        }

        // Auto refresh every 30 seconds
        setInterval(function() {
            // You can implement AJAX refresh here if needed
        }, 30000);
    </script>
@endsection

