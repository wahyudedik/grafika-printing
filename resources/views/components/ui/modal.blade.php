@props([
    'id' => null,
    'title' => '',
    'size' => 'md',
    'closeable' => true,
])

@php
    $sizes = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
        'full' => 'max-w-6xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div x-data="{ open: false }" x-on:open-modal.window="$event.detail === '{{ $id }}' ? open = true : null" x-on:close-modal.window="$event.detail === '{{ $id }}' ? open = false : null" {{ $attributes }}>
    {{-- Slot for trigger button --}}
    {{ $trigger ?? '' }}

    {{-- Modal --}}
    <template x-if="open">
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true" role="dialog">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/50 transition-opacity" x-on:click="{{ $closeable ? 'open = false' : '' }}" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

            {{-- Modal Content --}}
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-2xl {{ $sizeClass }} w-full max-h-[90vh] overflow-y-auto transform transition-all"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">

                    {{-- Header --}}
                    @if($title || isset($header))
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                            <div>
                                @if($title)
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                                @endif
                                {{ $header ?? '' }}
                            </div>
                            @if($closeable)
                                <button x-on:click="open = false" class="p-1 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors" type="button">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    @endif

                    {{-- Body --}}
                    <div class="px-6 py-4">
                        {{ $slot }}
                    </div>

                    {{-- Footer --}}
                    @if(isset($footer) && $footer->isNotEmpty())
                        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200">
                            {{ $footer }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </template>
</div>
