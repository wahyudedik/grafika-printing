@extends('dev.layouts.app')

@section('title', 'Manajemen Lelang')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Lelang</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola semua lelang dan moderasi konten</p>
        </div>
        <x.ui.button href="{{ route('admin.auctions.statistics') }}" variant="outline-primary" size="sm">
            <i class="fas fa-chart-bar"></i> Statistik
        </x.ui.button>
    </div>

    {{-- Filter Tabs --}}
    @php
        $statuses = [
            '' => ['label' => 'Semua', 'count' => \App\Models\Auction::count(), 'color' => 'primary'],
            'pending' => ['label' => 'Pending', 'count' => \App\Models\Auction::where('status', 'pending')->count(), 'color' => 'warning'],
            'active' => ['label' => 'Aktif', 'count' => \App\Models\Auction::where('status', 'active')->count(), 'color' => 'success'],
            'rejected' => ['label' => 'Ditolak', 'count' => \App\Models\Auction::where('status', 'rejected')->count(), 'color' => 'danger'],
        ];
    @endphp
    <div class="mb-6 flex flex-wrap gap-2">
        @foreach($statuses as $status => $info)
            @php $isActive = request('status') === $status; @endphp
            <a href="{{ route('admin.auctions.index', ['status' => $status]) }}"
               class="px-4 py-2 text-sm font-medium rounded-lg transition-colors
                @if($isActive)
                    @if($info['color'] === 'primary') bg-primary-600 text-white
                    @elseif($info['color'] === 'warning') bg-amber-500 text-white
                    @elseif($info['color'] === 'success') bg-emerald-500 text-white
                    @elseif($info['color'] === 'danger') bg-red-500 text-white
                    @endif
                @else
                    bg-white text-gray-700 border border-gray-300 hover:bg-gray-50
                    dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700
                @endif">
                {{ $info['label'] }} ({{ $info['count'] }})
            </a>
        @endforeach
    </div>

    {{-- Auctions Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($auctions as $auction)
            @php
                $statusColor = match($auction->status) {
                    'pending' => 'amber',
                    'active' => 'emerald',
                    'rejected' => 'red',
                    default => 'gray',
                };
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                {{-- Card Header --}}
                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 dark:text-white truncate pr-2">{{ Str::limit($auction->title, 30) }}</h3>
                    <span class="shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($auction->status === 'pending') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300
                        @elseif($auction->status === 'active') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300
                        @elseif($auction->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                        {{ ucfirst($auction->status) }}
                    </span>
                </div>

                {{-- Card Body --}}
                <div class="px-4 py-3 space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">User</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $auction->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Budget</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Rp {{ number_format($auction->budget, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Kategori</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $auction->category }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Quantity</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $auction->quantity }} pcs</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Deadline</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $auction->deadline->format('d M Y H:i') }}</p>
                    </div>
                    @if($auction->status == 'rejected' && $auction->rejection_reason)
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-2">
                            <p class="text-xs text-red-600 dark:text-red-400 font-medium">Alasan Ditolak</p>
                            <p class="text-sm text-red-700 dark:text-red-300">{{ $auction->rejection_reason }}</p>
                        </div>
                    @endif
                </div>

                {{-- Card Footer --}}
                <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex gap-2">
                        <x.ui.button href="{{ route('admin.auctions.show', $auction) }}" variant="outline-primary" size="xs" class="flex-1">
                            <i class="fas fa-eye text-xs"></i> Detail
                        </x.ui.button>
                        @if($auction->status == 'pending')
                            <form action="{{ route('admin.auctions.approve', $auction) }}" method="POST" class="flex-1" x-data>
                                @csrf
                                <x.ui.button type="submit" variant="outline-success" size="xs" class="w-full" @click="return confirm('Setujui lelang ini?')">
                                    <i class="fas fa-check text-xs"></i> Setujui
                                </x.ui.button>
                            </form>
                            <div class="flex-1" x-data="{ open: false }">
                                <x.ui.button type="button" variant="outline-danger" size="xs" class="w-full" @click="open = true">
                                    <i class="fas fa-times text-xs"></i> Tolak
                                </x.ui.button>
                                {{-- Reject Modal --}}
                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" x-cloak @click.away="open = false">
                                    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
                                    <div class="flex min-h-full items-center justify-center p-4">
                                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-md">
                                            <form action="{{ route('admin.auctions.reject', $auction) }}" method="POST">
                                                @csrf
                                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tolak Lelang</h3>
                                                </div>
                                                <div class="px-6 py-4">
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                                                    <textarea name="rejection_reason" rows="4" required placeholder="Masukkan alasan penolakan lelang..." class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:focus:ring-primary-400 dark:focus:border-primary-400"></textarea>
                                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Alasan ini akan dikirim ke user yang membuat lelang.</p>
                                                </div>
                                                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                                                    <x.ui.button type="button" variant="outline" size="sm" @click="open = false">Batal</x.ui.button>
                                                    <x.ui.button type="submit" variant="danger" size="sm">Tolak Lelang</x.ui.button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <i class="fas fa-gavel text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">Tidak ada lelang</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada lelang yang sesuai dengan filter yang dipilih.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($auctions->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $auctions->links() }}
        </div>
    @endif
@endsection
