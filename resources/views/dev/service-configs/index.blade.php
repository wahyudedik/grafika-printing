@extends('dev.layouts.app')

@section('title', 'Service Configurations')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Service Configurations</h2>
            <div class="page-pretitle">Pengaturan API Pihak Ketiga</div>
        </div>
        <div class="col-auto ms-auto">
            <div class="btn-list">
                <a href="{{ route('admin.service-configs.seed-defaults') }}" class="btn btn-outline-primary" onclick="return confirm('Import config default dari .env? Config yang sudah ada tidak akan ditimpa.')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 3v12"/>
                        <path d="M8 11l4 4 4-4"/>
                        <path d="M8 5l4-4 4 4"/>
                    </svg>
                    Import dari .env
                </a>
                <a href="{{ route('admin.service-configs.clear-cache') }}" class="btn btn-outline-warning" onclick="return confirm('Bersihkan semua cache config?')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"/>
                        <path d="M4 4l16 16"/>
                        <path d="M9 4l-5 5"/>
                        <path d="M4 9l5 -5"/>
                    </svg>
                    Clear Cache
                </a>
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

@if(session('info'))
    <div class="alert alert-info alert-dismissible" role="alert">
        <div class="d-flex">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
                    <path d="M12 16v.01"/>
                    <path d="M12 13v-2"/>
                </svg>
            </div>
            <div>{{ session('info') }}</div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
@endif

{{-- Statistics Cards --}}
<div class="row row-cards mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Config</div>
                </div>
                <div class="h1 mb-0">{{ $statistics['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Active</div>
                </div>
                <div class="h1 mb-0 text-green">{{ $statistics['active'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Inactive</div>
                </div>
                <div class="h1 mb-0 text-red">{{ $statistics['inactive'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Encrypted</div>
                </div>
                <div class="h1 mb-0 text-yellow">{{ $statistics['encrypted'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Service Cards --}}
<div class="row row-cards">
    @forelse($services as $serviceKey => $serviceInfo)
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-{{ $serviceKey === 'xendit' ? 'green' : 'blue' }} me-2" style="font-size: 0.8em;">
                                @if($serviceKey === 'xendit')
                                    💳
                                @else
                                    🚚
                                @endif
                            </span>
                            <div>
                                <h3 class="card-title">{{ $serviceInfo['name'] }}</h3>
                                <p class="card-subtitle">{{ $serviceInfo['description'] }}</p>
                            </div>
                        </div>
                        <div class="btn-list">
                            <button class="btn btn-outline-success btn-sm test-connection-btn" data-service="{{ $serviceKey }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10"/>
                                </svg>
                                Test
                            </button>
                            <a href="{{ route('admin.service-configs.show', $serviceKey) }}" class="btn btn-primary btn-sm">
                                Kelola
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M9 6l6 6l-6 6"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($configs[$serviceKey]) && $configs[$serviceKey]->count() > 0)
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                @foreach($configs[$serviceKey] as $config)
                                    <tr>
                                        <td class="text-muted" style="width: 40%;">{{ $config->label }}</td>
                                        <td>
                                            <code>{{ $config->getMaskedValue() }}</code>
                                        </td>
                                        <td class="text-end">
                                            @if($config->is_active)
                                                <span class="badge bg-green-lt">Active</span>
                                            @else
                                                <span class="badge bg-red-lt">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-muted text-center py-3">
                            Belum ada konfigurasi.
                            <a href="{{ route('admin.service-configs.seed-defaults') }}" class="link-primary">Import dari .env</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="empty">
                <p class="empty-title">Belum ada service</p>
                <p class="empty-subtitle text-muted">Tambahkan service configuration baru.</p>
            </div>
        </div>
    @endforelse
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
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg> Test';
        });
    });
});
</script>
@endpush
