@extends('dev.layouts.app')

@section('title', 'Pengaturan Biaya Admin')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Pengaturan
                    </div>
                    <h2 class="page-title">
                        Biaya Admin
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.admin-fees.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            Tambah Pengaturan
                        </a>
                        <a href="{{ route('admin.admin-fees.create') }}" class="btn btn-primary d-sm-none btn-icon"
                            aria-label="Tambah Pengaturan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 12l5 5l10 -10" />
                            </svg>
                        </div>
                        <div>
                            {{ session('success') }}
                        </div>
                    </div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            @endif

            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Daftar Pengaturan Biaya Admin</h3>
                            <div class="card-actions">
                                <a href="{{ route('admin.admin-fees.preview') }}" class="btn btn-outline-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                        <path d="M21 21l-6 -6" />
                                    </svg>
                                    Preview Biaya
                                </a>
                                <a href="{{ route('admin.admin-fees.transactions') }}" class="btn btn-outline-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                        <path d="M12 7v5l3 3" />
                                    </svg>
                                    Transaksi
                                </a>
                                <a href="{{ route('admin.admin-fees.statistics') }}" class="btn btn-outline-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 3v18h18" />
                                        <path d="M18.7 17l-5.1-5.2l-2.8 3.3l-2.2-2.2l-6.6 8" />
                                    </svg>
                                    Statistik
                                </a>
                                <a href="{{ route('admin.admin-fees.create') }}" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <line x1="12" y1="5" x2="12" y2="19" />
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                    Tambah Pengaturan
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($settings->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Tipe</th>
                                                <th>Nilai</th>
                                                <th>Kategori</th>
                                                <th>Status</th>
                                                <th>Berlaku</th>
                                                <th class="w-1">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($settings as $setting)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex py-1 align-items-center">
                                                            <div class="flex-fill">
                                                                <div class="font-weight-medium">{{ $setting->name }}</div>
                                                                <div class="text-muted">
                                                                    <small>{{ Str::limit($setting->description, 50) }}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $setting->type === 'percentage' ? 'blue' : 'green' }}">
                                                            {{ $setting->type === 'percentage' ? 'Persentase' : 'Tetap' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($setting->type === 'percentage')
                                                            {{ number_format($setting->value, 2) }}%
                                                        @else
                                                            Rp {{ number_format($setting->value, 0, ',', '.') }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-secondary">{{ ucfirst($setting->category) }}</span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $setting->is_active ? 'green' : 'red' }}">
                                                            {{ $setting->is_active ? 'Aktif' : 'Nonaktif' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="text-muted">
                                                            @if ($setting->effective_from)
                                                                <small>Dari:
                                                                    {{ $setting->effective_from->format('d/m/Y') }}</small><br>
                                                            @endif
                                                            @if ($setting->effective_until)
                                                                <small>Sampai:
                                                                    {{ $setting->effective_until->format('d/m/Y') }}</small>
                                                            @else
                                                                <small>Tidak terbatas</small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('admin.admin-fees.show', $setting) }}"
                                                                class="btn btn-outline-primary btn-sm">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon"
                                                                    width="24" height="24" viewBox="0 0 24 24"
                                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                        fill="none" />
                                                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                                    <path
                                                                        d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                                </svg>
                                                            </a>
                                                            <a href="{{ route('admin.admin-fees.edit', $setting) }}"
                                                                class="btn btn-outline-warning btn-sm">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon"
                                                                    width="24" height="24" viewBox="0 0 24 24"
                                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                        fill="none" />
                                                                    <path
                                                                        d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                                    <path
                                                                        d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                                    <path d="M16 5l3 3" />
                                                                </svg>
                                                            </a>
                                                            <form
                                                                action="{{ route('admin.admin-fees.toggle', $setting) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit"
                                                                    class="btn btn-outline-{{ $setting->is_active ? 'danger' : 'success' }} btn-sm"
                                                                    onclick="return confirm('{{ $setting->is_active ? 'Nonaktifkan' : 'Aktifkan' }} pengaturan ini?')">
                                                                    @if ($setting->is_active)
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            class="icon" width="24" height="24"
                                                                            viewBox="0 0 24 24" stroke-width="2"
                                                                            stroke="currentColor" fill="none"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z"
                                                                                fill="none" />
                                                                            <path d="M5 12l5 5l10 -10" />
                                                                        </svg>
                                                                    @else
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            class="icon" width="24" height="24"
                                                                            viewBox="0 0 24 24" stroke-width="2"
                                                                            stroke="currentColor" fill="none"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z"
                                                                                fill="none" />
                                                                            <path d="M5 12l5 5l10 -10" />
                                                                        </svg>
                                                                    @endif
                                                                </button>
                                                            </form>
                                                            <form
                                                                action="{{ route('admin.admin-fees.destroy', $setting) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-outline-danger btn-sm"
                                                                    onclick="return confirm('Hapus pengaturan ini?')">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon"
                                                                        width="24" height="24" viewBox="0 0 24 24"
                                                                        stroke-width="2" stroke="currentColor"
                                                                        fill="none" stroke-linecap="round"
                                                                        stroke-linejoin="round">
                                                                        <path stroke="none" d="M0 0h24v24H0z"
                                                                            fill="none" />
                                                                        <path d="M4 7l16 0" />
                                                                        <path d="M10 11l0 6" />
                                                                        <path d="M14 11l0 6" />
                                                                        <path
                                                                            d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                                        <path
                                                                            d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
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
                            @else
                                <div class="empty">
                                    <div class="empty-img">
                                        <img src="{{ asset('static/illustrations/undraw_printing_invoices_5r4r.svg') }}"
                                            height="128" alt="">
                                    </div>
                                    <p class="empty-title">Belum ada pengaturan biaya admin</p>
                                    <p class="empty-subtitle text-muted">
                                        Mulai dengan membuat pengaturan biaya admin pertama Anda.
                                    </p>
                                    <div class="empty-action">
                                        <a href="{{ route('admin.admin-fees.create') }}" class="btn btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <line x1="12" y1="5" x2="12" y2="19" />
                                                <line x1="5" y1="12" x2="19" y2="12" />
                                            </svg>
                                            Tambah Pengaturan
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
