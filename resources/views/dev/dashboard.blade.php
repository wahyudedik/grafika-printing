@extends('dev.layouts.app')

@section('title', 'Dashboard')
@section('content')
    <!-- Statistics Cards -->
    <div class="row row-deck row-cards mb-4">
        <!-- Total Users -->
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Users</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $stats['total_users'] }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-green d-block"></span>
                        </div>
                        <div class="col">
                            <a href="{{ route('admin.users.index') }}" class="text-decoration-none">Manage Users</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Vendors -->
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Vendors</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $stats['total_vendors'] }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-blue d-block"></span>
                        </div>
                        <div class="col">
                            <a href="{{ route('admin.vendors.index') }}" class="text-decoration-none">Manage Vendors</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Auctions -->
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Auctions</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $stats['total_auctions'] }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-yellow d-block"></span>
                        </div>
                        <div class="col">
                            <a href="{{ route('admin.auctions.index') }}" class="text-decoration-none">Manage Auctions</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Revenue</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-green d-block"></span>
                        </div>
                        <div class="col">
                            <a href="{{ route('admin.analytics.vendor-revenue') }}" class="text-decoration-none">View
                                Revenue</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Issues Alert -->
    @if ($stats['payment_issues'] > 0 || $stats['expired_payments'] > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading">⚠️ Payment Issues Detected!</h4>
                    <p>There are payment issues that need attention:</p>
                    <ul class="mb-0">
                        @if ($stats['payment_issues'] > 0)
                            <li><strong>{{ $stats['payment_issues'] }}</strong> auctions stuck in waiting payment for more
                                than 24 hours</li>
                        @endif
                        @if ($stats['expired_payments'] > 0)
                            <li><strong>{{ $stats['expired_payments'] }}</strong> expired payment links</li>
                        @endif
                    </ul>
                    <hr>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-warning">Manage Payment
                        Issues</a>
                </div>
            </div>
        </div>
    @endif

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Revenue Chart -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Monthly Revenue Trend</h3>
                </div>
                <div class="card-body">
                    <div id="revenueChart" style="height: 300px;"></div>
                </div>
            </div>
        </div>

        <!-- Auction Status Distribution -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Auction Status Distribution</h3>
                </div>
                <div class="card-body">
                    <div id="auctionStatusChart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Issues and Recent Activities -->
    <div class="row mb-4">
        <!-- Payment Issues -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Issues</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-warning text-white avatar">
                                                <i class="fas fa-clock"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                Stuck Payments
                                            </div>
                                            <div class="text-muted">
                                                {{ $stats['payment_issues'] }} auctions
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-danger text-white avatar">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                Expired Payments
                                            </div>
                                            <div class="text-muted">
                                                {{ $stats['expired_payments'] }} links
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-primary">Manage All
                            Issues</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Activities</h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach ($recentActivities as $activity)
                            <div class="timeline-item">
                                <div class="timeline-item-marker">
                                    <div class="timeline-item-marker-text">
                                        {{ $activity['time']->format('H:i') }}
                                    </div>
                                    <div
                                        class="timeline-item-marker-indicator bg-{{ $activity['type'] === 'auction_created' ? 'blue' : ($activity['type'] === 'payment_created' ? 'green' : 'orange') }}">
                                    </div>
                                </div>
                                <div class="timeline-item-content">
                                    <div class="timeline-item-heading">
                                        {{ $activity['message'] }}
                                    </div>
                                    <div class="text-muted">
                                        by {{ $activity['user'] }} • {{ $activity['time']->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor Performance -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top Performing Vendors</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Vendor</th>
                                    <th>Total Earnings</th>
                                    <th>Current Balance</th>
                                    <th>Success Rate</th>
                                    <th>Total Bids</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vendorPerformance as $vendor)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    {{ substr($vendor['name'], 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-weight-medium">{{ $vendor['name'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>Rp {{ number_format($vendor['total_earnings'], 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($vendor['current_balance'], 0, ',', '.') }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $vendor['success_rate'] >= 50 ? 'success' : ($vendor['success_rate'] >= 25 ? 'warning' : 'danger') }}">
                                                {{ $vendor['success_rate'] }}%
                                            </span>
                                        </td>
                                        <td>{{ $vendor['total_bids'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: @json($revenueChartData['months']),
                    datasets: [{
                        label: 'Revenue (Rp)',
                        data: @json($revenueChartData['revenue']),
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
                                    return 'Revenue: Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });

            // Auction Status Chart
            const statusCtx = document.getElementById('auctionStatusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Active', 'Waiting Payment', 'Paid', 'Completed', 'Closed', 'Rejected'],
                    datasets: [{
                        data: [
                            @json($auctionStatusDistribution['active']),
                            @json($auctionStatusDistribution['waiting_payment']),
                            @json($auctionStatusDistribution['paid']),
                            @json($auctionStatusDistribution['completed']),
                            @json($auctionStatusDistribution['closed']),
                            @json($auctionStatusDistribution['rejected'])
                        ],
                        backgroundColor: [
                            '#28a745',
                            '#ffc107',
                            '#17a2b8',
                            '#6f42c1',
                            '#6c757d',
                            '#dc3545'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
    </script>
@endpush
