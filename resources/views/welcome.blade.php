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

    <!-- Tabler Core CSS & Font Awesome (via Vite - no CDN dependency) -->
    @vite('resources/css/welcome.css')

    <style>
        :root {
            --primary: #667eea;
            --primary-dark: #5a67d8;
            --secondary: #764ba2;
            --gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-dark: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            --dark: #1a1a2e;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --bg-light: #f8fafc;
            --border: #e2e8f0;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            --radius: 12px;
            --radius-sm: 8px;
            --radius-lg: 16px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text-primary);
            background: #fff;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        .container-custom {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ========== NAVBAR ========== */
        .navbar-custom {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            transition: all 0.3s ease;
        }

        .navbar-custom.scrolled {
            box-shadow: var(--shadow-md);
        }

        .navbar-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .navbar-brand .logo-icon {
            width: 36px;
            height: 36px;
            background: var(--gradient);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
            font-size: 14px;
            letter-spacing: -0.5px;
        }

        .navbar-brand .brand-text {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.5px;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 4px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .navbar-nav a {
            padding: 8px 16px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
        }

        .navbar-nav a:hover {
            color: var(--primary);
            background: rgba(102, 126, 234, 0.08);
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-nav {
            padding: 8px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: var(--radius-sm);
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }

        .btn-nav-outline {
            color: var(--text-primary);
            background: transparent;
            border: 1px solid var(--border);
        }

        .btn-nav-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-nav-primary {
            background: var(--gradient);
            color: #fff;
        }

        .btn-nav-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text-primary);
            cursor: pointer;
            padding: 8px;
        }

        /* ========== HERO ========== */
        .hero {
            padding: 140px 24px 80px;
            background: var(--gradient-dark);
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: rgba(102, 126, 234, 0.15);
            border-radius: 50%;
            filter: blur(80px);
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: rgba(118, 75, 162, 0.1);
            border-radius: 50%;
            filter: blur(60px);
        }

        .hero .container-custom {
            position: relative;
            z-index: 1;
        }

        .hero-content {
            max-width: 700px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 100px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 24px;
            backdrop-filter: blur(4px);
        }

        .hero-badge i {
            color: #4ade80;
        }

        .hero h1 {
            font-size: 48px;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 20px;
            letter-spacing: -1px;
        }

        .hero h1 span {
            background: linear-gradient(135deg, #60a5fa, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 18px;
            line-height: 1.7;
            opacity: 0.8;
            margin-bottom: 32px;
            max-width: 560px;
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-hero {
            padding: 14px 28px;
            font-size: 15px;
            font-weight: 600;
            border-radius: var(--radius-sm);
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-hero-primary {
            background: #fff;
            color: var(--dark);
        }

        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .btn-hero-outline {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-hero-outline:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 48px;
            padding-top: 32px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .hero-stat {
            text-align: center;
        }

        .hero-stat .stat-number {
            font-size: 28px;
            font-weight: 800;
            display: block;
            margin-bottom: 4px;
        }

        .hero-stat .stat-label {
            font-size: 13px;
            opacity: 0.6;
            font-weight: 500;
        }

        /* ========== SECTIONS COMMON ========== */
        .section {
            padding: 80px 24px;
        }

        .section-header {
            text-align: center;
            margin-bottom: 48px;
        }

        .section-header .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: rgba(102, 126, 234, 0.08);
            color: var(--primary);
            border-radius: 100px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .section-header h2 {
            font-size: 32px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .section-header p {
            font-size: 16px;
            color: var(--text-secondary);
            max-width: 560px;
            margin: 0 auto;
        }

        /* ========== SOCIAL LINKS ========== */
        .social-section {
            padding: 60px 24px;
            background: var(--bg-light);
        }

        .social-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
        }

        .social-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            padding: 24px 16px;
            background: #fff;
            border-radius: var(--radius);
            text-decoration: none;
            color: var(--text-primary);
            border: 1px solid var(--border);
            transition: all 0.2s ease;
        }

        .social-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: transparent;
        }

        .social-card .social-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #fff;
        }

        .social-card .social-icon.instagram { background: linear-gradient(135deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
        .social-card .social-icon.facebook { background: #1877f2; }
        .social-card .social-icon.whatsapp { background: #25d366; }
        .social-card .social-icon.tiktok { background: #010101; }
        .social-card .social-icon.youtube { background: #ff0000; }

        .social-card .social-name {
            font-size: 14px;
            font-weight: 600;
        }

        .social-card .social-handle {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* ========== AUCTIONS ========== */
        .auctions-section {
            padding: 80px 24px;
        }

        .auctions-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .auction-card {
            background: #fff;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            overflow: hidden;
            transition: all 0.2s ease;
            text-decoration: none;
            color: inherit;
        }

        .auction-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: transparent;
        }

        .auction-card-header {
            padding: 20px 20px 0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .auction-badge {
            padding: 4px 10px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-active { background: #dcfce7; color: #16a34a; }
        .badge-closed { background: #f1f5f9; color: #64748b; }
        .badge-pending { background: #fef3c7; color: #d97706; }

        .auction-card-body {
            padding: 16px 20px;
        }

        .auction-card-body h5 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .auction-card-body p {
            font-size: 13px;
            color: var(--text-secondary);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .auction-meta {
            display: flex;
            gap: 16px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .auction-meta span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .auction-card-footer {
            padding: 16px 20px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .auction-price {
            font-size: 18px;
            font-weight: 800;
            color: var(--primary);
        }

        .auction-price small {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
        }

        .btn-auction {
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            background: var(--gradient);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-auction:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .view-all-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 32px;
            color: var(--primary);
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .view-all-link:hover {
            color: var(--primary-dark);
        }

        /* ========== HOW IT WORKS ========== */
        .how-section {
            padding: 80px 24px;
            background: var(--bg-light);
        }

        .how-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }

        .how-card {
            text-align: center;
            padding: 32px 20px;
            background: #fff;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            position: relative;
        }

        .how-card .step-number {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--gradient);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .how-card h5 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .how-card p {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* ========== FEATURES ========== */
        .features-section {
            padding: 80px 24px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .feature-card {
            padding: 28px 24px;
            background: #fff;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            transition: all 0.2s ease;
        }

        .feature-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: transparent;
        }

        .feature-card .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            font-size: 20px;
        }

        .feature-icon.blue { background: rgba(102, 126, 234, 0.1); color: var(--primary); }
        .feature-icon.green { background: rgba(34, 197, 94, 0.1); color: #16a34a; }
        .feature-icon.amber { background: rgba(245, 158, 11, 0.1); color: #d97706; }
        .feature-icon.cyan { background: rgba(6, 182, 212, 0.1); color: #0891b2; }

        .feature-card h5 {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .feature-card p {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin: 0;
        }

        /* ========== VENDOR BENEFITS ========== */
        .vendor-benefits {
            padding: 0 24px 80px;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .benefit-card {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 24px;
            background: #fff;
            border-radius: var(--radius);
            border: 1px solid var(--border);
        }

        .benefit-card .benefit-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 18px;
        }

        .benefit-icon.red { background: rgba(239, 68, 68, 0.1); color: #dc2626; }
        .benefit-icon.blue { background: rgba(102, 126, 234, 0.1); color: var(--primary); }
        .benefit-icon.green { background: rgba(34, 197, 94, 0.1); color: #16a34a; }

        .benefit-card h5 {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .benefit-card p {
            font-size: 13px;
            color: var(--text-secondary);
            margin: 0;
            line-height: 1.6;
        }

        /* ========== CTA ========== */
        .cta-section {
            padding: 80px 24px;
            background: var(--gradient-dark);
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 50%;
            filter: blur(80px);
        }

        .cta-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 48px;
        }

        .cta-content h2 {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }

        .cta-content p {
            font-size: 16px;
            opacity: 0.8;
            margin-bottom: 28px;
            max-width: 500px;
            line-height: 1.7;
        }

        .cta-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-cta {
            padding: 14px 28px;
            font-size: 15px;
            font-weight: 600;
            border-radius: var(--radius-sm);
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-cta-primary {
            background: #fff;
            color: var(--dark);
        }

        .btn-cta-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .btn-cta-outline {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-cta-outline:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .cta-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .cta-stat {
            text-align: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: var(--radius-sm);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .cta-stat .stat-value {
            font-size: 28px;
            font-weight: 800;
            display: block;
            margin-bottom: 4px;
        }

        .cta-stat .stat-text {
            font-size: 13px;
            opacity: 0.6;
        }

        /* ========== FOOTER ========== */
        .footer {
            padding: 60px 24px 32px;
            background: #fff;
            border-top: 1px solid var(--border);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 48px;
            margin-bottom: 40px;
        }

        .footer-brand .brand-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 12px;
        }

        .footer-brand p {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.7;
            margin-bottom: 16px;
        }

        .footer-contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .footer-contact-item i {
            width: 16px;
            color: var(--primary);
        }

        .footer-social {
            display: flex;
            gap: 8px;
            margin-top: 16px;
        }

        .footer-social a {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-light);
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .footer-social a:hover {
            background: var(--primary);
            color: #fff;
        }

        .footer-column h6 {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .footer-column ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-column ul li {
            margin-bottom: 8px;
        }

        .footer-column ul li a {
            font-size: 14px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .footer-column ul li a:hover {
            color: var(--primary);
        }

        .footer-bottom {
            padding-top: 24px;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            color: var(--text-muted);
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 1024px) {
            .hero h1 { font-size: 40px; }
            .hero-stats { grid-template-columns: repeat(3, 1fr); gap: 12px; }
            .auctions-grid { grid-template-columns: repeat(2, 1fr); }
            .features-grid { grid-template-columns: repeat(2, 1fr); }
            .how-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 32px; }
        }

        @media (max-width: 768px) {
            .navbar-nav { display: none; }
            .mobile-toggle { display: block; }
            .navbar-nav.active {
                display: flex;
                flex-direction: column;
                position: absolute;
                top: 64px;
                left: 0;
                right: 0;
                background: #fff;
                border-bottom: 1px solid var(--border);
                padding: 12px;
                box-shadow: var(--shadow-lg);
            }
            .hero { padding: 120px 24px 60px; }
            .hero h1 { font-size: 32px; }
            .hero p { font-size: 16px; }
            .hero-stats { grid-template-columns: repeat(3, 1fr); gap: 8px; margin-top: 32px; padding-top: 24px; }
            .hero-stat .stat-number { font-size: 22px; }
            .hero-actions { flex-direction: column; }
            .btn-hero { width: 100%; justify-content: center; }
            .social-grid { grid-template-columns: repeat(3, 1fr); }
            .auctions-grid { grid-template-columns: 1fr; }
            .how-grid { grid-template-columns: 1fr; }
            .features-grid { grid-template-columns: 1fr; }
            .benefits-grid { grid-template-columns: 1fr; }
            .cta-inner { flex-direction: column; text-align: center; }
            .cta-content p { margin: 0 auto 28px; }
            .cta-actions { justify-content: center; }
            .cta-stats { grid-template-columns: repeat(3, 1fr); }
            .cta-stat .stat-value { font-size: 22px; }
            .footer-grid { grid-template-columns: 1fr; gap: 32px; }
            .footer-bottom { flex-direction: column; gap: 8px; text-align: center; }
        }

        @media (max-width: 480px) {
            .hero h1 { font-size: 28px; }
            .social-grid { grid-template-columns: repeat(2, 1fr); }
            .section-header h2 { font-size: 26px; }
            .cta-content h2 { font-size: 28px; }
        }

        /* ========== ANIMATIONS ========== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-in {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .animate-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar-custom" id="navbar">
        <div class="navbar-inner">
            <a href="/" class="navbar-brand">
                <div class="logo-icon">CMYK</div>
                <span class="brand-text">GRAFIKA</span>
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
                <h1>{{ \App\Models\CmsSetting::get('hero_title', 'Solusi Percetakan<br><span>Mudah & Terpercaya</span>') }}</h1>
                <p>{{ \App\Models\CmsSetting::get('hero_subtitle', 'Temukan vendor percetakan terbaik dengan harga kompetitif melalui sistem lelang transparan. Pembayaran aman via Xendit.') }}</p>
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

            <div class="hero-stats">
                <div class="hero-stat">
                    <span class="stat-number">100+</span>
                    <span class="stat-label">Vendor Aktif</span>
                </div>
                <div class="hero-stat">
                    <span class="stat-number">500+</span>
                    <span class="stat-label">Proyek Selesai</span>
                </div>
                <div class="hero-stat">
                    <span class="stat-number">4.8★</span>
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

            @php
                $auctions = \App\Models\Auction::with('user')
                    ->where('status', '!=', 'draft')
                    ->latest()
                    ->take(6)
                    ->get();
            @endphp

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
                                    Rp {{ number_format($auction->budget_min ?? 0, 0, ',', '.') }}
                                    @if($auction->budget_max)
                                        - {{ number_format($auction->budget_max, 0, ',', '.') }}
                                    @endif
                                @else
                                    Harga Kompetitif
                                @endif
                            </div>
                            <a href="{{ route('user.auctions.show', $auction->id) }}" class="btn-auction">Lihat Detail</a>
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
                <span>{{ \App\Models\CmsSetting::get('footer_copyright', '©2025 Grafika Printing. Hak Cipta Terpelihara CV. Grafika Digital Solution') }}</span>
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
