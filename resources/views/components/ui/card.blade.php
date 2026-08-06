@props([
    'title' => null,
    'subtitle' => null,
    'headerActions' => false,
    'padding' => true,
    'bordered' => true,
    'hover' => false,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden' . ($hover ? ' transition-all duration-200 hover:shadow-md hover:-translate-y-0.5' : '')]) }}>
    @if($title || $headerActions || $subtitle || $header)
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                @if($title)
                    <h3 class="text-base font-semibold text-gray-900">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="text-sm text-gray-500 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            @if($headerActions)
                <div class="flex items-center gap-2">
                    {{ $headerActions }}
                </div>
            @endif
        </div>
    @endif

    @if($slot->isNotEmpty())
        <div {{ $attributes->only(['class']) }}>
            {{ $slot }}
        </div>
    @endif

    @if(isset($footer) && $footer->isNotEmpty())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $footer }}
        </div>
    @endif
</div>
