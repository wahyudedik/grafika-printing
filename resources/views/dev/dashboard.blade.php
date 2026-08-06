@extends('dev.layouts.app')

@section('title', 'Dashboard')
@section('content')
    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Users --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Total Users</p>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</span>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    Manage Users
                </a>
            </div>
        </div>

        {{-- Total Vendors --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Total Vendors</p>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-bold text-gray-900">{{ $stats['total_vendors'] }}</span>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <a href="{{ route('admin.vendors.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    Manage Vendors
                </a>
            </div>
        </div>

        {{-- Total Auctions --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Total Auctions</p>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-bold text-gray-900">{{ $stats['total_auctions'] }}</span>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <a href="{{ route('admin.auctions.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Manage Auctions
                </a>
            </div>
        </div>

        {{-- Total Revenue --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Total Revenue</p>
            <div class="flex items-end justify-between">
                <span class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</span>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <a href="{{ route('admin.analytics.vendor-revenue') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    View Revenue
                </a>
            </div>
        </div>
    </div>

    {{-- Payment Issues Alert --}}
    @if ($stats['payment_issues'] > 0 || $stats['expired_payments'] > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-amber-600"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-amber-800">⚠️ Payment Issues Detected!</h3>
                    <p class="text-sm text-amber-700 mt-1">There are payment issues that need attention:</p>
                    <ul class="mt-2 space-y-1 text-sm text-amber-700">
                        @if ($stats['payment_issues'] > 0)
                            <li class="flex items-center gap-2">
                                <i class="fas fa-clock text-xs"></i>
                                <strong>{{ $stats['payment_issues'] }}</strong> auctions stuck in waiting payment for more than 24 hours
                            </li>
                        @endif
                        @if ($stats['expired_payments'] > 0)
                            <li class="flex items-center gap-2">
                                <i class="fas fa-times-circle text-xs"></i>
                                <strong>{{ $stats['expired_payments'] }}</strong> expired payment links
                            </li>
                        @endif
                    </ul>
                    <div class="mt-3">
                        <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition-colors">
                            <i class="fas fa-cog text-xs"></i> Manage Payment Issues
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Charts Row --}}
    <div class="grid lg:grid-cols-3 gap-6 mb-6">
        {{-- Revenue Chart --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Monthly Revenue Trend</h3>
            </div>
            <div class="p-4">
                <div id="revenueChart" style="height: 300px;"></div>
            </div>
        </div>

        {{-- Auction Status Distribution --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Auction Status Distribution</h3>
            </div>
            <div class="p-4">
                <div id="auctionStatusChart" style="height: 300px;"></div>
            </div>
        </div>
    </div>

    {{-- Payment Issues and Recent Activities --}}
    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        {{-- Payment Issues --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Payment Issues</h3>
            </div>
            <div class="p-4 space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex items-center gap-3 p-3 bg-amber-50 rounded-lg border border-amber-200">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-amber-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Stuck Payments</p>
                            <p class="text-xs text-gray-500">{{ $stats['payment_issues'] }} auctions</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-red-50 rounded-lg border border-red-200">
                        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Expired Payments</p>
                            <p class="text-xs text-gray-500">{{ $stats['expired_payments'] }} links</p>
                        </div>
                    </div>
                </div>
                <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-cog text-xs"></i> Manage All Issues
                </a>
            </div>
        </div>

        {{-- Recent Activities --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Recent Activities</h3>
            </div>
            <div class="p-4">
                <div class="space-y-4">
                    @foreach ($recentActivities as $activity)
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="text-xs font-medium text-gray-500 whitespace-nowrap">{{ $activity['time']->format('H:i') }}</div>
                                <div class="w-3 h-3 rounded-full mt-1 flex-shrink-0
                                    {{ $activity['type'] === 'auction_created' ? 'bg-blue-500' : ($activity['type'] === 'payment_created' ? 'bg-green-500' : 'bg-amber-500') }}">
                                </div>
                            </div>
                            <div class="flex-1 pb-4 border-b border-gray-100 last:border-0">
                                <p class="text-sm font-medium text-gray-900">{{ $activity['message'] }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">by {{ $activity['user'] }} • {{ $activity['time']->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Vendor Performance --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900">Top Performing Vendors</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Vendor</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Total Earnings</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Current Balance</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Success Rate</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Total Bids</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($vendorPerformance as $vendor)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-sm font-bold flex-shrink-0">
                                        {{ substr($vendor['name'], 0, 1) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $vendor['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-700">Rp {{ number_format($vendor['total_earnings'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-gray-700">Rp {{ number_format($vendor['current_balance'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $vendor['success_rate'] >= 50 ? 'bg-green-100 text-green-800' : ($vendor['success_rate'] >= 25 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $vendor['success_rate'] }}%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $vendor['total_bids'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
