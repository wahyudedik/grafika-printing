@extends('layouts.auth')

@section('styles')
<style>
    .form-label-description {
        float: right;
        font-size: 0.8rem;
        font-weight: 500;
    }
</style>
@endsection

@section('content')
<div class="auth-form-header">
    <h1>Selamat Datang</h1>
    <p>Masuk ke akun Anda untuk melanjutkan</p>
</div>

@if ($errors->any())
    <div class="auth-alert auth-alert-error">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M12 9v4"/>
            <path d="M10.363 3.593l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/>
            <path d="M12 16h.01"/>
        </svg>
        <div>
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    </div>
@endif

<form method="POST" action="{{ route('login') }}" class="auth-form" autocomplete="off">
    @csrf

    <div class="form-group">
        <label class="form-label">Email</label>
        <div class="input-wrapper">
            <input type="email" name="email" class="form-control" placeholder="nama@email.com"
                value="{{ old('email') }}" required autofocus>
            <span class="input-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 7a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10a2 2 0 0 0 -2 -2h-10z"/>
                    <path d="M3 7l9 6l9 -6"/>
                </svg>
            </span>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">
            Password
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="form-label-description">Lupa password?</a>
            @endif
        </label>
        <div class="input-wrapper">
            <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
            <span class="input-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 11a7 7 0 0 1 14 0v7a1.78 1.78 0 0 1 -3.1 1.4a1.65 1.65 0 0 0 -2.6 0a1.65 1.65 0 0 1 -2.6 0a1.65 1.65 0 0 0 -2.6 0a1.78 1.78 0 0 1 -3.1 -1.4v-7z"/>
                    <path d="M12 4l0 2"/>
                </svg>
            </span>
            <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="auth-checkbox">
        <input type="checkbox" name="remember" id="remember">
        <label for="remember">Ingat saya di perangkat ini</label>
    </div>

    <button type="submit" class="btn-auth btn-auth-primary">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M9 8v-2a2 2 0 0 1 2 -2h7a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-2"/>
            <path d="M9 12h12l-3 -3"/>
            <path d="M18 15l-3 -3"/>
        </svg>
        Masuk
    </button>
</form>

<div class="auth-footer">
    Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
</div>
@endsection

@section('scripts')
<script>
    function togglePassword(fieldId, btn) {
        const field = document.getElementById(fieldId);
        const isPassword = field.type === 'password';
        field.type = isPassword ? 'text' : 'password';
        btn.innerHTML = isPassword
            ? '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17.94 17.94a10.07 10.07 0 0 1 -11.291 -11.291"/><path d="M10.5 10.5a2 2 0 1 0 3.511 3.511"/><path d="M8.4 8.4l7.6 7.6"/><path d="M21 3l-6 6"/><path d="M3 3l6 6"/></svg>'
            : '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/></svg>';
    }
</script>
@endsection
