@extends('dev.layouts.app')

@section('title', 'Edit User Lelang - ' . ($profile->user->name ?? 'Unknown'))

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Form Edit User Lelang</h2>
                </div>
                <form action="{{ route('admin.user-lelang.update', $profile) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-5 space-y-5">
                        {{-- User Info (readonly) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">User</label>
                            <div class="text-gray-900 dark:text-white font-semibold">{{ $profile->user->name ?? '-' }} ({{ $profile->user->email ?? '-' }})</div>
                        </div>

                        {{-- Company Info --}}
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Perusahaan</label>
                            <input type="text" name="company_name" id="company_name" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 @error('company_name') border-red-500 @enderror" value="{{ old('company_name', $profile->company_name) }}" placeholder="PT. contoh">
                            @error('company_name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Contact Info --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor Telepon</label>
                                <input type="text" name="phone_number" id="phone_number" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 @error('phone_number') border-red-500 @enderror" value="{{ old('phone_number', $profile->phone_number) }}" placeholder="08123456789">
                                @error('phone_number')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode Pos</label>
                                <input type="text" name="postal_code" id="postal_code" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 @error('postal_code') border-red-500 @enderror" value="{{ old('postal_code', $profile->postal_code) }}" placeholder="12345" maxlength="10">
                                @error('postal_code')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Address --}}
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat</label>
                            <textarea name="address" id="address" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 @error('address') border-red-500 @enderror" rows="2" placeholder="Alamat lengkap...">{{ old('address', $profile->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kota</label>
                                <input type="text" name="city" id="city" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 @error('city') border-red-500 @enderror" value="{{ old('city', $profile->city) }}" placeholder="Jakarta">
                                @error('city')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="province" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Provinsi</label>
                                <input type="text" name="province" id="province" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 @error('province') border-red-500 @enderror" value="{{ old('province', $profile->province) }}" placeholder="DKI Jakarta">
                                @error('province')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Status & Notes --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status <span class="text-red-500">*</span></label>
                            <select name="status" id="status" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 @error('status') border-red-500 @enderror" required>
                                <option value="active" {{ old('status', $profile->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="pending" {{ old('status', $profile->status) === 'pending' ? 'selected' : '' }}>Menunggu</option>
                                <option value="suspended" {{ old('status', $profile->status) === 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan Admin</label>
                            <textarea name="notes" id="notes" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 @error('notes') border-red-500 @enderror" rows="3" placeholder="Catatan internal...">{{ old('notes', $profile->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 flex gap-3">
                        <x.ui.button type="submit" variant="primary">
                            <i class="fas fa-check text-xs mr-1"></i> Simpan Perubahan
                        </x.ui.button>
                        <x.ui.button type="button" variant="outline" href="{{ route('admin.user-lelang.show', $profile) }}">
                            Batal
                        </x.ui.button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Statistik</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Terdaftar Sejak</span>
                        <div class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $profile->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Lelang Diikuti</span>
                        <div class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ $profile->total_auctions }}</div>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Menang</span>
                        <div class="mt-1 text-xl font-bold text-emerald-600 dark:text-emerald-400">{{ $profile->total_won }}</div>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Win Rate</span>
                        <div class="mt-1 text-xl font-bold text-primary-600 dark:text-primary-400">{{ $profile->win_rate }}%</div>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Belanja</span>
                        <div class="mt-1 text-xl font-bold text-amber-600 dark:text-amber-400">Rp {{ number_format($profile->total_spent, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            @if($profile->is_verified)
                <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-200 dark:border-emerald-800 shadow-sm p-5">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-xl text-emerald-600 dark:text-emerald-400"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-emerald-700 dark:text-emerald-300">Terverifikasi</div>
                            <div class="text-sm text-emerald-600 dark:text-emerald-400">
                                Diverifikasi pada {{ $profile->verified_at?->format('d M Y H:i') ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
