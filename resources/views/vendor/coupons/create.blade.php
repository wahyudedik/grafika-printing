@extends('layouts.vendor')

@section('title', 'Buat Kupon Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    {{-- Header --}}
    <div>
        <a href="{{ route('vendor.coupons.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors mb-3">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Kupon
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Buat Kupon Baru</h1>
        <p class="text-sm text-gray-500 mt-1">Isi form berikut untuk membuat kupon diskon baru.</p>
    </div>

    {{-- Form --}}
    <form action="{{ route('vendor.coupons.store') }}" method="POST">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">

            {{-- Code --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Kupon <span class="text-red-500">*</span></label>
                <input type="text" name="code" value="{{ old('code') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition uppercase"
                    placeholder="Contoh: DISKON10">
                @error('code')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kupon <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                    placeholder="Contoh: Diskon 10% untuk Pelanggan Baru">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition resize-none"
                    placeholder="Deskripsi singkat tentang kupon ini...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Type & Value --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Diskon <span class="text-red-500">*</span></label>
                    <select name="type" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                        x-data="{ type: '{{ old('type', 'percentage') }}' }" x-model="type">
                        <option value="percentage">Persentase (%)</option>
                        <option value="fixed">Nominal (Rp)</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Diskon <span class="text-red-500">*</span></label>
                    <input type="number" name="value" value="{{ old('value') }}" required min="0" step="0.01"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                        placeholder="Contoh: 10">
                    @error('value')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Minimum Order --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Pembelian (Rp)</label>
                <input type="number" name="minimum_order" value="{{ old('minimum_order', 0) }}" min="0" step="1000"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                    placeholder="0">
                @error('minimum_order')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Maximum Discount --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Maksimum Diskon (Rp) <span class="text-gray-400 text-xs">(untuk tipe persentase)</span></label>
                <input type="number" name="maximum_discount" value="{{ old('maximum_discount') }}" min="0" step="1000"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                    placeholder="Kosongkan untuk tanpa batas">
                @error('maximum_discount')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Usage Limits --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Batas Penggunaan Total</label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" min="1"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                        placeholder="Kosongkan untuk unlimited">
                    @error('usage_limit')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Batas per User</label>
                    <input type="number" name="usage_limit_per_user" value="{{ old('usage_limit_per_user', 1) }}" min="1"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                    @error('usage_limit_per_user')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Date Range --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Mulai</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                    @error('starts_at')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Sampai</label>
                    <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                    @error('expires_at')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Active Status --}}
            <div class="flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
                <span class="text-sm font-medium text-gray-700">Aktifkan kupon</span>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('vendor.coupons.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors">
                <i class="fas fa-save mr-2"></i>Simpan Kupon
            </button>
        </div>
    </form>
</div>
@endsection
