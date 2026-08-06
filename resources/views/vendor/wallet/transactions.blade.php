@extends('layouts.vendor')

@section('title', 'Riwayat Transaksi Wallet')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="text-sm text-gray-500 font-medium">Vendor Panel</div>
        <h2 class="text-2xl font-bold text-gray-900">Riwayat Transaksi Wallet</h2>
    </div>
    <a href="{{ route('vendor.wallet.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm">Kembali</a>
</div>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Wallet Summary --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="text-sm text-gray-500">Saldo Tersedia</div>
                <div class="text-xl font-bold text-green-600 mt-1">Rp {{ number_format($wallet->available_balance ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="text-sm text-gray-500">Total Pendapatan</div>
                <div class="text-xl font-bold mt-1">Rp {{ number_format($wallet->total_earned ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="text-sm text-gray-500">Total Ditarik</div>
                <div class="text-xl font-bold mt-1">Rp {{ number_format($wallet->total_withdrawn ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- Transactions Table --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Semua Transaksi</h3>
            </div>
            <div class="p-5">
                @if($transactions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Tanggal</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Jenis</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Kategori</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Deskripsi</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Jumlah</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4 text-gray-500">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                <td class="py-3 px-4">
                                    @if($transaction->type === 'credit')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Masuk</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Keluar</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">{{ $transaction->category_label ?? $transaction->category }}</td>
                                <td class="py-3 px-4 text-gray-500">{{ $transaction->description ?? '-' }}</td>
                                <td class="py-3 px-4 font-bold {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type === 'credit' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-gray-500">Rp {{ number_format($transaction->balance_after ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-center">{{ $transactions->links() }}</div>
                @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-lg font-medium text-gray-900">Belum ada transaksi</p>
                    <p class="text-sm text-gray-500 mt-1">Transaksi akan muncul di sini setelah ada aktivitas wallet</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
