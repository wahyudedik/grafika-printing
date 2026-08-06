@props([
    'variant' => 'secondary',
    'dot' => false,
    'removePadding' => false,
])

@php
    $variants = [
        'success' => 'bg-green-100 text-green-800',
        'danger' => 'bg-red-100 text-red-800',
        'warning' => 'bg-yellow-100 text-yellow-800',
        'info' => 'bg-blue-100 text-blue-800',
        'secondary' => 'bg-gray-100 text-gray-800',
        'primary' => 'bg-blue-100 text-blue-800',
        'purple' => 'bg-purple-100 text-purple-800',
        'pink' => 'bg-pink-100 text-pink-800',
        'indigo' => 'bg-indigo-100 text-indigo-800',
    ];

    $dotColors = [
        'success' => 'bg-green-500',
        'danger' => 'bg-red-500',
        'warning' => 'bg-yellow-500',
        'info' => 'bg-blue-500',
        'secondary' => 'bg-gray-500',
        'primary' => 'bg-blue-500',
        'purple' => 'bg-purple-500',
        'pink' => 'bg-pink-500',
        'indigo' => 'bg-indigo-500',
    ];

    $variantClass = $variants[$variant] ?? $variants['secondary'];
    $dotColor = $dotColors[$variant] ?? $dotColors['secondary'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center ' . ($removePadding ? '' : 'px-2.5 py-0.5 ') . 'rounded-full text-xs font-medium ' . $variantClass]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} mr-1.5"></span>
    @endif
    {{ $slot }}
</span>
