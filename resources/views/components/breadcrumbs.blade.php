{{--
    Breadcrumbs Component

    Penggunaan:
    1. Auto-generated dari route name (default):
       Layout akan otomatis generate breadcrumbs dari route name.

    2. Custom breadcrumbs via section:
       @push('breadcrumbs')
           <x-breadcrumbs :items="[
               ['label' => 'Beranda', 'url' => route('user.dashboard')],
               ['label' => 'Lelang', 'url' => route('user.auctions.index')],
               ['label' => 'Detail'],
           ]" />
       @endpush

    3. Simple mode (tanpa links):
       <x-breadcrumbs :items="['Beranda', 'Lelang', 'Detail Lelang']" />
--}}

@props(['items' => []])

@if(count($items) > 0)
<nav class="flex items-center text-sm text-gray-500 mb-4 print:hidden" aria-label="Breadcrumb">
    <ol class="flex items-center flex-wrap gap-1">
        @foreach($items as $index => $item)
            @if($index > 0)
                <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            @endif

            @if(is_array($item))
                @if($item['url'] ?? false)
                    @if($loop->last)
                        <span class="font-medium text-gray-900">{{ $item['label'] }}</span>
                    @else
                        <a href="{{ $item['url'] }}" class="hover:text-primary-600 transition-colors">{{ $item['label'] }}</a>
                    @endif
                @else
                    <span class="font-medium text-gray-900">{{ $item['label'] }}</span>
                @endif
            @else
                @if($loop->last)
                    <span class="font-medium text-gray-900">{{ $item }}</span>
                @else
                    <span>{{ $item }}</span>
                @endif
            @endif
        @endforeach
    </ol>
</nav>
@endif
