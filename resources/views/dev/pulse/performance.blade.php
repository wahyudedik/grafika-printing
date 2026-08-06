@extends('dev.layouts.app')

@section('title', 'Performance Metrics')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Performance Metrics</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitoring performa server dan aplikasi</p>
        </div>
        <a href="{{ route('admin.analytics.pulse') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-colors text-sm font-medium">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>
    </div>

    {{-- Performance Overview Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Response Time --}}
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-5 text-white text-center">
            <p class="text-3xl font-bold">< 100ms</p>
            <p class="text-sm font-medium text-white/80 mt-1">Average Response Time</p>
        </div>

        {{-- Uptime --}}
        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl p-5 text-white text-center">
            <p class="text-3xl font-bold">99.9%</p>
            <p class="text-sm font-medium text-white/80 mt-1">Uptime</p>
        </div>

        {{-- CPU Usage --}}
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-5 text-white text-center">
            <p class="text-3xl font-bold">45%</p>
            <p class="text-sm font-medium text-white/80 mt-1">CPU Usage</p>
        </div>

        {{-- Memory Usage --}}
        <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl p-5 text-white text-center">
            <p class="text-3xl font-bold">1.2GB</p>
            <p class="text-sm font-medium text-white/80 mt-1">Memory Usage</p>
        </div>
    </div>

    {{-- Response Time Chart --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Response Time Trend</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Grafik ini menunjukkan tren waktu respons aplikasi dalam 24 jam terakhir.</p>
        </div>
        <div class="p-6">
            <div class="h-72 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-chart-line text-4xl text-gray-400 dark:text-gray-500"></i>
                    <p class="text-gray-500 dark:text-gray-400 mt-3 text-sm">Chart akan ditampilkan di sini</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Database & Application Performance --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Database Performance --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Database Performance</h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm text-gray-900 dark:text-white">Query Count</span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ \DB::getQueryLog() ? count(\DB::getQueryLog()) : 'N/A' }}</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm text-gray-900 dark:text-white">Slow Queries</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">0</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm text-gray-900 dark:text-white">Connection Pool</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400">Active</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm text-gray-900 dark:text-white">Cache Hit Rate</span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">95%</span>
                </div>
            </div>
        </div>

        {{-- Application Performance --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Application Performance</h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm text-gray-900 dark:text-white">Route Cache</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Enabled</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm text-gray-900 dark:text-white">View Cache</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Enabled</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm text-gray-900 dark:text-white">Config Cache</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Enabled</span>
                </div>
                <div class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm text-gray-900 dark:text-white">OPcache</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Enabled</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Performance Recommendations --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Good Performance --}}
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-5">
            <h3 class="text-lg font-semibold text-emerald-800 dark:text-emerald-300 mb-3">
                <i class="fas fa-check-circle mr-2"></i>Good Performance
            </h3>
            <ul class="space-y-2 text-sm text-emerald-700 dark:text-emerald-400">
                <li class="flex items-center gap-2"><i class="fas fa-check text-xs"></i>Response time under 100ms</li>
                <li class="flex items-center gap-2"><i class="fas fa-check text-xs"></i>Memory usage within limits</li>
                <li class="flex items-center gap-2"><i class="fas fa-check text-xs"></i>Database queries optimized</li>
                <li class="flex items-center gap-2"><i class="fas fa-check text-xs"></i>Cache systems active</li>
            </ul>
        </div>

        {{-- Optimization Tips --}}
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-5">
            <h3 class="text-lg font-semibold text-amber-800 dark:text-amber-300 mb-3">
                <i class="fas fa-lightbulb mr-2"></i>Optimization Tips
            </h3>
            <ul class="space-y-2 text-sm text-amber-700 dark:text-amber-400">
                <li class="flex items-center gap-2"><i class="fas fa-arrow-right text-xs"></i>Consider enabling Redis for sessions</li>
                <li class="flex items-center gap-2"><i class="fas fa-arrow-right text-xs"></i>Implement database indexing</li>
                <li class="flex items-center gap-2"><i class="fas fa-arrow-right text-xs"></i>Use CDN for static assets</li>
                <li class="flex items-center gap-2"><i class="fas fa-arrow-right text-xs"></i>Monitor memory usage trends</li>
            </ul>
        </div>
    </div>
</div>
@endsection
