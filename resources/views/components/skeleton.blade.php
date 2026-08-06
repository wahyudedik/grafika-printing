{{--
    Loading Skeleton Component

    Penggunaan:
    <x-skeleton type="card" />
    <x-skeleton type="stats" :count="4" />
    <x-skeleton type="list" :count="3" />
    <x-skeleton type="table" :rows="5" />
    <x-skeleton type="text" width="1/2" />
--}}

@props([
    'type' => 'text',
    'count' => 1,
    'rows' => 3,
    'width' => 'full', // full, 1/2, 1/3, 1/4, 2/3, 3/4
])

@php
    $widthClass = match($width) {
        '1/2' => 'w-1/2',
        '1/3' => 'w-1/3',
        '1/4' => 'w-1/4',
        '2/3' => 'w-2/3',
        '3/4' => 'w-3/4',
        default => 'w-full',
    };
@endphp

@if($type === 'text')
    @for($i = 0; $i < $count; $i++)
        <div class="animate-pulse">
            <div class="h-4 {{ $widthClass }} bg-gray-200 rounded {{ $i === $count - 1 ? '' : 'mb-2' }}"></div>
        </div>
    @endfor

@elseif($type === 'card')
    <div class="bg-white rounded-xl border border-gray-200 p-5 animate-pulse">
        <div class="h-4 w-1/3 bg-gray-200 rounded mb-4"></div>
        <div class="h-8 w-1/4 bg-gray-200 rounded mb-2"></div>
        <div class="h-3 w-1/2 bg-gray-100 rounded"></div>
    </div>

@elseif($type === 'stats')
    <div class="grid grid-cols-2 lg:grid-cols-{{ $count }} gap-4">
        @for($i = 0; $i < $count; $i++)
            <div class="bg-white rounded-xl border border-gray-200 p-5 animate-pulse">
                <div class="h-3 w-1/2 bg-gray-200 rounded mb-3"></div>
                <div class="h-8 w-1/3 bg-gray-200 rounded mb-2"></div>
                <div class="h-3 w-1/4 bg-gray-100 rounded"></div>
            </div>
        @endfor
    </div>

@elseif($type === 'list')
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden animate-pulse">
        <div class="px-4 py-3 border-b border-gray-100">
            <div class="h-4 w-1/4 bg-gray-200 rounded"></div>
        </div>
        @for($i = 0; $i < $count; $i++)
            <div class="flex items-center justify-between px-4 py-3 {{ $i < $count - 1 ? 'border-b border-gray-50' : '' }}">
                <div class="flex-1">
                    <div class="h-4 w-2/3 bg-gray-200 rounded mb-1.5"></div>
                    <div class="h-3 w-1/4 bg-gray-100 rounded"></div>
                </div>
                <div class="h-6 w-16 bg-gray-200 rounded-full"></div>
            </div>
        @endfor
    </div>

@elseif($type === 'table')
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden animate-pulse">
        <div class="px-4 py-3 border-b border-gray-100">
            <div class="h-4 w-1/4 bg-gray-200 rounded"></div>
        </div>
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    @for($i = 0; $i < 4; $i++)
                        <th class="px-4 py-3"><div class="h-3 bg-gray-200 rounded w-3/4"></div></th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @for($r = 0; $r < $rows; $r++)
                    <tr class="{{ $r < $rows - 1 ? 'border-b border-gray-50' : '' }}">
                        @for($c = 0; $c < 4; $c++)
                            <td class="px-4 py-3"><div class="h-3 bg-gray-100 rounded {{ $c === 0 ? 'w-3/4' : 'w-1/2' }}"></div></td>
                        @endfor
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

@elseif($type === 'grid')
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @for($i = 0; $i < $count; $i++)
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden animate-pulse">
                <div class="aspect-square bg-gray-200"></div>
                <div class="p-3">
                    <div class="h-4 w-3/4 bg-gray-200 rounded mb-2"></div>
                    <div class="h-3 w-1/2 bg-gray-100 rounded"></div>
                </div>
            </div>
        @endfor
    </div>
@endif
