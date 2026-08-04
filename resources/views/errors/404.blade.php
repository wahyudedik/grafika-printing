<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Halaman Tidak Ditemukan | Grafika Printing</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0/dist/css/tabler.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .error-page {
            text-align: center;
            color: white;
            padding: 2rem;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 800;
            line-height: 1;
            opacity: 0.9;
            text-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .error-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .error-message {
            font-size: 1.1rem;
            opacity: 0.85;
            margin-bottom: 2rem;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-white {
            background: white;
            color: #f5576c;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-white:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            color: #e04050;
        }
        .btn-outline-white {
            background: transparent;
            color: white;
            border: 2px solid rgba(255,255,255,0.5);
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-outline-white:hover {
            background: rgba(255,255,255,0.15);
            border-color: white;
            color: white;
        }
    </style>
</head>

<body>
    <div class="error-page">
        <div class="error-icon">🔍</div>
        <div class="error-code">404</div>
        <h1 class="error-title">Halaman Tidak Ditemukan</h1>
        <p class="error-message">
            Halaman yang Anda cari tidak ditemukan atau telah dipindahkan.
            Silakan periksa URL atau kembali ke beranda.
        </p>
        <div class="error-actions">
            <a href="{{ route('welcome') }}" class="btn-white">Kembali ke Beranda</a>
            <a href="javascript:history.back()" class="btn-outline-white">Kembali</a>
        </div>
    </div>
</body>

</html>
