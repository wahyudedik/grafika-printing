<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Login - {{ config('app.name') }}</title>

    <!-- CSS files -->
    <link href="{{ asset('dist/css/tabler.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/tabler-flags.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/tabler-payments.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/tabler-vendors.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/demo.min.css') }}" rel="stylesheet" />
</head>

<body class="d-flex flex-column bg-white">
    <div class="row g-0 flex-fill">
        <div class="col-12 col-lg-6 col-xl-4 border-top-wide border-primary d-flex flex-column justify-content-center">
            <div class="container container-tight my-5 px-lg-5">
                <div class="text-center mb-4">
                    <h2 class="h3 text-center mb-3">Login to your account</h2>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" name="email" class="form-control" placeholder="your@email.com"
                            value="{{ old('email') }}" required autofocus>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mb-2">
                        <label class="form-label">
                            Password
                            @if (Route::has('password.request'))
                                <span class="form-label-description">
                                    <a href="{{ route('password.request') }}">Forgot password?</a>
                                </span>
                            @endif
                        </label>
                        <input type="password" name="password" class="form-control" placeholder="Your password"
                            required>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="mb-2">
                        <label class="form-check">
                            <input type="checkbox" name="remember" class="form-check-input" />
                            <span class="form-check-label">Remember me on this device</span>
                        </label>
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">Sign in</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-12 col-lg-6 col-xl-8 d-none d-lg-block">
            <div class="bg-cover h-100 min-vh-100"
                style="background-image: url(./dist/img/hero.jpg)">
            </div>
        </div>
    </div>

    <!-- Tabler Core -->
    <script src="{{ asset('dist/js/tabler.min.js') }}" defer></script>
</body>

</html>
