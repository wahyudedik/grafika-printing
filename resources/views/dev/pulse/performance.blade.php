@extends('dev.layouts.app')

@section('title', 'Performance Metrics')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 13a3 3 0 1 0 0 -6a3 3 0 0 0 0 6z" />
                            <path d="M12 1v6" />
                            <path d="M12 17v6" />
                        </svg>
                        Performance Metrics
                    </h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.pulse.index') }}" class="btn btn-primary btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Performance Overview -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <div class="h2 mb-0">
                                        < 100ms</div>
                                            <div class="text-white-50">Average Response Time</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <div class="h2 mb-0">99.9%</div>
                                        <div class="text-white-50">Uptime</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <div class="h2 mb-0">45%</div>
                                        <div class="text-white-50">CPU Usage</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <div class="h2 mb-0">1.2GB</div>
                                        <div class="text-white-50">Memory Usage</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Response Time Chart -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Response Time Trend</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <h4 class="alert-title">Performance Monitoring</h4>
                                            <div class="text-muted">
                                                Grafik ini menunjukkan tren waktu respons aplikasi dalam 24 jam terakhir.
                                            </div>
                                        </div>

                                        <!-- Placeholder untuk chart -->
                                        <div class="chart-container"
                                            style="height: 300px; background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                            <div class="text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted"
                                                    width="48" height="48" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M3 3v18h18" />
                                                    <path d="M18.5 15.5l-3 -3l-2 2l-3 -3l-3 3" />
                                                </svg>
                                                <div class="text-muted mt-2">Chart akan ditampilkan di sini</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Database Performance -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Database Performance</h4>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Query Count:</strong></td>
                                                <td>{{ \DB::getQueryLog() ? count(\DB::getQueryLog()) : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Slow Queries:</strong></td>
                                                <td><span class="badge bg-success">0</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Connection Pool:</strong></td>
                                                <td><span class="badge bg-primary">Active</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Cache Hit Rate:</strong></td>
                                                <td>95%</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Application Performance</h4>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Route Cache:</strong></td>
                                                <td><span class="badge bg-success">Enabled</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>View Cache:</strong></td>
                                                <td><span class="badge bg-success">Enabled</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Config Cache:</strong></td>
                                                <td><span class="badge bg-success">Enabled</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>OPcache:</strong></td>
                                                <td><span class="badge bg-success">Enabled</span></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Recommendations -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Performance Recommendations</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="alert alert-success">
                                                    <h5 class="alert-title">✅ Good Performance</h5>
                                                    <ul class="mb-0">
                                                        <li>Response time under 100ms</li>
                                                        <li>Memory usage within limits</li>
                                                        <li>Database queries optimized</li>
                                                        <li>Cache systems active</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="alert alert-warning">
                                                    <h5 class="alert-title">⚠️ Optimization Tips</h5>
                                                    <ul class="mb-0">
                                                        <li>Consider enabling Redis for sessions</li>
                                                        <li>Implement database indexing</li>
                                                        <li>Use CDN for static assets</li>
                                                        <li>Monitor memory usage trends</li>
                                                    </ul>
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
        </div>
    @endsection
