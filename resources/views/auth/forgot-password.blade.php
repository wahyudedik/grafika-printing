@extends('layouts.auth')

@section('content')
<div class="text-center mb-8">
    <div class="mx-auto w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M5 7a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10a2 2 0 0 0 -2 -2h-10z"/>
            <path d="M3 7l9 6l9 -6"/>
        </svg>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Lupa Password?</h1>
    <p class="text-sm text-gray-500">Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk mengatur ulang password.</p>
</div>

@if (session('status'))
    <div class="rounded-lg p-4 text-sm flex items-start gap-3 bg-emerald-50 text-emerald-700 border border-emerald-200 mb-6">
        <svg class="w-5 h-5 mt-0.5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M5 12l5 5l10 -10"/>
        </svg>
        <div>{{ session('status') }}</div>
    </div>
@endif

@if ($errors->any())
    <div class="rounded-lg p-4 text-sm flex items-start gap-3 bg-red-50 text-red-700 border border-red-200 mb-6">
        <svg class="w-5 h-5 mt-0.5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
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

<form method="POST" action="{{ route('password.email') }}" class="space-y-5" autocomplete="off">
    @csrf

    <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <div class="relative">
            <input type="email" name="email" class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition-colors" placeholder="nama@email.com"
                value="{{ old('email') }}" required autofocus>
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 7a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10a2 2 0 0 0 -2 -2h-10z"/>
                    <path d="M3 7l9 6l9 -6"/>
                </svg>
            </span>
        </div>
    </div>

    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 transition-colors">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M5 7a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10a2 2 0 0 0 -2 -2h-10z"/>
            <path d="M3 7l9 6l9 -6"/>
        </svg>
        Kirim Tautan Reset
    </button>
</form>

<div class="text-center mt-6">
    <p class="text-sm text-gray-500">
        Ingat password Anda? <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:text-primary-500 transition-colors">Masuk</a>
    </p>
</div>
@endsection
