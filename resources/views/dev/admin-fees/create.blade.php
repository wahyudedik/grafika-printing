@extends('dev.layouts.app')

@section('title', 'Tambah Pengaturan Biaya Admin')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-900">Tambah Pengaturan Biaya Admin</h1>
        <a href="{{ route('admin.admin-fees.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-1"></i>Kembali
        </a>
    </div>

    <form action="{{ route('admin.admin-fees.store') }}" method="POST">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pengaturan <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Biaya Admin Lelang"
                        class="block w-full rounded-lg border {{ $errors->has('name') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-primary-500 focus:ring-primary-500' }} px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-opacity-50">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select name="category"
                        class="block w-full rounded-lg border {{ $errors->has('category') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-primary-500 focus:ring-primary-500' }} px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-opacity-50">
                        <option value="">Pilih Kategori</option>
                        <option value="auction" {{ old('category') == 'auction' ? 'selected' : '' }}>Lelang</option>
                        <option value="payment" {{ old('category') == 'payment' ? 'selected' : '' }}>Pembayaran</option>
                        <option value="transaction" {{ old('category') == 'transaction' ? 'selected' : '' }}>Transaksi</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" placeholder="Deskripsi pengaturan biaya admin"
                    class="block w-full rounded-lg border {{ $errors->has('description') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-primary-500 focus:ring-primary-500' }} px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-opacity-50">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Biaya <span class="text-red-500">*</span></label>
                    <select name="type" id="type"
                        class="block w-full rounded-lg border {{ $errors->has('type') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-primary-500 focus:ring-primary-500' }} px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-opacity-50">
                        <option value="">Pilih Tipe</option>
                        <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Tetap (Rp)</option>
                        <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nilai <span class="text-red-500">*</span></label>
                    <input type="number" name="value" value="{{ old('value') }}" step="0.01" min="0" placeholder="Masukkan nilai biaya"
                        class="block w-full rounded-lg border {{ $errors->has('value') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-primary-500 focus:ring-primary-500' }} px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-opacity-50">
                    <p class="mt-1 text-xs text-gray-500" id="value-hint">Masukkan nilai biaya sesuai tipe yang dipilih</p>
                    @error('value')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Minimum</label>
                    <input type="number" name="minimum_amount" value="{{ old('minimum_amount') }}" step="0.01" min="0" placeholder="0"
                        class="block w-full rounded-lg border {{ $errors->has('minimum_amount') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-primary-500 focus:ring-primary-500' }} px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-opacity-50">
                    <p class="mt-1 text-xs text-gray-500">Biaya akan dikenakan jika jumlah lelang >= nilai ini</p>
                    @error('minimum_amount')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Maksimum</label>
                    <input type="number" name="maximum_amount" value="{{ old('maximum_amount') }}" step="0.01" min="0" placeholder="Tidak terbatas"
                        class="block w-full rounded-lg border {{ $errors->has('maximum_amount') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-primary-500 focus:ring-primary-500' }} px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-opacity-50">
                    <p class="mt-1 text-xs text-gray-500">Biaya akan dikenakan jika jumlah lelang <= nilai ini</p>
                    @error('maximum_amount')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Efektif Dari</label>
                    <input type="datetime-local" name="effective_from" value="{{ old('effective_from') }}"
                        class="block w-full rounded-lg border {{ $errors->has('effective_from') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-primary-500 focus:ring-primary-500' }} px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-opacity-50">
                    <p class="mt-1 text-xs text-gray-500">Kosongkan untuk langsung aktif</p>
                    @error('effective_from')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Efektif Sampai</label>
                    <input type="datetime-local" name="effective_until" value="{{ old('effective_until') }}"
                        class="block w-full rounded-lg border {{ $errors->has('effective_until') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-primary-500 focus:ring-primary-500' }} px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-opacity-50">
                    <p class="mt-1 text-xs text-gray-500">Kosongkan untuk tidak ada batas waktu</p>
                    @error('effective_until')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="text-sm text-gray-700">Aktifkan pengaturan ini</span>
                </label>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">
                <i class="fas fa-save mr-2"></i>Simpan
            </button>
            <a href="{{ route('admin.admin-fees.index') }}" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
        </div>
    </form>

    <script>
        document.getElementById('type').addEventListener('change', function() {
            const valueInput = document.querySelector('input[name="value"]');
            const valueHint = document.getElementById('value-hint');

            if (this.value === 'fixed') {
                valueInput.placeholder = 'Masukkan jumlah dalam Rupiah';
                valueHint.textContent = 'Masukkan jumlah biaya tetap dalam Rupiah (contoh: 5000)';
            } else if (this.value === 'percentage') {
                valueInput.placeholder = 'Masukkan persentase';
                valueHint.textContent = 'Masukkan persentase biaya (contoh: 10 untuk 10%)';
            } else {
                valueInput.placeholder = 'Masukkan nilai biaya';
                valueHint.textContent = 'Masukkan nilai biaya sesuai tipe yang dipilih';
            }
        });
    </script>
@endsection
