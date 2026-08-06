@props([
    'title' => '',
    'value' => '',
    'subtitle' => null,
    'icon' => null,
    'color' => 'primary',
    'trend' => null,
    'trendDirection' => 'up',
    'href' => null,
])

@php
    $colors = [
        'primary' => 'bg-blue-50 text-blue-600',
        'success' => 'bg-green-50 text-green-600',
        'danger' => 'bg-red-50 text-red-600',
        'warning' => 'bg-yellow-50 text-yellow-600',
        'info' => 'bg-cyan-50 text-cyan-600',
        'purple' => 'bg-purple-50 text-purple-600',
    ];
    $iconBg = $colors[$color] ?? $colors['primary'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-200 p-6 transition-all duration-200 hover:shadow-md' . ($href ? ' cursor-pointer' : '')]) }}>
    <div class="flex items-start justify-between">
        <div class="flex-1">
            @if($title)
                <p class="text-sm font-medium text-gray-500">{{ $title }}</p>
            @endif
            @if($value)
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $value }}</p>
            @endif
            @if($subtitle)
                <p class="text-sm text-gray-500 mt-1">{{ $subtitle }}</p>
            @endif
            @if($trend)
                <div class="flex items-center mt-2">
                    @if($trendDirection === 'up')
                        <svg class="w-4 h-4 text-green-500 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                        </svg>
                        <span class="text-sm text-green-600 font-medium">{{ $trend }}</span>
                    @else
                        <svg class="w-4 h-4 text-red-500 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 4.5l15 15m0 0V8.25m0 11.25H8.25" />
                        </svg>
                        <span class="text-sm text-red-600 font-medium">{{ $trend }}</span>
                    @endif
                </div>
            @endif
        </div>

        @if($icon)
            <div class="{{ $iconBg }} rounded-lg p-3">
                {!! $icon !!}
            </div>
        @endif
    </div>

    @if($href)
        <a href="{{ $href }}" class="absolute inset-0"></a>
    @endif
</div>
