@extends('dev.layouts.app')

@section('title', 'Manajemen User Lelang')

@section('content')
<div class="space-y-6">
    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total User Lelang</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400">{{ $stats['total'] }}</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Aktif</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">{{ $stats['active'] }}</span>
            </div>
            <div class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['active'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Ditangguhkan</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">{{ $stats['suspended'] }}</span>
            </div>
            <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $stats['suspended'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Terverifikasi</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400">{{ $stats['verified'] }}</span>
            </div>
            <div class="text-3xl font-bold text-sky-600 dark:text-sky-400">{{ $stats['verified'] }}</div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar User Lelang</h2>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    {{-- Search --}}
                    <form method="GET" class="flex flex-col sm:flex-row gap-2">
                        <div class="relative">
                            <input type="text" name="search" class="block w-full sm:w-64 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 pl-10 pr-4 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500" placeholder="Cari nama, email, atau perusahaan..." value="{{ request('search') }}">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        </div>
                        <select name="status" class="block w-full sm:w-40 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                        </select>
                        <x.ui.button type="submit" variant="primary" size="sm">
                            <i class="fas fa-filter text-xs mr-1"></i> Filter
                        </x.ui.button>
                        @if(request('search') || request('status'))
                            <x.ui.button type="button" variant="outline" size="sm" href="{{ route('admin.user-lelang.index') }}">
                                Reset
                            </x.ui.button>
                        @endif
                    </form>
                    <x.ui.button type="button" variant="success" href="{{ route('admin.user-lelang.create') }}">
                        <i class="fas fa-plus text-xs mr-1"></i>
                        <span class="hidden sm:inline">Tambah User Lelang</span>
                    </x.ui.button>
                </div>
            </div>
        </div>

        @if($profiles->count() > 0)
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">No</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Perusahaan</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Verifikasi</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lelang</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Menang</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Belanja</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($profiles as $index => $profile)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-5 py-4 text-gray-500 dark:text-gray-400">{{ ($profiles->currentPage() - 1) * $profiles->perPage() + $index + 1 }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 text-sm font-bold">
                                            {{ strtoupper(substr($profile->user->name ?? 'U', 0, 1)) }}
                                        </span>
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $profile->user->name ?? '-' }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $profile->user->email ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-gray-700 dark:text-gray-300">{{ $profile->company_name ?? '-' }}</td>
                                <td class="px-5 py-4">
                                    @php
                                        $statusColorMap = [
                                            'success' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                            'warning' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                            'danger' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                            'info' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400',
                                            'primary' => 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400',
                                            'secondary' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                        ];
                                        $badgeClass = $statusColorMap[$profile->status_color] ?? $statusColorMap['secondary'];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                        {{ $profile->status_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @if($profile->is_verified)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                            <i class="fas fa-check text-xs"></i>
                                            Terverifikasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Belum Verifikasi</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-gray-700 dark:text-gray-300">{{ $profile->total_auctions }}</td>
                                <td class="px-5 py-4 text-gray-700 dark:text-gray-300">{{ $profile->total_won }}</td>
                                <td class="px-5 py-4 text-gray-700 dark:text-gray-300">Rp {{ number_format($profile->total_spent, 0, ',', '.') }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex gap-1">
                                        <x.ui.button type="button" variant="ghost" size="icon-sm" href="{{ route('admin.user-lelang.show', $profile) }}" title="Lihat Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </x.ui.button>
                                        <x.ui.button type="button" variant="ghost" size="icon-sm" href="{{ route('admin.user-lelang.edit', $profile) }}" title="Edit">
                                            <i class="fas fa-pen text-sm"></i>
                                        </x.ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($profiles as $index => $profile)
                    @php
                        $statusColorMap = [
                            'success' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                            'warning' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                            'danger' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                            'info' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400',
                            'primary' => 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400',
                            'secondary' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                        ];
                        $badgeClass = $statusColorMap[$profile->status_color] ?? $statusColorMap['secondary'];
                    @endphp
                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 text-sm font-bold">
                                    {{ strtoupper(substr($profile->user->name ?? 'U', 0, 1)) }}
                                </span>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $profile->user->name ?? '-' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $profile->user->email ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="flex gap-1">
                                <x.ui.button type="button" variant="ghost" size="icon-sm" href="{{ route('admin.user-lelang.show', $profile) }}">
                                    <i class="fas fa-eye text-sm"></i>
                                </x.ui.button>
                                <x.ui.button type="button" variant="ghost" size="icon-sm" href="{{ route('admin.user-lelang.edit', $profile) }}">
                                    <i class="fas fa-pen text-sm"></i>
                                </x.ui.button>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Perusahaan:</span>
                                <span class="ml-1 text-gray-900 dark:text-white">{{ $profile->company_name ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Status:</span>
                                <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">{{ $profile->status_label }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Lelang:</span>
                                <span class="ml-1 text-gray-900 dark:text-white">{{ $profile->total_auctions }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Menang:</span>
                                <span class="ml-1 text-gray-900 dark:text-white">{{ $profile->total_won }}</span>
                            </div>
                        </div>
                        <div class="text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Total Belanja:</span>
                            <span class="ml-1 font-medium text-gray-900 dark:text-white">Rp {{ number_format($profile->total_spent, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($profiles->hasPages())
                <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Menampilkan {{ $profiles->firstItem() }}-{{ $profiles->lastItem() }} dari {{ $profiles->total() }} data
                    </div>
                    <div>
                        {{ $profiles->withQueryString()->links() }}
                    </div>
                </div>
            @endif
        @else
            <x-ui.empty-state icon="fas fa-users" title="Belum ada User Lelang" description="Tambahkan user lelang baru untuk mulai mengelola peserta lelang." size="lg">
                <x-slot name="actions">
                    <x.ui.button type="button" variant="primary" href="{{ route('admin.user-lelang.create') }}">
                        <i class="fas fa-plus text-xs mr-1"></i> Tambah User Lelang
                    </x.ui.button>
                </x-slot>
            </x-ui.empty-state>
        @endif
    </div>
</div>
@endsection
