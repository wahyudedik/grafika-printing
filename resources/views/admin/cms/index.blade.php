@extends('dev.layouts.app')

@section('title', 'CMS Management')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cogs me-2"></i>CMS Management
                        </h3>
                        <div class="card-actions">
                            <div class="btn-group" role="group">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSettingModal">
                                    <i class="fas fa-plus me-1"></i>Add New Setting
                                </button>
                                <a href="{{ route('admin.cms.preview') }}" class="btn btn-info" target="_blank">
                                    <i class="fas fa-eye me-1"></i>Preview Landing
                                </a>
                                <a href="{{ route('admin.cms.statistics') }}" class="btn btn-success">
                                    <i class="fas fa-chart-bar me-1"></i>Statistics
                                </a>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle"
                                        data-bs-toggle="dropdown">
                                        <i class="fas fa-cog me-1"></i>Tools
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('admin.cms.export') }}">
                                                <i class="fas fa-download me-2"></i>Export Settings
                                            </a></li>
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#importModal">
                                                <i class="fas fa-upload me-2"></i>Import Settings
                                            </a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="resetSettings()">
                                                <i class="fas fa-undo me-2"></i>Reset to Default
                                            </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($categories as $categoryKey => $categoryName)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i
                                                    class="fas fa-{{ $categoryKey === 'general' ? 'cog' : ($categoryKey === 'hero' ? 'image' : ($categoryKey === 'footer' ? 'footer' : ($categoryKey === 'contact' ? 'phone' : 'share'))) }} me-2"></i>
                                                {{ $categoryName }}
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            @if (isset($settings[$categoryKey]) && $settings[$categoryKey]->count() > 0)
                                                <p class="text-muted mb-3">{{ $settings[$categoryKey]->count() }} settings
                                                    configured</p>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.cms.show', $categoryKey) }}"
                                                        class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-edit me-1"></i>Edit
                                                    </a>
                                                    <a href="{{ route('admin.cms.show', $categoryKey) }}"
                                                        class="btn btn-outline-info btn-sm">
                                                        <i class="fas fa-eye me-1"></i>View
                                                    </a>
                                                </div>
                                            @else
                                                <p class="text-muted mb-3">No settings configured</p>
                                                <a href="{{ route('admin.cms.show', $categoryKey) }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-plus me-1"></i>Configure
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
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
                                    <label for="key" class="form-label">Key <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="key" name="key" required>
                                    <div class="form-text">Unique identifier for this setting</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $categoryKey => $categoryName)
                                            <option value="{{ $categoryKey }}">{{ $categoryName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control" id="sort_order" name="sort_order"
                                        value="0">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="label" class="form-label">Label <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="label" name="label" required>
                        </div>
                        <div class="mb-3">
                            <label for="value" class="form-label">Value</label>
                            <input type="text" class="form-control" id="value" name="value">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Setting</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Settings Modal -->
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import CMS Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.cms.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Select JSON File</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".json"
                                required>
                            <div class="form-text">Upload a JSON file exported from CMS settings</div>
                        </div>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> This will overwrite existing settings with the same keys.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Import Settings</button>
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

        // Reset settings function
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

        // Real-time preview functionality
        function previewSetting(key, value) {
            // Update preview elements in real-time
            const previewElements = document.querySelectorAll(`[data-cms-key="${key}"]`);
            previewElements.forEach(element => {
                if (element.tagName === 'IMG') {
                    element.src = value;
                } else {
                    element.textContent = value;
                }
            });
        }

        // Auto-save functionality
        let saveTimeout;

        function autoSave() {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                const form = document.getElementById('cmsForm');
                if (form) {
                    form.submit();
                }
            }, 2000); // Auto-save after 2 seconds of inactivity
        }

        // Add auto-save to form inputs
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('input', autoSave);
            });
        });
    </script>
@endsection
