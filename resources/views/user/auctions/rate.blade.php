@extends('layouts.user')

@section('title', 'Beri Rating Vendor')

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Beri Rating untuk {{ $vendor->name }}</h1>
            <p class="text-sm text-gray-500 mt-1">Lelang: {{ $auction->title }}</p>
        </div>
    </div>

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="mb-6 bg-green-50 border border-green-200 rounded-lg px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2"><i class="fas fa-check-circle text-green-600"></i><span class="text-sm text-green-800">{{ session('success') }}</span></div>
            <button @click="show = false" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 bg-red-50 border border-red-200 rounded-lg px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2"><i class="fas fa-exclamation-circle text-red-600"></i><span class="text-sm text-red-800">{{ session('error') }}</span></div>
            <button @click="show = false" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Form Rating</h2>
            </div>
            <div class="px-6 py-5">
                <form method="POST" action="{{ route('vendor.ratings.store', $auction) }}" x-data="ratingForm()">
                    @csrf

                    {{-- Overall Rating --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating Keseluruhan <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" @click="rating = {{ $i }}" class="text-3xl transition-colors focus:outline-none"
                                    :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'">
                                    &#9733;
                                </button>
                            @endfor
                            <input type="hidden" name="rating" :value="rating" required>
                        </div>
                        <p class="text-sm text-gray-600 mt-1" x-text="descriptions[rating] || 'Pilih rating'"></p>
                        @error('rating')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Detailed Ratings --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Rating Detail (Opsional)</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Kualitas Hasil</label>
                                <select name="rating_details[quality]" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Pilih rating</option>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('rating_details.quality') == $i ? 'selected' : '' }}>{{ $i }} &#9733;</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Kecepatan Pengerjaan</label>
                                <select name="rating_details[speed]" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Pilih rating</option>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('rating_details.speed') == $i ? 'selected' : '' }}>{{ $i }} &#9733;</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Pelayanan</label>
                                <select name="rating_details[service]" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Pilih rating</option>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('rating_details.service') == $i ? 'selected' : '' }}>{{ $i }} &#9733;</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Komunikasi</label>
                                <select name="rating_details[communication]" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Pilih rating</option>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('rating_details.communication') == $i ? 'selected' : '' }}>{{ $i }} &#9733;</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Comment --}}
                    <div class="mb-6">
                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Testimoni/Komentar</label>
                        <textarea id="comment" name="comment" rows="4" placeholder="Bagikan pengalaman Anda dengan vendor ini..."
                            class="w-full rounded-lg border {{ $errors->has('comment') ? 'border-red-300' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('user.auctions.show', $auction) }}" class="inline-flex items-center justify-center border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold py-2 px-4 rounded-lg transition">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                            <i class="fas fa-paper-plane mr-2"></i> Kirim Rating
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@once
<script>
    function ratingForm() {
        return {
            rating: {{ old('rating') ?: '0' }},
            descriptions: {
                1: 'Sangat Buruk',
                2: 'Buruk',
                3: 'Biasa',
                4: 'Baik',
                5: 'Sangat Baik'
            }
        }
    }
</script>
@endonce
