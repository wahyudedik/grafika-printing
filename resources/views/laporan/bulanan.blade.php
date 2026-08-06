@extends('layouts.vendor')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Laporan Penjualan Bulanan</h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="#" onclick="window.print();" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-print"></i>
                    Print
                </a>
                <a href="{{ route('vendor.laporan.export-penjualan', ['type' => 'monthly', 'date' => $selectedMonth->format('Y-m')]) }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
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
                <form action="{{ route('vendor.laporan.penjualan-bulanan') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Bulan</label>
                        <input type="month" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            name="month" value="{{ $selectedMonth->format('Y-m') }}" max="{{ now()->format('Y-m') }}">
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
                        <h3 class="text-lg font-semibold text-gray-900">Ringkasan Penjualan {{ $selectedMonth->format('F Y') }}</h3>
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
                        <h3 class="text-lg font-semibold text-gray-900">Penjualan Harian</h3>
                    </div>
                    <div class="p-6">
                        <div id="chart-penjualan-harian"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Products + Recent Transactions --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Produk Terlaris --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Produk Terlaris</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Terjual</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($produkTerlaris as $index => $produk)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ is_array($produk) ? $produk['nama_produk'] : $produk->nama_produk }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">{{ is_array($produk) ? $produk['total_qty'] : $produk->total_qty }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center">
                                        <x-ui.empty-state icon="fas fa-box-open" title="Tidak ada data" description="Belum ada data penjualan untuk periode ini." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Transaksi Terbaru --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Transaksi Terbaru</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($transaksis->take(5) as $transaksi)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaksi->tanggal_dibuat->format('d M') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $transaksi->kode }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $transaksi->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-receipt text-3xl text-gray-300 mb-3"></i>
                                            <p class="text-sm text-gray-500">Tidak ada transaksi</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const penjualanHarianData = {
                labels: [
                    @foreach (range(1, $selectedMonth->daysInMonth) as $day)
                        "{{ $day }}",
                    @endforeach
                ],
                data: [
                    @foreach (range(1, $selectedMonth->daysInMonth) as $day)
                        {{ $penjualanPerHari[sprintf('%02d', $day)]['total_penjualan'] ?? 0 }},
                    @endforeach
                ]
            };

            const options = {
                series: [{
                    name: 'Penjualan',
                    data: penjualanHarianData.data
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
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: penjualanHarianData.labels,
                    title: {
                        text: 'Tanggal'
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

            const chart = new ApexCharts(document.querySelector("#chart-penjualan-harian"), options);
            chart.render();
        });
    </script>
@endpush
