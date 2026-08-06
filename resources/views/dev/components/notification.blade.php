{{-- Theme toggle + Notification dropdown (Alpine.js) --}}
<div class="flex items-center gap-2" x-data="notificationDropdown()">
    {{-- Theme Toggle --}}
    <a href="?theme=light" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors" title="Enable light mode">
        <i class="fas fa-sun text-lg"></i>
    </a>

    {{-- Notification Bell --}}
    <div class="relative">
        <button @click="open = !open" @click.away="open = false" class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors" aria-label="Show notifications">
            <i class="fas fa-bell text-lg"></i>
            <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
        </button>

        {{-- Dropdown --}}
        <div x-show="open" x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Notifikasi Terbaru</h3>
            </div>
            <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
                <div class="px-4 py-3 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-red-500 mt-2 flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">Example 1</p>
                            <p class="text-xs text-gray-500 mt-0.5 truncate">Change deprecated html tags to text decoration classes (#29604)</p>
                        </div>
                        <button class="text-gray-400 hover:text-amber-500 transition-colors flex-shrink-0">
                            <i class="far fa-star text-sm"></i>
                        </button>
                    </div>
                </div>
                <div class="px-4 py-3 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-gray-300 mt-2 flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">Example 2</p>
                            <p class="text-xs text-gray-500 mt-0.5 truncate">justify-content:between ⇒ justify-content:space-between (#29734)</p>
                        </div>
                        <button class="text-amber-400 hover:text-amber-500 transition-colors flex-shrink-0">
                            <i class="fas fa-star text-sm"></i>
                        </button>
                    </div>
                </div>
                <div class="px-4 py-3 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-gray-300 mt-2 flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">Example 3</p>
                            <p class="text-xs text-gray-500 mt-0.5 truncate">Update change-version.js (#29736)</p>
                        </div>
                        <button class="text-gray-400 hover:text-amber-500 transition-colors flex-shrink-0">
                            <i class="far fa-star text-sm"></i>
                        </button>
                    </div>
                </div>
                <div class="px-4 py-3 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-green-500 mt-2 flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">Example 4</p>
                            <p class="text-xs text-gray-500 mt-0.5 truncate">Regenerate package-lock.json (#29730)</p>
                        </div>
                        <button class="text-gray-400 hover:text-amber-500 transition-colors flex-shrink-0">
                            <i class="far fa-star text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="px-4 py-2 border-t border-gray-200 text-center">
                <a href="#" class="text-xs font-medium text-primary-600 hover:text-primary-700 transition-colors">Lihat Semua Notifikasi</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function notificationDropdown() {
    return {
        open: false
    }
}
</script>
@endpush
