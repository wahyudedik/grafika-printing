<div class="relative" x-data="{ showNotifications: false }" @click.away="showNotifications = false">
    <button @click="showNotifications = !showNotifications" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors" aria-label="Show notifications">
        <i class="fas fa-bell text-lg"></i>
        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
    </button>

    <div x-show="showNotifications" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50 overflow-hidden">

        <div class="px-4 py-3 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Last updates</h3>
        </div>

        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
            <div class="px-4 py-3 hover:bg-gray-50 transition-colors">
                <div class="flex items-start gap-3">
                    <span class="mt-1 w-2 h-2 bg-red-500 rounded-full flex-shrink-0 animate-pulse"></span>
                    <div class="flex-1 min-w-0">
                        <a href="#" class="text-sm font-medium text-gray-900 hover:text-primary-600 block truncate">Example 1</a>
                        <p class="text-xs text-gray-500 mt-0.5 truncate">Change deprecated html tags to text decoration classes (#29604)</p>
                    </div>
                    <button class="p-1 text-gray-400 hover:text-yellow-500 rounded transition-colors flex-shrink-0" title="Star">
                        <i class="fas fa-star text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="px-4 py-3 hover:bg-gray-50 transition-colors">
                <div class="flex items-start gap-3">
                    <span class="mt-1 w-2 h-2 bg-gray-400 rounded-full flex-shrink-0"></span>
                    <div class="flex-1 min-w-0">
                        <a href="#" class="text-sm font-medium text-gray-900 hover:text-primary-600 block truncate">Example 2</a>
                        <p class="text-xs text-gray-500 mt-0.5 truncate">justify-content:between ⇒ justify-content:space-between (#29734)</p>
                    </div>
                    <button class="p-1 text-yellow-500 hover:text-yellow-600 rounded transition-colors flex-shrink-0" title="Starred">
                        <i class="fas fa-star text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="px-4 py-3 hover:bg-gray-50 transition-colors">
                <div class="flex items-start gap-3">
                    <span class="mt-1 w-2 h-2 bg-gray-400 rounded-full flex-shrink-0"></span>
                    <div class="flex-1 min-w-0">
                        <a href="#" class="text-sm font-medium text-gray-900 hover:text-primary-600 block truncate">Example 3</a>
                        <p class="text-xs text-gray-500 mt-0.5 truncate">Update change-version.js (#29736)</p>
                    </div>
                    <button class="p-1 text-gray-400 hover:text-yellow-500 rounded transition-colors flex-shrink-0" title="Star">
                        <i class="fas fa-star text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="px-4 py-3 hover:bg-gray-50 transition-colors">
                <div class="flex items-start gap-3">
                    <span class="mt-1 w-2 h-2 bg-green-500 rounded-full flex-shrink-0 animate-pulse"></span>
                    <div class="flex-1 min-w-0">
                        <a href="#" class="text-sm font-medium text-gray-900 hover:text-primary-600 block truncate">Example 4</a>
                        <p class="text-xs text-gray-500 mt-0.5 truncate">Regenerate package-lock.json (#29730)</p>
                    </div>
                    <button class="p-1 text-gray-400 hover:text-yellow-500 rounded transition-colors flex-shrink-0" title="Star">
                        <i class="fas fa-star text-sm"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="px-4 py-2 border-t border-gray-100 bg-gray-50">
            <a href="#" class="text-xs font-medium text-primary-600 hover:text-primary-800">View all notifications</a>
        </div>
    </div>
</div>
