@extends('dev.layouts.app')

@section('title', 'CMS Statistics')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-chart-bar text-gray-400"></i>CMS Statistics
        </h1>
        <x-ui.button variant="outline" :href="route('admin.cms.index')">
            <i class="fas fa-arrow-left mr-2"></i>Back to CMS
        </x-ui.button>
    </div>

    @php
        $stats = \App\Models\CmsSetting::getStatistics();
    @endphp

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-5 text-white">
            <div class="flex items-center gap-4">
                <i class="fas fa-cogs text-3xl opacity-80"></i>
                <div>
                    <h4 class="text-2xl font-bold">{{ $stats['total'] }}</h4>
                    <p class="text-blue-100 text-sm">Total Settings</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-5 text-white">
            <div class="flex items-center gap-4">
                <i class="fas fa-check-circle text-3xl opacity-80"></i>
                <div>
                    <h4 class="text-2xl font-bold">{{ $stats['active'] }}</h4>
                    <p class="text-green-100 text-sm">Active Settings</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-5 text-white">
            <div class="flex items-center gap-4">
                <i class="fas fa-pause-circle text-3xl opacity-80"></i>
                <div>
                    <h4 class="text-2xl font-bold">{{ $stats['inactive'] }}</h4>
                    <p class="text-yellow-100 text-sm">Inactive Settings</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl p-5 text-white">
            <div class="flex items-center gap-4">
                <i class="fas fa-layer-group text-3xl opacity-80"></i>
                <div>
                    <h4 class="text-2xl font-bold">{{ count($stats['by_category']) }}</h4>
                    <p class="text-cyan-100 text-sm">Categories</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h5 class="text-sm font-semibold text-gray-900 mb-4">Settings by Category</h5>
            <canvas id="categoryChart" width="400" height="200"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h5 class="text-sm font-semibold text-gray-900 mb-4">Settings by Type</h5>
            <canvas id="typeChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Category Breakdown -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h5 class="text-sm font-semibold text-gray-900">Category Breakdown</h5>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($stats['by_category'] as $category => $count)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">{{ ucfirst($category) }}</span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-900">{{ $count }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-primary-500 h-2 rounded-full" style="width: {{ ($count / $stats['total']) * 100 }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500 whitespace-nowrap">{{ number_format(($count / $stats['total']) * 100, 1) }}%</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <a href="{{ route('admin.cms.show', $category) }}" class="text-xs font-medium text-primary-600 hover:text-primary-800">
                                    <i class="fas fa-edit mr-1"></i>Manage
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryChart = new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($stats['by_category'])) !!},
                datasets: [{
                    data: {!! json_encode(array_values($stats['by_category'])) !!},
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40',
                        '#FF6384',
                        '#C9CBCF',
                        '#4BC0C0'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Type Chart
        const typeCtx = document.getElementById('typeChart').getContext('2d');
        const typeChart = new Chart(typeCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($stats['by_type'])) !!},
                datasets: [{
                    label: 'Settings Count',
                    data: {!! json_encode(array_values($stats['by_type'])) !!},
                    backgroundColor: '#36A2EB',
                    borderColor: '#36A2EB',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
