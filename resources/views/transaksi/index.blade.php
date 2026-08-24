@extends('layouts.vendor')

@section('title', 'Manajemen Transaksi')
@section('content')
    @php
        $statusConfig = [
            'pending' => ['label' => 'Pending', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
            'processing' => ['label' => 'Diproses', 'bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
            'quality_check' => ['label' => 'QC', 'bg' => 'bg-purple-100', 'text' => 'text-purple-800'],
            'completed' => ['label' => 'Selesai', 'bg' => 'bg-green-100', 'text' => 'text-green-800'],
            'cancelled' => ['label' => 'Dibatalkan', 'bg' => 'bg-red-100', 'text' => 'text-red-800'],
        ];
    @endphp
    <div class="bg-white rounded-xl shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex flex-col gap-4">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Transaksi</h3>
                <form action="{{ route('vendor.transactions.index') }}" method="GET" id="filter-form">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                        <div class="md:col-span-4">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary"
                                    placeholder="Cari kode/pelanggan...">
                            </div>
                        </div>
                        <div class="md:col-span-3">
                            <select name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary"
                                onchange="document.getElementById('filter-form').submit()">
                                <option value="">Semua Status</option>
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ request('status') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <input type="date" name="start_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary"
                                value="{{ request('start_date') }}" placeholder="Tanggal Mulai">
                        </div>
                        <div class="md:col-span-2">
                            <input type="date" name="end_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary"
                                value="{{ request('end_date') }}" placeholder="Tanggal Akhir">
                        </div>
                        <div class="md:col-span-1 flex gap-2">
                            <button type="submit"
                                class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <i class="fas fa-filter"></i>
                            </button>
                            <a href="{{ route('vendor.transactions.create') }}"
                                class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                <i class="fas fa-plus"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode Pembayaran</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progres</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($transaksis as $transaksi)
                        @php
                            $sc = $statusConfig[$transaksi->status] ?? ['label' => $transaksi->status, 'bg' => 'bg-gray-100', 'text' => 'text-gray-800'];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('vendor.transactions.show', $transaksi->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    {{ $transaksi->kode }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $transaksi->pelanggan->nama ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaksi->tanggal_dibuat->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $sc['bg'] }} {{ $sc['text'] }}">
                                    {{ $sc['label'] }}
                                </span>
                                @if ($transaksi->is_voided)
                                    <span class="ml-1 px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">VOIDED</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaksi->payment_method }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-600 text-xs font-medium w-8">{{ $transaksi->progress_percentage }}%</span>
                                    <div class="w-20 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $transaksi->progress_percentage }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('vendor.transactions.show', $transaksi->id) }}"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if (!$transaksi->is_voided)
                                        <a href="{{ route('vendor.transactions.edit', $transaksi->id) }}"
                                            class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    @if ($transaksi->canBeVoided())
                                        <a href="{{ route('vendor.transactions.void', $transaksi->id) }}"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Void">
                                            <i class="fas fa-ban"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-receipt text-gray-400 text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">Tidak ada data transaksi</p>
                                    <p class="text-sm text-gray-500 mt-1">Silahkan tambahkan transaksi baru atau ubah filter pencarian</p>
                                    <a href="{{ route('vendor.transactions.create') }}"
                                        class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-plus"></i> Tambah Transaksi
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden divide-y divide-gray-100">
            @forelse ($transaksis as $transaksi)
                <div class="p-4 space-y-2">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('vendor.transactions.show', $transaksi->id) }}" class="font-medium text-blue-600 hover:text-blue-800">
                                {{ $transaksi->kode }}
                            </a>
                            <p class="text-sm text-gray-500 mt-0.5">{{ $transaksi->pelanggan->nama ?? 'N/A' }}</p>
                        </div>
                        @php
                            $scMobile = $statusConfig[$transaksi->status] ?? ['label' => $transaksi->status, 'bg' => 'bg-gray-100', 'text' => 'text-gray-800'];
                        @endphp
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $scMobile['bg'] }} {{ $scMobile['text'] }} ml-2 flex-shrink-0">
                                {{ $scMobile['label'] }}
                            @if ($transaksi->is_voided)
                                <span class="ml-1 px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-red-100 text-red-800">VOIDED</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">{{ $transaksi->tanggal_dibuat->format('d/m/Y H:i') }}</span>
                        <span class="font-medium text-gray-900">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">{{ $transaksi->payment_method }}</span>
                        <span class="text-gray-300">·</span>
                        <span class="text-xs text-gray-500">{{ $transaksi->progress_percentage }}%</span>
                        <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                            <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $transaksi->progress_percentage }}%"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-1 pt-1">
                        <a href="{{ route('vendor.transactions.show', $transaksi->id) }}"
                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat">
                            <i class="fas fa-eye text-sm"></i>
                        </a>
                        @if (!$transaksi->is_voided)
                            <a href="{{ route('vendor.transactions.edit', $transaksi->id) }}"
                                class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                        @endif
                        @if ($transaksi->canBeVoided())
                            <a href="{{ route('vendor.transactions.void', $transaksi->id) }}"
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Void">
                                <i class="fas fa-ban text-sm"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-receipt text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-900">Tidak ada data transaksi</p>
                    <p class="text-sm text-gray-500 mt-1">Silahkan tambahkan transaksi baru</p>
                </div>
            @endforelse
        </div>

        @if ($transaksis->hasPages())
            <div class="border-t border-gray-200 px-6 py-4">
                {{ $transaksis->links('components.pagination') }}
            </div>
        @endif
    </div>

    {{-- confirmDelete(formId) is now globally available via components/alert.blade.php --}}
@endsection
