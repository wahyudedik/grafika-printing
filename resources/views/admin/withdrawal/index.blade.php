@extends('dev.layouts.app')

@section('title', 'Manajemen Penarikan')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="mb-6">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Admin Panel</p>
        <h2 class="text-2xl font-bold text-gray-900">Manajemen Penarikan</h2>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="p-6">
            <form action="{{ route('admin.withdrawals.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" name="status">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vendor</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" name="vendor_id">
                        <option value="">Semua Vendor</option>
                        @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="sm:w-48 flex items-end gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">Filter</button>
                    <a href="{{ route('admin.withdrawals.index') }}" class="flex-1 text-center inline-flex items-center justify-center border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold py-2 px-4 rounded-lg transition">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Withdrawal List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Penarikan</h3>
            <a href="{{ route('admin.withdrawals.statistics') }}" class="inline-flex items-center justify-center border border-cyan-300 text-cyan-700 hover:bg-cyan-50 font-semibold text-sm py-1 px-3 rounded-lg transition">
                <i class="fas fa-chart-bar mr-1.5 text-xs"></i>
                Statistik
            </a>
        </div>

        @if($withdrawals->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($withdrawals as $withdrawal)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $withdrawal->withdrawal_code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $withdrawal->vendor->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($withdrawal->method === 'bank_transfer')
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Transfer Bank</span>
                            @elseif($withdrawal->method === 'e_wallet')
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-purple-100 text-purple-800">E-Wallet</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Tunai</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($withdrawal->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Menunggu</span>
                            @elseif($withdrawal->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Disetujui</span>
                            @elseif($withdrawal->status === 'processing')
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Diproses</span>
                            @elseif($withdrawal->status === 'completed')
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-600 text-white">Selesai</span>
                            @elseif($withdrawal->status === 'rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">Ditolak</span>
                            @elseif($withdrawal->status === 'cancelled')
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Dibatalkan</span>
                            @elseif($withdrawal->status === 'failed')
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-600 text-white">Gagal</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $withdrawal->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <a href="{{ route('admin.withdrawals.show', $withdrawal) }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex justify-center">
                {{ $withdrawals->links() }}
            </div>
        </div>
        @else
        <div class="p-12 text-center">
            <div class="flex flex-col items-center">
                <i class="fas fa-wallet text-3xl text-gray-300 mb-3"></i>
                <p class="text-sm font-medium text-gray-900">Tidak ada penarikan</p>
                <p class="text-sm text-gray-500 mt-1">Tidak ada penarikan yang sesuai dengan filter</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
