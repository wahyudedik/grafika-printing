@extends('dev.layouts.app')

@section('title', 'CMS Settings - ' . $categoryName)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i
                                    class="fas fa-{{ $category === 'general' ? 'cog' : ($category === 'hero' ? 'image' : ($category === 'footer' ? 'footer' : ($category === 'contact' ? 'phone' : 'share'))) }} me-2"></i>
                                {{ $categoryName }} Settings
                            </h3>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.cms.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Back to CMS
                                </a>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSettingModal">
                                    <i class="fas fa-plus me-1"></i>Add Setting
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($settings->count() > 0)
                            <form action="{{ route('admin.cms.update') }}" method="POST" id="cmsForm">
                                @csrf
                                <div class="row">
                                    @foreach ($settings as $setting)
                                        <div class="col-md-6 col-lg-4 mb-4">
                                            <div class="card h-100">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h6 class="card-title mb-0">{{ $setting->label }}</h6>
                                                    <div class="d-flex gap-1">
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-{{ $setting->is_active ? 'success' : 'secondary' }}"
                                                            onclick="toggleSetting({{ $setting->id }})">
                                                            <i
                                                                class="fas fa-{{ $setting->is_active ? 'check' : 'times' }}"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="deleteSetting({{ $setting->id }})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <input type="hidden" name="settings[{{ $loop->index }}][id]"
                                                        value="{{ $setting->id }}">
                                                    <input type="hidden" name="settings[{{ $loop->index }}][key]"
                                                        value="{{ $setting->key }}">
                                                    <input type="hidden" name="settings[{{ $loop->index }}][type]"
                                                        value="{{ $setting->type }}">
                                                    <input type="hidden" name="settings[{{ $loop->index }}][category]"
                                                        value="{{ $setting->category }}">
                                                    <input type="hidden" name="settings[{{ $loop->index }}][label]"
                                                        value="{{ $setting->label }}">
                                                    <input type="hidden" name="settings[{{ $loop->index }}][description]"
                                                        value="{{ $setting->description }}">
                                                    <input type="hidden" name="settings[{{ $loop->index }}][is_active]"
                                                        value="{{ $setting->is_active ? '1' : '0' }}">
                                                    <input type="hidden" name="settings[{{ $loop->index }}][sort_order]"
                                                        value="{{ $setting->sort_order }}">

                                                    @if ($setting->type === 'image')
                                                        <div class="mb-3">
                                                            <label class="form-label">Current Image</label>
                                                            @if ($setting->value)
                                                                <div class="mb-2">
                                                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($setting->value) }}"
                                                                        alt="{{ $setting->label }}" class="img-thumbnail"
                                                                        style="max-width: 200px; max-height: 150px;">
                                                                </div>
                                                            @endif
                                                            <input type="file" class="form-control"
                                                                name="settings[{{ $loop->index }}][image]"
                                                                accept="image/*"
                                                                onchange="uploadImage(this, '{{ $setting->key }}')">
                                                        </div>
                                                    @elseif($setting->type === 'textarea')
                                                        <div class="mb-3">
                                                            <label class="form-label">Value</label>
                                                            <textarea class="form-control" name="settings[{{ $loop->index }}][value]" rows="3">{{ $setting->value }}</textarea>
                                                        </div>
                                                    @else
                                                        <div class="mb-3">
                                                            <label class="form-label">Value</label>
                                                            <input
                                                                type="{{ $setting->type === 'email' ? 'email' : ($setting->type === 'url' ? 'url' : 'text') }}"
                                                                class="form-control"
                                                                name="settings[{{ $loop->index }}][value]"
                                                                value="{{ $setting->value }}">
                                                        </div>
                                                    @endif

                                                    @if ($setting->description)
                                                        <small class="text-muted">{{ $setting->description }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-save me-2"></i>Save All Changes
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="text-center py-5">
                                <div class="empty-state">
                                    <div class="empty-icon mb-4">
                                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center"
                                            style="width: 80px; height: 80px;">
                                            <i class="fas fa-cog text-muted" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                    <h4 class="text-muted mb-3">No settings configured</h4>
                                    <p class="text-muted mb-4">Start by adding your first setting for this category.</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addSettingModal">
                                        <i class="fas fa-plus me-2"></i>Add First Setting
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Setting Modal -->
    <div class="modal fade" id="addSettingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Setting</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.cms.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="key" class="form-label">Key <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="key" name="key" required>
                                    <div class="form-text">Unique identifier for this setting</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Type <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="type" name="type" required>
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
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="label" class="form-label">Label <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="label" name="label" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control" id="sort_order" name="sort_order"
                                        value="0">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="value" class="form-label">Value</label>
                            <input type="text" class="form-control" id="value" name="value">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <input type="hidden" name="category" value="{{ $category }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Setting</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Auto-generate key from label
        document.getElementById('label').addEventListener('input', function() {
            const label = this.value;
            const key = label.toLowerCase().replace(/[^a-z0-9\s]/g, '').replace(/\s+/g, '_');
            document.getElementById('key').value = key;
        });

        // Toggle setting status
        function toggleSetting(id) {
            fetch(`{{ route('admin.cms.toggle', '') }}/${id}`, {
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
            if (confirm('Are you sure you want to delete this setting?')) {
                fetch(`{{ route('admin.cms.destroy', '') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            location.reload();
                        } else {
                            alert('Error deleting setting');
                        }
                    });
            }
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
                            // Update the image preview
                            const img = input.parentElement.querySelector('img');
                            if (img) {
                                img.src = data.url;
                            } else {
                                // Create new image element
                                const imgElement = document.createElement('img');
                                imgElement.src = data.url;
                                imgElement.className = 'img-thumbnail';
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
@endsection
