<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Auth' }} - {{ config('app.name', 'Grafika Printing') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link href="https://unpkg.com/@tabler/core@1.0.0/dist/css/tabler.min.css" rel="stylesheet">
    <link href="https://unpkg.com/@tabler/core@1.0.0/dist/css/tabler-flags.min.css" rel="stylesheet">
    <link href="https://unpkg.com/@tabler/core@1.0.0/dist/css/tabler-payments.min.css" rel="stylesheet">
    <link href="https://unpkg.com/@tabler/core@1.0.0/dist/css/tabler-vendors.min.css" rel="stylesheet">
    <link href="https://unpkg.com/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }

        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: #f8f9fa;
        }

        /* ===== Split Layout ===== */
        .auth-split {
            display: flex;
            min-height: 100vh;
        }

        /* Left Panel - Branding */
        .auth-brand {
            flex: 0 0 55%;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }

        .auth-brand::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.15) 0%, transparent 70%);
            animation: pulse-glow 8s ease-in-out infinite;
        }

        .auth-brand::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -30%;
            width: 80%;
            height: 80%;
            background: radial-gradient(circle, rgba(118, 75, 162, 0.12) 0%, transparent 70%);
            animation: pulse-glow 10s ease-in-out infinite reverse;
        }

        @keyframes pulse-glow {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 1; }
        }

        .brand-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 480px;
        }

        .brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 2rem;
        }

        .brand-logo-icon {
            width: 56px;
            height: 56px;
            background: #fff;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .brand-logo-icon::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 4px;
            right: 4px;
            height: 4px;
            background: linear-gradient(to right, #00d4ff, #e040fb, #ffeb3b, #212121);
            border-radius: 0 0 8px 8px;
        }

        .brand-logo-icon svg {
            width: 32px;
            height: 32px;
            color: #1a1a2e;
        }

        .brand-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.02em;
        }

        .brand-tagline {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.125rem;
            line-height: 1.7;
            margin-bottom: 2.5rem;
            font-weight: 300;
        }

        .brand-features {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            text-align: left;
        }

        .brand-feature {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
        }

        .brand-feature:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(4px);
        }

        .brand-feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-feature-icon.blue { background: rgba(102, 126, 234, 0.2); color: #667eea; }
        .brand-feature-icon.purple { background: rgba(118, 75, 162, 0.2); color: #764ba2; }
        .brand-feature-icon.cyan { background: rgba(0, 212, 255, 0.2); color: #00d4ff; }
        .brand-feature-icon.green { background: rgba(40, 167, 69, 0.2); color: #28a745; }

        .brand-feature-icon svg {
            width: 20px;
            height: 20px;
        }

        .brand-feature-text h4 {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 600;
            color: #fff;
        }

        .brand-feature-text p {
            margin: 0;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.5);
        }

        /* CMYK decorative dots */
        .brand-dots {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 2;
        }

        .brand-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            opacity: 0.4;
        }

        .brand-dot:nth-child(1) { background: #00d4ff; }
        .brand-dot:nth-child(2) { background: #e040fb; }
        .brand-dot:nth-child(3) { background: #ffeb3b; }
        .brand-dot:nth-child(4) { background: #fff; }

        /* Right Panel - Form */
        .auth-form-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            background: #fff;
            position: relative;
        }

        .auth-form-wrapper {
            width: 100%;
            max-width: 400px;
        }

        .auth-form-header {
            margin-bottom: 2rem;
        }

        .auth-form-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0 0 0.5rem;
            letter-spacing: -0.02em;
        }

        .auth-form-header p {
            color: #6c757d;
            font-size: 0.95rem;
            margin: 0;
            line-height: 1.5;
        }

        /* Form Styles */
        .auth-form .form-group {
            margin-bottom: 1.25rem;
        }

        .auth-form .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 0.5rem;
            display: block;
        }

        .auth-form .input-wrapper {
            position: relative;
        }

        .auth-form .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .auth-form .input-icon svg {
            width: 18px;
            height: 18px;
        }

        .auth-form .form-control {
            width: 100%;
            padding: 12px 14px 12px 44px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 0.9rem;
            color: #343a40;
            background: #fff;
            transition: all 0.2s ease;
            outline: none;
        }

        .auth-form .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        }

        .auth-form .form-control:focus + .input-icon,
        .auth-form .form-control:focus ~ .input-icon {
            color: #667eea;
        }

        .auth-form .form-control::placeholder {
            color: #ced4da;
        }

        .auth-form .form-control.no-icon {
            padding-left: 14px;
        }

        /* Password toggle */
        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #adb5bd;
            cursor: pointer;
            padding: 4px;
            transition: color 0.2s ease;
        }

        .password-toggle:hover {
            color: #667eea;
        }

        .password-toggle svg {
            width: 18px;
            height: 18px;
        }

        /* Checkbox */
        .auth-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1.25rem;
        }

        .auth-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            border: 2px solid #dee2e6;
            border-radius: 5px;
            cursor: pointer;
            accent-color: #667eea;
        }

        .auth-checkbox label {
            font-size: 0.85rem;
            color: #6c757d;
            margin: 0;
            cursor: pointer;
        }

        .auth-checkbox a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .auth-checkbox a:hover {
            text-decoration: underline;
        }

        /* Buttons */
        .btn-auth {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px 24px;
            font-size: 0.95rem;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.25s ease;
            text-decoration: none;
        }

        .btn-auth svg {
            width: 18px;
            height: 18px;
        }

        .btn-auth-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.35);
        }

        .btn-auth-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.45);
            color: #fff;
        }

        .btn-auth-primary:active {
            transform: translateY(0);
        }

        .btn-auth-outline {
            background: transparent;
            color: #667eea;
            border: 2px solid #e9ecef;
        }

        .btn-auth-outline:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
            color: #667eea;
        }

        /* Form footer links */
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .auth-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .auth-divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 1.5rem 0;
            color: #adb5bd;
            font-size: 0.8rem;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e9ecef;
        }

        /* Back to home */
        .auth-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #6c757d;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            margin-top: 2rem;
            transition: color 0.2s ease;
        }

        .auth-back:hover {
            color: #667eea;
        }

        .auth-back svg {
            width: 16px;
            height: 16px;
        }

        /* Alert */
        .auth-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 1.25rem;
            font-size: 0.85rem;
            line-height: 1.5;
        }

        .auth-alert svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .auth-alert-success {
            background: rgba(40, 167, 69, 0.08);
            border: 1px solid rgba(40, 167, 69, 0.2);
            color: #155724;
        }

        .auth-alert-error {
            background: rgba(220, 53, 69, 0.08);
            border: 1px solid rgba(220, 53, 69, 0.2);
            color: #721c24;
        }

        .auth-alert-warning {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            color: #856404;
        }

        /* Password strength */
        .password-strength {
            margin-top: 8px;
        }

        .strength-bar {
            height: 4px;
            border-radius: 2px;
            background: #e9ecef;
            overflow: hidden;
            margin-top: 6px;
        }

        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak { background: #dc3545; width: 25%; }
        .strength-fair { background: #ffc107; width: 50%; }
        .strength-good { background: #17a2b8; width: 75%; }
        .strength-strong { background: #28a745; width: 100%; }

        .strength-text {
            font-size: 0.75rem;
            color: #6c757d;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .auth-brand {
                flex: 0 0 45%;
                padding: 2rem;
            }

            .brand-tagline {
                font-size: 1rem;
            }

            .brand-features {
                gap: 0.75rem;
            }
        }

        @media (max-width: 768px) {
            .auth-split {
                flex-direction: column;
            }

            .auth-brand {
                flex: 0 0 auto;
                padding: 2rem 1.5rem;
                min-height: auto;
            }

            .brand-tagline {
                display: none;
            }

            .brand-features {
                display: none;
            }

            .auth-form-panel {
                padding: 2rem 1.5rem;
            }

            .auth-form-wrapper {
                max-width: 100%;
            }
        }

        /* Verification icon */
        .auth-icon-circle {
            width: 72px;
            height: 72px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
        }

        .auth-icon-circle svg {
            width: 32px;
            height: 32px;
            color: #fff;
        }

        /* Button group */
        .btn-group-vertical {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-group-row {
            display: flex;
            gap: 10px;
        }

        .btn-group-row .btn-auth {
            flex: 1;
        }
    </style>
    @yield('styles')
</head>

<body>
    <div class="auth-split">
        {{-- Left Panel: Branding --}}
        <div class="auth-brand">
            <div class="brand-content">
                <div class="brand-logo">
                    <div class="brand-logo-icon">
                        <img src="{{ asset('logo.png') }}" alt="Grafika Printing" width="40" height="40" style="border-radius: 10px;">
                    </div>
                    <span class="brand-name">GRAFIKA PRINTING</span>
                </div>

                <p class="brand-tagline">
                    Platform percetakan digital terlengkap di Indonesia.
                    Solusi cetak online untuk bisnis Anda.
                </p>

                <div class="brand-features">
                    <div class="brand-feature">
                        <div class="brand-feature-icon blue">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 3l8 4.5v9l-8 4.5l-8 -4.5v-9l8 -4.5" />
                                <path d="M12 12l8 -4.5" />
                                <path d="M12 12v9" />
                                <path d="M12 12l-8 -4.5" />
                            </svg>
                        </div>
                        <div class="brand-feature-text">
                            <h4>Multi-Tenant POS</h4>
                            <p>Sistem point of sale untuk vendor percetakan</p>
                        </div>
                    </div>

                    <div class="brand-feature">
                        <div class="brand-feature-icon purple">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 3l8 4.5v9l-8 4.5l-8 -4.5v-9l8 -4.5" />
                                <path d="M9 12l2 2l4 -4" />
                            </svg>
                        </div>
                        <div class="brand-feature-text">
                            <h4>Lelang & Tender</h4>
                            <p>Sistem lelang proyek percetakan</p>
                        </div>
                    </div>

                    <div class="brand-feature">
                        <div class="brand-feature-icon cyan">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 12l2 2l4 -4" />
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                            </svg>
                        </div>
                        <div class="brand-feature-text">
                            <h4>Pembayaran Aman</h4>
                            <p>Escrow payment via Xendit</p>
                        </div>
                    </div>

                    <div class="brand-feature">
                        <div class="brand-feature-icon green">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 15l6 -6" />
                                <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 -7.072a5 5 0 0 1 7.071 7.072a5 5 0 0 1 -10.142 0a5 5 0 0 1 -1.414 -2.828" />
                                <path d="M10.363 3.593l-2.37 .958a5 5 0 0 0 -1.427 1.427l-.958 2.37" />
                            </svg>
                        </div>
                        <div class="brand-feature-text">
                            <h4>Linktree Vendor</h4>
                            <p>Halaman linktree kustom untuk vendor</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="brand-dots">
                <div class="brand-dot"></div>
                <div class="brand-dot"></div>
                <div class="brand-dot"></div>
                <div class="brand-dot"></div>
            </div>
        </div>

        {{-- Right Panel: Form --}}
        <div class="auth-form-panel">
            <div class="auth-form-wrapper">
                @yield('content')

                <div class="text-center">
                    <a href="{{ route('welcome') }}" class="auth-back">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 12l14 0" />
                            <path d="M5 12l6 6" />
                            <path d="M5 12l6 -6" />
                        </svg>
                        Kembali ke beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/@tabler/core@1.0.0/dist/js/tabler.min.js"></script>
    @yield('scripts')
</body>

</html>
