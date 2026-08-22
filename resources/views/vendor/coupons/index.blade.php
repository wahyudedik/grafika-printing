@extends('layouts.vendor')

@section('title', 'Kelola Kupon')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Kupon</h1>
            <p class="text-sm text-gray-500 mt-1">Buat dan kelola kupon diskon untuk pelanggan Anda.</p>
        </div>
        <a href="{{ route('vendor.coupons.create') }}"
            class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors">
            <i class="fas fa-plus mr-2"></i>Buat Kupon
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-ticket-alt text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Kupon</p>
                    <p class="text-lg font-bold text-gray-900">{{ $coupons->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Aktif</p>
                    <p class="text-lg font-bold text-gray-900">{{ $coupons->where('is_active')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center">
                    <i class="fas fa-percent text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Digunakan</p>
                    <p class="text-lg font-bold text-gray-900">{{ $coupons->sum('used_count') }}x</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Coupons Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        @if($coupons->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-4">
                    <i class="fas fa-ticket-alt text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Kupon</h3>
                <p class="text-gray-500 mb-4">Buat kupon pertama Anda untuk menarik lebih banyak pelanggan.</p>
                <a href="{{ route('vendor.coupons.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Buat Kupon
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Kode</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Nama</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Tipe</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Nilai</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Min. Order</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Penggunaan</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Status</th>
                            <th class="text-right px-6 py-3 font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($coupons as $coupon)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-mono font-bold text-primary">{{ $coupon->code }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $coupon->name }}</p>
                                        @if($coupon->description)
                                            <p class="text-xs text-gray-500 mt-0.5">{{ Str::limit($coupon->description, 50) }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $coupon->type === 'percentage' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $coupon->type === 'percentage' ? 'Persentase' : 'Nominal' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    @if($coupon->type === 'percentage')
                                        {{ $coupon->value }}%
                                        @if($coupon->maximum_discount)
                                            <span class="text-xs text-gray-500">(max Rp {{ number_format($coupon->maximum_discount, 0, ',', '.') }})</span>
                                        @endif
                                    @else
                                        Rp {{ number_format($coupon->value, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    Rp {{ number_format($coupon->minimum_order, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-700">
                                        {{ $coupon->used_count }}
                                        @if($coupon->usage_limit)
                                            / {{ $coupon->usage_limit }}
                                        @else
                                            / ∞
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($coupon->isValid())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @elseif($coupon->is_active && $coupon->expires_at && $coupon->expires_at->isPast())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Kedaluwarsa
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2" x-data="{ open: false }">
                                        <form action="{{ route('vendor.coupons.toggle-active', $coupon) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="text-xs px-2 py-1 rounded-lg border transition-colors
                                                    {{ $coupon->is_active
                                                        ? 'border-yellow-300 text-yellow-700 hover:bg-yellow-50'
                                                        : 'border-green-300 text-green-700 hover:bg-green-50' }}">
                                                <i class="fas {{ $coupon->is_active ? 'fa-pause' : 'fa-play' }} mr-1"></i>
                                                {{ $coupon->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        <a href="{{ route('vendor.coupons.edit', $coupon) }}"
                                            class="text-xs px-2 py-1 rounded-lg border border-blue-300 text-blue-700 hover:bg-blue-50 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open"
                                                class="text-xs px-2 py-1 rounded-lg border border-red-300 text-red-700 hover:bg-red-50 transition-colors">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <div x-show="open" @click.outside="open = false" x-transition x-cloak
                                                class="absolute right-0 z-10 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2">
                                                <p class="px-4 py-1 text-xs text-gray-500">Hapus kupon ini?</p>
                                                <form action="{{ route('vendor.coupons.destroy', $coupon) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 transition-colors">
                                                        <i class="fas fa-trash mr-2"></i>Ya, Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
