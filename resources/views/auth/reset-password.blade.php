@extends('layouts.auth')

@section('content')
<div class="auth-form-header">
    <div class="auth-icon-circle">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M5 11a7 7 0 0 1 14 0v7a1.78 1.78 0 0 1 -3.1 1.4a1.65 1.65 0 0 0 -2.6 0a1.65 1.65 0 0 1 -2.6 0a1.65 1.65 0 0 0 -2.6 0a1.78 1.78 0 0 1 -3.1 -1.4v-7z"/>
            <path d="M12 4l0 2"/>
        </svg>
    </div>
    <h1>Atur Ulang Password</h1>
    <p>Buat password baru yang kuat untuk akun Anda</p>
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

<form method="POST" action="{{ route('password.store') }}" class="auth-form" autocomplete="off">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div class="form-group">
        <label class="form-label">Email</label>
        <div class="input-wrapper">
            <input type="email" name="email" class="form-control no-icon"
                value="{{ old('email', $request->email) }}" required readonly
                style="background: #f8f9fa; cursor: not-allowed;">
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Password Baru</label>
        <div class="input-wrapper">
            <input type="password" name="password" id="password" class="form-control" placeholder="Buat password baru" required autofocus>
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
        <div class="password-strength">
            <div class="strength-bar">
                <div class="strength-fill strength-weak" id="strength-fill"></div>
            </div>
            <span class="strength-text" id="strength-text">Masukkan password</span>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Konfirmasi Password</label>
        <div class="input-wrapper">
            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru" required>
            <span class="input-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 11a7 7 0 0 1 14 0v7a1.78 1.78 0 0 1 -3.1 1.4a1.65 1.65 0 0 0 -2.6 0a1.65 1.65 0 0 1 -2.6 0a1.65 1.65 0 0 0 -2.6 0a1.78 1.78 0 0 1 -3.1 -1.4v-7z"/>
                    <path d="M12 4l0 2"/>
                </svg>
            </span>
        </div>
    </div>

    <button type="submit" class="btn-auth btn-auth-primary">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M5 12l5 5l10 -10"/>
            <path d="M12 5l7.5 7.5"/>
            <path d="M12 5v7.5"/>
        </svg>
        Reset Password
    </button>
</form>

<div class="auth-footer">
    Ingat password Anda? <a href="{{ route('login') }}">Masuk</a>
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

    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strengthFill = document.getElementById('strength-fill');
        const strengthText = document.getElementById('strength-text');
        let strength = 0;

        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;

        const levels = {
            0: { cls: 'strength-weak', label: 'Sangat lemah', color: '#dc3545' },
            1: { cls: 'strength-weak', label: 'Lemah', color: '#dc3545' },
            2: { cls: 'strength-fair', label: 'Cukup', color: '#ffc107' },
            3: { cls: 'strength-good', label: 'Baik', color: '#17a2b8' },
            4: { cls: 'strength-strong', label: 'Kuat', color: '#28a745' },
            5: { cls: 'strength-strong', label: 'Sangat kuat', color: '#28a745' }
        };

        const level = password.length === 0
            ? { cls: 'strength-weak', label: 'Masukkan password', color: '#e9ecef' }
            : levels[strength];

        strengthFill.className = 'strength-fill ' + level.cls;
        strengthText.textContent = level.label;
        strengthText.style.color = level.color;
    });
</script>
@endsection
