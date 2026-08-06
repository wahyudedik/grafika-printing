@extends('dev.layouts.app')

@section('title', 'Statistik Mediasi')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">Admin Panel</p>
            <h1 class="text-2xl font-bold text-gray-900">Statistik Mediasi</h1>
        </div>
        <a href="{{ route('admin.mediation.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>
</div>

{{-- Ringkasan --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <p class="text-sm font-medium text-gray-500">Total Permintaan</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_requests'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <p class="text-sm font-medium text-gray-500">Pending</p>
        <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $stats['pending_requests'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <p class="text-sm font-medium text-gray-500">Dalam Review</p>
        <p class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['in_review'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <p class="text-sm font-medium text-gray-500">Selesai</p>
        <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['resolved'] }}</p>
    </div>
</div>

{{-- Keputusan --}}
<div class="bg-white rounded-xl shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Ringkasan Keputusan</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-medium text-green-600">Favor Pengguna</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['favor_user'] }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-medium text-blue-600">Favor Vendor</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['favor_vendor'] }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-medium text-yellow-600">Kompromi</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['compromise'] }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-600">Tanpa Kesalahan</p>
                <p class="text-2xl font-bold text-gray-600 mt-1">{{ $stats['no_fault'] }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Persentase --}}
@if($stats['resolved'] > 0)
<div class="bg-white rounded-xl shadow-sm">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Persentase Keputusan (dari {{ $stats['resolved'] }} yang diselesaikan)</h3>
    </div>
    <div class="p-6 space-y-4">
        @php
            $totalDecisions = $stats['favor_user'] + $stats['favor_vendor'] + $stats['compromise'] + $stats['no_fault'];
        @endphp
        @if($totalDecisions > 0)
        <div>
            <div class="flex justify-between mb-1">
                <span class="text-sm text-gray-700">Favor Pengguna</span>
                <span class="text-sm font-medium text-green-600">{{ round(($stats['favor_user'] / $totalDecisions) * 100, 1) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($stats['favor_user'] / $totalDecisions) * 100 }}%"></div>
            </div>
        </div>
        <div>
            <div class="flex justify-between mb-1">
                <span class="text-sm text-gray-700">Favor Vendor</span>
                <span class="text-sm font-medium text-blue-600">{{ round(($stats['favor_vendor'] / $totalDecisions) * 100, 1) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($stats['favor_vendor'] / $totalDecisions) * 100 }}%"></div>
            </div>
        </div>
        <div>
            <div class="flex justify-between mb-1">
                <span class="text-sm text-gray-700">Kompromi</span>
                <span class="text-sm font-medium text-yellow-600">{{ round(($stats['compromise'] / $totalDecisions) * 100, 1) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ ($stats['compromise'] / $totalDecisions) * 100 }}%"></div>
            </div>
        </div>
        <div>
            <div class="flex justify-between mb-1">
                <span class="text-sm text-gray-700">Tanpa Kesalahan</span>
                <span class="text-sm font-medium text-gray-600">{{ round(($stats['no_fault'] / $totalDecisions) * 100, 1) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gray-400 h-2 rounded-full" style="width: {{ ($stats['no_fault'] / $totalDecisions) * 100 }}%"></div>
            </div>
        </div>
        @endif
    </div>
</div>
@endif
@endsection
