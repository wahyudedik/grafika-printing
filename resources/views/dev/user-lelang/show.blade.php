@extends('dev.layouts.app')

@section('title', 'Detail User Lelang - ' . ($profile->user->name ?? 'Unknown'))

@section('content')
<div class="space-y-6" x-data="{ showSuspendModal: false, suspendReason: '' }">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Profile Card --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="p-5 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 text-3xl font-bold mb-3">
                        {{ strtoupper(substr($profile->user->name ?? 'U', 0, 2)) }}
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $profile->user->name ?? '-' }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $profile->user->email ?? '-' }}</p>
                    <div class="flex justify-center gap-2 mt-3">
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
                        @if($profile->is_verified)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                <i class="fas fa-check text-xs"></i>
                                Terverifikasi
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Belum Verifikasi</span>
                        @endif
                    </div>

                    {{-- Quick Stats --}}
                    <div class="grid grid-cols-3 gap-2 mt-4">
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-2">
                            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $profile->total_auctions }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Lelang</div>
                        </div>
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-2">
                            <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400">{{ $profile->total_won }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Menang</div>
                        </div>
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-2">
                            <div class="text-xl font-bold text-primary-600 dark:text-primary-400">{{ $profile->win_rate }}%</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Win Rate</div>
                        </div>
                    </div>

                    {{-- Win Rate Progress --}}
                    <div class="mt-4">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Tingkat Kemenangan</span>
                            <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $profile->win_rate }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-primary-500 h-2 rounded-full transition-all duration-500" style="width: {{ $profile->win_rate }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Profile Details --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Informasi Profil</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Perusahaan</span>
                        <div class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $profile->company_name ?? '-' }}</div>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Telepon</span>
                        <div class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $profile->phone_number ?? '-' }}</div>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Alamat</span>
                        <div class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $profile->address ?? '-' }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kota</span>
                            <div class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $profile->city ?? '-' }}</div>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Provinsi</span>
                            <div class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $profile->province ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Belanja</span>
                        <div class="mt-1 text-xl font-bold text-primary-600 dark:text-primary-400">Rp {{ number_format($profile->total_spent, 0, ',', '.') }}</div>
                    </div>
                    @if($profile->total_auctions > 0)
                        <div>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rata-rata per Lelang</span>
                            <div class="mt-1 font-semibold text-gray-900 dark:text-white">Rp {{ number_format($profile->total_spent / max($profile->total_auctions, 1), 0, ',', '.') }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Notes --}}
            @if($profile->notes)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Catatan Admin</h3>
                    </div>
                    <div class="p-5">
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-0">{{ $profile->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Actions Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Aksi</h3>
                </div>
                <div class="p-5">
                    <div class="flex flex-wrap gap-2">
                        <x.ui.button href="{{ route('admin.user-lelang.edit', $profile) }}" variant="warning" size="sm">
                            <i class="fas fa-pen text-xs"></i> Edit Profil
                        </x.ui.button>

                        @if(!$profile->is_verified)
                            <form action="{{ route('admin.user-lelang.verify', $profile) }}" method="POST" class="inline">
                                @csrf
                                <x.ui.button type="submit" variant="success" size="sm" onclick="return confirm('Yakin ingin memverifikasi profil ini?')">
                                    <i class="fas fa-check text-xs"></i> Verifikasi
                                </x.ui.button>
                            </form>
                        @endif

                        @if($profile->isActive())
                            <x.ui.button type="button" variant="danger" size="sm" @click="showSuspendModal = true">
                                <i class="fas fa-ban text-xs"></i> Tangguhkan
                            </x.ui.button>
                        @elseif($profile->isSuspended())
                            <form action="{{ route('admin.user-lelang.reactivate', $profile) }}" method="POST" class="inline">
                                @csrf
                                <x.ui.button type="submit" variant="success" size="sm" onclick="return confirm('Yakin ingin mengaktifkan kembali profil ini?')">
                                    <i class="fas fa-check-circle text-xs"></i> Aktifkan Kembali
                                </x.ui.button>
                            </form>
                        @endif

                        <form action="{{ route('admin.user-lelang.destroy', $profile) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <x.ui.button type="submit" variant="outline-danger" size="sm" onclick="return confirm('Yakin ingin menghapus profil ini? Tindakan ini tidak dapat dibatalkan.')">
                                <i class="fas fa-trash text-xs"></i> Hapus
                            </x.ui.button>
                        </form>

                        <x.ui.button href="{{ route('admin.user-lelang.index') }}" variant="secondary" size="sm">
                            <i class="fas fa-arrow-left text-xs"></i> Kembali
                        </x.ui.button>
                    </div>
                </div>
            </div>

            {{-- Auction Statistics --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Statistik Lelang</h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $auctionStats['total'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Lelang</div>
                        </div>
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $auctionStats['active'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Aktif</div>
                        </div>
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $auctionStats['completed'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Selesai</div>
                        </div>
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $auctionStats['won'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Menang</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Spending Analytics --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-chart-pie text-primary-500"></i>
                        Analisis Pengeluaran
                    </h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-center">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Pengeluaran</div>
                            <div class="text-lg font-bold text-primary-600 dark:text-primary-400">Rp {{ number_format($profile->total_spent, 0, ',', '.') }}</div>
                        </div>
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-center">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Rata-rata Bid</div>
                            @php
                                $avgBid = $recentAuctions->filter(fn($a) => $a->bids->count() > 0)
                                    ->flatMap(fn($a) => $a->bids)
                                    ->avg('bid_amount');
                            @endphp
                            <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($avgBid ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-center">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Bid Terakhir 30 Hari</div>
                            @php
                                $recentBids = \App\Models\AuctionBid::whereHas('auction', fn($q) => $q->where('user_id', $profile->user_id))
                                    ->where('created_at', '>=', now()->subDays(30))
                                    ->count();
                            @endphp
                            <div class="text-lg font-bold text-amber-600 dark:text-amber-400">{{ $recentBids }} bid</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Auctions --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Lelang Terbaru</h3>
                </div>
                @if($recentAuctions->count() > 0)
                    {{-- Desktop Table --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Judul</th>
                                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Harga Awal</th>
                                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bid Tertinggi</th>
                                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($recentAuctions as $auction)
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                            'active' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                            'completed' => 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400',
                                            'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                            'closed' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                        ];
                                        $statusClass = $statusColors[$auction->status] ?? $statusColors['closed'];
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-5 py-4">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $auction->title }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">{{ ucfirst($auction->status) }}</span>
                                        </td>
                                        <td class="px-5 py-4 text-gray-700 dark:text-gray-300">Rp {{ number_format($auction->starting_price, 0, ',', '.') }}</td>
                                        <td class="px-5 py-4">
                                            @php $lowestBid = $auction->getLowestBid(); @endphp
                                            @if($lowestBid)
                                                <span class="text-gray-700 dark:text-gray-300">Rp {{ number_format($lowestBid->bid_amount, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $auction->created_at->format('d M Y') }}</td>
                                        <td class="px-5 py-4">
                                            <x.ui.button href="{{ route('admin.auctions.show', $auction) }}" variant="ghost" size="icon-sm" title="Lihat">
                                                <i class="fas fa-eye text-sm"></i>
                                            </x.ui.button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($recentAuctions as $auction)
                            @php
                                $statusColors = [
                                    'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                    'active' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                    'completed' => 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400',
                                    'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    'closed' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                ];
                                $statusClass = $statusColors[$auction->status] ?? $statusColors['closed'];
                                $lowestBid = $auction->getLowestBid();
                            @endphp
                            <div class="p-4 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $auction->title }}</div>
                                    <x.ui.button href="{{ route('admin.auctions.show', $auction) }}" variant="ghost" size="icon-sm">
                                        <i class="fas fa-eye text-sm"></i>
                                    </x.ui.button>
                                </div>
                                <div class="flex items-center gap-3 text-sm">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">{{ ucfirst($auction->status) }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">{{ $auction->created_at->format('d M Y') }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Harga Awal:</span>
                                        <span class="ml-1 text-gray-900 dark:text-white">Rp {{ number_format($auction->starting_price, 0, ',', '.') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Bid Tertinggi:</span>
                                        <span class="ml-1 text-gray-900 dark:text-white">{{ $lowestBid ? 'Rp ' . number_format($lowestBid->bid_amount, 0, ',', '.') : '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <i class="fas fa-gavel text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-1">Belum ada lelang</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">User ini belum membuat lelang apapun.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Suspend Modal (Alpine.js) --}}
    <div x-show="showSuspendModal" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="suspendModalTitle" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-end sm:items-center justify-center p-4">
            {{-- Backdrop --}}
            <div x-show="showSuspendModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/75" @click="showSuspendModal = false"></div>

            {{-- Modal Panel --}}
            <div x-show="showSuspendModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative w-full max-w-lg transform overflow-hidden rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xl transition-all">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="suspendModalTitle">Tangguhkan User Lelang</h3>
                        <button type="button" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300" @click="showSuspendModal = false">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>
                <form action="{{ route('admin.user-lelang.suspend', $profile) }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <p class="text-sm text-gray-700 dark:text-gray-300">Anda yakin ingin menangguhkan profil <strong class="font-semibold">{{ $profile->user->name ?? '-' }}</strong>?</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">User ini tidak akan bisa mengikuti lelang baru sampai profil diaktifkan kembali.</p>
                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alasan Penangguhan <span class="text-red-500">*</span></label>
                            <textarea name="reason" id="reason" class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500" rows="3" placeholder="Masukkan alasan penangguhan..." required></textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                        <x.ui.button type="button" variant="secondary" size="sm" @click="showSuspendModal = false">Batal</x.ui.button>
                        <x.ui.button type="submit" variant="danger" size="sm">Tangguhkan</x.ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
