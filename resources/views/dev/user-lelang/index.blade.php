@extends('dev.layouts.app')

@section('title', 'Manajemen User Lelang')

@section('content')
<div class="row row-deck row-cards">
    <!-- Statistics Cards -->
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="subheader">Total User Lelang</div>
                    <span class="badge bg-primary-lt">{{ $stats['total'] }}</span>
                </div>
                <div class="h1 mb-0">{{ $stats['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="subheader">Aktif</div>
                    <span class="badge bg-success-lt">{{ $stats['active'] }}</span>
                </div>
                <div class="h1 mb-0 text-success">{{ $stats['active'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="subheader">Ditangguhkan</div>
                    <span class="badge bg-danger-lt">{{ $stats['suspended'] }}</span>
                </div>
                <div class="h1 mb-0 text-danger">{{ $stats['suspended'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="subheader">Terverifikasi</div>
                    <span class="badge bg-info-lt">{{ $stats['verified'] }}</span>
                </div>
                <div class="h1 mb-0 text-info">{{ $stats['verified'] }}</div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar User Lelang</h3>
                <div class="card-actions d-flex gap-2">
                    <!-- Search -->
                    <form method="GET" class="d-flex gap-2">
                        <div class="input-icon">
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Cari nama, email, atau perusahaan..."
                                value="{{ request('search') }}">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                    <path d="M21 21l-6 -6" />
                                </svg>
                            </span>
                        </div>
                        <select name="status" class="form-select form-select-sm" style="width: 150px;">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        @if(request('search') || request('status'))
                            <a href="{{ route('admin.user-lelang.index') }}" class="btn btn-sm btn-secondary">Reset</a>
                        @endif
                    </form>
                    <a href="{{ route('admin.user-lelang.create') }}" class="btn btn-sm btn-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        <span class="d-none d-sm-inline">Tambah User Lelang</span>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if($profiles->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th class="w-1">No</th>
                                    <th>User</th>
                                    <th>Perusahaan</th>
                                    <th>Status</th>
                                    <th>Verifikasi</th>
                                    <th>Lelang</th>
                                    <th>Menang</th>
                                    <th>Total Belanja</th>
                                    <th class="w-1">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($profiles as $index => $profile)
                                    <tr>
                                        <td class="text-muted">{{ ($profiles->currentPage() - 1) * $profiles->perPage() + $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-sm bg-primary-lt me-2">
                                                    {{ strtoupper(substr($profile->user->name ?? 'U', 0, 1)) }}
                                                </span>
                                                <div>
                                                    <div class="fw-bold">{{ $profile->user->name ?? '-' }}</div>
                                                    <small class="text-muted">{{ $profile->user->email ?? '-' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $profile->company_name ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $profile->status_color }}">
                                                {{ $profile->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($profile->is_verified)
                                                <span class="badge bg-success-lt">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M5 12l5 5l10 -10" />
                                                    </svg>
                                                    Terverifikasi
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-lt">Belum Verifikasi</span>
                                            @endif
                                        </td>
                                        <td>{{ $profile->total_auctions }}</td>
                                        <td>{{ $profile->total_won }}</td>
                                        <td>Rp {{ number_format($profile->total_spent, 0, ',', '.') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.user-lelang.show', $profile) }}"
                                                    class="btn btn-ghost-primary" title="Lihat Detail">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('admin.user-lelang.edit', $profile) }}"
                                                    class="btn btn-ghost-warning" title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5 -5.5Z" />
                                                        <path d="M15 5l4 4" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty">
                        <div class="empty-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M8 12l2 2l4 -4" />
                            </svg>
                        </div>
                        <p class="empty-title">Belum ada User Lelang</p>
                        <p class="empty-subtitle text-secondary">
                            Tambahkan user lelang baru untuk mulai mengelola peserta lelang.
                        </p>
                        <div class="empty-action">
                            <a href="{{ route('admin.user-lelang.create') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 5l0 14" />
                                    <path d="M5 12l14 0" />
                                </svg>
                                Tambah User Lelang
                            </a>
                        </div>
                    </div>
                @endif
            </div>
            @if($profiles->hasPages())
                <div class="card-footer d-flex align-items-center">
                    <div class="text-muted text-sm">
                        Menampilkan {{ $profiles->firstItem() }}-{{ $profiles->lastItem() }} dari {{ $profiles->total() }} data
                    </div>
                    <div class="ms-auto">
                        {{ $profiles->withQueryString()->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
