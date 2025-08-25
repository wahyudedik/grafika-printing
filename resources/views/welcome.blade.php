<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon.png') }}">
    <title>Grafika Printing - Smart Printing Management System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://unpkg.com/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet">
    <script src="https://unpkg.com/@tabler/core@latest/dist/js/tabler.min.js"></script>
    <style>
        .logo-container {
            background: #000;
            padding: 10px 20px;
            border-radius: 6px;
            position: relative;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 0;
        }

        .navbar-nav {
            margin: 0;
            padding: 0;
        }

        .navbar-nav .nav-item {
            margin: 0;
        }

        .navbar-nav .nav-link {
            margin: 0;
            padding: 8px 16px !important;
        }

        .logo-container::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(to right, #00FFFF 25%, #FF00FF 25% 50%, #FFFF00 50% 75%, #000000 75%);
            border-radius: 0 0 6px 6px;
        }

        .btn-pink {
            background: linear-gradient(45deg, #FF69B4, #FF1493);
            border: none;
            color: white;
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(255, 105, 180, 0.3);
        }

        .btn-pink:hover {
            background: linear-gradient(45deg, #FF1493, #FF69B4);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 105, 180, 0.4);
        }

        .social-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            margin: 8px;
            width: 220px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
        }

        .social-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .social-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            margin: 0 auto 12px;
            object-fit: cover;
            border: 3px solid #f8f9fa;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .mobile-preview {
            width: 140px;
            height: 240px;
            border-radius: 16px;
            object-fit: cover;
            margin: 12px auto;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            border: 2px solid #f0f0f0;
        }

        .project-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
        }

        .project-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .project-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .project-details {
            padding: 20px;
        }

        .project-status {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 12px;
            border-radius: 8px;
            margin: 12px 0;
            font-size: 0.9em;
            border-left: 4px solid #007bff;
        }

        .banner-content {
            background: #e9ecef;
            padding: 50px 40px;
            border-radius: 12px;
            position: relative;
            min-height: 320px;
        }

        .carousel-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: #f8f9fa;
            border: none;
            border-radius: 8px;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            z-index: 10;
            transition: all 0.3s ease;
        }

        .carousel-nav-btn:hover {
            background: #e9ecef;
            transform: translateY(-50%) scale(1.05);
        }

        .carousel-nav-prev {
            left: 25px;
        }

        .carousel-nav-next {
            right: 25px;
        }

        .receipt-stack {
            position: absolute;
            right: 100px;
            top: 50%;
            transform: translateY(-50%);
            width: 220px;
            height: 160px;
        }

        .banner-text {
            position: absolute;
            left: 100px;
            top: 50%;
            transform: translateY(-50%);
            max-width: 320px;
        }

        .overlay-text {
            position: absolute;
            top: 50px;
            right: 120px;
            text-align: center;
        }

        .gradient-text {
            font-size: 2.5em;
            font-weight: bold;
            background: linear-gradient(45deg, #FF69B4, #FF8C00);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            line-height: 0.9;
        }
    </style>
</head>

<body class="font-sans antialiased bg-light">
    <!-- Top Banner -->
    <div class="bg-white py-2 border-bottom">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-end">
                <span class="text-dark small">Yuk Ngeprint di grafika. Daftar Sekarang!</span>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="logo-container">
                    <span class="text-white fw-bold">GRAFIKA PRINTING</span>
                </div>
                <div class="d-flex align-items-center">
                    <nav class="navbar-nav me-4">
                        <ul class="navbar-nav d-flex align-items-center mb-0">
                            <li class="nav-item"><a class="nav-link text-dark px-3" href="#">Projek Cetak</a></li>
                            <li class="nav-item"><span class="text-muted mx-2">|</span></li>
                            <li class="nav-item"><a class="nav-link text-dark px-3" href="#">Layanan</a></li>
                            <li class="nav-item"><span class="text-muted mx-2">|</span></li>
                            <li class="nav-item"><a class="nav-link text-dark px-3" href="#">Cara Kerja</a></li>
                        </ul>
                    </nav>
                    <a href="{{ route('login') }}" class="btn btn-pink">LOGIN</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Banner Section -->
    <section class="py-5 bg-light">
        <div class="container-fluid px-4">
            <div class="banner-content position-relative">
                <!-- Carousel Navigation Buttons -->
                <button class="carousel-nav-btn carousel-nav-prev">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <polyline points="15,18 9,12 15,6"></polyline>
                    </svg>
                </button>
                <button class="carousel-nav-btn carousel-nav-next">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <polyline points="9,18 15,12 9,6"></polyline>
                    </svg>
                </button>

                <!-- Content -->
                <div class="banner-text">
                    <div class="text-dark fw-bold fs-4">Bahan HVS. NCR</div>
                    <div class="text-dark fw-bold fs-4">Sudah termasuk</div>
                    <div class="text-dark fw-bold fs-4">porforasi & potong</div>
                </div>
                <div class="receipt-stack">
                    <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=400&h=300&fit=crop"
                        alt="Receipt Books" class="w-100 h-100 object-fit-cover">
                </div>
                <div class="overlay-text">
                    <div class="gradient-text">NOTA</div>
                    <div class="gradient-text">ONLINE</div>
                    <div class="gradient-text">SHOP</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Media Section -->
    <section class="py-5 bg-white">
        <div class="container-fluid px-4">
            <h2 class="h2 text-center mb-5">Bikin Website anda di sini..!!!</h2>
            <div class="row justify-content-center">
                <div class="col-auto">
                    <div class="social-card">
                        <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face"
                            alt="@miss.buku" class="social-avatar">
                        <div class="fw-bold">@miss.buku</div>
                        <div class="text-muted small">188 Following</div>
                        <div class="text-muted small">13.1K Followers</div>
                        <div class="text-muted small">584 Posts</div>
                        <img src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=140&h=240&fit=crop"
                            alt="Mobile Preview" class="mobile-preview">
                    </div>
                </div>
                <div class="col-auto">
                    <div class="social-card">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face"
                            alt="@mokbranding" class="social-avatar">
                        <div class="fw-bold">@mokbranding</div>
                        <div class="text-muted small">4.8K Following</div>
                        <div class="text-muted small">42.9K Followers</div>
                        <div class="text-muted small">552 Posts</div>
                        <img src="https://images.unsplash.com/photo-1551650975-87deedd944c3?w=140&h=240&fit=crop"
                            alt="Mobile Preview" class="mobile-preview">
                    </div>
                </div>
                <div class="col-auto">
                    <div class="social-card">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face"
                            alt="@mudakreativ" class="social-avatar">
                        <div class="fw-bold">@mudakreativ</div>
                        <div class="text-muted small">236 Following</div>
                        <div class="text-muted small">16.6K Followers</div>
                        <div class="text-muted small">70 Posts</div>
                        <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?w=140&h=240&fit=crop"
                            alt="Mobile Preview" class="mobile-preview">
                    </div>
                </div>
                <div class="col-auto">
                    <div class="social-card">
                        <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face"
                            alt="@pearling" class="social-avatar">
                        <div class="fw-bold">@pearling</div>
                        <div class="text-muted small">1.6K Following</div>
                        <div class="text-muted small">31.7K Followers</div>
                        <div class="text-muted small">59 Posts</div>
                        <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=140&h=240&fit=crop"
                            alt="Mobile Preview" class="mobile-preview">
                    </div>
                </div>
                <div class="col-auto">
                    <div class="social-card">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&h=150&fit=crop&crop=face"
                            alt="@prodemy.asia" class="social-avatar">
                        <div class="fw-bold">@prodemy.asia</div>
                        <div class="text-muted small">590 Following</div>
                        <div class="text-muted small">5,405 Followers</div>
                        <div class="text-muted small">701 Posts</div>
                        <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=140&h=240&fit=crop"
                            alt="Mobile Preview" class="mobile-preview">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Auction Projects Section -->
    <section class="bg-light py-5">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 mb-0">PROJEK LELANG</h2>
                <a href="#" class="btn btn-danger">Lihat Semua</a>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="project-card">
                        <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=400&h=220&fit=crop"
                            alt="Bendera Printing" class="project-image">
                        <div class="project-details">
                            <h5 class="card-title">Bendera Printing Bahan Kain Satin Tebal</h5>
                            <div class="project-status">
                                <div>Jumlah Produksi: 40.000 pcs</div>
                                <div>Budget: Rp. 120.000</div>
                                <div>Deadline Pengerjakan: 10 Hari</div>
                            </div>
                            <div class="project-status">
                                <div>Status: Masih Memilih</div>
                                <div>Batas Penawaran: 25 Februari 2023</div>
                                <div>Pelamar: 4 Vendor</div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="#" class="btn btn-success btn-sm">PENAWARAN</a>
                                <a href="#" class="btn btn-secondary btn-sm">DETAIL</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="project-card">
                        <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=400&h=220&fit=crop"
                            alt="Cetak Amplop Lebaran" class="project-image">
                        <div class="project-details">
                            <h5 class="card-title">Cetak Amplop lebaran</h5>
                            <div class="project-status">
                                <div>Jumlah Produksi: 40.000 pcs</div>
                                <div>Budget: Rp. 120.000</div>
                                <div>Deadline Pengerjakan: 10 Hari</div>
                            </div>
                            <div class="project-status">
                                <div>Status: Masih Memilih</div>
                                <div>Batas Penawaran: 25 Februari 2023</div>
                                <div>Pelamar: 4 Vendor</div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="#" class="btn btn-success btn-sm">PENAWARAN</a>
                                <a href="#" class="btn btn-secondary btn-sm">DETAIL</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="project-card">
                        <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=220&fit=crop"
                            alt="Cetak Kalender 2023" class="project-image">
                        <div class="project-details">
                            <h5 class="card-title">Cetak Kalender 2023</h5>
                            <div class="project-status">
                                <div>Jumlah Produksi: 40.000 pcs</div>
                                <div>Budget: Rp. 120.000</div>
                                <div>Deadline Pengerjakan: 10 Hari</div>
                            </div>
                            <div class="project-status">
                                <div>Status: Masih Memilih</div>
                                <div>Batas Penawaran: 25 Februari 2023</div>
                                <div>Pelamar: 4 Vendor</div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="#" class="btn btn-success btn-sm">PENAWARAN</a>
                                <a href="#" class="btn btn-secondary btn-sm">DETAIL</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="project-card">
                        <img src="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=220&fit=crop"
                            alt="Kaos Partai Pemilu" class="project-image">
                        <div class="project-details">
                            <h5 class="card-title">Kaos Partai Pemilu</h5>
                            <div class="project-status">
                                <div>Jumlah Produksi: 40.000 pcs</div>
                                <div>Budget: Rp. 120.000</div>
                                <div>Deadline Pengerjakan: 10 Hari</div>
                            </div>
                            <div class="project-status">
                                <div>Status: Masih Memilih</div>
                                <div>Batas Penawaran: 25 Februari 2023</div>
                                <div>Pelamar: 4 Vendor</div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="#" class="btn btn-success btn-sm">PENAWARAN</a>
                                <a href="#" class="btn btn-secondary btn-sm">DETAIL</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="project-card">
                        <img src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=220&fit=crop"
                            alt="Cetak Kaos Konser" class="project-image">
                        <div class="project-details">
                            <h5 class="card-title">Cetak Kaos Konser</h5>
                            <div class="project-status">
                                <div>Jumlah Produksi: 40.000 pcs</div>
                                <div>Budget: Rp. 120.000</div>
                                <div>Deadline Pengerjakan: 10 Hari</div>
                            </div>
                            <div class="project-status">
                                <div>Status: Masih Memilih</div>
                                <div>Batas Penawaran: 25 Februari 2023</div>
                                <div>Pelamar: 4 Vendor</div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="#" class="btn btn-success btn-sm">PENAWARAN</a>
                                <a href="#" class="btn btn-secondary btn-sm">DETAIL</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="project-card">
                        <img src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=400&h=220&fit=crop"
                            alt="Buku Yasin Hardcover Emas" class="project-image">
                        <div class="project-details">
                            <h5 class="card-title">Buku Yasin Hardcover emas</h5>
                            <div class="project-status">
                                <div>Jumlah Produksi: 40.000 pcs</div>
                                <div>Budget: Rp. 120.000</div>
                                <div>Deadline Pengerjakan: 10 Hari</div>
                            </div>
                            <div class="project-status">
                                <div>Status: Masih Memilih</div>
                                <div>Batas Penawaran: 25 Februari 2023</div>
                                <div>Pelamar: 4 Vendor</div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="#" class="btn btn-success btn-sm">PENAWARAN</a>
                                <a href="#" class="btn btn-secondary btn-sm">DETAIL</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer bg-white py-5 mt-5 border-top">
        <div class="container-fluid px-4">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h3 class="h5 mb-2">GRAFIKA PRINTING</h3>
                    <div>081515876755</div>
                    <div>Pesantren Peterongan Jombang</div>
                    <div>info@grafikaprinting.com</div>
                    <div class="mt-2">
                        <a href="#" class="text-muted me-2">f</a>
                        <a href="#" class="text-muted me-2">🐦</a>
                        <a href="#" class="text-muted me-2">G+</a>
                        <a href="#" class="text-muted">📷</a>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <h3 class="h5 mb-2">Link Terkait</h3>
                    <ul class="list-unstyled">
                        <li><a href="#" class="link-primary">Tentang Grafika</a></li>
                        <li><a href="#" class="link-primary">Aturan Penggunaan</a></li>
                        <li><a href="#" class="link-primary">Kebijakan Privasi</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h3 class="h5 mb-2">Jam Pelayanan</h3>
                    <div>Senin - Jum'at : 09:00 - 17:00 WIB</div>
                    <div>Sabtu - Minggu : 09:00 - 15:00 WIB</div>
                </div>
            </div>
            <div class="text-center text-muted mt-3">©2025 Grafika Printing. Hak Cipta Terpelihara CV. Grafika Digital
                Solution</div>
        </div>
    </footer>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
