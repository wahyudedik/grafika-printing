@extends('dev.layouts.app')

@section('title', 'Server Statistics')

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
                            <path d="M9 19c-4.3 0 -8 -3.7 -8 -8s3.7 -8 8 -8s8 3.7 8 8s-3.7 8 -8 8z" />
                            <path d="M15 13l-3 -3l-3 3" />
                        </svg>
                        Server Statistics
                    </h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.analytics.pulse') }}" class="btn btn-primary btn-sm">
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
                    <!-- Server Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Server Information</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>PHP Version:</strong></td>
                                            <td>{{ PHP_VERSION }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Laravel Version:</strong></td>
                                            <td>{{ app()->version() }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Server Software:</strong></td>
                                            <td>{{ $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Operating System:</strong></td>
                                            <td>{{ PHP_OS }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Memory Limit:</strong></td>
                                            <td>{{ ini_get('memory_limit') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Max Execution Time:</strong></td>
                                            <td>{{ ini_get('max_execution_time') }}s</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Application Statistics</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Environment:</strong></td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ app()->environment() === 'production' ? 'success' : 'warning' }}">
                                                    {{ app()->environment() }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Debug Mode:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ config('app.debug') ? 'danger' : 'success' }}">
                                                    {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Cache Driver:</strong></td>
                                            <td>{{ config('cache.default') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Session Driver:</strong></td>
                                            <td>{{ config('session.driver') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Queue Driver:</strong></td>
                                            <td>{{ config('queue.default') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Database:</strong></td>
                                            <td>{{ config('database.default') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Metrics -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Performance Metrics</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <div class="card bg-primary text-white">
                                                <div class="card-body text-center">
                                                    <div class="h2 mb-0">
                                                        {{ number_format(memory_get_usage(true) / 1024 / 1024, 2) }} MB
                                                    </div>
                                                    <div class="text-white-50">Memory Usage</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="card bg-success text-white">
                                                <div class="card-body text-center">
                                                    <div class="h2 mb-0">
                                                        {{ number_format(memory_get_peak_usage(true) / 1024 / 1024, 2) }}
                                                        MB</div>
                                                    <div class="text-white-50">Peak Memory</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="card bg-warning text-white">
                                                <div class="card-body text-center">
                                                    <div class="h2 mb-0">
                                                        {{ number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3) }}s
                                                    </div>
                                                    <div class="text-white-50">Load Time</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="card bg-info text-white">
                                                <div class="card-body text-center">
                                                    <div class="h2 mb-0">{{ count(get_included_files()) }}</div>
                                                    <div class="text-white-50">Files Loaded</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Database Statistics -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Database Statistics</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <div class="h3 mb-0">{{ \App\Models\User::count() }}</div>
                                                    <div class="text-muted">Total Users</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <div class="h3 mb-0">{{ \App\Models\Vendor::count() }}</div>
                                                    <div class="text-muted">Total Vendors</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <div class="h3 mb-0">{{ \App\Models\Auction::count() }}</div>
                                                    <div class="text-muted">Total Auctions</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Health -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">System Health</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Disk Space</h5>
                                            <div class="progress mb-3">
                                                <div class="progress-bar" role="progressbar" style="width: 45%"></div>
                                            </div>
                                            <small class="text-muted">45% used (2.3 GB / 5.1 GB)</small>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Memory Usage</h5>
                                            <div class="progress mb-3">
                                                <div class="progress-bar bg-warning" role="progressbar"
                                                    style="width: 65%"></div>
                                            </div>
                                            <small class="text-muted">65% used (1.2 GB / 1.8 GB)</small>
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
