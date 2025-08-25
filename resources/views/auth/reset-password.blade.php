<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Reset Password - {{ config('app.name') }}</title>
    <link href="https://unpkg.com/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet">
    <link href="https://unpkg.com/@tabler/core@latest/dist/css/tabler-flags.min.css" rel="stylesheet">
    <link href="https://unpkg.com/@tabler/core@latest/dist/css/tabler-payments.min.css" rel="stylesheet">
    <link href="https://unpkg.com/@tabler/core@latest/dist/css/tabler-vendors.min.css" rel="stylesheet">
    <link href="https://unpkg.com/@tabler/icons@latest/iconfont/tabler-icons.min.css" rel="stylesheet">
    <style>
        .login-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logo-container {
            background: #000;
            padding: 12px 24px;
            border-radius: 8px;
            position: relative;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 0 auto 2rem;
            display: inline-block;
        }

        .logo-container::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(to right, #00FFFF 25%, #FF00FF 25% 50%, #FFFF00 50% 75%, #000000 75%);
            border-radius: 0 0 8px 8px;
        }

        .btn-primary-custom {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-primary-custom:hover {
            background: linear-gradient(45deg, #764ba2, #667eea);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }

        .form-control-custom:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 10;
        }

        .input-with-icon {
            padding-left: 40px;
        }

        .password-strength {
            margin-top: 8px;
            font-size: 0.875rem;
        }

        .strength-bar {
            height: 4px;
            border-radius: 2px;
            background: #e9ecef;
            overflow: hidden;
            margin-top: 4px;
        }

        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak {
            background: #dc3545;
            width: 25%;
        }

        .strength-fair {
            background: #ffc107;
            width: 50%;
        }

        .strength-good {
            background: #17a2b8;
            width: 75%;
        }

        .strength-strong {
            background: #28a745;
            width: 100%;
        }
    </style>
</head>

<body class="login-bg">
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <div class="logo-container">
                    <span class="text-white fw-bold fs-4">GRAFIKA PRINTING</span>
                </div>
            </div>

            <div class="card card-md login-card">
                <div class="card-body">
                    <h2 class="h2 text-center mb-4">Set New Password</h2>
                    <p class="text-muted text-center mb-4">Create a strong password for your account</p>

                    <form method="POST" action="{{ route('password.store') }}" autocomplete="off">
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <div class="mb-3">
                            <label class="form-label">Email address</label>
                            <div class="input-group input-group-flat">
                                <span class="input-group-text">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                        <path d="M3 7l9 6l9 -6" />
                                    </svg>
                                </span>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $request->email) }}" required readonly>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <div class="input-group input-group-flat">
                                <span class="input-group-text">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M5 11a7 7 0 0 1 14 0v7a1.78 1.78 0 0 1 -3.1 1.4a1.65 1.65 0 0 0 -2.6 0a1.65 1.65 0 0 1 -2.6 0a1.65 1.65 0 0 0 -2.6 0a1.78 1.78 0 0 1 -3.1 -1.4v-7z" />
                                        <path d="M12 4l0 2" />
                                    </svg>
                                </span>
                                <input type="password" name="password" class="form-control" placeholder="••••••••"
                                    required autofocus>
                            </div>
                            <div class="password-strength">
                                <div class="text-muted small">Password strength: <span id="strength-text">Weak</span>
                                </div>
                                <div class="strength-bar">
                                    <div class="strength-fill strength-weak" id="strength-fill"></div>
                                </div>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <div class="input-group input-group-flat">
                                <span class="input-group-text">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M5 11a7 7 0 0 1 14 0v7a1.78 1.78 0 0 1 -3.1 1.4a1.65 1.65 0 0 0 -2.6 0a1.65 1.65 0 0 1 -2.6 0a1.65 1.65 0 0 0 -2.6 0a1.78 1.78 0 0 1 -3.1 -1.4v-7z" />
                                        <path d="M12 4l0 2" />
                                    </svg>
                                </span>
                                <input type="password" name="password_confirmation" class="form-control"
                                    placeholder="••••••••" required>
                            </div>
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary-custom w-100">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M9 12l2 2l4 -4" />
                                    <path
                                        d="M21 12c-1 0 -2.5 -.5 -3 -1.5c-.5 -1 -1 -1.5 -2 -1.5s-1.5 .5 -2 1.5c-.5 1 -2 1.5 -3 1.5s-2.5 -.5 -3 -1.5c-.5 -1 -1 -1.5 -2 -1.5s-1.5 .5 -2 1.5c-.5 1 -2 1.5 -3 1.5" />
                                    <path d="M12 2l0 10" />
                                </svg>
                                Reset Password
                            </button>
                        </div>
                    </form>
                </div>

                <div class="text-center text-muted mt-3">
                    Remember your password?
                    <a href="{{ route('login') }}" tabindex="-1">Sign in</a>
                </div>
            </div>

            <div class="text-center text-muted mt-4">
                <a href="{{ route('welcome') }}" class="text-decoration-none text-white">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M5 12l14 0" />
                        <path d="M5 12l6 6" />
                        <path d="M5 12l6 -6" />
                    </svg>
                    Back to homepage
                </a>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/@tabler/core@latest/dist/js/tabler.min.js"></script>
    <script>
        // Password strength checker
        document.querySelector('input[name="password"]').addEventListener('input', function() {
            const password = this.value;
            const strengthFill = document.getElementById('strength-fill');
            const strengthText = document.getElementById('strength-text');

            let strength = 0;
            let strengthClass = 'strength-weak';
            let strengthLabel = 'Weak';

            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            switch (strength) {
                case 0:
                case 1:
                    strengthClass = 'strength-weak';
                    strengthLabel = 'Weak';
                    break;
                case 2:
                    strengthClass = 'strength-fair';
                    strengthLabel = 'Fair';
                    break;
                case 3:
                    strengthClass = 'strength-good';
                    strengthLabel = 'Good';
                    break;
                case 4:
                case 5:
                    strengthClass = 'strength-strong';
                    strengthLabel = 'Strong';
                    break;
            }

            strengthFill.className = `strength-fill ${strengthClass}`;
            strengthText.textContent = strengthLabel;
        });
    </script>
</body>

</html>
