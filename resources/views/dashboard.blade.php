@extends('layouts.layouts_dashboard')

@section('title', 'Dashboard')
@section('content')
    <!-- User and Vendor Count Widgets -->
    <div class="row row-deck row-cards mb-4">
        <!-- Users Count Widget -->
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Users</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ \App\Models\User::count() }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-green d-block"></span>
                        </div>
                        <div class="col">
                            <a href="{{ route('users.index') }}" class="text-decoration-none">Manage Users</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendors Count Widget -->
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Vendors</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ \App\Models\Vendor::count() }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-blue d-block"></span>
                        </div>
                        <div class="col">
                            <a href="{{ route('vendors.index') }}" class="text-decoration-none">Manage Vendors</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Products Widget -->
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Products</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">256</div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-purple d-block"></span>
                        </div>
                        <div class="col">
                            <a href="#" class="text-decoration-none">Manage Products</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Transactions Widget -->
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Today's Transactions</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">12</div>
                        <div class="me-auto">
                            <span class="text-green d-inline-flex align-items-center lh-1">
                                8% <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 17l6 -6l4 4l8 -8" />
                                    <path d="M14 7l7 0l0 7" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-orange d-block"></span>
                        </div>
                        <div class="col">
                            <a href="#" class="text-decoration-none">View Transactions</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Stats Widgets -->
    <div class="row row-deck row-cards mb-4">
        <!-- Monthly Transactions Widget -->
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Monthly Transactions</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">148</div>
                        <div class="me-auto">
                            <span class="text-green d-inline-flex align-items-center lh-1">
                                12% <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 17l6 -6l4 4l8 -8" />
                                    <path d="M14 7l7 0l0 7" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-yellow d-block"></span>
                        </div>
                        <div class="col">
                            <a href="#" class="text-decoration-none">View Monthly Report</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue Widget -->
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Monthly Revenue</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">Rp 24.5M</div>
                        <div class="me-auto">
                            <span class="text-green d-inline-flex align-items-center lh-1">
                                5% <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 17l6 -6l4 4l8 -8" />
                                    <path d="M14 7l7 0l0 7" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-teal d-block"></span>
                        </div>
                        <div class="col">
                            <a href="#" class="text-decoration-none">View Financial Report</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Order Value Widget -->
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Average Order Value</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">Rp 165K</div>
                        <div class="me-auto">
                            <span class="text-red d-inline-flex align-items-center lh-1">
                                -2% <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 7l6 6l4 -4l8 8" />
                                    <path d="M21 10l0 7l-7 0" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-cyan d-block"></span>
                        </div>
                        <div class="col">
                            <a href="#" class="text-decoration-none">View Analytics</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row row-deck row-cards">
        <!-- Popular Products Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Popular Products</h3>
                </div>
                <div class="card-body">
                    <div id="popular-products-chart" style="height: 250px;"></div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Monthly Revenue (Last 6 Months)</h3>
                </div>
                <div class="card-body">
                    <div id="monthly-revenue-chart" style="height: 250px;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Popular Products Chart
            var popularProductsOptions = {
                series: [{
                    data: [21, 17, 15, 12, 10, 8]
                }],
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: {
                        show: false,
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: true,
                    }
                },
                colors: ['#206bc4'],
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: ['Banner Printing', 'Business Cards', 'Flyers', 'Stickers', 'Posters',
                        'Brochures'
                    ],
                }
            };

            var popularProductsChart = new ApexCharts(document.querySelector("#popular-products-chart"),
                popularProductsOptions);
            popularProductsChart.render();

            // Monthly Revenue Chart
            var monthlyRevenueOptions = {
                series: [{
                    name: 'Revenue',
                    data: [18.2, 21.5, 19.8, 22.7, 24.5, 25.1]
                }],
                chart: {
                    height: 250,
                    type: 'line',
                    toolbar: {
                        show: false,
                    }
                },
                colors: ['#2fb344'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: ['June', 'July', 'August', 'September', 'October', 'November'],
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return "Rp " + val.toFixed(1) + "M";
                        }
                    }
                },
                markers: {
                    size: 5,
                    hover: {
                        size: 7
                    }
                }
            };

            var monthlyRevenueChart = new ApexCharts(document.querySelector("#monthly-revenue-chart"),
                monthlyRevenueOptions);
            monthlyRevenueChart.render();
        });
    </script>
@endpush
