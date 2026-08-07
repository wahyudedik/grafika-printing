@extends('layouts.auth')

@section('content')
<div class="text-center mb-8">
    <div class="mx-auto w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M5 11a7 7 0 0 1 14 0v7a1.78 1.78 0 0 1 -3.1 1.4a1.65 1.65 0 0 0 -2.6 0a1.65 1.65 0 0 1 -2.6 0a1.65 1.65 0 0 0 -2.6 0a1.78 1.78 0 0 1 -3.1 -1.4v-7z"/>
            <path d="M12 4l0 2"/>
        </svg>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Atur Ulang Password</h1>
    <p class="text-sm text-gray-500">Buat password baru yang kuat untuk akun Anda</p>
</div>

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

<form method="POST" action="{{ route('password.store') }}" class="space-y-5" autocomplete="off">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" class="w-full rounded-lg border border-gray-200 bg-gray-50 py-2.5 px-4 text-sm text-gray-500 cursor-not-allowed"
            value="{{ old('email', $request->email) }}" required readonly>
    </div>

    <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">Password Baru</label>
        <div class="relative">
            <input type="password" name="password" id="password" class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-10 text-sm text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition-colors" placeholder="Buat password baru" required autofocus>
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 11a7 7 0 0 1 14 0v7a1.78 1.78 0 0 1 -3.1 1.4a1.65 1.65 0 0 0 -2.6 0a1.65 1.65 0 0 1 -2.6 0a1.65 1.65 0 0 0 -2.6 0a1.78 1.78 0 0 1 -3.1 -1.4v-7z"/>
                    <path d="M12 4l0 2"/>
                </svg>
            </span>
            <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md p-1 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors" onclick="togglePassword('password', this)">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                </svg>
            </button>
        </div>
        <div class="mt-2">
            <div class="h-1.5 w-full rounded-full bg-gray-200 overflow-hidden">
                <div id="strength-fill" class="h-full w-full rounded-full bg-gray-200 transition-all duration-300"></div>
            </div>
            <span id="strength-text" class="text-xs text-gray-500 mt-1">Masukkan password</span>
        </div>
    </div>

    <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
        <div class="relative">
            <input type="password" name="password_confirmation" class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition-colors" placeholder="Ulangi password baru" required>
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 11a7 7 0 0 1 14 0v7a1.78 1.78 0 0 1 -3.1 1.4a1.65 1.65 0 0 0 -2.6 0a1.65 1.65 0 0 1 -2.6 0a1.65 1.65 0 0 0 -2.6 0a1.78 1.78 0 0 1 -3.1 -1.4v-7z"/>
                    <path d="M12 4l0 2"/>
                </svg>
            </span>
        </div>
    </div>

    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20 transition-colors">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M5 12l5 5l10 -10"/>
            <path d="M12 5l7.5 7.5"/>
            <path d="M12 5v7.5"/>
        </svg>
        Reset Password
    </button>
</form>

<div class="text-center mt-6">
    <p class="text-sm text-gray-500">
        Ingat password Anda? <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:text-primary-500 transition-colors">Masuk</a>
    </p>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initPasswordStrength('password', 'strength-fill', 'strength-text');
    });
</script>
@endsection
