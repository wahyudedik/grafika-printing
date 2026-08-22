@extends('dev.layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notifikasi</h1>
            <p class="text-sm text-gray-500 mt-1">Semua notifikasi admin</p>
        </div>
        @if(auth()->user()->unreadNotifications()->count() > 0)
            <form action="{{ route('admin.notifications.markAllRead') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center justify-center border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold text-sm py-1 px-3 rounded-lg transition">
                    <i class="fas fa-check-double mr-2"></i>Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>

    {{-- Notifications List --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @forelse($notifications as $notification)
            <div class="px-6 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors {{ $notification->read_at ? 'opacity-60' : '' }}">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 mt-0.5">
                        <div class="w-10 h-10 rounded-full {{ $notification->read_at ? 'bg-gray-100' : 'bg-red-100' }} flex items-center justify-center">
                            <i class="fas fa-bell {{ $notification->read_at ? 'text-gray-400' : 'text-red-600' }} text-sm"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">{{ $notification->data['message'] ?? 'Notifikasi baru' }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @if(!$notification->read_at)
                        <div class="flex-shrink-0">
                            <span class="w-2.5 h-2.5 bg-red-500 rounded-full block"></span>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="px-6 py-16 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-bell-slash text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada notifikasi</h3>
                <p class="text-sm text-gray-500">Notifikasi akan muncul di sini ketika ada aktivitas sistem.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
