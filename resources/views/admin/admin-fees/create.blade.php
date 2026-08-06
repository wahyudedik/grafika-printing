@extends('dev.layouts.app')

@section('title', 'Tambah Pengaturan Biaya Admin')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-gray-500">Pengaturan</p>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Pengaturan Biaya Admin</h1>
        </div>
        <a href="{{ route('admin.admin-fees.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.admin-fees.store') }}" method="POST">
        @csrf
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Form Pengaturan Biaya Admin</h3>
            </div>
            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pengaturan <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Biaya Admin 10%"
                            class="block w-full rounded-lg border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                        <select name="category"
                            class="block w-full rounded-lg border {{ $errors->has('category') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                            <option value="">Pilih Kategori</option>
                            <option value="auction" {{ old('category') == 'auction' ? 'selected' : '' }}>Lelang</option>
                            <option value="payment" {{ old('category') == 'payment' ? 'selected' : '' }}>Pembayaran</option>
                            <option value="transaction" {{ old('category') == 'transaction' ? 'selected' : '' }}>Transaksi</option>
                        </select>
                        @error('category') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Deskripsi pengaturan biaya admin"
                        class="block w-full rounded-lg border {{ $errors->has('description') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 focus:outline-none">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Tipe Biaya --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Biaya <span class="text-red-500">*</span></label>
                        <select name="type" id="type"
                            class="block w-full rounded-lg border {{ $errors->has('type') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                            <option value="">Pilih Tipe</option>
                            <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Biaya Tetap (Rupiah)</option>
                            <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Biaya Persentase (%)</option>
                        </select>
                        @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Nilai Biaya --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Biaya <span class="text-red-500">*</span></label>
                        <div class="flex">
                            <input type="number" name="value" value="{{ old('value') }}" step="0.01" min="0" placeholder="Masukkan nilai biaya"
                                class="block w-full rounded-l-lg border {{ $errors->has('value') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                            <span id="value-unit" class="inline-flex items-center px-3 rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 text-sm text-gray-500">Rp</span>
                        </div>
                        @error('value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Minimum --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Minimum</label>
                        <input type="number" name="minimum_amount" value="{{ old('minimum_amount', 0) }}" step="0.01" min="0" placeholder="Jumlah minimum"
                            class="block w-full rounded-lg border {{ $errors->has('minimum_amount') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                        @error('minimum_amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Maksimum --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Maksimum</label>
                        <input type="number" name="maximum_amount" value="{{ old('maximum_amount') }}" step="0.01" min="0" placeholder="Jumlah maksimum"
                            class="block w-full rounded-lg border {{ $errors->has('maximum_amount') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                        @error('maximum_amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Effective From --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Dari</label>
                        <input type="date" name="effective_from" value="{{ old('effective_from') }}"
                            class="block w-full rounded-lg border {{ $errors->has('effective_from') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                        @error('effective_from') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Effective Until --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Sampai</label>
                        <input type="date" name="effective_until" value="{{ old('effective_until') }}"
                            class="block w-full rounded-lg border {{ $errors->has('effective_until') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                        @error('effective_until') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Aktif --}}
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} id="is_active" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <label for="is_active" class="text-sm text-gray-700">Aktifkan pengaturan ini</label>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <a href="{{ route('admin.admin-fees.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Batal</a>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fa-solid fa-check"></i> Simpan Pengaturan
                </button>
            </div>
        </div>
    </form>
@endsection
