@extends('layouts.user')

@section('title', 'Profile Vendor - ' . $vendor->name)

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        @if ($vendor->logo)
                            <img src="{{ asset('storage/' . $vendor->logo) }}" alt="{{ $vendor->name }}"
                                class="rounded-circle me-3" width="60" height="60">
                        @else
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 60px; height: 60px;">
                                <span class="text-white fw-bold fs-4">{{ substr($vendor->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div>
                            <h3 class="mb-1">{{ $vendor->name }}</h3>
                            <p class="text-muted mb-0">{{ $vendor->email }} • {{ $vendor->phone }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Vendor Info -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Informasi Vendor</h3>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Alamat:</strong><br>
                                        {{ $vendor->address }}
                                    </div>
                                    @if ($vendor->website)
                                        <div class="mb-3">
                                            <strong>Website:</strong><br>
                                            <a href="{{ $vendor->website }}" target="_blank" class="text-decoration-none">
                                                {{ $vendor->website }}
                                                <i class="fas fa-external-link-alt ms-1" style="font-size: 0.8em;"></i>
                                            </a>
                                        </div>
                                    @endif
                                    <div class="mb-3">
                                        <strong>Status:</strong>
                                        <span class="badge {{ $vendor->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $vendor->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Bergabung:</strong><br>
                                        {{ $vendor->created_at->format('d M Y') }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Total Proyek:</strong><br>
                                        <span class="badge bg-primary">{{ $vendor->completedAuctions()->count() }}
                                            selesai</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rating Summary -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Rating & Testimoni</h3>
                                </div>
                                <div class="card-body">
                                    <!-- Rating Overview -->
                                    <div class="row mb-4">
                                        <div class="col-md-4 text-center">
                                            <div class="display-4 fw-bold text-warning">
                                                {{ number_format($averageRating, 1) }}</div>
                                            <div class="text-muted">Rating Rata-rata</div>
                                            <div class="mt-2">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= floor($averageRating))
                                                        <i class="fas fa-star text-warning"></i>
                                                    @elseif($i - 0.5 <= $averageRating)
                                                        <i class="fas fa-star-half-alt text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-warning"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="display-4 fw-bold text-primary">{{ $ratingCount }}</div>
                                            <div class="text-muted">Total Rating</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <div class="fw-bold">Distribusi Rating</div>
                                                @foreach ($ratingDistribution as $dist)
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="me-2">{{ $dist->rating }} ⭐</span>
                                                        <div class="progress flex-grow-1" style="height: 8px;">
                                                            <div class="progress-bar bg-warning"
                                                                style="width: {{ $ratingCount > 0 ? ($dist->count / $ratingCount) * 100 : 0 }}%">
                                                            </div>
                                                        </div>
                                                        <span class="ms-2 small">{{ $dist->count }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Recent Ratings -->
                                    <h6 class="mb-3">Testimoni Terbaru</h6>
                                    @if ($vendor->verifiedRatings->count() > 0)
                                        @foreach ($vendor->verifiedRatings->take(5) as $rating)
                                            <div class="border rounded p-3 mb-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <strong>{{ $rating->user->name }}</strong>
                                                        <div class="text-muted small">
                                                            {{ $rating->created_at->format('d M Y') }}</div>
                                                    </div>
                                                    <div>
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($i <= $rating->rating)
                                                                <i class="fas fa-star text-warning"></i>
                                                            @else
                                                                <i class="far fa-star text-warning"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                                @if ($rating->comment)
                                                    <p class="mb-0">{{ $rating->comment }}</p>
                                                @endif
                                                @if ($rating->rating_details)
                                                    <div class="mt-2">
                                                        @foreach ($rating->rating_details as $key => $value)
                                                            @if ($value)
                                                                <span class="badge bg-light text-dark me-1">
                                                                    {{ ucfirst($key) }}: {{ $value }}/5
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-star fa-3x mb-3"></i>
                                            <p>Belum ada testimoni untuk vendor ini</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
