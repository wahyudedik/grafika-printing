@extends('layouts.vendor')

@section('title', 'Profile Vendor - ' . $vendor->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <x-ui.breadcrumb :items="[['label' => 'Profile Vendor']]" />

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        {{-- Profile Header --}}
        <div class="px-6 py-5 border-b border-gray-200">
            <div class="flex items-center gap-4">
                @if ($vendor->logo)
                    <img src="{{ asset('storage/' . $vendor->logo) }}" alt="{{ $vendor->name }}" class="w-16 h-16 rounded-full object-cover border-2 border-gray-200">
                @else
                    <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center border-2 border-primary-200">
                        <span class="text-primary-700 font-bold text-xl">{{ substr($vendor->name, 0, 1) }}</span>
                    </div>
                @endif
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $vendor->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $vendor->email }} • {{ $vendor->phone }}</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Vendor Info --}}
                <div class="bg-gray-50 rounded-xl p-5">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Vendor</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-semibold text-gray-700">Alamat:</span>
                            <p class="text-sm text-gray-600 mt-1">{{ $vendor->address }}</p>
                        </div>
                        @if ($vendor->website)
                            <div>
                                <span class="text-sm font-semibold text-gray-700">Website:</span>
                                <p class="mt-1">
                                    <a href="{{ $vendor->website }}" target="_blank" class="text-primary-600 hover:text-primary-700 text-sm flex items-center gap-1">
                                        {{ $vendor->website }} <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>
                                </p>
                            </div>
                        @endif
                        <div>
                            <span class="text-sm font-semibold text-gray-700">Status:</span>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $vendor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $vendor->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-sm font-semibold text-gray-700">Bergabung:</span>
                            <p class="text-sm text-gray-600 mt-1">{{ $vendor->created_at->format('d M Y') }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-semibold text-gray-700">Total Proyek:</span>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                {{ $vendor->completedAuctions()->count() }} selesai
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Rating Summary --}}
                <div class="lg:col-span-2 bg-gray-50 rounded-xl p-5">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Rating & Testimoni</h3>

                    {{-- Rating Overview --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                        <div class="text-center">
                            <div class="text-4xl font-bold text-amber-500">{{ number_format($averageRating, 1) }}</div>
                            <div class="text-sm text-gray-500 mt-1">Rating Rata-rata</div>
                            <div class="mt-2 flex justify-center gap-0.5">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($averageRating))
                                        <i class="fas fa-star text-amber-400 text-sm"></i>
                                    @elseif($i - 0.5 <= $averageRating)
                                        <i class="fas fa-star-half-alt text-amber-400 text-sm"></i>
                                    @else
                                        <i class="far fa-star text-amber-400 text-sm"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-primary-600">{{ $ratingCount }}</div>
                            <div class="text-sm text-gray-500 mt-1">Total Rating</div>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-700 text-center mb-2">Distribusi Rating</div>
                            @foreach ($ratingDistribution as $dist)
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs text-gray-600 w-12">{{ $dist->rating }} ⭐</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-amber-400 h-2 rounded-full" style="width: {{ $ratingCount > 0 ? ($dist->count / $ratingCount) * 100 : 0 }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500 w-6 text-right">{{ $dist->count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Recent Ratings --}}
                    <h4 class="font-semibold text-gray-900 mb-3">Testimoni Terbaru</h4>
                    @if ($vendor->verifiedRatings->count() > 0)
                        @foreach ($vendor->verifiedRatings->take(5) as $rating)
                            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-3">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <strong class="text-gray-900">{{ $rating->user->name }}</strong>
                                        <div class="text-xs text-gray-500">{{ $rating->created_at->format('d M Y') }}</div>
                                    </div>
                                    <div class="flex gap-0.5">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $rating->rating)
                                                <i class="fas fa-star text-amber-400 text-sm"></i>
                                            @else
                                                <i class="far fa-star text-amber-400 text-sm"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                @if ($rating->comment)
                                    <p class="text-sm text-gray-600 mb-0">{{ $rating->comment }}</p>
                                @endif
                                @if ($rating->rating_details)
                                    <div class="mt-2 flex flex-wrap gap-1">
                                        @foreach ($rating->rating_details as $key => $value)
                                            @if ($value)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ ucfirst($key) }}: {{ $value }}/5
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-star text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">Belum ada testimoni untuk vendor ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
