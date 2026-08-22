@extends('layouts.vendor')

@section('title', 'Alert Stok Bahan')

@section('content')
<div x-data="stockAlerts()" x-init="init()">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Alert Stok Bahan</h2>
            <p class="text-sm text-gray-500 mt-1">Pantau kondisi stok bahan percetakan Anda</p>
        </div>
        <div class="flex items-center gap-3">
            @if($unreadCount > 0)
                <button
                    @click="markAllRead()"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Tandai Semua Dibaca
                </button>
            @endif
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex gap-2 mb-6">
        <a href="{{ route('vendor.pos.stock.alerts', ['filter' => 'unread']) }}"
            class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $filter === 'unread' ? 'bg-primary-600 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
            Belum Dibaca
            @if($unreadCount > 0)
                <span class="ml-1.5 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">{{ $unreadCount }}</span>
            @endif
        </a>
        <a href="{{ route('vendor.pos.stock.alerts', ['filter' => 'read']) }}"
            class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $filter === 'read' ? 'bg-primary-600 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
            Sudah Dibaca
        </a>
        <a href="{{ route('vendor.pos.stock.alerts', ['filter' => 'all']) }}"
            class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $filter === 'all' ? 'bg-primary-600 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
            Semua
        </a>
    </div>

    {{-- Alert List --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        @if($alerts->isEmpty())
            <div class="px-6 py-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 text-gray-300">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak ada alert</h3>
                <p class="text-sm text-gray-500">
                    @if($filter === 'unread')
                        Semua stok bahan dalam kondisi aman.
                    @else
                        Belum ada riwayat alert stok.
                    @endif
                </p>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($alerts as $alert)
                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors {{ $alert->is_read ? 'opacity-60' : '' }}"
                         x-data="{ showDetail: false }">
                        <div class="flex items-start gap-4">
                            {{-- Icon --}}
                            <div class="flex-shrink-0 mt-0.5">
                                @if($alert->type === 'out_of_stock')
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-red-100">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                    </span>
                                @elseif($alert->type === 'low_stock')
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-yellow-100">
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-100">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-sm font-semibold text-gray-900">{{ $alert->bahan->nama_bahan ?? 'Bahan tidak ditemukan' }}</h4>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ match($alert->type) {
                                            'out_of_stock' => 'bg-red-100 text-red-700',
                                            'low_stock' => 'bg-yellow-100 text-yellow-700',
                                            'restocked' => 'bg-green-100 text-green-700',
                                            default => 'bg-gray-100 text-gray-700',
                                        }}">
                                        {{ $alert->type_label }}
                                    </span>
                                    @if(!$alert->is_read)
                                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 mt-1">{{ $alert->message ?? '-' }}</p>
                                <div class="flex items-center gap-4 mt-2 text-xs text-gray-400">
                                    <span>Stok: {{ $alert->previous_stock }} → {{ $alert->current_stock }}</span>
                                    <span>Threshold: {{ $alert->threshold }}</span>
                                    <span>{{ $alert->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2">
                                @if(!$alert->is_read)
                                    <button
                                        @click="markAsRead({{ $alert->id }})"
                                        class="text-xs text-primary-600 hover:text-primary-700 font-medium whitespace-nowrap">
                                        Tandai Dibaca
                                    </button>
                                @endif
                                <button
                                    @click="showDetail = !showDetail"
                                    class="p-1 text-gray-400 hover:text-gray-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Detail Panel --}}
                        <div x-show="showDetail" x-transition class="mt-3 ml-14 p-4 bg-gray-50 rounded-lg" x-cloak>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Bahan</span>
                                    <p class="font-medium text-gray-900">{{ $alert->bahan->nama_bahan ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500">Satuan</span>
                                    <p class="font-medium text-gray-900">{{ $alert->bahan->satuan ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500">Stok Saat Ini</span>
                                    <p class="font-medium {{ $alert->current_stock <= 0 ? 'text-red-600' : 'text-yellow-600' }}">{{ $alert->current_stock }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500">Threshold Minimum</span>
                                    <p class="font-medium text-gray-900">{{ $alert->threshold }}</p>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <span class="text-gray-500 text-sm">Pesan:</span>
                                <p class="text-sm text-gray-700 mt-1">{{ $alert->message ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $alerts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function stockAlerts() {
    return {
        init() {
            // Auto-refresh unread count every 30 seconds
            setInterval(() => this.fetchUnreadCount(), 30000);
        },

        async fetchUnreadCount() {
            try {
                const response = await fetch('{{ route("vendor.pos.stock.alerts.unread-count") }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                // Update badge if exists
                const badge = document.getElementById('stock-alert-badge');
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count > 9 ? '9+' : data.count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            } catch (e) {
                console.error('Failed to fetch unread count:', e);
            }
        },

        async markAsRead(alertId) {
            try {
                const response = await fetch(`/vendor/pos/stock/alerts/${alertId}/read`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                if (data.success) {
                    window.location.reload();
                }
            } catch (e) {
                console.error('Failed to mark as read:', e);
            }
        },

        async markAllRead() {
            try {
                const response = await fetch('{{ route("vendor.pos.stock.alerts.mark-all-read") }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                if (data.success) {
                    window.location.reload();
                }
            } catch (e) {
                console.error('Failed to mark all as read:', e);
            }
        }
    };
}
</script>
@endpush
