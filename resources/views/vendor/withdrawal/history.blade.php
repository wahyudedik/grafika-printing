@extends('layouts.vendor')

@section('title', 'Riwayat Penarikan')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="text-sm text-gray-500 font-medium">Vendor Panel</div>
        <h2 class="text-2xl font-bold text-gray-900">Riwayat Penarikan</h2>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('vendor.withdrawal.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm">Kembali</a>
        <a href="{{ route('vendor.withdrawal.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium text-sm">Ajukan Penarikan</a>
    </div>
</div>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Semua Riwayat Penarikan</h3>
            </div>
            <div class="p-5">
                @if($withdrawals->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Kode</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Jumlah</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Biaya Admin</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Diterima</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Metode</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Status</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Tanggal</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($withdrawals as $withdrawal)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4 font-medium">{{ $withdrawal->withdrawal_code }}</td>
                                <td class="py-3 px-4 font-bold">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-gray-500">Rp {{ number_format($withdrawal->fee ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 font-bold text-green-600">Rp {{ number_format($withdrawal->net_amount ?? ($withdrawal->amount - ($withdrawal->fee ?? 0)), 0, ',', '.') }}</td>
                                <td class="py-3 px-4">
                                    @if($withdrawal->method === 'bank_transfer')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Transfer Bank</span>
                                    @elseif($withdrawal->method === 'e_wallet')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">E-Wallet</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Tunai</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($withdrawal->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Menunggu</span>
                                    @elseif($withdrawal->status === 'approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Disetujui</span>
                                    @elseif($withdrawal->status === 'processing')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Diproses</span>
                                    @elseif($withdrawal->status === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500 text-white">Selesai</span>
                                    @elseif($withdrawal->status === 'rejected')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
                                    @elseif($withdrawal->status === 'cancelled')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Dibatalkan</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-gray-500">{{ $withdrawal->created_at->format('d M Y H:i') }}</td>
                                <td class="py-3 px-4">
                                    <a href="{{ route('vendor.withdrawal.show', $withdrawal) }}" class="text-primary-600 hover:text-primary-700 font-medium text-sm">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-center">{{ $withdrawals->links() }}</div>
                @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    <p class="text-lg font-medium text-gray-900">Belum ada riwayat penarikan</p>
                    <p class="text-sm text-gray-500 mt-1">Riwayat penarikan Anda akan muncul di sini</p>
                    <a href="{{ route('vendor.withdrawal.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium text-sm">Ajukan Penarikan</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
