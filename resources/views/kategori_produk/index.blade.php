@extends('layouts.layouts_dashboard')

@section('title', 'Manajemen Kategori Produk')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center">
                <div>
                    <h3 class="card-title">Daftar Kategori Produk</h3>
                </div>
                <div class="d-flex gap-2 flex-grow-1 justify-content-end">
                    <form action="{{ route('kategori-produk.index') }}" method="GET" class="flex-grow-1">
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
                                placeholder="Cari kategori...">
                        </div>
                    </form>

                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Sort: {{ request('sort', 'nama_kategori') }} ({{ request('order', 'asc') }})
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item"
                                href="{{ route('kategori-produk.index', array_merge(request()->except(['sort', 'order']), ['sort' => 'nama_kategori', 'order' => 'asc'])) }}">Nama
                                (A-Z)</a>
                            <a class="dropdown-item"
                                href="{{ route('kategori-produk.index', array_merge(request()->except(['sort', 'order']), ['sort' => 'nama_kategori', 'order' => 'desc'])) }}">Nama
                                (Z-A)</a>
                            <a class="dropdown-item"
                                href="{{ route('kategori-produk.index', array_merge(request()->except(['sort', 'order']), ['sort' => 'created_at', 'order' => 'desc'])) }}">Terbaru</a>
                            <a class="dropdown-item"
                                href="{{ route('kategori-produk.index', array_merge(request()->except(['sort', 'order']), ['sort' => 'created_at', 'order' => 'asc'])) }}">Terlama</a>
                        </div>
                    </div>

                    <a href="{{ route('kategori-produk.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Tambah Kategori
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
                            <th>Nama Kategori</th>
                            <th>Slug</th>
                            <th>Jumlah Produk</th>
                            <th>Tanggal Dibuat</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kategori as $item)
                            <tr>
                                <td><input type="checkbox" class="form-check-input row-checkbox" name="ids[]"
                                        value="{{ $item->id }}"></td>
                                <td class="font-medium">{{ $item->nama_kategori }}</td>
                                <td>{{ $item->slug }}</td>
                                <td>{{ $item->produk->count() }}</td>
                                <td>{{ $item->created_at->format('d M Y, H:i') }}</td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('kategori-produk.show', $item->id) }}"
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
                                        <a href="{{ route('kategori-produk.edit', $item->id) }}"
                                            class="btn btn-icon btn-ghost-warning" data-bs-toggle="tooltip" title="Edit">
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
                                <td colspan="6" class="text-center py-4">
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
                                        <p class="empty-title">Tidak ada data kategori</p>
                                        <p class="empty-subtitle text-muted">
                                            Silahkan tambahkan kategori produk baru atau ubah filter pencarian
                                        </p>
                                        <div class="empty-action">
                                            <a href="{{ route('kategori-produk.create') }}" class="btn btn-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <line x1="12" y1="5" x2="12" y2="19" />
                                                    <line x1="5" y1="12" x2="19" y2="12" />
                                                </svg>
                                                Tambah Kategori
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
                        <button type="button" class="dropdown-item text-danger" onclick="confirmBatchDelete()">
                            Delete Selected
                        </button>
                    </div>
                </div>
                {{ $kategori->links('dev.components.pagination') }}
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
                            deleteForm.action = `{{ route('kategori-produk.destroy', '') }}/${id}`;
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
                            batchForm.action = "{{ route('kategori-produk.batch-delete') }}";
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
            });
        </script>
    @endpush
@endsection
