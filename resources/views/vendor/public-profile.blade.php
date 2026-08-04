<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $vendor->name }} - Grafika Printing</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .vendor-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
        }
        .vendor-logo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        .vendor-logo-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: bold;
            border: 4px solid rgba(255, 255, 255, 0.3);
        }
        .info-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 1rem;
        }
        .rating-stars {
            color: #f59e0b;
            font-size: 1.2rem;
        }
        .badge-active {
            background: #10b981;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .badge-inactive {
            background: #ef4444;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>
    <div class="page">
        <!-- Vendor Header -->
        <div class="vendor-header">
            <div class="container-xl">
                <div class="row align-items-center">
                    <div class="col-auto">
                        @if($vendor->logo)
                            <img src="{{ asset('vendors_logo/' . $vendor->logo) }}" alt="{{ $vendor->name }}" class="vendor-logo">
                        @else
                            <div class="vendor-logo-placeholder">
                                {{ strtoupper(substr($vendor->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <div class="col">
                        <h1 class="mb-1">{{ $vendor->name }}</h1>
                        <div class="d-flex align-items-center gap-3">
                            @if($vendor->is_active)
                                <span class="badge-active"><i class="fas fa-check-circle me-1"></i>Aktif</span>
                            @else
                                <span class="badge-inactive"><i class="fas fa-times-circle me-1"></i>Tidak Aktif</span>
                            @endif
                            <div class="rating-stars">
                                @php
                                    $avgRating = $vendor->average_rating;
                                    $ratingCount = $vendor->rating_count;
                                @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($avgRating))
                                        <i class="fas fa-star"></i>
                                    @elseif($i - $avgRating < 1 && $i - $avgRating > 0)
                                        <i class="fas fa-star-half-alt"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                                <span class="ms-2 text-white-50">({{ number_format($avgRating, 1) }} · {{ $ratingCount }} ulasan)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor Details -->
        <div class="page-body">
            <div class="container-xl">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- About -->
                        <div class="info-card">
                            <h3 class="mb-3"><i class="fas fa-store me-2"></i>Tentang Vendor</h3>
                            <div class="row">
                                @if($vendor->email)
                                    <div class="col-md-6 mb-2">
                                        <strong><i class="fas fa-envelope me-2 text-muted"></i>Email</strong>
                                        <p>{{ $vendor->email }}</p>
                                    </div>
                                @endif
                                @if($vendor->phone)
                                    <div class="col-md-6 mb-2">
                                        <strong><i class="fas fa-phone me-2 text-muted"></i>Telepon</strong>
                                        <p>{{ $vendor->phone }}</p>
                                    </div>
                                @endif
                                @if($vendor->address)
                                    <div class="col-12 mb-2">
                                        <strong><i class="fas fa-map-marker-alt me-2 text-muted"></i>Alamat</strong>
                                        <p>{{ $vendor->address }}</p>
                                    </div>
                                @endif
                                @if($vendor->website)
                                    <div class="col-12 mb-2">
                                        <strong><i class="fas fa-globe me-2 text-muted"></i>Website</strong>
                                        <p><a href="{{ $vendor->website }}" target="_blank">{{ $vendor->website }}</a></p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Ratings -->
                        <div class="info-card">
                            <h3 class="mb-3"><i class="fas fa-star me-2"></i>Ulasan Pelanggan</h3>
                            @php
                                $verifiedRatings = $vendor->verifiedRatings()->with('user')->latest()->limit(10)->get();
                            @endphp
                            @if($verifiedRatings->count() > 0)
                                @foreach($verifiedRatings as $rating)
                                    <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                                        <div class="avatar avatar-sm me-3">
                                            {{ strtoupper(substr($rating->user->name ?? 'U', 0, 2)) }}
                                        </div>
                                        <div class="flex-fill">
                                            <div class="d-flex justify-content-between">
                                                <strong>{{ $rating->user->name ?? 'Anonymous' }}</strong>
                                                <div class="rating-stars" style="font-size: 0.9rem;">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $rating->rating)
                                                            <i class="fas fa-star"></i>
                                                        @else
                                                            <i class="far fa-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>
                                            @if($rating->feedback)
                                                <p class="text-muted mb-0 mt-1">{{ $rating->feedback }}</p>
                                            @endif
                                            <small class="text-muted">{{ $rating->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-star fa-2x mb-2"></i>
                                    <p>Belum ada ulasan</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Quick Info -->
                        <div class="info-card">
                            <h4 class="mb-3">Informasi Cepat</h4>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-calendar me-3 text-muted"></i>
                                <div>
                                    <small class="text-muted">Bergabung</small>
                                    <div>{{ $vendor->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-box me-3 text-muted"></i>
                                <div>
                                    <small class="text-muted">Produk</small>
                                    <div>{{ $vendor->produk()->count() }} produk</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-handshake me-3 text-muted"></i>
                                <div>
                                    <small class="text-muted">Transaksi Selesai</small>
                                    <div>{{ $vendor->transaksi()->where('status', 'completed')->count() }} transaksi</div>
                                </div>
                            </div>
                        </div>

                        <!-- Back Link -->
                        <div class="info-card text-center">
                            <a href="{{ route('welcome') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer footer-transparent d-print-none">
            <div class="container-xl">
                <div class="row text-center align-items-center flex-row-reverse">
                    <div class="col-12">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item">
                                Copyright © {{ date('Y') }}
                                <a href="{{ route('welcome') }}" class="link-secondary">Grafika Printing</a>.
                                All rights reserved.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0/dist/js/tabler.min.js"></script>
</body>

</html>
