@extends('layouts.layouts_dashboard')

@section('title', 'Alat Manajemen')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center">
                <div>
                    <h3 class="card-title">Daftar Alat</h3>
                </div>
                <div class="d-flex gap-2 flex-grow-1 justify-content-end ">
                    <form action="{{ route('alat.index') }}" method="GET" class="flex-grow-1">
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <circle cx="10" cy="10" r="7" />
                                    <line x1="21" y1="21" x2="15" y2="15" />
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="Search alat...">
                        </div>
                    </form>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Filter Status: {{ request('status') ? ucfirst(request('status')) : 'Semua' }}
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item"
                                href="{{ route('alat.index', array_merge(request()->except('status'), ['status' => ''])) }}">Semua</a>
                            <a class="dropdown-item"
                                href="{{ route('alat.index', array_merge(request()->except('status'), ['status' => 'aktif'])) }}">Aktif</a>
                            <a class="dropdown-item"
                                href="{{ route('alat.index', array_merge(request()->except('status'), ['status' => 'maintenance'])) }}">Maintenance</a>
                            <a class="dropdown-item"
                                href="{{ route('alat.index', array_merge(request()->except('status'), ['status' => 'rusak'])) }}">Rusak</a>
                        </div>
                    </div>
                    <a href="{{ route('alat.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Tambah Alat
                    </a>
                </div>
            </div>
        </div>

        <form id="batch-form" method="POST">
            @csrf
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap">
                    <thead>
                        <tr>
                            <th class="w-1"><input type="checkbox" class="form-check-input" id="select-all"></th>
                            <th>Nama Alat</th>
                            <th>Merek</th>
                            <th>Model</th>
                            <th>Spesifikasi</th>
                            <th>Status</th>
                            <th>Tanggal Pembelian</th>
                            <th>Kapasitas/Jam</th>
                            <th>Tersedia</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($alat as $item)
                            <tr>
                                <td><input type="checkbox" class="form-check-input row-checkbox" name="ids[]"
                                        value="{{ $item->id }}"></td>
                                <td class="font-medium">{{ $item->nama_alat }}</td>
                                <td>{{ $item->merek }}</td>
                                <td>{{ $item->model }}</td>
                                <td>{{ Str::limit($item->spesifikasi_alat, 50) }}</td>
                                <td><span class="badge bg-{{ $item->status_color }}-lt">{{ $item->status }}</span></td>
                                <td>{{ $item->tanggal_pembelian->format('d M Y') }}</td>
                                <td>{{ $item->kapasitas_cetak_per_jam }}</td>
                                <td><span
                                        class="badge text-white {{ $item->tersedia ? 'bg-success' : 'bg-danger' }}">{{ $item->tersedia ? 'Ya' : 'Tidak' }}</span>
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('alat.show', $item->id) }}" class="btn btn-icon btn-ghost-info"
                                            data-bs-toggle="tooltip" title="View">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye"
                                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                <path
                                                    d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('alat.edit', $item->id) }}"
                                            class="btn btn-icon btn-ghost-warning" data-bs-toggle="tooltip"
                                            title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-edit" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path
                                                    d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('alat.destroy', $item->id) }}" method="POST"
                                            class="inline" id="delete-form-{{ $item->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-icon btn-ghost-danger"
                                                data-bs-toggle="tooltip" title="Delete"
                                                onclick="confirmDelete('delete-form-{{ $item->id }}')">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-trash" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M4 7l16 0" />
                                                    <path d="M10 11l0 6" />
                                                    <path d="M14 11l0 6" />
                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center">
                <div class="batch-actions btn-group me-3" style="display: none;">
                    <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Batch Actions <span class="selected-count">(0)</span>
                    </button>
                    <div class="dropdown-menu">
                        <button type="button" class="dropdown-item" onclick="updateBatchStatus('aktif')">
                            Set Aktif
                        </button>
                        <button type="button" class="dropdown-item" onclick="updateBatchStatus('maintenance')">
                            Set Maintenance
                        </button>
                        <button type="button" class="dropdown-item" onclick="updateBatchStatus('rusak')">
                            Set Rusak
                        </button>
                        <div class="dropdown-divider"></div>
                        <button type="button" class="dropdown-item text-danger" onclick="confirmBatchDelete()">
                            Delete Selected
                        </button>
                    </div>
                </div>
                {{ $alat->links('dev.components.pagination') }}
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            // Batch selection functionality
            const selectAllCheckbox = document.getElementById('select-all');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const batchActions = document.querySelector('.batch-actions');
            const selectedCountEl = document.querySelector('.selected-count');
            const batchForm = document.getElementById('batch-form');

            // Select all functionality
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                updateBatchActionsVisibility();
            });

            // Individual checkbox handling
            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateBatchActionsVisibility();

                    // Update "select all" checkbox
                    const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(rowCheckboxes).some(cb => cb.checked);

                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;
                });
            });

            // Update batch actions visibility based on selections
            function updateBatchActionsVisibility() {
                const selectedCount = Array.from(rowCheckboxes).filter(cb => cb.checked).length;

                if (selectedCount > 0) {
                    batchActions.style.display = 'block';
                    selectedCountEl.textContent = `(${selectedCount})`;
                } else {
                    batchActions.style.display = 'none';
                }
            }

            // Status update for batch items
            function updateBatchStatus(status) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: `Are you sure you want to set ${status} status for the selected items?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, update it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        showLoading('Updating status...');
                        batchForm.action = "{{ route('alat.batch-update-status') }}";
                        batchForm.method = "POST";

                        // Add status input
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'status';
                        input.value = status;
                        batchForm.appendChild(input);

                        // Add method override for PUT
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'PUT';
                        batchForm.appendChild(methodInput);

                        batchForm.submit();
                    }
                });
            }

            // Confirm batch delete
            function confirmBatchDelete() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete them!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        showLoading('Deleting...');
                        batchForm.action = "{{ route('alat.batch-delete') }}";
                        batchForm.method = "POST";

                        // Add method override for DELETE
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        batchForm.appendChild(methodInput);

                        batchForm.submit();
                    }
                });
            }
        </script>
    @endpush
@endsection
