<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Models\CmsSetting::get('site_name', 'Grafika Printing') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Fonts (Google Fonts - keeps CDN for font files, non-critical) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Welcome page styles (Font Awesome + custom CSS via Vite) -->
    @vite('resources/css/welcome.css')

</head>

<body>
    <!-- Navbar -->
    <nav class="navbar-custom" id="navbar">
        <div class="navbar-inner">
            <a href="/" class="navbar-brand">
                <img src="{{ asset('logo.png') }}" alt="Grafika" class="logo-icon" style="width: 36px; height: 36px; border-radius: 8px; object-fit: cover;">
                <span class="brand-text">GRAFIKA PRINTING</span>
            </a>

            <ul class="navbar-nav" id="navMenu">
                <li><a href="#social">Media Sosial</a></li>
                <li><a href="#auctions">Lelang</a></li>
                <li><a href="#how">Cara Kerja</a></li>
                <li><a href="#features">Fitur</a></li>
                <li><a href="#cta">Daftar</a></li>
            </ul>

            <div class="navbar-actions">
                @auth
                    @if (auth()->user()->usertype === 'vendor')
                        <a href="{{ route('vendor.dashboard') }}" class="btn-nav btn-nav-primary">Dashboard</a>
                    @elseif (auth()->user()->usertype === 'dev')
                        <a href="{{ route('admin.dashboard') }}" class="btn-nav btn-nav-primary">Admin Panel</a>
                    @else
                        <a href="{{ route('user.dashboard') }}" class="btn-nav btn-nav-primary">Dashboard</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn-nav btn-nav-outline">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-nav btn-nav-primary">Daftar</a>
                @endauth

                <button class="mobile-toggle" onclick="toggleNav()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container-custom">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-circle" style="font-size: 6px;"></i>
                    {{ \App\Models\CmsSetting::get('site_tagline', 'Platform Percetakan #1 di Indonesia') }}
                </div>
                <h1>{!! \App\Models\CmsSetting::get('hero_title', 'Solusi Percetakan<br><span>Mudah & Terpercaya</span>') !!}</h1>
                <p>{!! \App\Models\CmsSetting::get('hero_subtitle', 'Temukan vendor percetakan terbaik dengan harga kompetitif melalui sistem lelang transparan. Pembayaran aman via Xendit.') !!}</p>
                <div class="hero-actions">
                    @auth
                        @if (auth()->user()->usertype === 'user')
                            <a href="{{ route('user.auctions.create') }}" class="btn-hero btn-hero-primary">
                                <i class="fas fa-plus"></i> Buat Lelang Baru
                            </a>
                            <a href="{{ route('user.dashboard') }}" class="btn-hero btn-hero-outline">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        @elseif (auth()->user()->usertype === 'vendor')
                            <a href="{{ route('vendor.dashboard') }}" class="btn-hero btn-hero-primary">
                                <i class="fas fa-tachometer-alt"></i> Dashboard Vendor
                            </a>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="btn-hero btn-hero-primary">
                                <i class="fas fa-tachometer-alt"></i> Admin Panel
                            </a>
                        @endif
                    @else
                        <a href="{{ route('register') }}" class="btn-hero btn-hero-primary">
                            <i class="fas fa-rocket"></i> Mulai Gratis
                        </a>
                        <a href="{{ route('login') }}" class="btn-hero btn-hero-outline">
                            <i class="fas fa-sign-in-alt"></i> Masuk
                        </a>
                    @endauth
                </div>
            </div>

            @php
                $vendorCount = \App\Models\Vendor::count();
                $completedAuctions = class_exists('App\Models\Auction') ? \App\Models\Auction::where('status', 'completed')->count() : 0;
                $avgRating = \App\Models\VendorRating::where('is_verified', true)->avg('rating');
                $vendorStats = \App\Models\CmsSetting::get('stats_vendor_count', $vendorCount > 0 ? $vendorCount . '+' : '0+');
                $projectStats = \App\Models\CmsSetting::get('stats_project_count', $completedAuctions > 0 ? $completedAuctions . '+' : '0+');
                $ratingStats = \App\Models\CmsSetting::get('stats_rating', $avgRating ? number_format($avgRating, 1) . '★' : '4.8★');
            @endphp
            <div class="hero-stats">
                <div class="hero-stat">
                    <span class="stat-number">{{ $vendorStats }}</span>
                    <span class="stat-label">Vendor Aktif</span>
                </div>
                <div class="hero-stat">
                    <span class="stat-number">{{ $projectStats }}</span>
                    <span class="stat-label">Proyek Selesai</span>
                </div>
                <div class="hero-stat">
                    <span class="stat-number">{{ $ratingStats }}</span>
                    <span class="stat-label">Rating Vendor</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Media Section -->
    <section class="social-section" id="social">
        <div class="container-custom">
            <div class="section-header">
                <div class="section-badge">
                    <i class="fas fa-share-alt"></i> Ikuti Kami
                </div>
                <h2>Media Sosial</h2>
                <p>Temukan kami di platform media sosial favorit Anda</p>
            </div>

            <div class="social-grid">
                @php
                    $socialMedia = \App\Models\CmsSetting::getSocialMedia();
                @endphp

                <a href="{{ \App\Models\CmsSetting::get('social_instagram', '#') }}" class="social-card" target="_blank" rel="noopener">
                    <div class="social-icon instagram">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <div class="social-name">Instagram</div>
                    <div class="social-handle">@grafikaprinting</div>
                </a>
                <a href="{{ \App\Models\CmsSetting::get('social_facebook', '#') }}" class="social-card" target="_blank" rel="noopener">
                    <div class="social-icon facebook">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                    <div class="social-name">Facebook</div>
                    <div class="social-handle">Grafika Printing</div>
                </a>
                <a href="{{ \App\Models\CmsSetting::get('social_whatsapp', '#') }}" class="social-card" target="_blank" rel="noopener">
                    <div class="social-icon whatsapp">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div class="social-name">WhatsApp</div>
                    <div class="social-handle">Chat Langsung</div>
                </a>
                <a href="{{ \App\Models\CmsSetting::get('social_tiktok', '#') }}" class="social-card" target="_blank" rel="noopener">
                    <div class="social-icon tiktok">
                        <i class="fab fa-tiktok"></i>
                    </div>
                    <div class="social-name">TikTok</div>
                    <div class="social-handle">@grafikaprinting</div>
                </a>
                <a href="{{ \App\Models\CmsSetting::get('social_youtube', '#') }}" class="social-card" target="_blank" rel="noopener">
                    <div class="social-icon youtube">
                        <i class="fab fa-youtube"></i>
                    </div>
                    <div class="social-name">YouTube</div>
                    <div class="social-handle">Grafika Printing</div>
                </a>
            </div>
        </div>
    </section>

    <!-- Auctions Section -->
    <section class="auctions-section" id="auctions">
        <div class="container-custom">
            <div class="section-header">
                <div class="section-badge">
                    <i class="fas fa-gavel"></i> Proyek Lelang
                </div>
                <h2>Lelang Percetakan Terbaru</h2>
                <p>Lihat proyek lelang aktif dan temukan vendor terbaik untuk kebutuhan cetak Anda</p>
            </div>

            <div class="auctions-grid">
                @forelse ($auctions as $auction)
                    <div class="auction-card">
                        <div class="auction-card-header">
                            <span class="auction-badge badge-{{ $auction->status === 'active' ? 'active' : ($auction->status === 'pending_approval' ? 'pending' : 'closed') }}">
                                {{ $auction->status === 'active' ? 'Aktif' : ($auction->status === 'pending_approval' ? 'Menunggu' : ($auction->status === 'completed' ? 'Selesai' : 'Ditutup')) }}
                            </span>
                            <span style="font-size: 12px; color: var(--text-muted);">
                                <i class="fas fa-clock me-1"></i>{{ $auction->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <div class="auction-card-body">
                            <h5>{{ $auction->title }}</h5>
                            <p>{{ $auction->description ?? 'Deskripsi proyek percetakan' }}</p>
                            <div class="auction-meta">
                                <span><i class="fas fa-layer-group"></i> {{ $auction->quantity ?? '-' }} pcs</span>
                                <span><i class="fas fa-users"></i> {{ $auction->bids_count ?? $auction->bids()->count() }} penawaran</span>
                            </div>
                        </div>
                        <div class="auction-card-footer">
                            <div class="auction-price">
                                @if($auction->budget_min || $auction->budget_max)
                                    Rp {{ number_format($auction->budget_min ?? $auction->estimated_budget ?? 0, 0, ',', '.') }}
                                    @if($auction->budget_max)
                                        - {{ number_format($auction->budget_max, 0, ',', '.') }}
                                    @endif
                                @else
                                    Harga Kompetitif
                                @endif
                            </div>
                            @auth
                                <a href="{{ route('user.auctions.show', $auction->id) }}" class="btn-auction">Lihat Detail</a>
                            @else
                                <a href="{{ route('login') }}" class="btn-auction">Masuk untuk Detail</a>
                            @endauth
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                        <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(102, 126, 234, 0.1); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                            <i class="fas fa-gavel" style="font-size: 24px; color: var(--primary);"></i>
                        </div>
                        <h5 style="font-weight: 700; margin-bottom: 8px;">Belum Ada Lelang</h5>
                        <p style="color: var(--text-secondary); font-size: 14px;">Lelang pertama akan segera tersedia. Daftar untuk mendapatkan notifikasi!</p>
                    </div>
                @endforelse
            </div>

            <div style="text-align: center; margin-top: 32px;">
                @auth
                    @if (auth()->user()->usertype === 'user')
                        <a href="{{ route('user.auctions.index') }}" class="view-all-link">
                            Lihat Semua Lelang <i class="fas fa-arrow-right"></i>
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="view-all-link">
                        Daftar untuk Melihat Lelang <i class="fas fa-arrow-right"></i>
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-section" id="how">
        <div class="container-custom">
            <div class="section-header">
                <div class="section-badge">
                    <i class="fas fa-lightbulb"></i> Cara Kerja
                </div>
                <h2>Proses Sederhana</h2>
                <p>Empat langkah mudah untuk mendapatkan hasil cetak terbaik</p>
            </div>

            <div class="how-grid">
                <div class="how-card">
                    <div class="step-number">1</div>
                    <h5>Buat Permintaan</h5>
                    <p>Isi detail proyek cetak Anda termasuk spesifikasi, file, dan deadline</p>
                </div>
                <div class="how-card">
                    <div class="step-number">2</div>
                    <h5>Vendor Menawar</h5>
                    <p>Vendor percetakan memberikan penawaran harga terbaik untuk proyek Anda</p>
                </div>
                <div class="how-card">
                    <div class="step-number">3</div>
                    <h5>Pilih Pemenang</h5>
                    <p>Bandingkan penawaran dan pilih vendor yang paling sesuai dengan kebutuhan</p>
                </div>
                <div class="how-card">
                    <div class="step-number">4</div>
                    <h5>Proses & Kirim</h5>
                    <p>Vendor memproses pesanan dan mengirim hasil cetak langsung ke alamat Anda</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container-custom">
            <div class="section-header">
                <div class="section-badge">
                    <i class="fas fa-star"></i> Keunggulan
                </div>
                <h2>Mengapa Memilih Grafika?</h2>
                <p>Platform percetakan terlengkap dengan teknologi modern</p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon blue">
                        <i class="fas fa-gavel"></i>
                    </div>
                    <h5>Sistem Lelang</h5>
                    <p>Dapatkan harga terbaik melalui lelang transparan dari vendor percetakan terpercaya</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon green">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5>Pembayaran Aman</h5>
                    <p>Terintegrasi Xendit untuk pembayaran QRIS, transfer bank, dan e-wallet yang aman</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon amber">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h5>Real-time Tracking</h5>
                    <p>Pantau status pesanan secara real-time dari proses produksi hingga pengiriman</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon cyan">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h5>Escrow Payment</h5>
                    <p>Dana dipegang aman oleh sistem hingga pesanan terkonfirmasi diterima dengan baik</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Vendor Benefits -->
    <section class="vendor-benefits">
        <div class="container-custom">
            <div class="section-header">
                <div class="section-badge">
                    <i class="fas fa-store"></i> Untuk Vendor
                </div>
                <h2>Manajemen Bisnis Lengkap</h2>
                <p>Kelola bisnis percetakan Anda dengan mudah melalui platform kami</p>
            </div>

            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon red">
                        <i class="fas fa-store"></i>
                    </div>
                    <div>
                        <h5>POS System</h5>
                        <p>Sistem point of sale lengkap untuk mengelola produk, inventori, dan transaksi harian</p>
                    </div>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon blue">
                        <i class="fas fa-link"></i>
                    </div>
                    <div>
                        <h5>Linktree Vendor</h5>
                        <p>Buat halaman linktree profesional dengan custom URL dan pembayaran QRIS</p>
                    </div>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon green">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <h5>Wallet & Withdrawal</h5>
                        <p>Sistem wallet terintegrasi untuk menerima pembayaran dan penarikan dana instan</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="cta">
        <div class="container-custom">
            <div class="cta-inner">
                <div class="cta-content">
                    <h2>Siap Memulai Proyek Cetak Anda?</h2>
                    <p>Bergabung dengan Grafika Printing dan dapatkan harga terbaik untuk kebutuhan percetakan Anda. Vendor dapat mengelola bisnis dengan mudah.</p>
                    <div class="cta-actions">
                        @auth
                            @if (auth()->user()->usertype === 'user')
                                <a href="{{ route('user.auctions.create') }}" class="btn-cta btn-cta-primary">
                                    <i class="fas fa-plus-circle"></i> Buat Lelang Baru
                                </a>
                                <a href="{{ route('user.dashboard') }}" class="btn-cta btn-cta-outline">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            @elseif (auth()->user()->usertype === 'vendor')
                                <a href="{{ route('vendor.dashboard') }}" class="btn-cta btn-cta-primary">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard Vendor
                                </a>
                            @else
                                <a href="{{ route('admin.dashboard') }}" class="btn-cta btn-cta-primary">
                                    <i class="fas fa-tachometer-alt"></i> Admin Panel
                                </a>
                            @endif
                        @else
                            <a href="{{ route('register') }}" class="btn-cta btn-cta-primary">
                                <i class="fas fa-rocket"></i> Daftar Gratis Sekarang
                            </a>
                            <a href="{{ route('login') }}" class="btn-cta btn-cta-outline">
                                <i class="fas fa-sign-in-alt"></i> Masuk
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="cta-stats">
                    <div class="cta-stat">
                        <span class="stat-value">100+</span>
                        <span class="stat-text">Vendor Aktif</span>
                    </div>
                    <div class="cta-stat">
                        <span class="stat-value">500+</span>
                        <span class="stat-text">Proyek Selesai</span>
                    </div>
                    <div class="cta-stat">
                        <span class="stat-value">4.8★</span>
                        <span class="stat-text">Rating Vendor</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container-custom">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="brand-name">{{ \App\Models\CmsSetting::get('site_name', 'Grafika Printing') }}</div>
                    <p>Platform percetakan terpercaya dengan sistem lelang transparan. Temukan vendor terbaik untuk kebutuhan cetak Anda.</p>
                    <div class="footer-contact-item">
                        <i class="fas fa-phone"></i>
                        {{ \App\Models\CmsSetting::get('contact_phone', '081515876755') }}
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-envelope"></i>
                        {{ \App\Models\CmsSetting::get('contact_email', 'info@grafikaprinting.com') }}
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ \App\Models\CmsSetting::get('contact_address', 'Pesantren Peterongan Jombang') }}
                    </div>
                    <div class="footer-social">
                        <a href="{{ \App\Models\CmsSetting::get('social_facebook', '#') }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="{{ \App\Models\CmsSetting::get('social_instagram', '#') }}" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="{{ \App\Models\CmsSetting::get('social_twitter', '#') }}" target="_blank"><i class="fab fa-twitter"></i></a>
                        <a href="{{ \App\Models\CmsSetting::get('social_youtube', '#') }}" target="_blank"><i class="fab fa-youtube"></i></a>
                        <a href="{{ \App\Models\CmsSetting::get('social_whatsapp', '#') }}" target="_blank"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h6>Platform</h6>
                    <ul>
                        <li><a href="#auctions">Lelang Percetakan</a></li>
                        <li><a href="#how">Cara Kerja</a></li>
                        <li><a href="#features">Fitur</a></li>
                        <li><a href="#cta">Daftar Vendor</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h6>Lainnya</h6>
                    <ul>
                        <li><a href="{{ \App\Models\CmsSetting::get('footer_about', '#') }}">Tentang Grafika</a></li>
                        <li><a href="{{ \App\Models\CmsSetting::get('footer_terms', '#') }}">Aturan Penggunaan</a></li>
                        <li><a href="{{ \App\Models\CmsSetting::get('footer_privacy', '#') }}">Kebijakan Privasi</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h6>Jam Layanan</h6>
                    <p style="font-size: 13px; color: var(--text-secondary); line-height: 1.7;">
                        {{ \App\Models\CmsSetting::get('contact_hours', 'Senin - Jumat: 09:00 - 17:00 WIB') }}<br>
                        {{ \App\Models\CmsSetting::get('contact_hours_weekend', 'Sabtu - Minggu: 09:00 - 15:00 WIB') }}
                    </p>
                </div>
            </div>
            <div class="footer-bottom">
                <span>{{ \App\Models\CmsSetting::get('footer_copyright', '©2026 Grafika Printing. Hak Cipta Terpelihara CV. Grafika Digital Solution') }}</span>
                <span>Dibuat dengan <i class="fas fa-heart" style="color: #ef4444; font-size: 11px;"></i> di Indonesia</span>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 20) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Mobile nav toggle
        function toggleNav() {
            document.getElementById('navMenu').classList.toggle('active');
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    // Close mobile nav
                    document.getElementById('navMenu').classList.remove('active');
                }
            });
        });

        // Intersection Observer for animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.animate-in').forEach(el => observer.observe(el));
    </script>
</body>

</html>
