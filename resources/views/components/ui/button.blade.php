@php
    // Explicitly extract all props from attributes bag to avoid conflicts
    $href = $attributes->pull('href', $href ?? null);
    $variant = $attributes->pull('variant', $variant ?? 'primary');
    $size = $attributes->pull('size', $size ?? 'md');
    $icon = $attributes->pull('icon', $icon ?? null);
    $type = $attributes->pull('type', $type ?? 'button');
    $disabled = $attributes->pull('disabled', $disabled ?? false);
    $loading = $attributes->pull('loading', $loading ?? false);

    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    $variants = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
        'secondary' => 'bg-gray-200 text-gray-800 hover:bg-gray-300 focus:ring-gray-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        'success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
        'warning' => 'bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-yellow-500',
        'info' => 'bg-cyan-600 text-white hover:bg-cyan-700 focus:ring-cyan-500',
        'ghost' => 'text-gray-600 hover:bg-gray-100 focus:ring-gray-500',
        'outline' => 'border border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-gray-500',
        'outline-primary' => 'border border-blue-300 text-blue-700 hover:bg-blue-50 focus:ring-blue-500',
        'outline-danger' => 'border border-red-300 text-red-700 hover:bg-red-50 focus:ring-red-500',
        'outline-success' => 'border border-green-300 text-green-700 hover:bg-green-50 focus:ring-green-500',
        'outline-warning' => 'border border-yellow-300 text-yellow-700 hover:bg-yellow-50 focus:ring-yellow-500',
        'outline-info' => 'border border-cyan-300 text-cyan-700 hover:bg-cyan-50 focus:ring-cyan-500',
    ];

    $sizes = [
        'xs' => 'px-2.5 py-1 text-xs',
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
        'icon' => 'p-2',
        'icon-sm' => 'p-1.5',
    ];

    $variantClass = $variants[$variant] ?? $variants['primary'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $finalClasses = trim($baseClasses . ' ' . $variantClass . ' ' . $sizeClass);
@endphp

@if($href)
    <a href="{{ $href }}" {!! $attributes->merge(['class' => $finalClasses]) !!}>
        @if($loading)
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @elseif($icon && !$slot->isEmpty())
            <x-dynamic-component :component="$icon" class="w-4 h-4 mr-2" />
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {!! $attributes->merge(['class' => $finalClasses]) !!} @if($disabled) disabled @endif>
        @if($loading)
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @endif
        {{ $slot }}
    </button>
@endif
