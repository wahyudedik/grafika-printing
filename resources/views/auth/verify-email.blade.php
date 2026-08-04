@extends('layouts.auth')

@section('content')
<div class="auth-form-header">
    <div class="auth-icon-circle">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M5 7a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10a2 2 0 0 0 -2 -2h-10z"/>
            <path d="M3 7l9 6l9 -6"/>
            <path d="M12 12l3.405 2.013a1 1 0 0 1 .595 .887v1.1l2 1.1"/>
        </svg>
    </div>
    <h1>Verifikasi Email</h1>
    <p>Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengklik tautan yang kami kirimkan.</p>
</div>

@if (session('status') == 'verification-link-sent')
    <div class="auth-alert auth-alert-success">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M5 12l5 5l10 -10"/>
        </svg>
        <div>Tautan verifikasi baru telah dikirim ke alamat email Anda.</div>
    </div>
@endif

<div class="btn-group-vertical">
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-auth btn-auth-primary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M5 7a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10a2 2 0 0 0 -2 -2h-10z"/>
                <path d="M3 7l9 6l9 -6"/>
            </svg>
            Kirim Ulang Email Verifikasi
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-auth btn-auth-outline">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"/>
                <path d="M9 12h12l-3 -3"/>
                <path d="M18 15l-3 -3"/>
            </svg>
            Keluar
        </button>
    </form>
</div>

<div class="auth-footer" style="margin-top: 1.5rem;">
    <span>Tidak menerima email?</span><br>
    <span>Cek folder spam atau <a href="{{ route('verification.send') }}">klik di sini untuk mengirim ulang</a></span>
</div>
@endsection
