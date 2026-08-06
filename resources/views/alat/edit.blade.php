@extends('layouts.vendor')

@section('title', 'Edit Alat')
@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6">
            <div>
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <h4 class="text-sm font-medium text-red-800 mb-2">Error!</h4>
                        <ul class="list-disc list-inside text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('vendor.tools.update', $alat->id) }}" method="POST"
                    onsubmit="showLoading('Memperbarui alat...')" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="bg-white rounded-xl shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Edit Alat</h3>
                        </div>
                        <div class="px-6 py-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Nama Alat <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nama_alat"
                                        value="{{ old('nama_alat', $alat->nama_alat) }}"
                                        placeholder="Masukkan nama alat"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm @error('nama_alat') border-red-500 @enderror">
                                    @error('nama_alat')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Merek <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="merek"
                                        value="{{ old('merek', $alat->merek) }}"
                                        placeholder="Masukkan merek alat"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm @error('merek') border-red-500 @enderror">
                                    @error('merek')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Model <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="model"
                                        value="{{ old('model', $alat->model) }}"
                                        placeholder="Masukkan model alat"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm @error('model') border-red-500 @enderror">
                                    @error('model')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm @error('status') border-red-500 @enderror">
                                        <option value="">Pilih status</option>
                                        <option value="aktif" {{ old('status', $alat->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="maintenance" {{ old('status', $alat->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="rusak" {{ old('status', $alat->status) == 'rusak' ? 'selected' : '' }}>Rusak</option>
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Spesifikasi Alat <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="spesifikasi_alat" rows="4"
                                        placeholder="Masukkan spesifikasi alat"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm @error('spesifikasi_alat') border-red-500 @enderror">{{ old('spesifikasi_alat', $alat->spesifikasi_alat) }}</textarea>
                                    @error('spesifikasi_alat')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Tanggal Pembelian <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="tanggal_pembelian"
                                        value="{{ old('tanggal_pembelian', $alat->tanggal_pembelian->format('Y-m-d')) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm @error('tanggal_pembelian') border-red-500 @enderror">
                                    @error('tanggal_pembelian')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Kapasitas Cetak / Jam <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="kapasitas_cetak_per_jam"
                                        value="{{ old('kapasitas_cetak_per_jam', $alat->kapasitas_cetak_per_jam) }}"
                                        placeholder="Masukkan kapasitas cetak per jam"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm @error('kapasitas_cetak_per_jam') border-red-500 @enderror">
                                    @error('kapasitas_cetak_per_jam')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Tersedia <span class="text-red-500">*</span>
                                    </label>
                                    <select name="tersedia"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm @error('tersedia') border-red-500 @enderror">
                                        <option value="1" {{ old('tersedia', $alat->tersedia ? '1' : '0') == '1' ? 'selected' : '' }}>Ya</option>
                                        <option value="0" {{ old('tersedia', $alat->tersedia ? '1' : '0') == '0' ? 'selected' : '' }}>Tidak</option>
                                    </select>
                                    @error('tersedia')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                            <button type="submit"
                                class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                                <i class="fas fa-save"></i>
                                Update
                            </button>
                            <a href="{{ route('vendor.tools.index') }}"
                                class="inline-flex items-center gap-2 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
                                <i class="fas fa-times"></i>
                                Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
