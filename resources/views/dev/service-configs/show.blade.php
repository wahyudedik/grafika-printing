@extends('dev.layouts.app')

@section('title', $serviceInfo['name'] . ' - Service Configuration')

@section('content')
<div x-data="serviceConfigShow()" class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <x.ui.button href="{{ route('admin.service-configs.index') }}" variant="ghost" size="icon">
                <i class="fas fa-arrow-left"></i>
            </x.ui.button>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $serviceInfo['name'] }} Configuration</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $serviceInfo['description'] }}</p>
            </div>
        </div>
        <x.ui.button type="button" variant="outline-success" @click="testConnection()" :disabled="testing">
            <i x-show="!testing" class="fas fa-plug mr-1"></i>
            <svg x-show="testing" class="animate-spin h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span x-text="testing ? 'Testing...' : 'Test Connection'"></span>
        </x.ui.button>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <x-ui.alert type="success" :dismissible="true">{{ session('success') }}</x-ui.alert>
    @endif

    @if(session('error'))
        <x-ui.alert type="danger" :dismissible="true">{{ session('error') }}</x-ui.alert>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Configuration Forms --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-sliders-h text-primary-600 mr-2"></i>Konfigurasi
                    </h2>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($configs as $config)
                        <div x-data="{ showPassword: false }" class="p-5">
                            <form action="{{ route('admin.service-configs.update', $config) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                        {{ $config->label }}
                                        @if($config->is_encrypted)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 ml-1">🔒 Encrypted</span>
                                        @endif
                                    </label>
                                    @if($config->description)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $config->description }}</p>
                                    @endif
                                </div>

                                <div class="flex items-end gap-3">
                                    <div class="flex-1 relative">
                                        <input :type="showPassword ? 'text' : '{{ $config->is_encrypted ? 'password' : 'text' }}'"
                                               name="value"
                                               class="w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm font-mono focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                               value="{{ $config->getDecryptedValue() }}"
                                               placeholder="{{ $config->label }}">
                                        @if($config->is_encrypted)
                                            <button type="button" @click="showPassword = !showPassword" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-sm"></i>
                                            </button>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="is_active" value="1" {{ $config->is_active ? 'checked' : '' }} class="sr-only peer">
                                            <div class="w-9 h-5 bg-gray-200 dark:bg-gray-600 peer-focus:ring-2 peer-focus:ring-primary-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary-600"></div>
                                        </label>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Active</span>
                                    </div>
                                    <input type="hidden" name="label" value="{{ $config->label }}">
                                    <input type="hidden" name="description" value="{{ $config->description }}">
                                    <x.ui.button type="submit" variant="primary">
                                        <i class="fas fa-check text-xs mr-1"></i>
                                        Simpan
                                    </x.ui.button>
                                </div>
                            </form>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada konfigurasi untuk {{ $serviceInfo['name'] }}.</p>
                            <x.ui.button href="{{ route('admin.service-configs.seed-defaults') }}" variant="primary" onclick="event.preventDefault(); confirmAction({ title: 'Import Config Default', text: 'Import config default dari .env?', icon: 'question', confirmText: 'Ya, Import', onConfirm: () => window.location.href = '{{ route('admin.service-configs.seed-defaults') }}' })">
                                <i class="fas fa-file-import mr-1"></i> Import dari .env
                            </x.ui.button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Info Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-info-circle text-sky-600 mr-2"></i>Informasi
                    </h3>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Service</span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $serviceInfo['name'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Total Config</span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $configs->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Active</span>
                        <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ $configs->where('is_active', true)->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Inactive</span>
                        <span class="text-sm font-bold text-red-600 dark:text-red-400">{{ $configs->where('is_active', false)->count() }}</span>
                    </div>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-red-200 dark:border-red-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/10">
                    <h3 class="text-base font-semibold text-red-700 dark:text-red-300">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Danger Zone
                    </h3>
                </div>
                <div class="p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Nonaktifkan semua config untuk service ini.</p>
                    <div class="space-y-2">
                        @foreach($configs as $config)
                            @if($config->is_active)
                                <form action="{{ route('admin.service-configs.toggle', $config) }}" method="POST" class="inline">
                                    @csrf
                                    <x.ui.button type="submit" variant="outline-warning" size="sm" class="w-full text-left">
                                        Nonaktifkan {{ $config->label }}
                                    </x.ui.button>
                                </form>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
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
                    <x.ui.button type="button" variant="outline" @click="showTestModal = false" class="w-full">Tutup</x.ui.button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function serviceConfigShow() {
    return {
        testing: false,
        showTestModal: false,
        testSuccess: false,
        testMessage: '',
        async testConnection() {
            this.testing = true;
            try {
                const response = await fetch('{{ route("admin.service-configs.test-connection") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ service_name: '{{ $service }}' })
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
                this.testing = false;
            }
        }
    };
}
</script>
@endpush
