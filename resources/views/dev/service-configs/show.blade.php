@extends('dev.layouts.app')

@section('title', $serviceInfo['name'] . ' - Service Configuration')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="d-flex align-items-center mb-1">
                <a href="{{ route('admin.service-configs.index') }}" class="btn btn-icon btn-ghost-secondary me-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M15 6l-6 6l6 6"/>
                    </svg>
                </a>
                <h2 class="page-title">{{ $serviceInfo['name'] }} Configuration</h2>
            </div>
            <div class="page-pretitle">{{ $serviceInfo['description'] }}</div>
        </div>
        <div class="col-auto ms-auto">
            <div class="btn-list">
                <button class="btn btn-outline-success test-connection-btn" data-service="{{ $service }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 12l5 5l10 -10"/>
                    </svg>
                    Test Connection
                </button>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <div class="d-flex">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 12l5 5l10 -10"/>
                </svg>
            </div>
            <div>{{ session('success') }}</div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible" role="alert">
        <div class="d-flex">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 9v4"/>
                    <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/>
                    <path d="M12 16h.01"/>
                </svg>
            </div>
            <div>{{ session('error') }}</div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 17l2 -4l2 4l-2 4z"/>
                        <path d="M8 17l4 -8l4 8l-4 8z"/>
                        <path d="M16 17l2 -4l2 4l-2 4z"/>
                    </svg>
                    Konfigurasi
                </h3>
            </div>
            <div class="card-body">
                @forelse($configs as $config)
                    <form action="{{ route('admin.service-configs.update', $config) }}" method="POST" class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        @csrf
                        @method('PUT')

                        <div class="row align-items-center mb-2">
                            <div class="col">
                                <label class="form-label fw-bold">
                                    {{ $config->label }}
                                    @if($config->is_encrypted)
                                        <span class="badge bg-yellow-lt ms-1" style="font-size: 0.7em;">
                                            🔒 Encrypted
                                        </span>
                                    @endif
                                </label>
                                @if($config->description)
                                    <div class="form-hint">{{ $config->description }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="row g-2 align-items-end">
                            <div class="col">
                                <div class="input-group">
                                    <input type="{{ $config->is_encrypted ? 'password' : 'text' }}"
                                           name="value"
                                           class="form-control font-monospace"
                                           value="{{ $config->getDecryptedValue() }}"
                                           placeholder="{{ $config->label }}">
                                    <button type="button" class="btn btn-ghost-secondary toggle-password-btn" title="Toggle visibility">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ $config->is_active ? 'checked' : '' }} id="active_{{ $config->id }}">
                                    <label class="form-check-label" for="active_{{ $config->id }}">Active</label>
                                </div>
                            </div>
                            <input type="hidden" name="label" value="{{ $config->label }}">
                            <input type="hidden" name="description" value="{{ $config->description }}">
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l5 5l10 -10"/>
                                    </svg>
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                @empty
                    <div class="text-muted text-center py-4">
                        Belum ada konfigurasi untuk {{ $serviceInfo['name'] }}.
                        <br><br>
                        <a href="{{ route('admin.service-configs.seed-defaults') }}" class="btn btn-primary" onclick="return confirm('Import config default dari .env?')">
                            Import dari .env
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Info Card --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
                        <path d="M12 16v.01"/>
                        <path d="M12 13v-2"/>
                    </svg>
                    Informasi
                </h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted mb-1">Service</div>
                    <div class="fw-bold">{{ $serviceInfo['name'] }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted mb-1">Total Config</div>
                    <div class="fw-bold">{{ $configs->count() }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted mb-1">Active</div>
                    <div class="fw-bold text-green">{{ $configs->where('is_active', true)->count() }}</div>
                </div>
                <div>
                    <div class="text-muted mb-1">Inactive</div>
                    <div class="fw-bold text-red">{{ $configs->where('is_active', false)->count() }}</div>
                </div>
            </div>
        </div>

        {{-- Danger Zone --}}
        <div class="card border-danger">
            <div class="card-header bg-danger-lt">
                <h3 class="card-title text-danger">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 9v4"/>
                        <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/>
                        <path d="M12 16h.01"/>
                    </svg>
                    Danger Zone
                </h3>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Nonaktifkan semua config untuk service ini atau hapus semua config.</p>
                <div class="btn-list">
                    @foreach($configs as $config)
                        @if($config->is_active)
                            <form action="{{ route('admin.service-configs.toggle', $config) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning btn-sm">
                                    Nonaktifkan {{ $config->label }}
                                </button>
                            </form>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Test Connection Result Modal --}}
<div class="modal modal-blur fade" id="testResultModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-status" id="testResultStatus"></div>
            <div class="modal-body text-center py-4">
                <div id="testResultIcon" class="mb-2"></div>
                <h3 id="testResultTitle" class="mb-1"></h3>
                <p id="testResultMessage" class="text-muted"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn w-100" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Toggle password visibility
document.querySelectorAll('.toggle-password-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = this.closest('.input-group').querySelector('input');
        if (input.type === 'password') {
            input.type = 'text';
            this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/><path d="M3 3l18 18"/></svg>';
        } else {
            input.type = 'password';
            this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/></svg>';
        }
    });
});

// Test connection
document.querySelectorAll('.test-connection-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const service = this.dataset.service;
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Testing...';

        fetch('{{ route("admin.service-configs.test-connection") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ service_name: service })
        })
        .then(response => response.json())
        .then(data => {
            const modal = new bootstrap.Modal(document.getElementById('testResultModal'));
            const status = document.getElementById('testResultStatus');
            const icon = document.getElementById('testResultIcon');
            const title = document.getElementById('testResultTitle');
            const message = document.getElementById('testResultMessage');

            if (data.success) {
                status.className = 'modal-status bg-success';
                icon.innerHTML = '<span style="font-size: 3em;">✅</span>';
                title.textContent = 'Berhasil!';
                title.className = 'mb-1 text-green';
            } else {
                status.className = 'modal-status bg-danger';
                icon.innerHTML = '<span style="font-size: 3em;">❌</span>';
                title.textContent = 'Gagal';
                title.className = 'mb-1 text-red';
            }
            message.textContent = data.message;

            modal.show();
        })
        .catch(error => {
            alert('Error: ' + error.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg> Test Connection';
        });
    });
});
</script>
@endpush
