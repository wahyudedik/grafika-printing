@extends('layouts.vendor')

@section('title', 'Profil Vendor - ' . $vendor->name)

@section('content')
<x-ui.breadcrumb :items="[
    ['label' => 'Dashboard', 'url' => route('vendor.dashboard')],
    ['label' => 'Profil Vendor']
]" />

<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header Profil --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        {{-- Banner --}}
        <div class="h-32 bg-gradient-to-r from-primary-500 to-primary-700 relative">
            @if($vendor->banner_path)
                <img src="{{ asset('storage/' . $vendor->banner_path) }}" alt="Banner" class="w-full h-full object-cover">
            @endif
        </div>

        {{-- Avatar & Info --}}
        <div class="px-6 pb-6">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-12 relative z-10">
                <div class="w-24 h-24 rounded-full border-4 border-white bg-white shadow-lg overflow-hidden flex-shrink-0">
                    @if($vendor->logo_path)
                        <img src="{{ asset('vendors_logo/' . $vendor->logo_path) }}" alt="{{ $vendor->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-primary-100 flex items-center justify-center">
                            <span class="text-2xl font-bold text-primary-600">{{ substr($vendor->name, 0, 2) }}</span>
                        </div>
                    @endif
                </div>
                <div class="flex-1 pb-1">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $vendor->name }}</h1>
                    @if($vendor->address)
                        <p class="text-sm text-gray-500 mt-1"><i class="fas fa-map-marker-alt mr-1"></i>{{ $vendor->address }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-2 pb-1">
                    @if($vendor->is_active)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            <i class="fas fa-check-circle mr-1"></i> Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                            <i class="fas fa-times-circle mr-1"></i> Nonaktif
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik Rating --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
            <div class="text-3xl font-bold text-yellow-500 mb-1">{{ number_format($averageRating, 1) }}</div>
            <div class="flex items-center justify-center gap-1 mb-2">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= floor($averageRating))
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                    @elseif($i - $averageRating < 1 && $i - $averageRating > 0)
                        <i class="fas fa-star-half-alt text-yellow-400 text-sm"></i>
                    @else
                        <i class="far fa-star text-yellow-400 text-sm"></i>
                    @endif
                @endfor
            </div>
            <div class="text-sm text-gray-500">{{ $ratingCount }} ulasan</div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-medium text-gray-500 mb-3">Distribusi Rating</h3>
            @for($i = 5; $i >= 1; $i--)
                @php
                    $count = $ratingDistribution[$i] ?? 0;
                    $percentage = $ratingCount > 0 ? ($count / $ratingCount) * 100 : 0;
                @endphp
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="text-xs text-gray-500 w-3">{{ $i }}</span>
                    <i class="fas fa-star text-yellow-400 text-xs"></i>
                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                    <span class="text-xs text-gray-500 w-6 text-right">{{ $count }}</span>
                </div>
            @endfor
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-medium text-gray-500 mb-3">Informasi</h3>
            <div class="space-y-2 text-sm">
                <div class="flex items-center gap-2">
                    <i class="fas fa-phone text-gray-400 w-4"></i>
                    <span class="text-gray-700">{{ $vendor->phone ?? '-' }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-envelope text-gray-400 w-4"></i>
                    <span class="text-gray-700">{{ $vendor->email ?? '-' }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-calendar text-gray-400 w-4"></i>
                    <span class="text-gray-700">Bergabung {{ $vendor->created_at->format('M Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Ulasan --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Ulasan Pelanggan</h2>

        @if($vendor->verifiedRatings && $vendor->verifiedRatings->count() > 0)
            <div class="space-y-4">
                @foreach($vendor->verifiedRatings as $rating)
                    <div class="p-4 border border-gray-100 rounded-lg">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                                    <span class="text-xs font-medium text-primary-600">{{ substr($rating->user->name ?? 'U', 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 text-sm">{{ $rating->user->name ?? 'Anonymous' }}</div>
                                    <div class="text-xs text-gray-500">{{ $rating->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $rating->rating)
                                        <i class="fas fa-star text-yellow-400 text-xs"></i>
                                    @else
                                        <i class="far fa-star text-yellow-400 text-xs"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        @if($rating->comment)
                            <p class="text-sm text-gray-600 mt-2">{{ $rating->comment }}</p>
                        @endif
                        @if($rating->rating_details)
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach($rating->rating_details as $key => $value)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">
                                        {{ ucfirst($key) }}: {{ $value }}/5
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <x-ui.empty-state
                icon="star"
                title="Belum ada ulasan"
                description="Ulasan dari pelanggan akan muncul di sini."
            />
        @endif
    </div>
</div>
@endsection
