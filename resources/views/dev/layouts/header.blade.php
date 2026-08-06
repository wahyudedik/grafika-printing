    <nav class="bg-gray-900 border-b border-gray-700" x-data="{ mobileOpen: false, userDropdown: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14">
                <a class="flex items-center gap-2" href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('logo.png') }}" alt="Grafika Printing" height="28" width="28" class="rounded-md">
                    <span class="text-white font-bold text-lg">Dev Portal</span>
                </a>
                <button class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-700 lg:hidden" type="button" @click="mobileOpen = !mobileOpen"
                    aria-controls="navbarNav" :aria-expanded="mobileOpen" aria-label="Toggle navigation">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x-show="!mobileOpen" d="M4 6h16M4 12h16M4 18h16"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x-show="mobileOpen" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div class="hidden lg:flex lg:items-center lg:gap-1" :class="{ 'flex flex-col absolute top-14 left-0 right-0 bg-gray-800 border-b border-gray-700 p-4 z-50': mobileOpen }" id="navbarNav">
                    <ul class="flex items-center gap-1 lg:mr-auto">
                        <li>
                            <a class="px-3 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors" href="{{ route('admin.dashboard') }}">Dashboard</a>
                        </li>
                        <li>
                            <a class="px-3 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors" href="{{ route('admin.dashboard') }}">My Apps</a>
                        </li>
                        <li>
                            <a class="px-3 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors" href="{{ route('admin.dashboard') }}">Documentation</a>
                        </li>
                    </ul>
                    <ul class="flex items-center gap-1">
                        <li class="relative" @click.away="userDropdown = false">
                            <button class="flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors" type="button"
                                @click="userDropdown = !userDropdown" :aria-expanded="userDropdown">
                                {{ Auth::user()->name }}
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': userDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <ul class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50" x-show="userDropdown" x-transition x-cloak>
                                <li><a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors" href="{{ route('admin.dashboard') }}">Profile</a></li>
                                <li><a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors" href="{{ route('admin.dashboard') }}">Settings</a></li>
                                <li><hr class="border-t border-gray-200 my-1"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
