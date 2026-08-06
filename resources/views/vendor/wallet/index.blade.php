@extends('layouts.vendor')

@section('title', 'Wallet Dashboard')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Wallet Dashboard</h2>
        <p class="text-sm text-gray-500">Kelola saldo dan penarikan dana Anda</p>
    </div>
    <a href="{{ route('vendor.wallet.create-withdrawal') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium">
        <i class="fas fa-money-bill-wave"></i>
        Tarik Dana
    </a>
</div>

@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
        <div class="flex items-center gap-2 text-green-800">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button @click="show = false" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
    </div>
@endif

@if (session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center justify-between">
        <div class="flex items-center gap-2 text-red-800">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button @click="show = false" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
    </div>
@endif

{{-- Wallet Overview --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-sm text-gray-500 mb-2">Saldo Tersedia</div>
        <div class="text-2xl font-bold mb-3">Rp {{ number_format($stats['available_balance']) }}</div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-sm text-gray-500 mb-2">Total Pendapatan</div>
        <div class="text-2xl font-bold mb-3">Rp {{ number_format($stats['total_earned']) }}</div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-primary-500 h-2 rounded-full" style="width: 100%"></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-sm text-gray-500 mb-2">Total Ditarik</div>
        <div class="text-2xl font-bold mb-3">Rp {{ number_format($stats['total_withdrawn']) }}</div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-blue-500 h-2 rounded-full" style="width: 100%"></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-sm text-gray-500 mb-2">Pending Penarikan</div>
        <div class="text-2xl font-bold mb-3">Rp {{ number_format($stats['pending_withdrawals']) }}</div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-amber-500 h-2 rounded-full" style="width: 100%"></div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Recent Transactions --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Transaksi Terbaru</h3>
            <a href="{{ route('vendor.wallet.transactions') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">Lihat Semua →</a>
        </div>
        <div class="p-5">
            @if ($transactions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Tanggal</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Jenis</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Kategori</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Jumlah</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Saldo</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $transaction)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4 text-gray-500">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transaction->type === 'credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $transaction->type === 'credit' ? 'Masuk' : 'Keluar' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">{{ $transaction->category_label }}</td>
                                    <td class="py-3 px-4 font-bold {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->formatted_amount }}
                                    </td>
                                    <td class="py-3 px-4 text-gray-500">Rp {{ number_format($transaction->balance_after) }}</td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $transaction->status_color }}-100 text-{{ $transaction->status_color }}-800">
                                            {{ $transaction->status_label }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <p class="text-lg font-medium text-gray-900">Belum ada transaksi</p>
                    <p class="text-sm text-gray-500 mt-1">Transaksi akan muncul di sini setelah ada pembayaran dari lelang.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Pending Withdrawals --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Penarikan Pending</h3>
            <a href="{{ route('vendor.wallet.withdrawals') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">Lihat Semua →</a>
        </div>
        <div class="p-5">
            @if ($pendingWithdrawals->count() > 0)
                @foreach ($pendingWithdrawals as $withdrawal)
                    <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <div>
                            <div class="font-bold">Rp {{ number_format($withdrawal->amount) }}</div>
                            <div class="text-xs text-gray-500">{{ $withdrawal->created_at->format('d M Y') }}</div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $withdrawal->status_color }}-100 text-{{ $withdrawal->status_color }}-800">
                            {{ $withdrawal->status_label }}
                        </span>
                    </div>
                @endforeach
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <p class="text-sm font-medium text-gray-900">Tidak ada penarikan pending</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
