@extends('layouts.vendor')

@section('title', 'Penarikan Dana')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="text-sm text-gray-500 font-medium">Vendor Panel</div>
        <h2 class="text-2xl font-bold text-gray-900">Penarikan Dana</h2>
    </div>
    <a href="{{ route('vendor.withdrawal.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/></svg>
        Ajukan Penarikan
    </a>
</div>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Wallet Summary --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500">Saldo Tersedia</div>
                        <div class="text-xl font-bold mt-1">Rp {{ number_format($wallet->available_balance ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v6a2 2 0 002 2h3m0 0h6m-6 0H9m6 0v6m-6-6h6m0-3H9"/></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500">Total Ditarik</div>
                        <div class="text-xl font-bold mt-1">Rp {{ number_format($wallet->total_withdrawn ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12l7 7M19 12l-7 7"/></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500">Menunggu Proses</div>
                        <div class="text-xl font-bold mt-1">{{ $withdrawals->where('status', 'pending')->count() }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500">Minimum Penarikan</div>
                        <div class="text-xl font-bold mt-1">Rp {{ number_format($minWithdrawal, 0, ',', '.') }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Withdrawal List --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Penarikan</h3>
                <a href="{{ route('vendor.withdrawal.history') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">Lihat Semua →</a>
            </div>
            <div class="p-5">
                @if($withdrawals->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Kode</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Jumlah</th>
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
                                <td class="py-3 px-4 font-bold text-green-600">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</td>
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

                <div class="mt-4 flex justify-center">
                    {{ $withdrawals->links() }}
                </div>
                @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    <p class="text-lg font-medium text-gray-900">Belum ada penarikan</p>
                    <p class="text-sm text-gray-500 mt-1">Ajukan penarikan dana pertama Anda</p>
                    <a href="{{ route('vendor.withdrawal.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium">
                        Ajukan Penarikan
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
