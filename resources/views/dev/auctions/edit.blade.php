@extends('dev.layouts.app')

@section('title', 'Edit Lelang')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Lelang</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Edit data lelang {{ $auction->title }}</p>
        </div>
        <a href="{{ route('admin.auctions.show', $auction) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:text-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Detail
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Form Edit Lelang</h2>
                </div>
                <form action="{{ route('admin.auctions.update', $auction) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Title --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Judul Lelang <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $auction->title) }}" required
                               class="w-full px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 dark:focus:border-primary-400
                               @error('title') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-600 @enderror">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Category --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                            <select name="category" required
                                    class="w-full px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 dark:focus:border-primary-400
                                    @error('category') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-600 @enderror">
                                <option value="">Pilih Kategori</option>
                                @foreach(['Stiker', 'Banner', 'Flyer', 'Brochure', 'Poster', 'Kartu Nama', 'Undangan', 'Buku', 'Lainnya'] as $cat)
                                    <option value="{{ $cat }}" {{ old('category', $auction->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Quantity --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jumlah Produksi <span class="text-red-500">*</span></label>
                            <input type="number" name="quantity" value="{{ old('quantity', $auction->quantity) }}" min="1" required
                                   class="w-full px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 dark:focus:border-primary-400
                                   @error('quantity') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-600 @enderror">
                            @error('quantity')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Budget --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Budget Maksimal (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="budget" value="{{ old('budget', $auction->budget) }}" min="0" step="1000" required
                                   class="w-full px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 dark:focus:border-primary-400
                                   @error('budget') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-600 @enderror">
                            @error('budget')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Deadline --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Deadline <span class="text-red-500">*</span></label>
                            <input type="date" name="deadline" value="{{ old('deadline', $auction->deadline->format('Y-m-d')) }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required
                                   class="w-full px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 dark:focus:border-primary-400
                                   @error('deadline') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-600 @enderror">
                            @error('deadline')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Deskripsi <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="4" required
                                  class="w-full px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 dark:focus:border-primary-400
                                  @error('description') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-600 @enderror">{{ old('description', $auction->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Specifications --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Spesifikasi <span class="text-red-500">*</span></label>
                        <textarea name="specifications" rows="4" required
                                  class="w-full px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 dark:focus:border-primary-400
                                  @error('specifications') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-600 @enderror">{{ old('specifications', $auction->specifications) }}</textarea>
                        @error('specifications')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- File Upload --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">File Pendukung</label>
                        <input type="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               class="w-full px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white file:mr-3 file:py-1.5 file:px-3 file:text-sm file:font-medium file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900/30 dark:file:text-primary-300 dark:file:hover:bg-primary-900/50
                               @error('file') border-red-500 dark:border-red-500 @else border-gray-300 dark:border-gray-600 @enderror">
                        @error('file')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: PDF, DOC, DOCX, JPG, JPEG, PNG (Maksimal 10MB)</p>
                        @if($auction->file_path)
                            <div class="mt-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">File saat ini:</p>
                                <a href="{{ asset('storage/' . $auction->file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-primary-700 bg-primary-50 border border-primary-300 rounded-lg hover:bg-primary-100 dark:text-primary-300 dark:bg-primary-900/30 dark:border-primary-700 dark:hover:bg-primary-900/50 transition-colors">
                                    <i class="fas fa-file-alt"></i>
                                    Lihat File
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Submit --}}
                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors" data-loading>
                            <i class="fas fa-check"></i>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.auctions.show', $auction) }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:text-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Auction Info --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Informasi Lelang</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">ID Lelang</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">#{{ $auction->id }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Status Saat Ini</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                            @if($auction->status === 'pending') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300
                            @elseif($auction->status === 'active') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300
                            @elseif($auction->status === 'closed') bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300
                            @elseif($auction->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                            {{ ucfirst($auction->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Dibuat Oleh</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $auction->user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $auction->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Dibuat</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $auction->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Terakhir Update</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $auction->updated_at->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Penawaran</p>
                        <p class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ $auction->bids->count() }}</p>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Aksi Cepat</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <a href="{{ route('admin.auctions.show', $auction) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-primary-700 bg-primary-50 border border-primary-300 rounded-lg hover:bg-primary-100 dark:text-primary-300 dark:bg-primary-900/30 dark:border-primary-700 dark:hover:bg-primary-900/50 transition-colors">
                        <i class="fas fa-eye"></i>
                        Lihat Detail
                    </a>
                    @if($auction->status === 'active')
                        <form action="{{ route('admin.auctions.close', $auction) }}" method="POST" x-data>
                            @csrf
                            <button type="submit" @click="return confirm('Tutup lelang ini?')" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-amber-700 bg-amber-50 border border-amber-300 rounded-lg hover:bg-amber-100 dark:text-amber-300 dark:bg-amber-900/30 dark:border-amber-700 dark:hover:bg-amber-900/50 transition-colors">
                                <i class="fas fa-times-circle"></i>
                                Tutup Lelang
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('admin.auctions.destroy', $auction) }}" method="POST" x-data>
                        @csrf
                        @method('DELETE')
                        <button type="submit" @click="return confirm('Hapus lelang ini? Tindakan ini tidak dapat dibatalkan!')" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100 dark:text-red-300 dark:bg-red-900/30 dark:border-red-700 dark:hover:bg-red-900/50 transition-colors">
                            <i class="fas fa-trash"></i>
                            Hapus Lelang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
