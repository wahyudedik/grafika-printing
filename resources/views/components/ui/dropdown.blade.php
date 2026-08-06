@props([
    'align' => 'right',
    'width' => '48',
    'trigger' => null,
])

@php
    $widths = [
        '48' => 'w-48',
        '56' => 'w-56',
        '64' => 'w-64',
    ];
    $widthClass = $widths[$width] ?? 'w-48';
    $alignClass = $align === 'left' ? 'left-0' : 'right-0';
@endphp

<div x-data="{ open: false }" class="relative" {{ $attributes }}>
    {{-- Trigger --}}
    <div x-on:click="open = !open">
        {{ $trigger }}
    </div>

    {{-- Dropdown Menu --}}
    <div x-show="open"
         x-on:click.away="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute {{ $alignClass }} mt-2 {{ $widthClass }} bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50"
         x-cloak>
        {{ $slot }}
    </div>
</div>
