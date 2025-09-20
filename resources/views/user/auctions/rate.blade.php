@extends('layouts.user')

@section('title', 'Beri Rating Vendor')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Beri Rating untuk {{ $vendor->name }}</h3>
                    <div class="card-subtitle">Lelang: {{ $auction->title }}</div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('vendor.ratings.store', $auction) }}">
                        @csrf

                        <!-- Overall Rating -->
                        <div class="mb-4">
                            <label class="form-label">Rating Keseluruhan <span class="text-danger">*</span></label>
                            <div class="rating-input">
                                <div class="d-flex gap-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <label class="rating-star" for="rating_{{ $i }}">
                                            <input type="radio" name="rating" id="rating_{{ $i }}"
                                                value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}
                                                required>
                                            <i class="fas fa-star"></i>
                                        </label>
                                    @endfor
                                </div>
                                <div class="rating-text mt-2">
                                    <span id="rating-description">Pilih rating</span>
                                </div>
                            </div>
                            @error('rating')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Detailed Ratings -->
                        <div class="mb-4">
                            <label class="form-label">Rating Detail (Opsional)</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Kualitas Hasil</label>
                                        <select name="rating_details[quality]" class="form-control">
                                            <option value="">Pilih rating</option>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}"
                                                    {{ old('rating_details.quality') == $i ? 'selected' : '' }}>
                                                    {{ $i }} ⭐
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Kecepatan Pengerjaan</label>
                                        <select name="rating_details[speed]" class="form-control">
                                            <option value="">Pilih rating</option>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}"
                                                    {{ old('rating_details.speed') == $i ? 'selected' : '' }}>
                                                    {{ $i }} ⭐
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Pelayanan</label>
                                        <select name="rating_details[service]" class="form-control">
                                            <option value="">Pilih rating</option>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}"
                                                    {{ old('rating_details.service') == $i ? 'selected' : '' }}>
                                                    {{ $i }} ⭐
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Komunikasi</label>
                                        <select name="rating_details[communication]" class="form-control">
                                            <option value="">Pilih rating</option>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}"
                                                    {{ old('rating_details.communication') == $i ? 'selected' : '' }}>
                                                    {{ $i }} ⭐
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Comment -->
                        <div class="mb-4">
                            <label for="comment" class="form-label">Testimoni/Komentar</label>
                            <textarea class="form-control @error('comment') is-invalid @enderror" id="comment" name="comment" rows="4"
                                placeholder="Bagikan pengalaman Anda dengan vendor ini...">{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('user.auctions.show', $auction) }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Kirim Rating</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .rating-input .rating-star {
            cursor: pointer;
            font-size: 2rem;
            color: #ddd;
            transition: color 0.2s;
        }

        .rating-input .rating-star:hover,
        .rating-input .rating-star:hover~.rating-star {
            color: #ffc107;
        }

        .rating-input input[type="radio"] {
            display: none;
        }

        .rating-input input[type="radio"]:checked~.rating-star,
        .rating-input input[type="radio"]:checked~.rating-star~.rating-star {
            color: #ffc107;
        }

        .rating-input input[type="radio"]:checked+.rating-star {
            color: #ffc107;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ratingInputs = document.querySelectorAll('input[name="rating"]');
            const ratingDescription = document.getElementById('rating-description');

            const descriptions = {
                1: 'Sangat Buruk',
                2: 'Buruk',
                3: 'Biasa',
                4: 'Baik',
                5: 'Sangat Baik'
            };

            ratingInputs.forEach(input => {
                input.addEventListener('change', function() {
                    ratingDescription.textContent = descriptions[this.value];
                });
            });
        });
    </script>
@endsection
