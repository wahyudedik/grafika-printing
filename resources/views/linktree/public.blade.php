<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $linktree->meta_title ?: $linktree->title . ' - Linktree' }}</title>
    <meta name="description" content="{{ $linktree->meta_description ?: $linktree->bio }}">
    <meta property="og:title" content="{{ $linktree->title }}">
    <meta property="og:description" content="{{ $linktree->bio }}">
    @if($linktree->avatar)
    <meta property="og:image" content="{{ asset('linktree/avatars/' . $linktree->avatar) }}">
    @endif
    <meta property="og:url" content="{{ url('/l/' . $linktree->custom_url) }}">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <meta name="twitter:title" content="{{ $linktree->title }}">
    <meta name="twitter:description" content="{{ $linktree->bio }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            background-color: {{ $linktree->bg_color }};
            color: {{ $linktree->text_color }};
            display: flex;
            justify-content: center;
            padding: 0;
            margin: 0;
        }

        .container {
            max-width: 420px;
            width: 100%;
            padding: 24px 16px 40px;
        }

        /* Banner */
        .banner {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 16px;
        }

        .banner-placeholder {
            width: 100%;
            height: 60px;
            border-radius: 12px;
            margin-bottom: 16px;
        }

        /* Profile */
        .profile {
            text-align: center;
            margin-bottom: 24px;
        }

        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid {{ $linktree->primary_color }}20;
            margin-bottom: 12px;
        }

        .avatar-placeholder {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: {{ $linktree->primary_color }};
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            margin: 0 auto 12px;
            border: 3px solid {{ $linktree->primary_color }}20;
        }

        .name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .bio {
            font-size: 14px;
            opacity: 0.7;
            line-height: 1.4;
        }

        /* Links */
        .links {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }

        .link-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 20px;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: center;
        }

        .link-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .link-item:active {
            transform: translateY(0);
        }

        /* Button Styles */
        .btn-rounded {
            border-radius: 8px;
        }

        .btn-square {
            border-radius: 4px;
        }

        .btn-pill {
            border-radius: 50px;
        }

        /* QRIS */
        .qris-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .qris-image {
            max-width: 180px;
            border-radius: 8px;
            border: 2px solid {{ $linktree->primary_color }}20;
        }

        .qris-label {
            font-size: 12px;
            opacity: 0.6;
            margin-top: 6px;
        }

        /* Social Links */
        .socials {
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.2s ease;
            background: {{ $linktree->primary_color }}10;
        }

        .social-link:hover {
            transform: scale(1.1);
            background: {{ $linktree->primary_color }}25;
        }

        .social-link svg {
            width: 20px;
            height: 20px;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 32px;
            font-size: 11px;
            opacity: 0.4;
        }

        .footer a {
            color: inherit;
            text-decoration: none;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .container {
                padding: 16px 12px 32px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Banner -->
        @if($linktree->banner)
        <img src="{{ asset('linktree/banners/' . $linktree->banner) }}" alt="Banner" class="banner">
        @else
        <div class="banner-placeholder" style="background: linear-gradient(135deg, {{ $linktree->primary_color }}30, {{ $linktree->secondary_color }}30);"></div>
        @endif

        <!-- Profile -->
        <div class="profile">
            @if($linktree->avatar)
            <img src="{{ asset('linktree/avatars/' . $linktree->avatar) }}" alt="{{ $linktree->title }}" class="avatar">
            @else
            <div class="avatar-placeholder">{{ strtoupper(substr($linktree->title, 0, 1)) }}</div>
            @endif
            <div class="name">{{ $linktree->title }}</div>
            @if($linktree->bio)
            <div class="bio">{{ $linktree->bio }}</div>
            @endif
        </div>

        <!-- Links -->
        <div class="links">
            @foreach($linktree->activeLinks as $link)
            <a href="{{ route('linktree.click', [$linktree->custom_url, $link->id]) }}"
               class="link-item btn-{{ $linktree->button_style }}"
               style="background: {{ $linktree->primary_color }}; color: white;">
                {!! $link->icon_html !!}
                {{ $link->title }}
            </a>
            @endforeach
        </div>

        <!-- QRIS -->
        @if($linktree->show_qris && $linktree->qris_image)
        <div class="qris-section">
            <img src="{{ asset('linktree/qris/' . $linktree->qris_image) }}" alt="QRIS" class="qris-image">
            <div class="qris-label">Scan untuk pembayaran QRIS</div>
        </div>
        @endif

        <!-- Social Links -->
        @if($linktree->activeSocials->count() > 0)
        <div class="socials">
            @foreach($linktree->activeSocials as $social)
            <a href="{{ $social->url }}" target="_blank" rel="noopener noreferrer" class="social-link" title="{{ ucfirst($social->platform) }}">
                {!! $social->icon_html !!}
            </a>
            @endforeach
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <a href="{{ url('/') }}">Grafika Printing</a>
        </div>
    </div>

    <script>
        // Track page view (already done server-side)
    </script>
</body>
</html>
