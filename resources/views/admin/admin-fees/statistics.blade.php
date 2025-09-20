@extends('dev.layouts.app')

@section('title', 'Statistik Biaya Admin')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Pengaturan
                    </div>
                    <h2 class="page-title">
                        Statistik Biaya Admin
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.admin-fees.index') }}" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 6l6 6l-6 6" />
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Statistics Cards -->
            <div class="row row-deck row-cards mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-primary text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                            <path d="M12 7v5l3 3" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        Total Transaksi
                                    </div>
                                    <div class="text-muted">
                                        {{ number_format($statistics['total_transactions'] ?? 0) }} transaksi
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-success text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                            <path
                                                d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                            <path d="M16 5l3 3" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        Total Pendapatan Admin
                                    </div>
                                    <div class="text-muted">
                                        Rp {{ number_format($statistics['total_admin_revenue'] ?? 0, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-warning text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M3 3v18h18" />
                                            <path d="M18.7 17l-5.1-5.2l-2.8 3.3l-2.2-2.2l-6.6 8" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        Rata-rata Biaya Admin
                                    </div>
                                    <div class="text-muted">
                                        Rp {{ number_format($statistics['average_admin_fee'] ?? 0, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-info text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />
                                            <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />
                                            <line x1="16" y1="5" x2="19" y2="8" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        Persentase Rata-rata
                                    </div>
                                    <div class="text-muted">
                                        {{ number_format($statistics['average_percentage'] ?? 0, 2) }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row row-deck row-cards mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Grafik Pendapatan Biaya Admin</h3>
                            <div class="card-actions">
                                <div class="dropdown">
                                    <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                            <path d="M12 7v5l3 3" />
                                        </svg>
                                        Periode
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['period' => '7days']) }}">7 Hari
                                            Terakhir</a>
                                        <a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['period' => '30days']) }}">30 Hari
                                            Terakhir</a>
                                        <a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['period' => '90days']) }}">90 Hari
                                            Terakhir</a>
                                        <a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['period' => '1year']) }}">1 Tahun
                                            Terakhir</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="revenue-chart" style="height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Statistics -->
            <div class="row row-deck row-cards">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Statistik Berdasarkan Status</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>Jumlah</th>
                                            <th>Total Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <span class="badge bg-yellow">Pending</span>
                                            </td>
                                            <td>{{ number_format($statistics['status_breakdown']['pending']['count'] ?? 0) }}
                                            </td>
                                            <td>Rp
                                                {{ number_format($statistics['status_breakdown']['pending']['total'] ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="badge bg-green">Paid</span>
                                            </td>
                                            <td>{{ number_format($statistics['status_breakdown']['paid']['count'] ?? 0) }}
                                            </td>
                                            <td>Rp
                                                {{ number_format($statistics['status_breakdown']['paid']['total'] ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="badge bg-red">Failed</span>
                                            </td>
                                            <td>{{ number_format($statistics['status_breakdown']['failed']['count'] ?? 0) }}
                                            </td>
                                            <td>Rp
                                                {{ number_format($statistics['status_breakdown']['failed']['total'] ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="badge bg-blue">Refunded</span>
                                            </td>
                                            <td>{{ number_format($statistics['status_breakdown']['refunded']['count'] ?? 0) }}
                                            </td>
                                            <td>Rp
                                                {{ number_format($statistics['status_breakdown']['refunded']['total'] ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Top 5 Vendor Berdasarkan Biaya Admin</h3>
                        </div>
                        <div class="card-body">
                            @if (isset($statistics['top_vendors']) && count($statistics['top_vendors']) > 0)
                                <div class="table-responsive">
                                    <table class="table table-vcenter">
                                        <thead>
                                            <tr>
                                                <th>Vendor</th>
                                                <th>Jumlah Transaksi</th>
                                                <th>Total Biaya Admin</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($statistics['top_vendors'] as $vendor)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex py-1 align-items-center">
                                                            <div class="flex-fill">
                                                                <div class="font-weight-medium">{{ $vendor['name'] }}
                                                                </div>
                                                                <div class="text-muted">
                                                                    <small>{{ $vendor['email'] }}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ number_format($vendor['transaction_count']) }}</td>
                                                    <td>Rp {{ number_format($vendor['total_admin_fee'], 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="empty">
                                    <div class="empty-img">
                                        <img src="{{ asset('static/illustrations/undraw_printing_invoices_5r4r.svg') }}"
                                            height="64" alt="">
                                    </div>
                                    <p class="empty-title">Belum ada data vendor</p>
                                    <p class="empty-subtitle text-muted">
                                        Data akan muncul setelah ada transaksi biaya admin.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Options -->
            <div class="row row-deck row-cards mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Export Data</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('admin.admin-fees.transactions') }}?export=excel"
                                        class="btn btn-outline-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                            <path
                                                d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                            <path d="M8 11l4 4l4 -4" />
                                        </svg>
                                        Export ke Excel
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('admin.admin-fees.transactions') }}?export=pdf"
                                        class="btn btn-outline-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                            <path
                                                d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                            <path d="M8 11l4 4l4 -4" />
                                        </svg>
                                        Export ke PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Revenue Chart
            const ctx = document.getElementById('revenue-chart').getContext('2d');
            const revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($statistics['chart_data']['labels'] ?? []) !!},
                    datasets: [{
                        label: 'Pendapatan Biaya Admin',
                        data: {!! json_encode($statistics['chart_data']['revenue'] ?? []) !!},
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Pendapatan: Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
