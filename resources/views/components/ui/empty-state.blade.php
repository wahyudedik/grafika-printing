@props([
    'icon' => 'fas fa-inbox',
    'title' => 'Tidak ada data',
    'description' => null,
    'size' => 'md', // sm, md, lg
])

@php
    $iconSizes = [
        'sm' => 'text-3xl',
        'md' => 'text-5xl',
        'lg' => 'text-6xl',
    ];
    $iconSize = $iconSizes[$size] ?? $iconSizes['md'];
@endphp

<div class="flex flex-col items-center justify-center py-12 px-4 text-center">
    <i class="{{ $icon }} {{ $iconSize }} text-gray-300 mb-4"></i>
    <h3 class="text-lg font-medium text-gray-900 mb-1">{{ $title }}</h3>
    @if($description)
        <p class="text-sm text-gray-500 max-w-md">{{ $description }}</p>
    @endif
    @if(isset($actions) && $actions->isNotEmpty())
        <div class="mt-4">
            {{ $actions }}
        </div>
    @endif
</div>
