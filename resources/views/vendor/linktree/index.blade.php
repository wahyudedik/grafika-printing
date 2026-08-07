@extends('layouts.vendor')

@section('title', 'Linktree Management')

@section('content')
<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-link mr-2 text-primary-600"></i>Linktree Management
            </h1>
            <p class="mt-1 text-sm text-gray-500">Kelola halaman linktree toko Anda</p>
        </div>
        <x.ui.button href="{{ route('vendor.linktree.create') }}">
            <i class="fas fa-plus mr-2"></i>Buat Linktree
        </x.ui.button>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <span class="text-sm text-emerald-800">{{ session('success') }}</span>
        </div>
        <button @click="show = false" class="text-emerald-600 hover:text-emerald-800"><i class="fas fa-times"></i></button>
    </div>
    @endif

    @if($linktrees->isEmpty())
        {{-- Empty State --}}
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                <i class="fas fa-link text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Linktree</h3>
            <p class="text-gray-500 mb-6">Buat linktree pertama Anda untuk berbagi tautan penting toko Anda.</p>
            <x.ui.button href="{{ route('vendor.linktree.create') }}">
                <i class="fas fa-plus mr-2"></i>Buat Linktree Sekarang
            </x.ui.button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($linktrees as $linktree)
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden {{ $linktree->is_active ? 'ring-2 ring-emerald-200' : '' }}">
                {{-- Status Bar --}}
                <div class="h-1.5 {{ $linktree->is_active ? 'bg-emerald-500' : 'bg-gray-200' }}"></div>

                <div class="p-5">
                    {{-- Avatar & Title --}}
                    <div class="flex items-center gap-3 mb-4">
                        @if($linktree->avatar)
                            <img src="{{ asset('linktree/avatars/' . $linktree->avatar) }}" alt="Avatar" class="w-12 h-12 rounded-full object-cover">
                        @else
                            <div class="w-12 h-12 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-lg font-bold">
                                {{ strtoupper(substr($linktree->title, 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <h3 class="font-semibold text-gray-900 truncate">{{ $linktree->title }}</h3>
                            <span class="text-sm text-gray-500">/l/{{ $linktree->custom_url }}</span>
                        </div>
                    </div>

                    {{-- Badges --}}
                    <div class="flex items-center gap-2 mb-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $linktree->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ $linktree->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ ucfirst($linktree->template) }}
                        </span>
                    </div>

                    {{-- Bio --}}
                    @if($linktree->bio)
                        <p class="text-sm text-gray-500 truncate mb-3">{{ $linktree->bio }}</p>
                    @endif

                    {{-- Stats --}}
                    <div class="grid grid-cols-4 gap-2 text-center mb-4 py-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-bold text-gray-900">{{ $linktree->active_links_count ?? 0 }}</div>
                            <div class="text-xs text-gray-500">Links</div>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">{{ $linktree->active_socials_count ?? 0 }}</div>
                            <div class="text-xs text-gray-500">Social</div>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">{{ number_format($linktree->views_count) }}</div>
                            <div class="text-xs text-gray-500">Views</div>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">{{ number_format($linktree->clicks_count) }}</div>
                            <div class="text-xs text-gray-500">Clicks</div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 flex-wrap">
                        <x.ui.button href="{{ route('vendor.linktree.edit', $linktree) }}" variant="outline-primary" size="xs">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </x.ui.button>
                        <form action="{{ route('vendor.linktree.toggle-active', $linktree) }}" method="POST" class="inline">
                            @csrf
                            <x.ui.button type="submit" variant="{{ $linktree->is_active ? 'warning' : 'success' }}" size="xs">
                                {{ $linktree->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </x.ui.button>
                        </form>
                        @if($linktree->is_active)
                            <x.ui.button href="{{ route('linktree.public', $linktree->custom_url) }}" variant="info" size="xs">
                                <i class="fas fa-external-link-alt mr-1"></i>Lihat
                            </x.ui.button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
