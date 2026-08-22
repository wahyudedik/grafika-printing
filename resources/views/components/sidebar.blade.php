@props(['menus' => [], 'brandName' => 'Grafika', 'brandSubtitle' => ''])

<aside
    x-data
    @toggle-sidebar.window="$store.sidebar.collapsed = !$store.sidebar.collapsed"
    @close-mobile-sidebar.window="$store.sidebar.mobileOpen = false"
    class="fixed inset-y-0 left-0 z-40 flex flex-col bg-white border-r border-gray-200 transition-all duration-300 sidebar-responsive"
    :class="[
        $store.sidebar?.collapsed ? 'w-[72px]' : 'w-64',
        $store.sidebar?.mobileOpen ? 'sidebar-is-open' : ''
    ]">

    {{-- Logo & Brand --}}
    <div class="flex items-center h-16 px-4 border-b border-gray-200 shrink-0">
        <a href="{{ route('welcome') }}" class="flex items-center gap-2.5">
            <img src="{{ asset('logo.png') }}" alt="Grafika" class="w-8 h-8 rounded-lg shrink-0">
            <div x-show="!$store.sidebar?.collapsed" x-cloak class="flex flex-col min-w-0">
                <span class="text-sm font-bold text-gray-900 truncate">{{ $brandName }}</span>
                @if($brandSubtitle)
                    <span class="text-xs text-gray-400 truncate">{{ $brandSubtitle }}</span>
                @endif
            </div>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto sidebar-scroll py-4 px-3 space-y-1">
        @foreach($menus as $group)
            {{-- Section Header --}}
            @if(isset($group['label']))
                <p x-show="!$store.sidebar?.collapsed" x-cloak class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-wider text-gray-400">
                    {{ $group['label'] }}
                </p>
                <div x-show="$store.sidebar?.collapsed" x-cloak class="my-3 mx-auto w-8 border-t border-gray-200"></div>
            @endif

            {{-- Menu Items --}}
            @foreach(($group['items'] ?? []) as $item)
                @if(isset($item['children']) && count($item['children']) > 0)
                    {{-- Accordion Sub-Menu --}}
                    @php
                        // Parent accordion: open jika ada child yang aktif, tapi JANGAN beri class active
                        $hasActiveChild = false;
                        foreach ($item['children'] as $child) {
                            if (request()->routeIs($child['route'] ?? '__none__')) {
                                $hasActiveChild = true;
                                break;
                            }
                        }
                    @endphp
                    <div x-data="{ open: {{ $hasActiveChild ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                            {{ $hasActiveChild ? 'text-gray-900 bg-gray-100' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                            :title="$store.sidebar?.collapsed ? '{{ $item['label'] }}' : ''">
                            <span class="shrink-0 w-5 h-5 flex items-center justify-center">{!! $item['icon'] ?? '' !!}</span>
                            <span x-show="!$store.sidebar?.collapsed" x-cloak class="flex-1 text-left truncate">{{ $item['label'] }}</span>
                            <svg x-show="!$store.sidebar?.collapsed" x-cloak class="w-4 h-4 shrink-0 transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-collapse x-cloak
                            :class="$store.sidebar?.collapsed ? 'ml-0' : 'ml-5 mt-1 space-y-0.5 border-l border-gray-200 pl-3'">
                            @foreach($item['children'] as $child)
                                <a href="{{ $child['url'] }}"
                                    class="block px-3 py-2 text-sm rounded-lg transition-colors
                                    {{ request()->routeIs($child['route'] ?? '__none__') ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                                    {{ $child['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    {{-- Single Menu Item --}}
                    <a href="{{ $item['url'] ?? '#' }}"
                        @if(!empty($item['target'])) target="{{ $item['target'] }}" rel="noopener noreferrer" @endif
                        class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                        {{ request()->routeIs($item['route'] ?? '__none__') ? 'sidebar-link-active' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                        :title="$store.sidebar?.collapsed ? '{{ $item['label'] }}' : ''">
                        <span class="shrink-0 w-5 h-5 flex items-center justify-center">{!! $item['icon'] ?? '' !!}</span>
                        <span x-show="!$store.sidebar?.collapsed" x-cloak class="truncate">{{ $item['label'] }}</span>
                        @if(!empty($item['badge']) && $item['badge'] > 0)
                            <span x-show="!$store.sidebar?.collapsed" x-cloak
                                class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-bold text-white bg-red-500 rounded-full shrink-0">
                                {{ $item['badge'] > 9 ? '9+' : $item['badge'] }}
                            </span>
                        @endif
                    </a>
                @endif
            @endforeach
        @endforeach
    </nav>

    {{-- Close Button (mobile) --}}
    <div class="flex items-center justify-center border-t border-gray-200 p-3 shrink-0 lg:hidden">
        <button @click="$store.sidebar.mobileOpen = false"
            class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Collapse Toggle (desktop) --}}
    <div class="hidden lg:flex items-center justify-center border-t border-gray-200 p-3 shrink-0">
        <button @click="$dispatch('toggle-sidebar')"
            class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5 transition-transform duration-300"
                :class="$store.sidebar?.collapsed ? 'rotate-180' : ''"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
        </button>
    </div>
</aside>
