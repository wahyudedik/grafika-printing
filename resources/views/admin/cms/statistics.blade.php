@extends('dev.layouts.app')

@section('title', 'CMS Statistics')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar me-2"></i>CMS Statistics
                        </h3>
                        <div class="card-actions">
                            <a href="{{ route('admin.cms.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to CMS
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            $stats = \App\Models\CmsSetting::getStatistics();
                        @endphp

                        <!-- Overview Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-cogs fa-2x"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h4 class="mb-0">{{ $stats['total'] }}</h4>
                                                <p class="mb-0">Total Settings</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-check-circle fa-2x"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h4 class="mb-0">{{ $stats['active'] }}</h4>
                                                <p class="mb-0">Active Settings</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-pause-circle fa-2x"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h4 class="mb-0">{{ $stats['inactive'] }}</h4>
                                                <p class="mb-0">Inactive Settings</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-layer-group fa-2x"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h4 class="mb-0">{{ count($stats['by_category']) }}</h4>
                                                <p class="mb-0">Categories</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Settings by Category -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Settings by Category</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="categoryChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Settings by Type -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Settings by Type</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="typeChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Category Details -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Category Breakdown</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Category</th>
                                                        <th>Count</th>
                                                        <th>Percentage</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($stats['by_category'] as $category => $count)
                                                        <tr>
                                                            <td>
                                                                <span
                                                                    class="badge bg-primary">{{ ucfirst($category) }}</span>
                                                            </td>
                                                            <td>{{ $count }}</td>
                                                            <td>
                                                                <div class="progress" style="height: 20px;">
                                                                    <div class="progress-bar" role="progressbar"
                                                                        style="width: {{ ($count / $stats['total']) * 100 }}%"
                                                                        aria-valuenow="{{ ($count / $stats['total']) * 100 }}"
                                                                        aria-valuemin="0" aria-valuemax="100">
                                                                        {{ number_format(($count / $stats['total']) * 100, 1) }}%
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('admin.cms.show', $category) }}"
                                                                    class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-edit"></i> Manage
                                                                </a>
                                                            </td>
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
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
