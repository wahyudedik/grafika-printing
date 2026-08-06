@props([
    'striped' => true,
    'hoverable' => true,
    'compact' => false,
    'responsive' => true,
    'bordered' => false,
])

@php
$wrapperClass = $responsive ? 'overflow-x-auto' : '';
$tableClass = collect([
    'w-full text-sm text-left',
    $striped ? 'divide-y divide-gray-200' : '',
    $bordered ? 'border border-gray-200' : '',
])->filter()->join(' ');

$rowClass = collect([
    'bg-white',
    $hoverable ? 'hover:bg-gray-50' : '',
    $striped ? '[&:nth-child(even)]:bg-gray-50/50' : '',
])->filter()->join(' ');
@endphp

<div {{ $attributes->merge(['class' => $wrapperClass]) }}>
    <table class="{{ $tableClass }}">
        {{ $slot }}
    </table>
</div>
