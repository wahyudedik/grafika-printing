@extends('dev.layouts.app')

@section('title', 'Detail Pengaturan Biaya Admin')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-gray-500">Pengaturan</p>
            <h1 class="text-2xl font-bold text-gray-900">Detail Pengaturan Biaya Admin</h1>
        </div>
        <div class="flex gap-2">
            <x-ui.button variant="outline" :href="route('admin.admin-fees.index')">
                <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
            </x-ui.button>
            <x-ui.button variant="warning" :href="route('admin.admin-fees.edit', $adminFee)">
                <i class="fa-solid fa-pen mr-2"></i> Edit
            </x-ui.button>
        </div>
    </div>

    <div class="space-y-6">
        {{-- Informasi Pengaturan --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Informasi Pengaturan</h3>
                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $adminFee->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $adminFee->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nama Pengaturan</label>
                        <div class="text-sm text-gray-900">{{ $adminFee->name }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Kategori</label>
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-700">{{ ucfirst($adminFee->category) }}</span>
                    </div>
                    @if ($adminFee->description)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Deskripsi</label>
                            <div class="text-sm text-gray-900">{{ $adminFee->description }}</div>
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tipe Biaya</label>
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $adminFee->type === 'percentage' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                            {{ $adminFee->type === 'percentage' ? 'Persentase' : 'Tetap' }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nilai Biaya</label>
                        <div class="text-lg font-bold text-gray-900">
                            @if ($adminFee->type === 'percentage')
                                {{ number_format($adminFee->value, 2) }}%
                            @else
                                Rp {{ number_format($adminFee->value, 0, ',', '.') }}
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Jumlah Minimum</label>
                        <div class="text-sm text-gray-900">
                            @if ($adminFee->minimum_amount > 0)
                                Rp {{ number_format($adminFee->minimum_amount, 0, ',', '.') }}
                            @else
                                <span class="text-gray-400">Tidak ada batasan minimum</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Jumlah Maksimum</label>
                        <div class="text-sm text-gray-900">
                            @if ($adminFee->maximum_amount > 0)
                                Rp {{ number_format($adminFee->maximum_amount, 0, ',', '.') }}
                            @else
                                <span class="text-gray-400">Tidak ada batasan maksimum</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Berlaku Dari</label>
                        <div class="text-sm text-gray-900">{{ $adminFee->effective_from ? $adminFee->effective_from->format('d F Y') : 'Segera' }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Berlaku Sampai</label>
                        <div class="text-sm text-gray-900">{{ $adminFee->effective_until ? $adminFee->effective_until->format('d F Y') : 'Tidak terbatas' }}</div>
                    </div>
                    @if ($adminFee->conditions)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Kondisi Tambahan</label>
                            <pre class="text-sm text-gray-600 bg-gray-50 rounded-lg p-3 overflow-x-auto">{{ json_encode($adminFee->conditions, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Informasi Sistem --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Informasi Sistem</h3>
            </div>
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Dibuat Oleh</label>
                        <div class="text-sm text-gray-900">
                            @if ($adminFee->createdBy)
                                {{ $adminFee->createdBy->name }} ({{ $adminFee->createdBy->email }})
                            @else
                                <span class="text-gray-400">Tidak diketahui</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Dibuat Pada</label>
                        <div class="text-sm text-gray-900">{{ $adminFee->created_at->format('d F Y H:i:s') }}</div>
                    </div>
                    @if ($adminFee->updatedBy)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Diperbarui Oleh</label>
                            <div class="text-sm text-gray-900">{{ $adminFee->updatedBy->name }} ({{ $adminFee->updatedBy->email }})</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Diperbarui Pada</label>
                            <div class="text-sm text-gray-900">{{ $adminFee->updated_at->format('d F Y H:i:s') }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Aksi --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Aksi</h3>
            </div>
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <form action="{{ route('admin.admin-fees.toggle', $adminFee) }}" method="POST">
                        @csrf @method('PATCH')
                        <x-ui.button type="submit" :variant="$adminFee->is_active ? 'danger' : 'success'" class="w-full"
                            onclick="return confirm('{{ $adminFee->is_active ? 'Nonaktifkan' : 'Aktifkan' }} pengaturan ini?')">
                            <i class="fa-solid fa-toggle-{{ $adminFee->is_active ? 'on' : 'off' }} mr-2"></i>
                            {{ $adminFee->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </x-ui.button>
                    </form>
                    <form action="{{ route('admin.admin-fees.destroy', $adminFee) }}" method="POST">
                        @csrf @method('DELETE')
                        <x-ui.button type="submit" variant="danger" class="w-full"
                            onclick="return confirm('Hapus pengaturan ini? Tindakan ini tidak dapat dibatalkan.')">
                            <i class="fa-solid fa-trash mr-2"></i> Hapus Pengaturan
                        </x-ui.button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
