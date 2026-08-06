@extends('layouts.vendor')

@section('title', 'Tambah Kategori Produk')
@section('content')
    <div class="max-w-2xl mx-auto">
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
                    <div>
                        <h4 class="text-sm font-semibold text-red-800">Error!</h4>
                        <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('vendor.categories.store') }}" method="POST" class="bg-white rounded-xl shadow-sm"
            onsubmit="showLoading('Menambahkan kategori...')">
            @csrf
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Tambah Kategori Produk Baru</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Kategori <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_kategori"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_kategori') border-red-500 @enderror"
                            value="{{ old('nama_kategori') }}" placeholder="Masukkan nama kategori">
                        @error('nama_kategori')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-200 px-6 py-4 flex items-center justify-end gap-3">
                <a href="{{ route('vendor.categories.index') }}"
                    class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times mr-1"></i> Batal
                </a>
                <button type="submit"
                    class="px-4 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
@endsection
