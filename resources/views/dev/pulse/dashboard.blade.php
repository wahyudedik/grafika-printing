@extends('dev.layouts.app')

@section('title', 'Statistik Server')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laravel Pulse Dashboard</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitoring real-time performa aplikasi</p>
        </div>
        <a href="{{ route('admin.analytics.pulse.statistics') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-colors text-sm font-medium">
            <i class="fas fa-sync-alt"></i>
            Refresh
        </a>
    </div>

    {{-- Status Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Server Status --}}
        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/80">Server Status</p>
                    <p class="text-2xl font-bold mt-1">Online</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-server text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Response Time --}}
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/80">Response Time</p>
                    <p class="text-2xl font-bold mt-1">< 100ms</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-bolt text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Memory Usage --}}
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/80">Memory Usage</p>
                    <p class="text-2xl font-bold mt-1">45%</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-memory text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Active Users --}}
        <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white/80">Active Users</p>
                    <p class="text-2xl font-bold mt-1">24</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Pulse Dashboard Embed --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Real-time Monitoring</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Dashboard monitoring real-time untuk melihat performa aplikasi, request, dan aktivitas sistem.</p>
        </div>
        <div class="p-6">
            <iframe src="{{ route('admin.analytics.pulse') }}" width="100%" height="800" frameborder="0" class="rounded-xl border border-gray-200 dark:border-gray-700"></iframe>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('admin.analytics.pulse.statistics') }}" class="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-pie text-primary-600 dark:text-primary-400"></i>
                    </div>
                    <span class="font-medium text-gray-900 dark:text-white text-sm">Server Statistics</span>
                </a>
                <a href="{{ route('admin.analytics.pulse.performance') }}" class="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tachometer-alt text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <span class="font-medium text-gray-900 dark:text-white text-sm">Performance</span>
                </a>
                <a href="{{ route('admin.analytics.pulse.activity') }}" class="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <span class="font-medium text-gray-900 dark:text-white text-sm">User Activity</span>
                </a>
                <a href="{{ route('admin.analytics.pulse') }}" class="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="w-10 h-10 bg-sky-100 dark:bg-sky-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-desktop text-sky-600 dark:text-sky-400"></i>
                    </div>
                    <span class="font-medium text-gray-900 dark:text-white text-sm">Full Dashboard</span>
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto refresh setiap 30 detik
    setInterval(function() {
        location.reload();
    }, 30000);
</script>
@endpush
@endsection
