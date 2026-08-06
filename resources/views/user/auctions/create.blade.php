@extends('layouts.user')

@section('title', 'Buat Permintaan Cetak')

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Buat Permintaan Cetak Baru</h1>
            <p class="text-sm text-gray-500 mt-1">Buat permintaan cetak dan biarkan vendor memberikan penawaran terbaik</p>
        </div>
    </div>

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="mb-6 bg-green-50 border border-green-200 rounded-lg px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-green-600"></i>
                <span class="text-sm text-green-800">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <form method="POST" action="{{ route('user.auctions.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Form Main --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="font-semibold text-gray-900">Detail Permintaan</h2>
                    </div>
                    <div class="px-6 py-5 space-y-5">
                        {{-- Title --}}
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Permintaan <span class="text-red-500">*</span></label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" required
                                class="w-full rounded-lg border {{ $errors->has('title') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            @error('title')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Detail <span class="text-red-500">*</span></label>
                            <textarea id="description" name="description" rows="4" required
                                class="w-full rounded-lg border {{ $errors->has('description') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Category & Quantity --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                                <select id="category" name="category" required
                                    class="w-full rounded-lg border {{ $errors->has('category') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Pilih Kategori</option>
                                    @foreach(['Banner', 'Stiker', 'Kartu Nama', 'Flyer', 'Poster', 'Brosur', 'Kalender', 'Buku', 'Kaos', 'Lainnya'] as $cat)
                                        <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Produksi <span class="text-red-500">*</span></label>
                                <input type="number" id="quantity" name="quantity" value="{{ old('quantity') }}" min="1" required
                                    class="w-full rounded-lg border {{ $errors->has('quantity') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                @error('quantity')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Budget & Deadline --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="budget" class="block text-sm font-medium text-gray-700 mb-1">Budget Maksimal <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-500">Rp</span>
                                    <input type="number" id="budget" name="budget" value="{{ old('budget') }}" min="0" step="0.01" required
                                        class="w-full rounded-lg border {{ $errors->has('budget') ? 'border-red-300' : 'border-gray-300' }} pl-8 pr-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                @error('budget')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1">Deadline Pengerjaan <span class="text-red-500">*</span></label>
                                <input type="date" id="deadline" name="deadline" value="{{ old('deadline') }}" required
                                    class="w-full rounded-lg border {{ $errors->has('deadline') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                @error('deadline')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Specifications --}}
                        <div>
                            <label for="specifications" class="block text-sm font-medium text-gray-700 mb-1">Spesifikasi Teknis</label>
                            <textarea id="specifications" name="specifications" rows="3"
                                placeholder="Contoh: Ukuran A4, Kertas 80gsm, Full Color, Finishing Laminating"
                                class="w-full rounded-lg border {{ $errors->has('specifications') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('specifications') }}</textarea>
                            @error('specifications')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- File Upload --}}
                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-1">File Desain/Referensi</label>
                            <input type="file" id="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 file:cursor-pointer">
                            <p class="text-xs text-gray-400 mt-1">Format: PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 10MB)</p>
                            @error('file')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Informasi Pengiriman --}}
                        <div class="bg-gray-50 rounded-lg border border-gray-200 px-5 py-4">
                            <h3 class="font-semibold text-gray-900 mb-4">Informasi Pengiriman</h3>

                            <div class="mb-4">
                                <label for="alamat_pengiriman" class="block text-sm font-medium text-gray-700 mb-1">Alamat Pengiriman <span class="text-red-500">*</span></label>
                                <textarea id="alamat_pengiriman" name="alamat_pengiriman" rows="3" placeholder="Masukkan alamat lengkap untuk pengiriman" required
                                    class="w-full rounded-lg border {{ $errors->has('alamat_pengiriman') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('alamat_pengiriman') }}</textarea>
                                @error('alamat_pengiriman')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="no_telp" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon <span class="text-red-500">*</span></label>
                                    <input type="tel" id="no_telp" name="no_telp" value="{{ old('no_telp') }}" required
                                        placeholder="08123456789"
                                        class="w-full rounded-lg border {{ $errors->has('no_telp') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <p class="text-xs text-gray-400 mt-1">Format: 08123456789, +628123456789</p>
                                    @error('no_telp')
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="email_pengiriman" class="block text-sm font-medium text-gray-700 mb-1">Email untuk Notifikasi</label>
                                    <input type="email" id="email_pengiriman" name="email_pengiriman" value="{{ old('email_pengiriman', auth()->user()->email) }}" placeholder="email@example.com"
                                        class="w-full rounded-lg border {{ $errors->has('email_pengiriman') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @error('email_pengiriman')
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="catatan_khusus" class="block text-sm font-medium text-gray-700 mb-1">Catatan Khusus</label>
                                <textarea id="catatan_khusus" name="catatan_khusus" rows="2" placeholder="Catatan khusus untuk vendor (opsional)"
                                    class="w-full rounded-lg border {{ $errors->has('catatan_khusus') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('catatan_khusus') }}</textarea>
                                @error('catatan_khusus')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <a href="{{ route('user.auctions.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                                <i class="fas fa-paper-plane mr-2"></i> Buat Permintaan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Tips --}}
                <div class="bg-primary-50 rounded-xl border border-primary-100 px-5 py-4">
                    <h3 class="font-semibold text-primary-900 mb-3">Tips Membuat Permintaan</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2 text-sm text-primary-800">
                            <i class="fas fa-check-circle text-primary-500 mt-0.5 flex-shrink-0"></i>
                            Jelaskan kebutuhan dengan detail
                        </li>
                        <li class="flex items-start gap-2 text-sm text-primary-800">
                            <i class="fas fa-check-circle text-primary-500 mt-0.5 flex-shrink-0"></i>
                            Sertakan spesifikasi yang jelas
                        </li>
                        <li class="flex items-start gap-2 text-sm text-primary-800">
                            <i class="fas fa-check-circle text-primary-500 mt-0.5 flex-shrink-0"></i>
                            Upload file desain jika ada
                        </li>
                        <li class="flex items-start gap-2 text-sm text-primary-800">
                            <i class="fas fa-check-circle text-primary-500 mt-0.5 flex-shrink-0"></i>
                            Set budget yang realistis
                        </li>
                        <li class="flex items-start gap-2 text-sm text-primary-800">
                            <i class="fas fa-check-circle text-primary-500 mt-0.5 flex-shrink-0"></i>
                            Berikan deadline yang wajar
                        </li>
                    </ul>
                </div>

                {{-- Alur Kerja --}}
                <div class="bg-white rounded-xl border border-gray-200 px-5 py-4">
                    <h3 class="font-semibold text-gray-900 mb-3">Alur Kerja</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="flex-shrink-0 w-7 h-7 rounded-full bg-primary-100 text-primary-700 text-xs font-bold flex items-center justify-center">1</span>
                            <span class="text-sm text-gray-700">Buat permintaan</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="flex-shrink-0 w-7 h-7 rounded-full bg-primary-100 text-primary-700 text-xs font-bold flex items-center justify-center">2</span>
                            <span class="text-sm text-gray-700">Vendor memberikan penawaran</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="flex-shrink-0 w-7 h-7 rounded-full bg-primary-100 text-primary-700 text-xs font-bold flex items-center justify-center">3</span>
                            <span class="text-sm text-gray-700">Pilih vendor terbaik</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="flex-shrink-0 w-7 h-7 rounded-full bg-primary-100 text-primary-700 text-xs font-bold flex items-center justify-center">4</span>
                            <span class="text-sm text-gray-700">Proses pengerjaan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
