<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak | Grafika Printing</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            -webkit-font-smoothing: antialiased;
        }
        .error-container { text-align: center; padding: 2rem; max-width: 32rem; }
        .error-icon { font-size: 3.5rem; margin-bottom: 1rem; opacity: 0.85; }
        .error-code {
            font-size: 8rem; font-weight: 800; line-height: 1;
            opacity: 0.9; text-shadow: 0 4px 20px rgba(0,0,0,0.15);
            letter-spacing: -0.04em;
        }
        h1 { font-size: 1.5rem; font-weight: 600; margin-top: 0.75rem; margin-bottom: 0.75rem; }
        .error-desc { font-size: 1.05rem; opacity: 0.85; margin-bottom: 2rem; line-height: 1.6; max-width: 28rem; margin-left: auto; margin-right: auto; }
        .error-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-block; padding: 0.75rem 1.5rem; border-radius: 0.5rem;
            font-weight: 600; text-decoration: none; font-size: 0.95rem;
            transition: all 0.2s ease; cursor: pointer; border: none;
        }
        .btn-primary { background: #fff; color: #667eea; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
        .btn-outline { background: transparent; color: #fff; border: 2px solid rgba(255,255,255,0.5); }
        .btn-outline:hover { background: rgba(255,255,255,0.15); border-color: rgba(255,255,255,0.8); }
        @media (max-width: 480px) {
            .error-code { font-size: 5rem; }
            h1 { font-size: 1.25rem; }
            .error-desc { font-size: 0.95rem; }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">🔒</div>
        <div class="error-code">403</div>
        <h1>Akses Ditolak</h1>
        <p class="error-desc">
            Anda tidak memiliki izin untuk mengakses halaman ini.
            Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
        </p>
        <div class="error-actions">
            <a href="{{ route('welcome') }}" class="btn btn-primary">Kembali ke Beranda</a>
            <a href="javascript:history.back()" class="btn btn-outline">Kembali</a>
        </div>
    </div>
</body>
</html>
