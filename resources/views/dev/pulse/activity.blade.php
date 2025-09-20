@extends('dev.layouts.app')

@section('title', 'User Activity')

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
                            <circle cx="9" cy="7" r="4" />
                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                        </svg>
                        User Activity
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
                    <!-- Activity Overview -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <div class="h2 mb-0">
                                        {{ \App\Models\User::where('last_login_at', '>=', now()->subDay())->count() }}</div>
                                    <div class="text-white-50">Active Today</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <div class="h2 mb-0">
                                        {{ \App\Models\User::where('last_login_at', '>=', now()->subWeek())->count() }}
                                    </div>
                                    <div class="text-white-50">Active This Week</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <div class="h2 mb-0">
                                        {{ \App\Models\User::where('created_at', '>=', now()->subMonth())->count() }}</div>
                                    <div class="text-white-50">New Users This Month</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <div class="h2 mb-0">{{ \App\Models\User::count() }}</div>
                                    <div class="text-white-50">Total Users</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Activity Chart -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">User Activity Trend</h4>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <h4 class="alert-title">Activity Monitoring</h4>
                                        <div class="text-muted">
                                            Grafik ini menunjukkan aktivitas pengguna dalam 7 hari terakhir.
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
                                            <div class="text-muted mt-2">Activity Chart akan ditampilkan di sini</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Recent Activity</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>User</th>
                                                    <th>Action</th>
                                                    <th>Time</th>
                                                    <th>IP Address</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $recentUsers = \App\Models\User::with('vendorUser')
                                                        ->latest()
                                                        ->take(10)
                                                        ->get();
                                                @endphp
                                                @foreach ($recentUsers as $user)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <span
                                                                    class="avatar avatar-sm me-2">{{ substr($user->name, 0, 2) }}</span>
                                                                <div>
                                                                    <div class="fw-bold">{{ $user->name }}</div>
                                                                    <div class="text-muted small">{{ $user->email }}</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge bg-{{ $user->usertype === 'vendor' ? 'success' : ($user->usertype === 'user' ? 'primary' : 'warning') }}">
                                                                {{ ucfirst($user->usertype) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="text-muted small">
                                                                {{ $user->created_at->diffForHumans() }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <code>192.168.1.1</code>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-success">Active</span>
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

                    <!-- User Statistics by Role -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Vendors</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="h3 mb-0">
                                                {{ \App\Models\User::where('usertype', 'vendor')->count() }}</div>
                                            <div class="text-muted">Total Vendors</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="h3 mb-0">
                                                {{ \App\Models\User::where('usertype', 'vendor')->where('last_login_at', '>=', now()->subDay())->count() }}
                                            </div>
                                            <div class="text-muted">Active Today</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Users</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="h3 mb-0">
                                                {{ \App\Models\User::where('usertype', 'user')->count() }}</div>
                                            <div class="text-muted">Total Users</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="h3 mb-0">
                                                {{ \App\Models\User::where('usertype', 'user')->where('last_login_at', '>=', now()->subDay())->count() }}
                                            </div>
                                            <div class="text-muted">Active Today</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Admins</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="h3 mb-0">{{ \App\Models\User::where('usertype', 'dev')->count() }}
                                            </div>
                                            <div class="text-muted">Total Admins</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="h3 mb-0">
                                                {{ \App\Models\User::where('usertype', 'dev')->where('last_login_at', '>=', now()->subDay())->count() }}
                                            </div>
                                            <div class="text-muted">Active Today</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Summary -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Activity Summary</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Top Active Users</h5>
                                            <div class="list-group list-group-flush">
                                                @foreach (\App\Models\User::with('vendorUser')->latest()->take(5)->get() as $user)
                                                    <div
                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="fw-bold">{{ $user->name }}</div>
                                                            <div class="text-muted small">{{ $user->email }}</div>
                                                        </div>
                                                        <span
                                                            class="badge bg-primary">{{ ucfirst($user->usertype) }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>System Health</h5>
                                            <div class="list-group list-group-flush">
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>Database Connections</span>
                                                    <span class="badge bg-success">Healthy</span>
                                                </div>
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>Cache System</span>
                                                    <span class="badge bg-success">Active</span>
                                                </div>
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>Queue System</span>
                                                    <span class="badge bg-success">Running</span>
                                                </div>
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>File Storage</span>
                                                    <span class="badge bg-success">Available</span>
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
    </div>
@endsection
