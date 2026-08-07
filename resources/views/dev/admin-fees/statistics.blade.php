@extends('dev.layouts.app')

@section('title', 'Statistik Biaya Admin')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-900">Statistik Biaya Admin</h1>
        <x.ui.button href="{{ route('admin.admin-fees.index') }}" variant="outline">
            <i class="fas fa-times mr-1"></i>Kembali ke Pengaturan
        </x.ui.button>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                    class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                    class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div class="flex items-end">
                <x.ui.button type="submit" variant="primary">
                    <i class="fas fa-search mr-1"></i>Filter
                </x.ui.button>
            </div>
        </form>
    </div>

    <!-- Statistics Cards Row 1 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Transaksi</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($statistics['total_transactions']) }}</p>
            <p class="text-xs text-gray-500 mt-1">Transaksi biaya admin</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Nilai Lelang</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">Rp {{ number_format($statistics['total_auction_amount'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">Nilai total lelang</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya Admin</p>
            <p class="text-2xl font-bold text-yellow-600 mt-2">Rp {{ number_format($statistics['total_admin_fees'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">Pendapatan admin</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya Payment</p>
            <p class="text-2xl font-bold text-cyan-600 mt-2">Rp {{ number_format($statistics['total_payment_fees'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">Biaya payment gateway</p>
        </div>
    </div>

    <!-- Statistics Cards Row 2 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Admin Menerima</p>
            <p class="text-2xl font-bold text-green-600 mt-2">Rp {{ number_format($statistics['total_admin_receives'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">Total yang diterima admin</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Vendor Menerima</p>
            <p class="text-2xl font-bold text-primary-600 mt-2">Rp {{ number_format($statistics['total_vendor_receives'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">Total yang diterima vendor</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Rata-rata Biaya Admin</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $statistics['average_admin_fee_percentage'] }}%</p>
            <p class="text-xs text-gray-500 mt-1">Persentase rata-rata</p>
        </div>
    </div>

    <!-- Statistics Cards Row 3 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Rata-rata Nilai Transaksi</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">Rp {{ number_format($statistics['average_transaction_amount'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">Nilai rata-rata per transaksi</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Periode Laporan</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
            <p class="text-xs text-gray-500 mt-1">Rentang waktu laporan</p>
        </div>
    </div>
@endsection
