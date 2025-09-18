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
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSettingModal">
                                <i class="fas fa-plus me-1"></i>Add New Setting
                            </button>
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
                                    <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
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
@endsection

@section('scripts')
    <script>
        // Auto-generate key from label
        document.getElementById('label').addEventListener('input', function() {
            const label = this.value;
            const key = label.toLowerCase().replace(/[^a-z0-9\s]/g, '').replace(/\s+/g, '_');
            document.getElementById('key').value = key;
        });
    </script>
@endsection
