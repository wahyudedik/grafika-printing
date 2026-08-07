@extends('dev.layouts.app')

@section('title', 'CMS Management')

@section('content')
    <div x-data="{ showAddModal: false, showImportModal: false, showToolsDropdown: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-cogs text-blue-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">CMS Management</h2>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <x-ui.button variant="primary" @click="showAddModal = true">
                        <i class="fas fa-plus mr-2"></i>
                        Add New Setting
                    </x-ui.button>
                    <x-ui.button variant="info" :href="route('admin.cms.preview')">
                        <i class="fas fa-eye mr-2"></i>
                        Preview Landing
                    </x-ui.button>
                    <x-ui.button variant="success" :href="route('admin.cms.statistics')">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Statistics
                    </x-ui.button>
                    <div class="relative" @click.away="showToolsDropdown = false">
                        <x-ui.button variant="outline" @click="showToolsDropdown = !showToolsDropdown">
                            <i class="fas fa-cog mr-2"></i>
                            Tools
                            <i class="fas fa-chevron-down text-xs"></i>
                        </x-ui.button>
                        <div x-show="showToolsDropdown" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-10">
                            <a class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50" href="{{ route('admin.cms.export') }}">
                                <i class="fas fa-download text-xs text-gray-400"></i>Export Settings
                            </a>
                            <a class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50" href="#" @click="showToolsDropdown = false; showImportModal = true">
                                <i class="fas fa-upload text-xs text-gray-400"></i>Import Settings
                            </a>
                            <hr class="my-1 border-gray-100">
                            <a class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50" href="#" @click="showToolsDropdown = false; resetSettings()">
                                <i class="fas fa-undo text-xs"></i>Reset to Default
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Category Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($categories as $categoryKey => $categoryName)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h5 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-{{ $categoryKey === 'general' ? 'cog' : ($categoryKey === 'hero' ? 'image' : ($categoryKey === 'footer' ? 'footer' : ($categoryKey === 'contact' ? 'phone' : 'share'))) }} text-gray-400"></i>
                                {{ $categoryName }}
                            </h5>
                        </div>
                        <div class="p-6">
                            @if (isset($settings[$categoryKey]) && $settings[$categoryKey]->count() > 0)
                                <p class="text-sm text-gray-500 mb-4">{{ $settings[$categoryKey]->count() }} settings configured</p>
                                <div class="flex items-center gap-2">
                                    <x-ui.button variant="outline-primary" :href="route('admin.cms.show', $categoryKey)" size="sm">
                                        <i class="fas fa-edit text-xs mr-1"></i>Edit
                                    </x-ui.button>
                                    <x-ui.button variant="outline-info" :href="route('admin.cms.show', $categoryKey)" size="sm">
                                        <i class="fas fa-eye text-xs mr-1"></i>View
                                    </x-ui.button>
                                </div>
                            @else
                                <p class="text-sm text-gray-500 mb-4">No settings configured</p>
                                <x-ui.button variant="primary" :href="route('admin.cms.show', $categoryKey)" size="sm">
                                    <i class="fas fa-plus text-xs mr-1"></i>Configure
                                </x-ui.button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Add Setting Modal --}}
        <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showAddModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500/75" @click="showAddModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="showAddModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-bottom transition-all transform bg-white shadow-xl rounded-xl sm:align-middle" @click.outside="showAddModal = false">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Add New Setting</h3>
                        <button type="button" @click="showAddModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <form action="{{ route('admin.cms.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Key <span class="text-red-500">*</span></label>
                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" id="key" name="key" required>
                                    <p class="mt-1 text-xs text-gray-500">Unique identifier for this setting</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $categoryKey => $categoryName)
                                            <option value="{{ $categoryKey }}">{{ $categoryName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" id="type" name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="text">Text</option>
                                        <option value="textarea">Textarea</option>
                                        <option value="url">URL</option>
                                        <option value="email">Email</option>
                                        <option value="phone">Phone</option>
                                        <option value="image">Image</option>
                                        <option value="social">Social Media</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                                    <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" id="sort_order" name="sort_order" value="0">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Label <span class="text-red-500">*</span></label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" id="label" name="label" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" id="value" name="value">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" id="description" name="description" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                            <x-ui.button variant="outline" @click="showAddModal = false">Cancel</x-ui.button>
                            <x-ui.button variant="primary" type="submit">Create Setting</x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Import Settings Modal --}}
        <div x-show="showImportModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showImportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500/75" @click="showImportModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="showImportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-bottom transition-all transform bg-white shadow-xl rounded-xl sm:align-middle" @click.outside="showImportModal = false">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Import CMS Settings</h3>
                        <button type="button" @click="showImportModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <form action="{{ route('admin.cms.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Select JSON File</label>
                                <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" id="file" name="file" accept=".json" required>
                                <p class="mt-1 text-xs text-gray-500">Upload a JSON file exported from CMS settings</p>
                            </div>
                            <div class="flex items-start gap-3 p-3 rounded-lg bg-amber-50 border border-amber-200">
                                <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                                <p class="text-sm text-amber-800"><strong>Warning:</strong> This will overwrite existing settings with the same keys.</p>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                            <x-ui.button variant="outline" @click="showImportModal = false">Cancel</x-ui.button>
                            <x-ui.button variant="primary" type="submit">Import Settings</x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('label').addEventListener('input', function() {
            const label = this.value;
            const key = label.toLowerCase().replace(/[^a-z0-9\s]/g, '').replace(/\s+/g, '_');
            document.getElementById('key').value = key;
        });

        function resetSettings() {
            if (confirm('Are you sure you want to reset all CMS settings to default? This action cannot be undone.')) {
                fetch('{{ route('admin.cms.reset') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while resetting settings');
                });
            }
        }

        function previewSetting(key, value) {
            const previewElements = document.querySelectorAll(`[data-cms-key="${key}"]`);
            previewElements.forEach(element => {
                if (element.tagName === 'IMG') {
                    element.src = value;
                } else {
                    element.textContent = value;
                }
            });
        }

        let saveTimeout;
        function autoSave() {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                const form = document.getElementById('cmsForm');
                if (form) {
                    form.submit();
                }
            }, 2000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('input', autoSave);
            });
        });
    </script>
@endsection
