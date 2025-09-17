@extends('dev.layouts.app')

@section('title', 'Statistik Server')

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
                            <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                        </svg>
                        Laravel Pulse Dashboard
                    </h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.pulse.statistics') }}" class="btn btn-primary btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 19c-4.3 0 -8 -3.7 -8 -8s3.7 -8 8 -8s8 3.7 8 8s-3.7 8 -8 8z" />
                                <path d="M15 13l-3 -3l-3 3" />
                            </svg>
                            Refresh
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-fill">
                                            <div class="h3 mb-0">Server Status</div>
                                            <div class="text-white-50">Online</div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M9 12l2 2l4 -4" />
                                                <path d="M21 12c-1 0 -3 -1 -3 -3s2 -3 3 -3s3 1 3 3s-2 3 -3 3" />
                                                <path d="M3 12c1 0 3 -1 3 -3s-2 -3 -3 -3s-3 1 -3 3s2 3 3 3" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-fill">
                                            <div class="h3 mb-0">Response Time</div>
                                            <div class="text-white-50">
                                                < 100ms</div>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M12 13a3 3 0 1 0 0 -6a3 3 0 0 0 0 6z" />
                                                    <path d="M12 1v6" />
                                                    <path d="M12 17v6" />
                                                    <path d="M4.22 4.22l4.24 4.24" />
                                                    <path d="M15.54 15.54l4.24 4.24" />
                                                    <path d="M1 12h6" />
                                                    <path d="M17 12h6" />
                                                    <path d="M4.22 19.78l4.24 -4.24" />
                                                    <path d="M15.54 8.46l4.24 -4.24" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-fill">
                                                <div class="h3 mb-0">Memory Usage</div>
                                                <div class="text-white-50">45%</div>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M6 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-11a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2z" />
                                                    <path d="M8 8h6" />
                                                    <path d="M8 12h6" />
                                                    <path d="M8 16h4" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-fill">
                                                <div class="h3 mb-0">Active Users</div>
                                                <div class="text-white-50">24</div>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <circle cx="9" cy="7" r="4" />
                                                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                                    <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pulse Dashboard Embed -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Real-time Monitoring</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <h4 class="alert-title">Laravel Pulse Dashboard</h4>
                                            <div class="text-muted">
                                                Dashboard monitoring real-time untuk melihat performa aplikasi, request, dan
                                                aktivitas sistem.
                                            </div>
                                        </div>

                                        <!-- Pulse Dashboard akan di-embed di sini -->
                                        <div class="pulse-dashboard">
                                            <iframe src="{{ route('pulse.dashboard') }}" width="100%" height="800"
                                                frameborder="0" style="border: 1px solid #e9ecef; border-radius: 8px;">
                                            </iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Quick Actions</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <a href="{{ route('admin.pulse.statistics') }}"
                                                    class="btn btn-outline-primary w-100">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2"
                                                        width="24" height="24" viewBox="0 0 24 24"
                                                        stroke-width="2" stroke="currentColor" fill="none"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path
                                                            d="M9 19c-4.3 0 -8 -3.7 -8 -8s3.7 -8 8 -8s8 3.7 8 8s-3.7 8 -8 8z" />
                                                        <path d="M15 13l-3 -3l-3 3" />
                                                    </svg>
                                                    Server Statistics
                                                </a>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <a href="{{ route('admin.pulse.performance') }}"
                                                    class="btn btn-outline-success w-100">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2"
                                                        width="24" height="24" viewBox="0 0 24 24"
                                                        stroke-width="2" stroke="currentColor" fill="none"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M12 13a3 3 0 1 0 0 -6a3 3 0 0 0 0 6z" />
                                                        <path d="M12 1v6" />
                                                        <path d="M12 17v6" />
                                                    </svg>
                                                    Performance
                                                </a>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <a href="{{ route('admin.pulse.activity') }}"
                                                    class="btn btn-outline-warning w-100">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2"
                                                        width="24" height="24" viewBox="0 0 24 24"
                                                        stroke-width="2" stroke="currentColor" fill="none"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <circle cx="9" cy="7" r="4" />
                                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                    </svg>
                                                    User Activity
                                                </a>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <a href="{{ route('admin.pulse.index') }}"
                                                    class="btn btn-outline-info w-100">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2"
                                                        width="24" height="24" viewBox="0 0 24 24"
                                                        stroke-width="2" stroke="currentColor" fill="none"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                    </svg>
                                                    Full Dashboard
                                                </a>
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

    @push('scripts')
        <script>
            // Auto refresh setiap 30 detik
            setInterval(function() {
                location.reload();
            }, 30000);
        </script>
    @endpush
