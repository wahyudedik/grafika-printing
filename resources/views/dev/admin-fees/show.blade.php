@extends('dev.layouts.app')

@section('title', 'Detail Pengaturan Biaya Admin')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-900">{{ $adminFee->name }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.admin-fees.edit', $adminFee) }}" class="px-4 py-2 text-sm font-medium text-white bg-yellow-500 rounded-lg hover:bg-yellow-600">
                <i class="fas fa-edit mr-1"></i>Edit
            </a>
            <a href="{{ route('admin.admin-fees.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                <i class="fas fa-times mr-1"></i>Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Nama Pengaturan</label>
                <p class="text-sm text-gray-900">{{ $adminFee->name }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Kategori</label>
                <p><span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">{{ $adminFee->category }}</span></p>
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Deskripsi</label>
            <p class="text-sm text-gray-900">{{ $adminFee->description ?: '-' }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Tipe Biaya</label>
                <p>
                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $adminFee->type === 'fixed' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                        {{ $adminFee->type === 'fixed' ? 'Tetap' : 'Persentase' }}
                    </span>
                </p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Nilai</label>
                <p class="text-sm text-gray-900">
                    @if ($adminFee->type === 'fixed')
                        Rp {{ number_format($adminFee->value, 0, ',', '.') }}
                    @else
                        {{ $adminFee->value }}%
                    @endif
                </p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Status</label>
                <p>
                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $adminFee->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $adminFee->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Jumlah Minimum</label>
                <p class="text-sm text-gray-900">Rp {{ number_format($adminFee->minimum_amount, 0, ',', '.') }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Jumlah Maksimum</label>
                <p class="text-sm text-gray-900">
                    {{ $adminFee->maximum_amount ? 'Rp ' . number_format($adminFee->maximum_amount, 0, ',', '.') : 'Tidak terbatas' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Efektif Dari</label>
                <p class="text-sm text-gray-900">{{ $adminFee->effective_from ? $adminFee->effective_from->format('d/m/Y H:i') : 'Sekarang' }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Efektif Sampai</label>
                <p class="text-sm text-gray-900">{{ $adminFee->effective_until ? $adminFee->effective_until->format('d/m/Y H:i') : 'Tidak ada batas waktu' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Dibuat Oleh</label>
                <p class="text-sm text-gray-900">{{ $adminFee->createdBy->name ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Diperbarui Oleh</label>
                <p class="text-sm text-gray-900">{{ $adminFee->updatedBy->name ?? '-' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Dibuat Pada</label>
                <p class="text-sm text-gray-900">{{ $adminFee->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Diperbarui Pada</label>
                <p class="text-sm text-gray-900">{{ $adminFee->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        @if ($adminFee->conditions)
            <div class="mt-6">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Kondisi Tambahan</label>
                <pre class="bg-gray-50 p-4 rounded-lg text-xs text-gray-700 overflow-x-auto">{{ json_encode($adminFee->conditions, JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif
    </div>
@endsection
