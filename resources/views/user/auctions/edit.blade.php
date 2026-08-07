@extends('layouts.user')

@section('title', 'Edit Permintaan Cetak')

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Permintaan Cetak</h1>
            <p class="text-sm text-gray-500 mt-1">Perbarui informasi permintaan cetak Anda</p>
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

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 bg-red-50 border border-red-200 rounded-lg px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-red-600"></i>
                <span class="text-sm text-red-800">{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Payment Status Warning --}}
    @if ($auction->status === 'paid' || $auction->status === 'completed')
        <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-3">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-lock text-yellow-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-yellow-800">Lelang Sudah Dibayar!</p>
                    <p class="text-xs text-yellow-700 mt-0.5">Lelang ini sudah dibayar dan tidak dapat diedit lagi. Status: <span class="font-medium">{{ ucfirst($auction->status) }}</span></p>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('user.auctions.update', $auction) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

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
                            <input type="text" id="title" name="title" value="{{ old('title', $auction->title) }}" required
                                class="w-full rounded-lg border {{ $errors->has('title') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            @error('title')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Detail <span class="text-red-500">*</span></label>
                            <textarea id="description" name="description" rows="4" required
                                class="w-full rounded-lg border {{ $errors->has('description') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('description', $auction->description) }}</textarea>
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
                                        <option value="{{ $cat }}" {{ old('category', $auction->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Produksi <span class="text-red-500">*</span></label>
                                <input type="number" id="quantity" name="quantity" value="{{ old('quantity', $auction->quantity) }}" min="1" required
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
                                    <input type="number" id="budget" name="budget" value="{{ old('budget', $auction->budget) }}" min="0" step="0.01" required
                                        class="w-full rounded-lg border {{ $errors->has('budget') ? 'border-red-300' : 'border-gray-300' }} pl-8 pr-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                @error('budget')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1">Deadline Pengerjaan <span class="text-red-500">*</span></label>
                                <input type="date" id="deadline" name="deadline" value="{{ old('deadline', $auction->deadline->format('Y-m-d')) }}" required
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
                                class="w-full rounded-lg border {{ $errors->has('specifications') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('specifications', $auction->specifications) }}</textarea>
                            @error('specifications')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- File Upload --}}
                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-1">File Desain/Referensi</label>
                            @if ($auction->file_path)
                                <div class="mb-2">
                                    <x-ui.button :href="asset('storage/auction_files/' . $auction->file_path)" variant="outline-info" size="sm" target="_blank">
                                        <i class="fas fa-file mr-1"></i> Lihat File Saat Ini
                                    </x-ui.button>
                                </div>
                            @endif
                            <input type="file" id="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 file:cursor-pointer">
                            <p class="text-xs text-gray-400 mt-1">Format: PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 10MB). File baru akan menggantikan file lama.</p>
                            @error('file')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <x-ui.button :href="route('user.auctions.show', $auction)" variant="outline">
                                Batal
                            </x-ui.button>
                            <x-ui.button type="submit" variant="primary">
                                <i class="fas fa-save mr-2"></i> Simpan Perubahan
                            </x-ui.button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Status Lelang --}}
                <div class="bg-white rounded-xl border border-gray-200 px-5 py-4">
                    <h3 class="font-semibold text-gray-900 mb-3">Status Lelang</h3>
                    @php
                        $statusColors = [
                            'active' => 'bg-green-100 text-green-700',
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'closed' => 'bg-blue-100 text-blue-700',
                            'rejected' => 'bg-red-100 text-red-700',
                            'paid' => 'bg-green-100 text-green-700',
                            'completed' => 'bg-green-100 text-green-700',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$auction->status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ ucfirst($auction->status) }}
                    </span>
                    <div class="mt-3 space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Penawaran</span>
                            <span class="font-semibold text-gray-900">{{ $auction->getBidCount() }} vendor</span>
                        </div>
                        @if ($auction->getLowestBid())
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Terendah</span>
                                <span class="font-semibold text-gray-900">Rp {{ number_format($auction->getLowestBid()) }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="bg-gray-50 rounded-xl border border-gray-200 px-5 py-4">
                    <h3 class="font-semibold text-gray-900 mb-3">Catatan</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2 text-sm text-gray-600">
                            <i class="fas fa-info-circle text-gray-400 mt-0.5 flex-shrink-0"></i>
                            Edit hanya bisa dilakukan jika lelang masih aktif
                        </li>
                        <li class="flex items-start gap-2 text-sm text-gray-600">
                            <i class="fas fa-info-circle text-gray-400 mt-0.5 flex-shrink-0"></i>
                            Perubahan akan terlihat oleh vendor
                        </li>
                        <li class="flex items-start gap-2 text-sm text-gray-600">
                            <i class="fas fa-info-circle text-gray-400 mt-0.5 flex-shrink-0"></i>
                            File baru akan menggantikan file lama
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </form>
@endsection
