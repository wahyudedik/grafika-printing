@extends('dev.layouts.app')

@section('title', 'Detail Pengaturan Biaya Admin')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Pengaturan
                    </div>
                    <h2 class="page-title">
                        Detail Pengaturan Biaya Admin
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.admin-fees.index') }}" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 6l6 6l-6 6" />
                            </svg>
                            Kembali
                        </a>
                        <a href="{{ route('admin.admin-fees.edit', $adminFee) }}" class="btn btn-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                <path d="M16 5l3 3" />
                            </svg>
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Pengaturan</h3>
                            <div class="card-actions">
                                <span class="badge bg-{{ $adminFee->is_active ? 'green' : 'red' }} fs-6">
                                    {{ $adminFee->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Pengaturan</label>
                                        <div class="form-control-plaintext">{{ $adminFee->name }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Kategori</label>
                                        <div class="form-control-plaintext">
                                            <span class="badge bg-secondary">{{ ucfirst($adminFee->category) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($adminFee->description)
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <div class="form-control-plaintext">{{ $adminFee->description }}</div>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tipe Biaya</label>
                                        <div class="form-control-plaintext">
                                            <span
                                                class="badge bg-{{ $adminFee->type === 'percentage' ? 'blue' : 'green' }}">
                                                {{ $adminFee->type === 'percentage' ? 'Persentase' : 'Tetap' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nilai Biaya</label>
                                        <div class="form-control-plaintext">
                                            @if ($adminFee->type === 'percentage')
                                                <strong>{{ number_format($adminFee->value, 2) }}%</strong>
                                            @else
                                                <strong>Rp {{ number_format($adminFee->value, 0, ',', '.') }}</strong>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Jumlah Minimum</label>
                                        <div class="form-control-plaintext">
                                            @if ($adminFee->minimum_amount > 0)
                                                Rp {{ number_format($adminFee->minimum_amount, 0, ',', '.') }}
                                            @else
                                                <span class="text-muted">Tidak ada batasan minimum</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Jumlah Maksimum</label>
                                        <div class="form-control-plaintext">
                                            @if ($adminFee->maximum_amount > 0)
                                                Rp {{ number_format($adminFee->maximum_amount, 0, ',', '.') }}
                                            @else
                                                <span class="text-muted">Tidak ada batasan maksimum</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Berlaku Dari</label>
                                        <div class="form-control-plaintext">
                                            @if ($adminFee->effective_from)
                                                {{ $adminFee->effective_from->format('d F Y') }}
                                            @else
                                                <span class="text-muted">Segera</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Berlaku Sampai</label>
                                        <div class="form-control-plaintext">
                                            @if ($adminFee->effective_until)
                                                {{ $adminFee->effective_until->format('d F Y') }}
                                            @else
                                                <span class="text-muted">Tidak terbatas</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($adminFee->conditions)
                                <div class="mb-3">
                                    <label class="form-label">Kondisi Tambahan</label>
                                    <div class="form-control-plaintext">
                                        <pre class="text-muted">{{ json_encode($adminFee->conditions, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Sistem</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Dibuat Oleh</label>
                                        <div class="form-control-plaintext">
                                            @if ($adminFee->createdBy)
                                                {{ $adminFee->createdBy->name }} ({{ $adminFee->createdBy->email }})
                                            @else
                                                <span class="text-muted">Tidak diketahui</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Dibuat Pada</label>
                                        <div class="form-control-plaintext">
                                            {{ $adminFee->created_at->format('d F Y H:i:s') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($adminFee->updatedBy)
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Diperbarui Oleh</label>
                                            <div class="form-control-plaintext">
                                                {{ $adminFee->updatedBy->name }} ({{ $adminFee->updatedBy->email }})
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Diperbarui Pada</label>
                                            <div class="form-control-plaintext">
                                                {{ $adminFee->updated_at->format('d F Y H:i:s') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Aksi</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <form action="{{ route('admin.admin-fees.toggle', $adminFee) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="btn btn-{{ $adminFee->is_active ? 'danger' : 'success' }} w-100"
                                            onclick="return confirm('{{ $adminFee->is_active ? 'Nonaktifkan' : 'Aktifkan' }} pengaturan ini?')">
                                            @if ($adminFee->is_active)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M5 12l5 5l10 -10" />
                                                </svg>
                                                Nonaktifkan
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M5 12l5 5l10 -10" />
                                                </svg>
                                                Aktifkan
                                            @endif
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form action="{{ route('admin.admin-fees.destroy', $adminFee) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger w-100"
                                            onclick="return confirm('Hapus pengaturan ini? Tindakan ini tidak dapat dibatalkan.')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 7l16 0" />
                                                <path d="M10 11l0 6" />
                                                <path d="M14 11l0 6" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
