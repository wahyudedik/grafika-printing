@extends('dev.layouts.app')

@section('title', 'Pengaturan Biaya Admin')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-900">Pengaturan Biaya Admin</h1>
        <div class="flex flex-wrap gap-2">
            <x.ui.button href="{{ route('admin.admin-fees.preview') }}" variant="outline-primary">
                <i class="fas fa-eye mr-1"></i>Preview Biaya
            </x.ui.button>
            <x.ui.button href="{{ route('admin.admin-fees.transactions') }}" variant="outline-info">
                <i class="fas fa-clock mr-1"></i>Transaksi
            </x.ui.button>
            <x.ui.button href="{{ route('admin.admin-fees.statistics') }}" variant="outline-success">
                <i class="fas fa-chart-line mr-1"></i>Statistik
            </x.ui.button>
            <x.ui.button href="{{ route('admin.admin-fees.create') }}" variant="primary">
                <i class="fas fa-plus mr-1"></i>Tambah Pengaturan
            </x.ui.button>
        </div>
    </div>

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="flex items-center gap-3 p-4 mb-4 text-green-800 bg-green-50 border border-green-200 rounded-lg">
            <i class="fas fa-check-circle text-green-500"></i>
            <div class="flex-1 text-sm font-medium">{{ session('success') }}</div>
            <button @click="show = false" class="text-green-500 hover:text-green-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Efektif Dari</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Efektif Sampai</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($settings as $setting)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <div class="text-sm font-medium text-gray-900">{{ $setting->name }}</div>
                                <div class="text-xs text-gray-500">{{ $setting->description }}</div>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">{{ $setting->category }}</span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $setting->type === 'fixed' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                    {{ $setting->type === 'fixed' ? 'Tetap' : 'Persentase' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-900">
                                @if ($setting->type === 'fixed')
                                    Rp {{ number_format($setting->value, 0, ',', '.') }}
                                @else
                                    {{ $setting->value }}%
                                @endif
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $setting->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $setting->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $setting->effective_from ? $setting->effective_from->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $setting->effective_until ? $setting->effective_until->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $setting->createdBy->name ?? '-' }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <x.ui.button href="{{ route('admin.admin-fees.show', $setting) }}" variant="ghost" size="icon-sm" title="Lihat">
                                        <i class="fas fa-eye text-sm"></i>
                                    </x.ui.button>
                                    <x.ui.button href="{{ route('admin.admin-fees.edit', $setting) }}" variant="ghost" size="icon-sm" title="Edit">
                                        <i class="fas fa-edit text-sm"></i>
                                    </x.ui.button>
                                    <form action="{{ route('admin.admin-fees.toggle', $setting) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <x.ui.button type="submit" variant="ghost" size="icon-sm" title="{{ $setting->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="fas fa-eye-{{ $setting->is_active ? 'slash' : '' }} text-sm"></i>
                                        </x.ui.button>
                                    </form>
                                    <form id="destroy-adminfee-{{ $setting->id }}" action="{{ route('admin.admin-fees.destroy', $setting) }}" method="POST" class="inline"
                                        x-data @submit.prevent="confirmFormSubmit('destroy-adminfee-{{ $setting->id }}', { title: 'Hapus Pengaturan?', text: 'Apakah Anda yakin ingin menghapus pengaturan ini?', confirmText: 'Ya, Hapus', confirmColor: '#d33' })">
                                        @csrf
                                        @method('DELETE')
                                        <x.ui.button type="submit" variant="ghost" size="icon-sm" title="Hapus">
                                            <i class="fas fa-trash text-sm"></i>
                                        </x.ui.button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-coins text-gray-400 text-xl"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900 mb-1">Tidak ada pengaturan biaya admin</p>
                                    <p class="text-xs text-gray-500 mb-3">Belum ada pengaturan biaya admin yang dibuat.</p>
                                    <x.ui.button href="{{ route('admin.admin-fees.create') }}" variant="primary">
                                        <i class="fas fa-plus mr-1"></i>Tambah Pengaturan
                                    </x.ui.button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
