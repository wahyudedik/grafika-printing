@extends('dev.layouts.app')

@section('title', 'Service Configurations')

@section('content')
<div x-data="serviceConfig()" class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Service Configurations</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pengaturan API Pihak Ketiga</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.service-configs.seed-defaults') }}" onclick="return confirm('Import config default dari .env? Config yang sudah ada tidak akan ditimpa.')" class="inline-flex items-center gap-2 px-4 py-2 border border-primary-300 dark:border-primary-600 text-primary-700 dark:text-primary-300 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors text-sm font-medium">
                <i class="fas fa-file-import"></i>
                <span>Import dari .env</span>
            </a>
            <a href="{{ route('admin.service-configs.clear-cache') }}" onclick="return confirm('Bersihkan semua cache config?')" class="inline-flex items-center gap-2 px-4 py-2 border border-amber-300 dark:border-amber-600 text-amber-700 dark:text-amber-300 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors text-sm font-medium">
                <i class="fas fa-broom"></i>
                <span>Clear Cache</span>
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <div class="flex-1 text-sm text-emerald-800 dark:text-emerald-200">{{ session('success') }}</div>
                <button @click="show = false" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
                </div>
                <div class="flex-1 text-sm text-red-800 dark:text-red-200">{{ session('error') }}</div>
                <button @click="show = false" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div x-data="{ show: true }" x-show="show" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-800 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-info-circle text-sky-600 dark:text-sky-400"></i>
                </div>
                <div class="flex-1 text-sm text-sky-800 dark:text-sky-200">{{ session('info') }}</div>
                <button @click="show = false" class="text-sky-600 dark:text-sky-400 hover:text-sky-800 dark:hover:text-sky-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">Total Config</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $statistics['total'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">Active</div>
            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $statistics['active'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">Inactive</div>
            <div class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $statistics['inactive'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">Encrypted</div>
            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $statistics['encrypted'] }}</div>
        </div>
    </div>

    {{-- Service Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($services as $serviceKey => $serviceInfo)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg {{ $serviceKey === 'xendit' ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-sky-100 dark:bg-sky-900/30' }} text-lg">
                            {{ $serviceKey === 'xendit' ? '💳' : '🚚' }}
                        </span>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $serviceInfo['name'] }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $serviceInfo['description'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="testConnection('{{ $serviceKey }}')" :disabled="testing === '{{ $serviceKey }}'" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-emerald-300 dark:border-emerald-600 text-emerald-700 dark:text-emerald-300 rounded-lg hover:bg-emerald-50 dark:hover:bg-emerald-900/20 text-xs font-medium transition-colors disabled:opacity-50">
                            <i x-show="testing !== '{{ $serviceKey }}'" class="fas fa-plug"></i>
                            <svg x-show="testing === '{{ $serviceKey }}'" class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Test
                        </button>
                        <a href="{{ route('admin.service-configs.show', $serviceKey) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-xs font-medium transition-colors">
                            Kelola
                            <i class="fas fa-chevron-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
                <div class="p-5">
                    @if(isset($configs[$serviceKey]) && $configs[$serviceKey]->count() > 0)
                        <div class="space-y-2">
                            @foreach($configs[$serviceKey] as $config)
                                <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $config->label }}</span>
                                    <div class="flex items-center gap-3">
                                        <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded text-gray-800 dark:text-gray-200">{{ $config->getMaskedValue() }}</code>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                                            {{ $config->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                            {{ $config->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada konfigurasi.</p>
                            <a href="{{ route('admin.service-configs.seed-defaults') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline mt-1 inline-block">Import dari .env</a>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-cog text-gray-400 text-2xl"></i>
                </div>
                <p class="text-lg font-medium text-gray-900 dark:text-white">Belum ada service</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tambahkan service configuration baru.</p>
            </div>
        @endforelse
    </div>

    {{-- Test Connection Result Modal (Alpine.js) --}}
    <div x-show="showTestModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showTestModal = false"></div>
            <div x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl transform transition-all sm:my-8 sm:max-w-sm sm:w-full">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center" :class="testSuccess ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-red-100 dark:bg-red-900/30'">
                        <span class="text-3xl" x-text="testSuccess ? '✅' : '❌'"></span>
                    </div>
                    <h3 class="text-lg font-semibold mb-1" :class="testSuccess ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'" x-text="testSuccess ? 'Berhasil!' : 'Gagal'"></h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="testMessage"></p>
                </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-750 rounded-b-xl">
                    <button @click="showTestModal = false" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-sm font-medium transition-colors">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function serviceConfig() {
    return {
        testing: null,
        showTestModal: false,
        testSuccess: false,
        testMessage: '',
        async testConnection(service) {
            this.testing = service;
            try {
                const response = await fetch('{{ route("admin.service-configs.test-connection") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ service_name: service })
                });
                const data = await response.json();
                this.testSuccess = data.success;
                this.testMessage = data.message;
                this.showTestModal = true;
            } catch (error) {
                this.testSuccess = false;
                this.testMessage = 'Error: ' + error.message;
                this.showTestModal = true;
            } finally {
                this.testing = null;
            }
        }
    };
}
</script>
@endpush
