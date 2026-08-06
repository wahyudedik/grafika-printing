@extends('layouts.vendor')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Laporan Penjualan Tahunan</h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="#" onclick="window.print();" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-print"></i>
                    Print
                </a>
                <a href="{{ route('vendor.laporan.export-penjualan', ['type' => 'yearly', 'date' => $year]) }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-file-pdf"></i>
                    Export PDF
                </a>
            </div>
        </div>

        {{-- Filter --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Filter</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('vendor.laporan.penjualan-tahunan') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Tahun</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" name="year">
                            @foreach ($years as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:w-32 flex items-end">
                        <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">Terapkan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Summary + Chart --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
            {{-- Summary Card --}}
            <div class="lg:col-span-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Ringkasan Penjualan {{ $year }}</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="bg-gray-50 rounded-lg p-4 text-center">
                                <div class="text-3xl font-bold text-gray-900">{{ $totalTransaksi }}</div>
                                <div class="text-sm text-gray-500 mt-1">Total Transaksi</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4 text-center">
                                <div class="text-3xl font-bold text-gray-900">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
                                <div class="text-sm text-gray-500 mt-1">Total Penjualan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chart Card --}}
            <div class="lg:col-span-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Penjualan Bulanan</h3>
                    </div>
                    <div class="p-6">
                        <div id="chart-penjualan-bulanan"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Customers + Status Distribution --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Pelanggan Terbaik --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Pelanggan Terbaik</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pelanggan</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pembelian</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($pelangganTerbaik as $index => $pelanggan)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $pelanggan->nama }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">Rp {{ number_format($pelanggan->total_pembelian, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-users text-3xl text-gray-300 mb-3"></i>
                                            <p class="text-sm text-gray-500">Tidak ada data</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Status Distribution Chart --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Distribusi Status Transaksi</h3>
                </div>
                <div class="p-6">
                    <div id="chart-status-distribution"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Monthly sales chart
            const penjualanBulananData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                data: [
                    @foreach (range(1, 12) as $month)
                        {{ $penjualanPerBulan[sprintf('%02d', $month)]['total_penjualan'] ?? 0 }},
                    @endforeach
                ]
            };

            const optionsMonthly = {
                series: [{
                    name: 'Penjualan',
                    data: penjualanBulananData.data
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: false,
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 3,
                        columnWidth: '50%',
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: penjualanBulananData.labels,
                    title: {
                        text: 'Bulan'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Total Penjualan (Rp)'
                    },
                    labels: {
                        formatter: function(val) {
                            return "Rp " + val.toLocaleString('id-ID');
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "Rp " + val.toLocaleString('id-ID');
                        }
                    }
                }
            };

            const chartMonthly = new ApexCharts(document.querySelector("#chart-penjualan-bulanan"), optionsMonthly);
            chartMonthly.render();

            // Transaction status distribution chart
            const statusData = {
                labels: ['Pending', 'Processing', 'Quality Check', 'Completed', 'Cancelled'],
                series: [
                    {{ $transaksis->where('status', 'pending')->count() }},
                    {{ $transaksis->where('status', 'processing')->count() }},
                    {{ $transaksis->where('status', 'quality_check')->count() }},
                    {{ $transaksis->where('status', 'completed')->count() }},
                    {{ $transaksis->where('status', 'cancelled')->count() }}
                ]
            };

            const optionsStatus = {
                series: statusData.series,
                chart: {
                    width: 380,
                    type: 'pie',
                },
                labels: statusData.labels,
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                colors: ['#FFC107', '#3498DB', '#9B59B6', '#2ECC71', '#E74C3C']
            };

            const chartStatus = new ApexCharts(document.querySelector("#chart-status-distribution"), optionsStatus);
            chartStatus.render();
        });
    </script>
@endpush
