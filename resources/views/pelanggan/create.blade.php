@extends('layouts.vendor')

@section('title', 'Tambah Pelanggan')
@section('content')
    <div class="max-w-3xl mx-auto">
        <form action="{{ route('vendor.customers.store') }}" method="POST"
            onsubmit="showLoading('Menambahkan pelanggan...')" enctype="multipart/form-data">
            @csrf
            <div class="bg-white rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Pelanggan Baru</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan <span class="text-red-500">*</span></label>
                            <input type="text"
                                class="w-full px-4 py-2 border {{ $errors->has('nama') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-primary focus:border-primary' }} rounded-lg text-sm transition"
                                name="nama" value="{{ old('nama') }}" placeholder="Masukkan nama pelanggan">
                            @error('nama')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email"
                                class="w-full px-4 py-2 border {{ $errors->has('email') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-primary focus:border-primary' }} rounded-lg text-sm transition"
                                name="email" value="{{ old('email') }}" placeholder="Masukkan email pelanggan">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                            <input type="text"
                                class="w-full px-4 py-2 border {{ $errors->has('no_telp') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-primary focus:border-primary' }} rounded-lg text-sm transition"
                                name="no_telp" value="{{ old('no_telp') }}" placeholder="Masukkan nomor telepon">
                            @error('no_telp')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                            <textarea
                                class="w-full px-4 py-2 border {{ $errors->has('alamat') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-primary focus:border-primary' }} rounded-lg text-sm transition"
                                name="alamat" rows="3"
                                placeholder="Masukkan alamat pelanggan">{{ old('alamat') }}</textarea>
                            @error('alamat')
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
                    <a href="{{ route('vendor.customers.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                        <i class="fas fa-times"></i>
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection
