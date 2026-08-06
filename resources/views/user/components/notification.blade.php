{{--
    Notification Dropdown Component
    Used in user layout header
--}}
<div x-data="notificationDropdown()" class="relative">
    {{-- Tombol Notifikasi --}}
    <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
        <i class="fas fa-bell text-lg"></i>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <span class="absolute -top-0.5 -right-0.5 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
                {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
            </span>
        @endif
    </button>

    {{-- Dropdown Panel --}}
    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Notifikasi</h3>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form action="{{ route('user.notifications.markAllRead') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>

        {{-- Daftar Notifikasi --}}
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
            @forelse(auth()->user()->notifications->take(10) as $notification)
                <div class="px-4 py-3 hover:bg-gray-50 transition-colors {{ $notification->read_at ? 'opacity-60' : '' }}">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                                <i class="fas fa-bell text-primary-600 text-xs"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">{{ $notification->data['message'] ?? 'Notifikasi baru' }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @if(!$notification->read_at)
                            <div class="flex-shrink-0">
                                <span class="w-2 h-2 bg-primary-500 rounded-full"></span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-bell-slash text-gray-400"></i>
                    </div>
                    <p class="text-sm text-gray-500">Belum ada notifikasi</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if(auth()->user()->notifications->count() > 10)
        <div class="px-4 py-2 border-t border-gray-100 text-center">
            <a href="{{ route('user.notifications.index') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                Lihat semua notifikasi
            </a>
        </div>
        @endif
    </div>
</div>

@once
<script>
    function notificationDropdown() {
        return {
            open: false
        }
    }
</script>
@endonce
