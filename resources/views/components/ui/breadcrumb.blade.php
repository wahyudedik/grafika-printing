@props([
    'items' => [],
])

<nav class="flex items-center gap-2 text-sm text-gray-500 mb-4 flex-wrap">
    <a href="{{ route('vendor.dashboard') }}" class="hover:text-primary-600 transition-colors">
        <i class="fas fa-home"></i> Dashboard
    </a>
    @foreach($items as $item)
        <span class="text-gray-300">/</span>
        @if(isset($item['url']) && $item['url'])
            <a href="{{ $item['url'] }}" class="hover:text-primary-600 transition-colors">{{ $item['label'] }}</a>
        @else
            <span class="text-gray-900 font-medium">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
