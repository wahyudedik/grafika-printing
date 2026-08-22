@extends('dev.layouts.app')

@section('title', 'CMS Settings - ' . $categoryName)

@section('content')
    <div x-data="{ showAddModal: false }">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-{{ $category === 'general' ? 'cog' : ($category === 'hero' ? 'image' : ($category === 'footer' ? 'footer' : ($category === 'contact' ? 'phone' : 'share'))) }} text-gray-400"></i>
                {{ $categoryName }} Settings
            </h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.cms.index') }}" class="inline-flex items-center justify-center border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold py-2 px-4 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back to CMS
                </a>
                <button @click="showAddModal = true" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                    <i class="fas fa-plus mr-2"></i>Add Setting
                </button>
            </div>
        </div>

        @if ($settings->count() > 0)
            <form action="{{ route('admin.cms.update') }}" method="POST" id="cmsForm">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($settings as $setting)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                                <h6 class="text-sm font-semibold text-gray-900">{{ $setting->label }}</h6>
                                <div class="flex gap-1">
                                    <button type="button"
                                        class="p-1.5 rounded-lg text-xs {{ $setting->is_active ? 'text-green-600 hover:bg-green-50' : 'text-gray-400 hover:bg-gray-50' }}"
                                        onclick="toggleSetting({{ $setting->id }})">
                                        <i class="fas fa-{{ $setting->is_active ? 'check' : 'times' }}"></i>
                                    </button>
                                    <button type="button" class="p-1.5 rounded-lg text-xs text-red-600 hover:bg-red-50"
                                        onclick="deleteSetting({{ $setting->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="p-4">
                                <input type="hidden" name="settings[{{ $loop->index }}][id]" value="{{ $setting->id }}">
                                <input type="hidden" name="settings[{{ $loop->index }}][key]" value="{{ $setting->key }}">
                                <input type="hidden" name="settings[{{ $loop->index }}][type]" value="{{ $setting->type }}">
                                <input type="hidden" name="settings[{{ $loop->index }}][category]" value="{{ $setting->category }}">
                                <input type="hidden" name="settings[{{ $loop->index }}][label]" value="{{ $setting->label }}">
                                <input type="hidden" name="settings[{{ $loop->index }}][description]" value="{{ $setting->description }}">
                                <input type="hidden" name="settings[{{ $loop->index }}][is_active]" value="{{ $setting->is_active ? '1' : '0' }}">
                                <input type="hidden" name="settings[{{ $loop->index }}][sort_order]" value="{{ $setting->sort_order }}">

                                @if ($setting->type === 'image')
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Image</label>
                                        @if ($setting->value)
                                            <div class="mb-2">
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($setting->value) }}"
                                                    alt="{{ $setting->label }}" class="rounded-lg border border-gray-200 object-cover" style="max-width: 200px; max-height: 150px;">
                                            </div>
                                        @endif
                                        <input type="file" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                                            name="settings[{{ $loop->index }}][image]" accept="image/*"
                                            onchange="uploadImage(this, '{{ $setting->key }}')">
                                    </div>
                                @elseif($setting->type === 'textarea')
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                                        <textarea class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500" name="settings[{{ $loop->index }}][value]" rows="3">{{ $setting->value }}</textarea>
                                    </div>
                                @else
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                                        <input type="{{ $setting->type === 'email' ? 'email' : ($setting->type === 'url' ? 'url' : 'text') }}"
                                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                            name="settings[{{ $loop->index }}][value]" value="{{ $setting->value }}">
                                    </div>
                                @endif

                                @if ($setting->description)
                                    <p class="text-xs text-gray-500">{{ $setting->description }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-6">
                    <button type="submit" class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                        <i class="fas fa-save mr-2"></i>Save All Changes
                    </button>
                </div>
            </form>
        @else
            <div class="flex flex-col items-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-cog text-gray-400 text-2xl"></i>
                </div>
                <h4 class="text-gray-500 mb-2">No settings configured</h4>
                <p class="text-gray-400 text-sm mb-4">Start by adding your first setting for this category.</p>
                <button @click="showAddModal = true" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                    <i class="fas fa-plus mr-2"></i>Add First Setting
                </button>
            </div>
        @endif
    </div>

    <!-- Add Setting Modal (Alpine.js) -->
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showAddModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity bg-gray-500/75" @click="showAddModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div x-show="showAddModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-bottom transition-all transform bg-white shadow-xl rounded-xl sm:align-middle"
                @click.outside="showAddModal = false">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Add New Setting</h3>
                    <button type="button" @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form action="{{ route('admin.cms.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="key" class="block text-sm font-medium text-gray-700 mb-1">Key <span class="text-red-500">*</span></label>
                                <input type="text" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500" id="key" name="key" required>
                                <p class="mt-1 text-xs text-gray-500">Unique identifier for this setting</p>
                            </div>
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                                <select class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500" id="type" name="type" required>
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
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="label" class="block text-sm font-medium text-gray-700 mb-1">Label <span class="text-red-500">*</span></label>
                                <input type="text" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500" id="label" name="label" required>
                            </div>
                            <div>
                                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                                <input type="number" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500" id="sort_order" name="sort_order" value="0">
                            </div>
                        </div>
                        <div>
                            <label for="value" class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                            <input type="text" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500" id="value" name="value">
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500" id="description" name="description" rows="3"></textarea>
                        </div>
                        <input type="hidden" name="category" value="{{ $category }}">
                    </div>
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                        <button @click="showAddModal = false" type="button" class="inline-flex items-center justify-center border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold py-2 px-4 rounded-lg transition">Cancel</button>
                        <button type="submit" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">Create Setting</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Auto-generate key from label
        document.getElementById('label').addEventListener('input', function() {
            const label = this.value;
            const key = label.toLowerCase().replace(/[^a-z0-9\s]/g, '').replace(/\s+/g, '_');
            document.getElementById('key').value = key;
        });

        // Toggle setting status
        function toggleSetting(id) {
            fetch('{{ route('admin.cms.toggle', '__ID__') }}'.replace('__ID__', id), {
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
                });
        }

        // Delete setting
        function deleteSetting(id) {
            confirmAction({
                title: 'Hapus Pengaturan?',
                text: 'Are you sure you want to delete this setting?',
                icon: 'warning',
                confirmColor: '#d33',
                confirmText: 'Ya, Hapus',
                onConfirm: () => {
                    fetch('{{ route('admin.cms.destroy', '__ID__') }}'.replace('__ID__', id), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => {
                            if (response.ok) {
                                location.reload();
                            } else {
                                safeSwalFire({ icon: 'error', title: 'Gagal menghapus pengaturan' });
                            }
                        });
                }
            });
        }

        // Upload image
        function uploadImage(input, key) {
            const file = input.files[0];
            if (file) {
                const formData = new FormData();
                formData.append('image', file);
                formData.append('key', key);

                fetch('{{ route('admin.cms.upload-image') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const img = input.parentElement.querySelector('img');
                            if (img) {
                                img.src = data.url;
                            } else {
                                const imgElement = document.createElement('img');
                                imgElement.src = data.url;
                                imgElement.className = 'rounded-lg border border-gray-200 object-cover';
                                imgElement.style.maxWidth = '200px';
                                imgElement.style.maxHeight = '150px';
                                input.parentElement.insertBefore(imgElement, input);
                            }
                        } else {
                            alert('Error: ' + data.message);
                        }
                    });
            }
        }
    </script>
@endpush
