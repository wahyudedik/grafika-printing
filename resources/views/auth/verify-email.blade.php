@extends('layouts.auth')

@section('content')
<div class="text-center mb-8">
    <div class="mx-auto w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M5 7a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10a2 2 0 0 0 -2 -2h-10z"/>
            <path d="M3 7l9 6l9 -6"/>
            <path d="M12 12l3.405 2.013a1 1 0 0 1 .595 .887v1.1l2 1.1"/>
        </svg>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Verifikasi Email</h1>
    <p class="text-sm text-gray-500">Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengklik tautan yang kami kirimkan.</p>
</div>

@if (session('status') == 'verification-link-sent')
    <div class="rounded-lg p-4 text-sm flex items-start gap-3 bg-emerald-50 text-emerald-700 border border-emerald-200 mb-6">
        <svg class="w-5 h-5 mt-0.5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M5 12l5 5l10 -10"/>
        </svg>
        <div>Tautan verifikasi baru telah dikirim ke alamat email Anda.</div>
    </div>
@endif

<div class="space-y-3">
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 transition-colors">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M5 7a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10a2 2 0 0 0 -2 -2h-10z"/>
                <path d="M3 7l9 6l9 -6"/>
            </svg>
            Kirim Ulang Email Verifikasi
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500/20 transition-colors">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"/>
                <path d="M9 12h12l-3 -3"/>
                <path d="M18 15l-3 -3"/>
            </svg>
            Keluar
        </button>
    </form>
</div>

<div class="text-center mt-6">
    <p class="text-sm text-gray-500">
        Tidak menerima email?<br>
        Cek folder spam atau <a href="{{ route('verification.send') }}" class="font-medium text-primary-600 hover:text-primary-500 transition-colors">klik di sini untuk mengirim ulang</a>
    </p>
</div>
@endsection
