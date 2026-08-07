@extends('dev.layouts.app')

@section('title', 'Tambah User Lelang Baru')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Form Tambah User Lelang</h2>
                </div>
                <form action="{{ route('admin.user-lelang.store') }}" method="POST">
                    @csrf
                    <div class="p-5 space-y-5">
                        {{-- User Selection --}}
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih User <span class="text-red-500">*</span></label>
                            <select name="user_id" id="user_id" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 @error('user_id') border-red-500 @enderror" required>
                                <option value="">-- Pilih User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            @if($users->isEmpty())
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Semua user sudah memiliki profil lelang.</p>
                            @endif
                        </div>

                        {{-- Company Info --}}
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Perusahaan</label>
                            <input type="text" name="company_name" id="company_name" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 @error('company_name') border-red-500 @enderror" value="{{ old('company_name') }}" placeholder="PT. contoh">
                            @error('company_name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Contact Info --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor Telepon</label>
                                <input type="text" name="phone_number" id="phone_number" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 @error('phone_number') border-red-500 @enderror" value="{{ old('phone_number') }}" placeholder="08123456789">
                                @error('phone_number')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode Pos</label>
                                <input type="text" name="postal_code" id="postal_code" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 @error('postal_code') border-red-500 @enderror" value="{{ old('postal_code') }}" placeholder="12345" maxlength="10">
                                @error('postal_code')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Address --}}
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat</label>
                            <textarea name="address" id="address" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 @error('address') border-red-500 @enderror" rows="2" placeholder="Alamat lengkap...">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kota</label>
                                <input type="text" name="city" id="city" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 @error('city') border-red-500 @enderror" value="{{ old('city') }}" placeholder="Jakarta">
                                @error('city')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="province" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Provinsi</label>
                                <input type="text" name="province" id="province" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 @error('province') border-red-500 @enderror" value="{{ old('province') }}" placeholder="DKI Jakarta">
                                @error('province')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Status & Notes --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status <span class="text-red-500">*</span></label>
                            <select name="status" id="status" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 @error('status') border-red-500 @enderror" required>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                                <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan Admin</label>
                            <textarea name="notes" id="notes" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 @error('notes') border-red-500 @enderror" rows="3" placeholder="Catatan internal...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 flex gap-3">
                        <x.ui.button type="submit" variant="primary" :disabled="$users->isEmpty()">
                            <i class="fas fa-plus text-xs mr-1"></i> Simpan
                        </x.ui.button>
                        <x.ui.button type="button" variant="outline" href="{{ route('admin.user-lelang.index') }}">
                            Batal
                        </x.ui.button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar Tips --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-info-circle text-primary-500"></i>
                        Panduan
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-1">User Lelang</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            User Lelang adalah pengguna yang aktif mengikuti lelang di platform.
                            Mereka dapat membuat lelang, menawar produk, dan memenangkan lelang.
                        </p>
                    </div>

                    <div>
                        <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Status:</h5>
                        <ul class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
                            <li class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Aktif</span>
                                - Dapat mengikuti lelang
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Menunggu</span>
                                - Menunggu verifikasi
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Ditangguhkan</span>
                                - Tidak dapat mengikuti lelang
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
