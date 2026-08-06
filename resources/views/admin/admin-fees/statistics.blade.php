@extends('dev.layouts.app')

@section('title', 'Statistik Biaya Admin')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-gray-500">Pengaturan</p>
            <h1 class="text-2xl font-bold text-gray-900">Statistik Biaya Admin</h1>
        </div>
        <a href="{{ route('admin.admin-fees.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-primary-100 flex items-center justify-center">
                    <i class="fa-solid fa-clock-rotate-left text-primary-600 text-xl"></i>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Total Transaksi</div>
                    <div class="text-xl font-bold text-gray-900">{{ number_format($statistics['total_transactions'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
                    <i class="fa-solid fa-coins text-green-600 text-xl"></i>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Total Pendapatan Admin</div>
                    <div class="text-xl font-bold text-gray-900">Rp {{ number_format($statistics['total_admin_revenue'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-amber-600 text-xl"></i>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Rata-rata Biaya Admin</div>
                    <div class="text-xl font-bold text-gray-900">Rp {{ number_format($statistics['average_admin_fee'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-cyan-100 flex items-center justify-center">
                    <i class="fa-solid fa-percent text-cyan-600 text-xl"></i>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Persentase Rata-rata</div>
                    <div class="text-xl font-bold text-gray-900">{{ number_format($statistics['average_percentage'] ?? 0, 2) }}%</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Revenue Chart --}}
    <div class="bg-white rounded-xl shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Grafik Pendapatan Biaya Admin</h3>
            <div class="relative" x-data="{ openPeriod: false }" @click.away="openPeriod = false">
                <button class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors" @click="openPeriod = !openPeriod">
                    <i class="fa-solid fa-clock"></i> Periode <i class="fa-solid fa-chevron-down text-xs"></i>
                </button>
                <div x-show="openPeriod" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                    <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" href="{{ request()->fullUrlWithQuery(['period' => '7days']) }}">7 Hari Terakhir</a>
                    <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" href="{{ request()->fullUrlWithQuery(['period' => '30days']) }}">30 Hari Terakhir</a>
                    <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" href="{{ request()->fullUrlWithQuery(['period' => '90days']) }}">90 Hari Terakhir</a>
                    <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" href="{{ request()->fullUrlWithQuery(['period' => '1year']) }}">1 Tahun Terakhir</a>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div id="revenue-chart" style="height: 300px;"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- Status Breakdown --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Statistik Berdasarkan Status</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900">{{ number_format($statistics['status_breakdown']['pending']['count'] ?? 0) }} transaksi</div>
                            <div class="text-xs text-gray-500">Rp {{ number_format($statistics['status_breakdown']['pending']['total'] ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Paid</span>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900">{{ number_format($statistics['status_breakdown']['paid']['count'] ?? 0) }} transaksi</div>
                            <div class="text-xs text-gray-500">Rp {{ number_format($statistics['status_breakdown']['paid']['total'] ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">Failed</span>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900">{{ number_format($statistics['status_breakdown']['failed']['count'] ?? 0) }} transaksi</div>
                            <div class="text-xs text-gray-500">Rp {{ number_format($statistics['status_breakdown']['failed']['total'] ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Refunded</span>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900">{{ number_format($statistics['status_breakdown']['refunded']['count'] ?? 0) }} transaksi</div>
                            <div class="text-xs text-gray-500">Rp {{ number_format($statistics['status_breakdown']['refunded']['total'] ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Vendors --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Top 5 Vendor Berdasarkan Biaya Admin</h3>
            </div>
            <div class="p-6">
                @if (isset($statistics['top_vendors']) && count($statistics['top_vendors']) > 0)
                    <div class="space-y-3">
                        @foreach ($statistics['top_vendors'] as $vendor)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $vendor['name'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $vendor['email'] }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">{{ number_format($vendor['transaction_count']) }} transaksi</div>
                                    <div class="text-xs text-gray-500">Rp {{ number_format($vendor['total_admin_fee'], 0, ',', '.') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fa-solid fa-users text-gray-300 text-3xl mb-3"></i>
                        <p class="text-sm text-gray-500">Belum ada data vendor</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Export --}}
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Export Data</h3>
        </div>
        <div class="p-6">
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.admin-fees.transactions') }}?export=excel"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <i class="fa-solid fa-file-excel"></i> Export ke Excel
                </a>
                <a href="{{ route('admin.admin-fees.transactions') }}?export=pdf"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                    <i class="fa-solid fa-file-pdf"></i> Export ke PDF
                </a>
            </div>
        </div>
    </div>
@endsection
