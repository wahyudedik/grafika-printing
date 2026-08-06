@extends('dev.layouts.app')

@section('title', 'User Activity')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">User Activity</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitoring aktivitas pengguna</p>
        </div>
        <a href="{{ route('admin.analytics.pulse') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-colors text-sm font-medium">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>
    </div>

    {{-- Activity Overview Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Active Today --}}
        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl p-5 text-white text-center">
            <p class="text-3xl font-bold">{{ \App\Models\User::where('last_login_at', '>=', now()->subDay())->count() }}</p>
            <p class="text-sm font-medium text-white/80 mt-1">Active Today</p>
        </div>

        {{-- Active This Week --}}
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-5 text-white text-center">
            <p class="text-3xl font-bold">{{ \App\Models\User::where('last_login_at', '>=', now()->subWeek())->count() }}</p>
            <p class="text-sm font-medium text-white/80 mt-1">Active This Week</p>
        </div>

        {{-- New Users This Month --}}
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-5 text-white text-center">
            <p class="text-3xl font-bold">{{ \App\Models\User::where('created_at', '>=', now()->subMonth())->count() }}</p>
            <p class="text-sm font-medium text-white/80 mt-1">New Users This Month</p>
        </div>

        {{-- Total Users --}}
        <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl p-5 text-white text-center">
            <p class="text-3xl font-bold">{{ \App\Models\User::count() }}</p>
            <p class="text-sm font-medium text-white/80 mt-1">Total Users</p>
        </div>
    </div>

    {{-- User Activity Chart --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Activity Trend</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Grafik ini menunjukkan aktivitas pengguna dalam 7 hari terakhir.</p>
        </div>
        <div class="p-6">
            <div class="h-72 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-chart-line text-4xl text-gray-400 dark:text-gray-500"></i>
                    <p class="text-gray-500 dark:text-gray-400 mt-3 text-sm">Activity Chart akan ditampilkan di sini</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activity</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left px-6 py-3 font-medium text-gray-500 dark:text-gray-400">User</th>
                        <th class="text-left px-6 py-3 font-medium text-gray-500 dark:text-gray-400">Role</th>
                        <th class="text-left px-6 py-3 font-medium text-gray-500 dark:text-gray-400">Time</th>
                        <th class="text-left px-6 py-3 font-medium text-gray-500 dark:text-gray-400">IP Address</th>
                        <th class="text-left px-6 py-3 font-medium text-gray-500 dark:text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @php
                        $recentUsers = \App\Models\User::with('vendorUser')
                            ->latest()
                            ->take(10)
                            ->get();
                    @endphp
                    @forelse($recentUsers as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                    <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">{{ substr($user->name, 0, 2) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            @if($user->usertype === 'vendor')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Vendor</span>
                            @elseif($user->usertype === 'user')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400">User</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">{{ ucfirst($user->usertype) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $user->created_at->diffForHumans() }}</td>
                        <td class="px-6 py-3">
                            <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">192.168.1.1</code>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Active</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-users text-4xl text-gray-400 dark:text-gray-500 mb-3"></i>
                                <p class="text-gray-500 dark:text-gray-400">Belum ada aktivitas pengguna</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- User Statistics by Role --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Vendors --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Vendors</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Models\User::where('usertype', 'vendor')->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Vendors</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ \App\Models\User::where('usertype', 'vendor')->where('last_login_at', '>=', now()->subDay())->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Active Today</p>
                </div>
            </div>
        </div>

        {{-- Users --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Users</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Models\User::where('usertype', 'user')->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Users</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ \App\Models\User::where('usertype', 'user')->where('last_login_at', '>=', now()->subDay())->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Active Today</p>
                </div>
            </div>
        </div>

        {{-- Admins --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Admins</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Models\User::where('usertype', 'dev')->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Admins</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ \App\Models\User::where('usertype', 'dev')->where('last_login_at', '>=', now()->subDay())->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Active Today</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Activity Summary --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Top Active Users --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top Active Users</h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach(\App\Models\User::with('vendorUser')->latest()->take(5)->get() as $user)
                <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                            <span class="text-xs font-semibold text-primary-600 dark:text-primary-400">{{ substr($user->name, 0, 2) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400">{{ ucfirst($user->usertype) }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- System Health --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">System Health</h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm text-gray-900 dark:text-white">Database Connections</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Healthy</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm text-gray-900 dark:text-white">Cache System</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Active</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm text-gray-900 dark:text-white">Queue System</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Running</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm text-gray-900 dark:text-white">File Storage</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Available</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
