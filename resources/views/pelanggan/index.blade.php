@extends('layouts.layouts_dashboard')

@section('title', 'Pelanggan Manajemen')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center">
                <div>
                    <h3 class="card-title">Daftar Pelanggan</h3>
                </div>
                <div class="d-flex gap-2 flex-grow-1 justify-content-end ">
                    <form action="{{ route('pelanggan.index') }}" method="GET" class="flex-grow-1">
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
                                placeholder="Cari pelanggan...">
                        </div>
                    </form>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Filter Status:
                            {{ request('status') ? (request('status') == 'active' ? 'Aktif' : 'Non-Aktif') : 'Semua' }}
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item"
                                href="{{ route('pelanggan.index', array_merge(request()->except('status'), ['status' => ''])) }}">Semua</a>
                            <a class="dropdown-item"
                                href="{{ route('pelanggan.index', array_merge(request()->except('status'), ['status' => 'active'])) }}">Aktif</a>
                            <a class="dropdown-item"
                                href="{{ route('pelanggan.index', array_merge(request()->except('status'), ['status' => 'inactive'])) }}">Non-Aktif</a>
                        </div>
                    </div>
                    <a href="{{ route('pelanggan.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Tambah Pelanggan
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
                            <th>Kode</th>
                            <th>Nama Pelanggan</th>
                            <th>Kontak</th>
                            <th>Alamat</th>
                            <th>Transaksi Terakhir</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pelanggan as $item)
                            <tr>
                                <td><input type="checkbox" class="form-check-input row-checkbox" name="ids[]"
                                        value="{{ $item->id }}"></td>
                                <td><span class="text-muted">{{ $item->kode }}</span></td>
                                <td class="font-medium">{{ $item->nama }}</td>
                                <td>
                                    @if ($item->email)
                                        <div>{{ $item->email }}</div>
                                    @endif
                                    @if ($item->no_telp)
                                        <div>{{ $item->no_telp }}</div>
                                    @endif
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($item->alamat, 50) }}</td>
                                <td>
                                    @if ($item->transaksi_terakhir)
                                        <span class="badge bg-success text-white">
                                            {{ $item->transaksi_terakhir->format('d M Y') }}
                                        </span>
                                    @else
                                        <span class="badge bg-muted text-white">Belum ada</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('pelanggan.show', $item->id) }}"
                                            class="btn btn-icon btn-ghost-info" data-bs-toggle="tooltip" title="View">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye"
                                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                <path
                                                    d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('pelanggan.edit', $item->id) }}"
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
                                        <form action="{{ route('pelanggan.destroy', $item->id) }}" method="POST"
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
                        <button type="button" class="dropdown-item text-danger" onclick="confirmBatchDelete()">
                            Delete Selected
                        </button>
                    </div>
                </div>
                {{ $pelanggan->links('dev.components.pagination') }}
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

            // Confirm batch delete
            function confirmBatchDelete() {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Pelanggan yang dipilih akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        showLoading('Menghapus pelanggan...');
                        batchForm.action = "{{ route('pelanggan.batch-delete') }}";
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

            // Individual delete confirmation
            function confirmDelete(formId) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        showLoading('Menghapus...');
                        document.getElementById(formId).submit();
                    }
                });
            }
        </script>
    @endpush
@endsection
