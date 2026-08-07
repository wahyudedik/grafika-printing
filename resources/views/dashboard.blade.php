@extends('layouts.vendor')

@section('title', 'Dashboard')
@section('content')
    {{-- Stats Widgets --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Users --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="text-sm font-medium text-gray-500 mb-1">Total Users</div>
            <div class="text-3xl font-bold text-gray-900">{{ $userCount }}</div>
            <div class="mt-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <a href="{{ route('vendor.users.index') }}" class="text-sm text-primary-600 hover:underline">Manage Users</a>
            </div>
        </div>

        {{-- Total Vendors --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="text-sm font-medium text-gray-500 mb-1">Total Vendors</div>
            <div class="text-3xl font-bold text-gray-900">{{ $vendorCount }}</div>
            <div class="mt-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                <a href="#" class="text-sm text-primary-600 hover:underline">Manage Vendors</a>
            </div>
        </div>

        {{-- Total Products --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="text-sm font-medium text-gray-500 mb-1">Total Products</div>
            <div class="text-3xl font-bold text-gray-900">{{ $productCount }}</div>
            <div class="mt-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></span>
                <a href="{{ route('vendor.products.index') }}" class="text-sm text-primary-600 hover:underline">Manage Products</a>
            </div>
        </div>

        {{-- Today's Transactions --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="text-sm font-medium text-gray-500 mb-1">Today's Transactions</div>
            <div class="flex items-baseline gap-2">
                <div class="text-3xl font-bold text-gray-900">{{ $todayTransactions }}</div>
                <span class="text-sm font-medium {{ $todayGrowth >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $todayGrowth }}%
                    @if ($todayGrowth >= 0)
                        <i class="fa-solid fa-arrow-up text-xs"></i>
                    @else
                        <i class="fa-solid fa-arrow-down text-xs"></i>
                    @endif
                </span>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                <a href="{{ route('vendor.transactions.index') }}" class="text-sm text-primary-600 hover:underline">View Transactions</a>
            </div>
        </div>
    </div>

    {{-- Inventory Status --}}
    <div class="bg-white rounded-xl shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Inventory Status</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Low Stock Materials --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Low Stock Materials</h4>
                    @if (count($lowStockMaterials) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Material</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($lowStockMaterials as $material)
                                        <tr>
                                            <td class="px-3 py-2 text-sm text-gray-900">{{ $material->nama_bahan }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-600">{{ $material->stock_status_label }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-600">{{ $material->satuan }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No materials with low stock.</p>
                    @endif
                </div>

                {{-- Stats & Order Status --}}
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-red-50 rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-red-600">{{ $outOfStockCount }}</div>
                            <div class="text-xs text-red-600 mt-1">Out of Stock</div>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ $bahanCount }}</div>
                            <div class="text-xs text-blue-600 mt-1">Total Materials</div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Order Status</h4>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                                <span class="text-sm text-gray-600">Pending: <strong>{{ $pendingOrdersCount }}</strong></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                <span class="text-sm text-gray-600">Processing: <strong>{{ $processingOrdersCount }}</strong></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                <span class="text-sm text-gray-600">Completed: <strong>{{ $completedOrdersCount }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Monthly Stats Widgets --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        {{-- Monthly Transactions --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="text-sm font-medium text-gray-500 mb-1">Monthly Transactions</div>
            <div class="flex items-baseline gap-2">
                <div class="text-3xl font-bold text-gray-900">{{ $monthlyTransactions }}</div>
                <span class="text-sm font-medium {{ $monthlyGrowth >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $monthlyGrowth }}%
                    @if ($monthlyGrowth >= 0)
                        <i class="fa-solid fa-arrow-up text-xs"></i>
                    @else
                        <i class="fa-solid fa-arrow-down text-xs"></i>
                    @endif
                </span>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></span>
                <a href="{{ route('vendor.transactions.index') }}" class="text-sm text-primary-600 hover:underline">View Monthly Report</a>
            </div>
        </div>

        {{-- Monthly Revenue --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="text-sm font-medium text-gray-500 mb-1">Monthly Revenue</div>
            <div class="flex items-baseline gap-2">
                <div class="text-3xl font-bold text-gray-900">Rp {{ number_format($monthlyRevenue, 1) }}M</div>
                <span class="text-sm font-medium {{ $monthlyRevenueGrowth >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $monthlyRevenueGrowth }}%
                    @if ($monthlyRevenueGrowth >= 0)
                        <i class="fa-solid fa-arrow-up text-xs"></i>
                    @else
                        <i class="fa-solid fa-arrow-down text-xs"></i>
                    @endif
                </span>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                <a href="{{ route('vendor.laporan.penjualan-bulanan') }}" class="text-sm text-primary-600 hover:underline">View Financial Report</a>
            </div>
        </div>

        {{-- Average Order Value --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="text-sm font-medium text-gray-500 mb-1">Average Order Value</div>
            <div class="flex items-baseline gap-2">
                <div class="text-3xl font-bold text-gray-900">Rp {{ number_format($averageOrderValue, 0) }}K</div>
                <span class="text-sm font-medium {{ $averageOrderValueGrowth >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $averageOrderValueGrowth }}%
                    @if ($averageOrderValueGrowth >= 0)
                        <i class="fa-solid fa-arrow-up text-xs"></i>
                    @else
                        <i class="fa-solid fa-arrow-down text-xs"></i>
                    @endif
                </span>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-500 animate-pulse"></span>
                <a href="{{ route('vendor.laporan.penjualan-harian') }}" class="text-sm text-primary-600 hover:underline">View Analytics</a>
            </div>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="bg-white rounded-xl shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
            <a href="{{ route('vendor.transactions.index') }}" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $recentTransactions = \App\Models\Vendor\Transaksi::with('pelanggan')
                            ->orderBy('tanggal_dibuat', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp

                    @forelse($recentTransactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $transaction->kode }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->pelanggan->nama ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($transaction->tanggal_dibuat)->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format((float) $transaction->total_harga, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($transaction->status == 'pending')
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                                @elseif($transaction->status == 'processing')
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Processing</span>
                                @elseif($transaction->status == 'quality_check')
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-purple-100 text-purple-700">Quality Check</span>
                                @elseif($transaction->status == 'completed')
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Completed</span>
                                @elseif($transaction->status == 'cancelled')
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">Cancelled</span>
                                @else
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-700">{{ ucfirst($transaction->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                <a href="{{ route('vendor.transactions.invoice', $transaction->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-primary-700 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors">Invoice</a>
                                <a href="{{ route('vendor.transactions.show', $transaction->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-primary-700 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">No transactions found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Order Progress --}}
    <div class="bg-white rounded-xl shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Order Progress</h3>
        </div>
        <div class="p-6">
            @php
                $totalOrders = $pendingOrdersCount + $processingOrdersCount + $completedOrdersCount;
                $pendingPercentage = $totalOrders > 0 ? ($pendingOrdersCount / $totalOrders) * 100 : 0;
                $processingPercentage = $totalOrders > 0 ? ($processingOrdersCount / $totalOrders) * 100 : 0;
                $completedPercentage = $totalOrders > 0 ? ($completedOrdersCount / $totalOrders) * 100 : 0;
            @endphp

            {{-- Progress Bar --}}
            <div class="w-full bg-gray-200 rounded-full h-4 mb-6 flex overflow-hidden">
                @if ($pendingPercentage > 0)
                    <div class="bg-yellow-500 h-4 transition-all duration-500" style="width: {{ $pendingPercentage }}%"></div>
                @endif
                @if ($processingPercentage > 0)
                    <div class="bg-blue-500 h-4 transition-all duration-500" style="width: {{ $processingPercentage }}%"></div>
                @endif
                @if ($completedPercentage > 0)
                    <div class="bg-green-500 h-4 transition-all duration-500" style="width: {{ $completedPercentage }}%"></div>
                @endif
            </div>

            {{-- Legend --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                        <span class="text-sm text-gray-600">Pending ({{ $pendingOrdersCount }})</span>
                    </div>
                    <span class="text-sm font-medium text-gray-900">{{ number_format($pendingPercentage, 1) }}%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                        <span class="text-sm text-gray-600">Processing ({{ $processingOrdersCount }})</span>
                    </div>
                    <span class="text-sm font-medium text-gray-900">{{ number_format($processingPercentage, 1) }}%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                        <span class="text-sm text-gray-600">Completed ({{ $completedOrdersCount }})</span>
                    </div>
                    <span class="text-sm font-medium text-gray-900">{{ number_format($completedPercentage, 1) }}%</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Popular Products Chart --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Popular Products</h3>
            </div>
            <div class="p-6">
                <div id="popular-products-chart" style="height: 250px;"></div>
            </div>
        </div>

        {{-- Monthly Revenue Chart --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Monthly Revenue (Last 6 Months)</h3>
            </div>
            <div class="p-6">
                <div id="monthly-revenue-chart" style="height: 250px;"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Pass data to external script
        window.popularProductsData = @json($popularProducts);
        window.revenueData = @json($revenueData);
    </script>
    <script src="{{ asset('js/dashboard-charts.js') }}"></script>
@endpush
