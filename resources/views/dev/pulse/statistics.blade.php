@extends('dev.layouts.app')

@section('title', 'Server Statistics')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Server Statistics</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Informasi detail server dan aplikasi</p>
        </div>
        <a href="{{ route('admin.analytics.pulse') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-colors text-sm font-medium">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>
    </div>

    {{-- Server Info & Application Stats --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Server Information --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Server Information</h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">PHP Version</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ PHP_VERSION }}</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Laravel Version</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ app()->version() }}</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Server Software</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' }}</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Operating System</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ PHP_OS }}</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Memory Limit</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ ini_get('memory_limit') }}</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Max Execution Time</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ ini_get('max_execution_time') }}s</span>
                </div>
            </div>
        </div>

        {{-- Application Statistics --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Application Statistics</h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Environment</span>
                    @if(app()->environment() === 'production')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">production</span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">{{ app()->environment() }}</span>
                    @endif
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Debug Mode</span>
                    @if(config('app.debug'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">Enabled</span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Disabled</span>
                    @endif
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Cache Driver</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ config('cache.default') }}</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Session Driver</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ config('session.driver') }}</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Queue Driver</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ config('queue.default') }}</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Database</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ config('database.default') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Performance Metrics --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Performance Metrics</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Memory Usage --}}
                <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl p-5 text-white text-center">
                    <p class="text-2xl font-bold">{{ number_format(memory_get_usage(true) / 1024 / 1024, 2) }} MB</p>
                    <p class="text-sm font-medium text-white/80 mt-1">Memory Usage</p>
                </div>

                {{-- Peak Memory --}}
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-5 text-white text-center">
                    <p class="text-2xl font-bold">{{ number_format(memory_get_peak_usage(true) / 1024 / 1024, 2) }} MB</p>
                    <p class="text-sm font-medium text-white/80 mt-1">Peak Memory</p>
                </div>

                {{-- Load Time --}}
                <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-5 text-white text-center">
                    <p class="text-2xl font-bold">{{ number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3) }}s</p>
                    <p class="text-sm font-medium text-white/80 mt-1">Load Time</p>
                </div>

                {{-- Files Loaded --}}
                <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl p-5 text-white text-center">
                    <p class="text-2xl font-bold">{{ count(get_included_files()) }}</p>
                    <p class="text-sm font-medium text-white/80 mt-1">Files Loaded</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Database Statistics --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Database Statistics</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="text-center p-4 border border-gray-200 dark:border-gray-700 rounded-xl">
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ \App\Models\User::count() }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Users</p>
                </div>
                <div class="text-center p-4 border border-gray-200 dark:border-gray-700 rounded-xl">
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ \App\Models\Vendor::count() }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Vendors</p>
                </div>
                <div class="text-center p-4 border border-gray-200 dark:border-gray-700 rounded-xl">
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ \App\Models\Auction::count() }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Auctions</p>
                </div>
            </div>
        </div>
    </div>

    {{-- System Health --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">System Health</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Disk Space --}}
                <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Disk Space</h4>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="bg-primary-500 h-3 rounded-full" style="width: 45%"></div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">45% used (2.3 GB / 5.1 GB)</p>
                </div>

                {{-- Memory Usage --}}
                <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Memory Usage</h4>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="bg-amber-500 h-3 rounded-full" style="width: 65%"></div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">65% used (1.2 GB / 1.8 GB)</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
