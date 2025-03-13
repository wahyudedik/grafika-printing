@extends('layouts.layouts_dashboard')

@section('title', 'Manajemen Transaksi')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-column flex-md-row gap-3">
                <div>
                    <h3 class="card-title">Daftar Transaksi</h3>
                </div>
                <div class="d-flex flex-column flex-grow-1">
                    <form action="{{ route('transaksi.index') }}" method="GET" id="filter-form">
                                                <div class="row g-2">
                            <div class="col-md-6">
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
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control" placeholder="Cari kode/pelanggan...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select" onchange="document.getElementById('filter-form').submit()">
                                    <option value="">Semua Status</option>
                                    @foreach ($statusOptions as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ request('status') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('transaksi.create') }}" class="btn btn-primary w-124">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <line x1="12" y1="5" x2="12" y2="19" />
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                    Tambah Transaksi
                                </a>
                            </div>
                        </div>
                        <div class="row g-2 mt-2">
                            <div class="col-md-5">
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ request('start_date') }}" placeholder="Tanggal Mulai">
                            </div>
                            <div class="col-md-5">
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}"
                                    placeholder="Tanggal Akhir">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-filter"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path
                                            d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z">
                                        </path>
                                    </svg>
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>
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
                            <th>Kode</th>
                            <th>Pelanggan</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Metode Pembayaran</th>
                            <th>Progres</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksis as $transaksi)
                            <tr>
                                <td><input type="checkbox" class="form-check-input row-checkbox" name="ids[]"
                                        value="{{ $transaksi->id }}"></td>
                                <td>
                                    <a href="{{ route('transaksi.show', $transaksi->id) }}" class="text-reset">
                                        {{ $transaksi->kode }}
                                    </a>
                                </td>
                                <td>{{ $transaksi->pelanggan->nama ?? 'N/A' }}</td>
                                <td>{{ $transaksi->tanggal_dibuat->format('d/m/Y H:i') }}</td>
                                <td>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow',
                                            'processing' => 'bg-blue',
                                            'quality_check' => 'bg-purple',
                                            'completed' => 'bg-green',
                                            'cancelled' => 'bg-red',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Pending',
                                            'processing' => 'Diproses',
                                            'quality_check' => 'QC',
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Dibatalkan',
                                        ];
                                    @endphp
                                    <span class="badge {{ $statusColors[$transaksi->status] }}">
                                        {{ $statusLabels[$transaksi->status] }}
                                    </span>
                                </td>
                                <td>{{ $transaksi->payment_method }}</td>
                                <td>
                                    <div class="row align-items-center">
                                        <div class="col-12 col-lg-auto">{{ $transaksi->progress_percentage }}%</div>
                                        <div class="col">
                                            <div class="progress" style="width: 5rem">
                                                <div class="progress-bar"
                                                    style="width: {{ $transaksi->progress_percentage }}%"
                                                    role="progressbar"
                                                    aria-valuenow="{{ $transaksi->progress_percentage }}"
                                                    aria-valuemin="0" aria-valuemax="100"
                                                    aria-label="{{ $transaksi->progress_percentage }}% Complete">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('transaksi.show', $transaksi->id) }}"
                                            class="btn btn-icon btn-ghost-info" data-bs-toggle="tooltip" title="View">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-eye" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                <path
                                                    d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('transaksi.edit', $transaksi->id) }}"
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
                                        <button type="button" class="btn btn-icon btn-ghost-danger delete-btn"
                                            data-id="{{ $transaksi->id }}" data-bs-toggle="tooltip" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-trash" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 7l16 0" />
                                                <path d="M10 11l0 6" />
                                                <path d="M14 11l0 6" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="empty">
                                        <div class="empty-img">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="128"
                                                height="128" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                <path d="M9 10l.01 0" />
                                                <path d="M15 10l.01 0" />
                                                <path d="M9.5 15.25a3.5 3.5 0 0 1 5 0" />
                                            </svg>
                                        </div>
                                        <p class="empty-title">Tidak ada data transaksi</p>
                                        <p class="empty-subtitle text-muted">
                                            Silahkan tambahkan transaksi baru atau ubah filter pencarian
                                        </p>
                                        <div class="empty-action">
                                            <a href="{{ route('transaksi.create') }}" class="btn btn-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <line x1="12" y1="5" x2="12" y2="19" />
                                                    <line x1="5" y1="12" x2="19" y2="12" />
                                                </svg>
                                                Tambah Transaksi
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center">
                <div class="batch-actions btn-group me-3" style="display: none;">
                    <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Batch Actions <span class="selected-count">(0)</span>
                    </button>
                    <div class="dropdown-menu">
                        <button type="button" class="dropdown-item" onclick="updateBatchStatus()">
                            Ubah Status
                        </button>
                        <div class="dropdown-divider"></div>
                        <button type="button" class="dropdown-item text-danger" onclick="confirmBatchDelete()">
                            Hapus Terpilih
                        </button>
                    </div>
                </div>
                                {{ $transaksis->links('dev.components.pagination') }}
            </div>
        </form>
    </div>

    <!-- Hidden delete form -->
    <form id="delete-form" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Batch selection functionality
                const selectAllCheckbox = document.getElementById('select-all');
                const rowCheckboxes = document.querySelectorAll('.row-checkbox');
                const batchActions = document.querySelector('.batch-actions');
                const selectedCountEl = document.querySelector('.selected-count');
                const batchForm = document.getElementById('batch-form');
                const deleteForm = document.getElementById('delete-form');
                const deleteButtons = document.querySelectorAll('.delete-btn');

                // Setup delete buttons
                deleteButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        confirmDelete(id);
                    });
                });

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

                // Individual delete confirmation
                window.confirmDelete = function(id) {
                    Swal.fire({
                        title: 'Anda yakin?',
                        text: "Data transaksi yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Sedang Memproses',
                                text: 'Mohon tunggu...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            deleteForm.action = `{{ route('transaksi.destroy', '') }}/${id}`;
                            deleteForm.submit();
                        }
                    });
                };

                // Batch delete confirmation
                window.confirmBatchDelete = function() {
                    Swal.fire({
                        title: 'Anda yakin?',
                        text: "Semua transaksi yang dipilih akan dihapus dan tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus semua!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Sedang Memproses',
                                text: 'Mohon tunggu...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            batchForm.action = "{{ route('transaksi.batch-delete') }}";
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
                };

                // Update status for batch items
                window.updateBatchStatus = function() {
                    const statusOptions = @json($statusOptions);

                    let statusOptionsHtml = '';
                    Object.entries(statusOptions).forEach(([value, label]) => {
                        statusOptionsHtml += `<option value="${value}">${label}</option>`;
                    });

                    Swal.fire({
                        title: 'Ubah Status',
                        html: `
                            <div class="mb-3">
                                <select class="form-select" id="status-select">
                                    <option value="">Pilih Status</option>
                                    ${statusOptionsHtml}
                                </select>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Update',
                        cancelButtonText: 'Batal',
                        preConfirm: () => {
                            const selectedStatus = document.getElementById('status-select').value;
                            if (!selectedStatus) {
                                Swal.showValidationMessage('Silakan pilih status');
                                return false;
                            }
                            return selectedStatus;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Sedang Memproses',
                                text: 'Mohon tunggu...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            batchForm.action = "{{ route('transaksi.batch-update') }}";
                            batchForm.method = "POST";

                            // Add action and status inputs
                            const actionInput = document.createElement('input');
                            actionInput.type = 'hidden';
                            actionInput.name = 'action';
                            actionInput.value = 'status';
                            batchForm.appendChild(actionInput);

                            const statusInput = document.createElement('input');
                            statusInput.type = 'hidden';
                            statusInput.name = 'status';
                            statusInput.value = result.value;
                            batchForm.appendChild(statusInput);

                            // Add method override for PUT
                            const methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            methodInput.value = 'PUT';
                            batchForm.appendChild(methodInput);

                            batchForm.submit();
                        }
                    });
                };
            });
        </script>
    @endpush
@endsection


