@extends('dev.layouts.app')

@section('title', 'Pengaturan Biaya Admin')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-gray-500">Pengaturan</p>
            <h1 class="text-2xl font-bold text-gray-900">Biaya Admin</h1>
        </div>
        <a href="{{ route('admin.admin-fees.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
            <i class="fa-solid fa-plus"></i> Tambah Pengaturan
        </a>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="flex items-center gap-3 p-4 mb-4 text-green-800 bg-green-50 border border-green-200 rounded-lg">
            <i class="fa-solid fa-check-circle text-green-500"></i>
            <div class="flex-1 text-sm font-medium">{{ session('success') }}</div>
            <button @click="show = false" class="text-green-500 hover:text-green-700">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Main Card --}}
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Pengaturan Biaya Admin</h3>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.admin-fees.preview') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <i class="fa-solid fa-magnifying-glass"></i> Preview Biaya
                </a>
                <a href="{{ route('admin.admin-fees.transactions') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-cyan-700 bg-cyan-50 rounded-lg hover:bg-cyan-100 transition-colors">
                    <i class="fa-solid fa-clock-rotate-left"></i> Transaksi
                </a>
                <a href="{{ route('admin.admin-fees.statistics') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <i class="fa-solid fa-chart-line"></i> Statistik
                </a>
                <a href="{{ route('admin.admin-fees.create') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fa-solid fa-plus"></i> Tambah
                </a>
            </div>
        </div>

        <div class="p-6">
            @if ($settings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Berlaku</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($settings as $setting)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $setting->name }}</div>
                                        <div class="text-xs text-gray-500">{{ Str::limit($setting->description, 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $setting->type === 'percentage' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                            {{ $setting->type === 'percentage' ? 'Persentase' : 'Tetap' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if ($setting->type === 'percentage')
                                            {{ number_format($setting->value, 2) }}%
                                        @else
                                            Rp {{ number_format($setting->value, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-700">{{ ucfirst($setting->category) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $setting->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $setting->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                        @if ($setting->effective_from)
                                            Dari: {{ $setting->effective_from->format('d/m/Y') }}<br>
                                        @endif
                                        @if ($setting->effective_until)
                                            Sampai: {{ $setting->effective_until->format('d/m/Y') }}
                                        @else
                                            Tidak terbatas
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('admin.admin-fees.show', $setting) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Lihat"><i class="fa-solid fa-eye text-sm"></i></a>
                                            <a href="{{ route('admin.admin-fees.edit', $setting) }}" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg" title="Edit"><i class="fa-solid fa-pen text-sm"></i></a>
                                            <form action="{{ route('admin.admin-fees.toggle', $setting) }}" method="POST" class="inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="p-2 text-{{ $setting->is_active ? 'red' : 'green' }}-600 hover:bg-{{ $setting->is_active ? 'red' : 'green' }}-50 rounded-lg" title="{{ $setting->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <i class="fa-solid fa-toggle-{{ $setting->is_active ? 'on' : 'off' }} text-sm"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.admin-fees.destroy', $setting) }}" method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Hapus" onclick="return confirm('Hapus pengaturan ini?')">
                                                    <i class="fa-solid fa-trash text-sm"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fa-solid fa-coins text-gray-300 text-5xl mb-4"></i>
                    <p class="text-lg font-medium text-gray-900 mb-1">Belum ada pengaturan biaya admin</p>
                    <p class="text-sm text-gray-500 mb-4">Mulai dengan membuat pengaturan biaya admin pertama Anda.</p>
                    <a href="{{ route('admin.admin-fees.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                        <i class="fa-solid fa-plus"></i> Tambah Pengaturan
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
