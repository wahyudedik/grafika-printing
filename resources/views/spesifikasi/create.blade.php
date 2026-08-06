@extends('layouts.vendor')

@section('title', 'Tambah Spesifikasi')
@section('content')
    <div class="max-w-3xl mx-auto">
        <form action="{{ route('vendor.specifications.store') }}" method="POST"
            onsubmit="showLoading('Menambahkan spesifikasi...')">
            @csrf
            <div class="bg-white rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Spesifikasi Baru</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Spesifikasi <span class="text-red-500">*</span></label>
                            <input type="text"
                                class="w-full px-4 py-2 border {{ $errors->has('nama_spesifikasi') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-primary focus:border-primary' }} rounded-lg text-sm transition"
                                name="nama_spesifikasi" value="{{ old('nama_spesifikasi') }}"
                                placeholder="Contoh: Ukuran, Warna, Jumlah Halaman">
                            @error('nama_spesifikasi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Input <span class="text-red-500">*</span></label>
                            <select
                                class="w-full px-4 py-2 border {{ $errors->has('tipe_input') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-primary focus:border-primary' }} rounded-lg text-sm transition"
                                name="tipe_input" id="tipe_input">
                                <option value="">Pilih tipe input</option>
                                @foreach ($tipeInput as $key => $value)
                                    <option value="{{ $value }}"
                                        {{ old('tipe_input') == $value ? 'selected' : '' }}>
                                        {{ ucfirst($key) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipe_input')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="satuan_field">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                            <input type="text"
                                class="w-full px-4 py-2 border {{ $errors->has('satuan') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-primary focus:border-primary' }} rounded-lg text-sm transition"
                                name="satuan" value="{{ old('satuan') }}" placeholder="Contoh: cm, pcs, halaman">
                            <p class="mt-1 text-xs text-gray-500">Opsional. Cocok untuk tipe number (contoh: cm, kg, pcs)</p>
                            @error('satuan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition">
                        <i class="fas fa-save"></i>
                        Simpan
                    </button>
                    <a href="{{ route('vendor.specifications.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                        <i class="fas fa-times"></i>
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            const tipeInputSelect = document.getElementById('tipe_input');
            const satuanField = document.getElementById('satuan_field');

            function toggleSatuanField() {
                if (tipeInputSelect.value === 'number') {
                    satuanField.style.display = 'block';
                } else {
                    satuanField.style.display = 'none';
                }
            }

            toggleSatuanField();
            tipeInputSelect.addEventListener('change', toggleSatuanField);
        </script>
    @endpush
@endsection
