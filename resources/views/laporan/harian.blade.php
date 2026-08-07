@extends('layouts.vendor')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Laporan Penjualan Harian</h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="#" onclick="window.print();" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-print"></i>
                    Print
                </a>
                <a href="{{ route('vendor.laporan.export-penjualan', ['type' => 'daily', 'date' => $selectedDate->format('Y-m-d')]) }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
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
                <form action="{{ route('vendor.laporan.penjualan-harian') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            name="date" value="{{ $selectedDate->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="sm:w-32 flex items-end">
                        <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">Terapkan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Summary + Chart --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Summary Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Ringkasan Penjualan {{ $selectedDate->format('d F Y') }}</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
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

            {{-- Chart Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Penjualan Per Jam</h3>
                </div>
                <div class="p-6">
                    <div id="chart-penjualan-per-jam"></div>
                </div>
            </div>
        </div>

        {{-- Detail Transaksi --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Detail Transaksi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Transaksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode Pembayaran</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($transaksis as $index => $transaksi)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $transaksi->kode }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaksi->tanggal_dibuat->format('H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $transaksi->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($transaksi->status == 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    @elseif($transaksi->status == 'processing')
                                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Processing</span>
                                    @elseif($transaksi->status == 'quality_check')
                                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-purple-100 text-purple-800">Quality Check</span>
                                    @elseif($transaksi->status == 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Completed</span>
                                    @elseif($transaksi->status == 'cancelled')
                                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">Cancelled</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ ucfirst($transaksi->payment_method ?? '-') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-receipt text-3xl text-gray-300 mb-3"></i>
                                        <p class="text-sm text-gray-500">Tidak ada transaksi pada tanggal ini</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const penjualanPerJamData = {
                labels: [
                    @foreach (range(0, 23) as $hour)
                        "{{ sprintf('%02d', $hour) }}:00",
                    @endforeach
                ],
                data: [
                    @foreach (range(0, 23) as $hour)
                        {{ $penjualanPerJam[$hour]['total_penjualan'] ?? 0 }},
                    @endforeach
                ]
            };

            const options = {
                series: [{
                    name: 'Penjualan',
                    data: penjualanPerJamData.data
                }],
                chart: {
                    type: 'bar',
                    height: 300,
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
                    categories: penjualanPerJamData.labels,
                },
                yaxis: {
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

            const chart = new ApexCharts(document.querySelector("#chart-penjualan-per-jam"), options);
            chart.render();
        });
    </script>
@endpush
