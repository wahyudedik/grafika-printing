@extends('layouts.layouts_dashboard')

@section('title', 'Manajemen Bahan')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center">
                <div>
                    <h3 class="card-title">Daftar Bahan</h3>
                </div>
                <div class="d-flex gap-2 flex-grow-1 justify-content-end">
                    <form action="{{ route('bahan.index') }}" method="GET" class="flex-grow-1">
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
                                placeholder="Cari bahan...">
                        </div>
                    </form>

                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Filter Stok: {{ request('stok') ? ucfirst(request('stok')) : 'Semua' }}
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item"
                                href="{{ route('bahan.index', array_merge(request()->except('stok'), ['stok' => ''])) }}">Semua</a>
                            <a class="dropdown-item"
                                href="{{ route('bahan.index', array_merge(request()->except('stok'), ['stok' => 'available'])) }}">Tersedia</a>
                            <a class="dropdown-item"
                                href="{{ route('bahan.index', array_merge(request()->except('stok'), ['stok' => 'low'])) }}">Stok
                                Rendah</a>
                            <a class="dropdown-item"
                                href="{{ route('bahan.index', array_merge(request()->except('stok'), ['stok' => 'out'])) }}">Habis</a>
                        </div>
                    </div>

                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Harga Grosir:
                            {{ request('has_wholesale') ? (request('has_wholesale') == 'yes' ? 'Ada' : 'Tidak Ada') : 'Semua' }}
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item"
                                href="{{ route('bahan.index', array_merge(request()->except('has_wholesale'), ['has_wholesale' => ''])) }}">Semua</a>
                            <a class="dropdown-item"
                                href="{{ route('bahan.index', array_merge(request()->except('has_wholesale'), ['has_wholesale' => 'yes'])) }}">Ada</a>
                            <a class="dropdown-item"
                                href="{{ route('bahan.index', array_merge(request()->except('has_wholesale'), ['has_wholesale' => 'no'])) }}">Tidak
                                Ada</a>
                        </div>
                    </div>

                    <a href="{{ route('bahan.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Tambah Bahan
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
                            <th>Nama Bahan</th>
                            <th>HPP</th>
                            <th>Satuan</th>
                            <th>Stok</th>
                            <th>Harga Grosir</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bahan as $item)
                            <tr>
                                <td><input type="checkbox" class="form-check-input row-checkbox" name="ids[]"
                                        value="{{ $item->id }}"></td>
                                <td class="font-medium">{{ $item->nama_bahan }}</td>
                                <td>Rp {{ number_format($item->hpp, 0, ',', '.') }}</td>
                                <td>{{ $item->satuan }}</td>
                                <td>{!! $item->stock_status_label !!}</td>
                                <td>
                                    @if ($item->wholesalePrices->count() > 0)
                                        <span class="badge bg-primary text-white">{{ $item->wholesalePrices->count() }} tier
                                            harga</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak ada</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('bahan.show', $item->id) }}" class="btn btn-icon btn-ghost-info"
                                            data-bs-toggle="tooltip" title="View">
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
                                        <a href="{{ route('bahan.edit', $item->id) }}"
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
                                            data-id="{{ $item->id }}" data-bs-toggle="tooltip" title="Delete">
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
                                <td colspan="7" class="text-center py-4">
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
                                        <p class="empty-title">Tidak ada data bahan</p>
                                        <p class="empty-subtitle text-muted">
                                            Silahkan tambahkan bahan baru atau ubah filter pencarian
                                        </p>
                                        <div class="empty-action">
                                            <a href="{{ route('bahan.create') }}" class="btn btn-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <line x1="12" y1="5" x2="12" y2="19" />
                                                    <line x1="5" y1="12" x2="19" y2="12" />
                                                </svg>
                                                Tambah Bahan
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
                        <button type="button" class="dropdown-item" onclick="updateBatchStock('add')">
                            Tambah Stok
                        </button>
                        <button type="button" class="dropdown-item" onclick="updateBatchStock('subtract')">
                            Kurangi Stok
                        </button>
                        <button type="button" class="dropdown-item" onclick="updateBatchStock('set')">
                            Set Stok
                        </button>
                        <div class="dropdown-divider"></div>
                        <button type="button" class="dropdown-item text-danger" onclick="confirmBatchDelete()">
                            Delete Selected
                        </button>
                    </div>
                </div>
                {{ $bahan->links('dev.components.pagination') }}
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
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            showLoading('Menghapus...');
                            deleteForm.action = `{{ route('bahan.destroy', '') }}/${id}`;
                            deleteForm.submit();
                        }
                    });
                };

                // Batch delete confirmation
                window.confirmBatchDelete = function() {
                    Swal.fire({
                        title: 'Anda yakin?',
                        text: "Semua data yang dipilih akan dihapus dan tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus semua!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            showLoading('Menghapus...');
                            batchForm.action = "{{ route('bahan.batch-delete') }}";
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

                // Update stock for batch items
                window.updateBatchStock = function(action) {
                    let title, text, placeholder;

                    if (action === 'add') {
                        title = 'Tambah Stok';
                        text = 'Masukkan jumlah yang ingin ditambahkan:';
                        placeholder = 'Jumlah untuk ditambahkan';
                    } else if (action === 'subtract') {
                        title = 'Kurangi Stok';
                        text = 'Masukkan jumlah yang ingin dikurangi:';
                        placeholder = 'Jumlah untuk dikurangi';
                    } else {
                        title = 'Set Stok';
                        text = 'Masukkan jumlah stok baru:';
                        placeholder = 'Jumlah stok baru';
                    }

                    Swal.fire({
                        title: title,
                        text: text,
                        input: 'number',
                        inputAttributes: {
                            min: 0,
                            step: 1
                        },
                        inputPlaceholder: placeholder,
                        showCancelButton: true,
                        confirmButtonText: 'Update',
                        cancelButtonText: 'Batal',
                        showLoaderOnConfirm: true,
                        preConfirm: (value) => {
                            if (!value || value < 0) {
                                Swal.showValidationMessage('Silakan masukkan angka yang valid');
                            }
                            return value;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            showLoading('Updating stock...');
                            batchForm.action = "{{ route('bahan.batch-update-stock') }}";
                            batchForm.method = "POST";

                            // Add inputs
                            const actionInput = document.createElement('input');
                            actionInput.type = 'hidden';
                            actionInput.name = 'action';
                            actionInput.value = action;
                            batchForm.appendChild(actionInput);

                            const valueInput = document.createElement('input');
                            valueInput.type = 'hidden';
                            valueInput.name = 'value';
                            valueInput.value = result.value;
                            batchForm.appendChild(valueInput);

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
