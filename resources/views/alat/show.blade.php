@extends('layouts.vendor')

@section('title', 'Detail Alat')
@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6">
            <div>
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Detail Alat</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Alat</label>
                                <div class="text-sm text-gray-900">{{ $alat->nama_alat }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Merek</label>
                                <div class="text-sm text-gray-900">{{ $alat->merek }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Model</label>
                                <div class="text-sm text-gray-900">{{ $alat->model }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                                <div>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($alat->status === 'aktif') bg-green-100 text-green-700
                                        @elseif($alat->status === 'maintenance') bg-amber-100 text-amber-700
                                        @else bg-red-100 text-red-700
                                        @endif">
                                        {{ ucfirst($alat->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Spesifikasi Alat</label>
                                <div class="text-sm text-gray-900">{{ $alat->spesifikasi_alat }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Pembelian</label>
                                <div class="text-sm text-gray-900">{{ $alat->tanggal_pembelian->format('d M Y') }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Kapasitas Cetak / Jam</label>
                                <div class="text-sm text-gray-900">{{ $alat->kapasitas_cetak_per_jam }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Tersedia</label>
                                <div>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $alat->tersedia ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $alat->tersedia ? 'Ya' : 'Tidak' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                        <a href="{{ route('vendor.tools.edit', $alat->id) }}"
                            class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                            <i class="fas fa-edit"></i>
                            Edit
                        </a>
                        <a href="{{ route('vendor.tools.index') }}"
                            class="inline-flex items-center gap-2 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
                            <i class="fas fa-arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
