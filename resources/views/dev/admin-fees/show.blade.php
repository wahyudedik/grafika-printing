@extends('dev.layouts.app')

@section('title', 'Detail Pengaturan Biaya Admin')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $adminFee->name }}</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.admin-fees.edit', $adminFee) }}" class="btn btn-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                            </svg>
                            Edit
                        </a>
                        <a href="{{ route('admin.admin-fees.index') }}" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M18 6l-12 12" />
                                <path d="M6 6l12 12" />
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Pengaturan</label>
                                <p class="form-control-plaintext">{{ $adminFee->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Kategori</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-blue-lt">{{ $adminFee->category }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <p class="form-control-plaintext">{{ $adminFee->description ?: '-' }}</p>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tipe Biaya</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-{{ $adminFee->type === 'fixed' ? 'green' : 'orange' }}-lt">
                                        {{ $adminFee->type === 'fixed' ? 'Tetap' : 'Persentase' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nilai</label>
                                <p class="form-control-plaintext">
                                    @if ($adminFee->type === 'fixed')
                                        Rp {{ number_format($adminFee->value, 0, ',', '.') }}
                                    @else
                                        {{ $adminFee->value }}%
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-{{ $adminFee->is_active ? 'green' : 'red' }}-lt">
                                        {{ $adminFee->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jumlah Minimum</label>
                                <p class="form-control-plaintext">
                                    Rp {{ number_format($adminFee->minimum_amount, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jumlah Maksimum</label>
                                <p class="form-control-plaintext">
                                    {{ $adminFee->maximum_amount ? 'Rp ' . number_format($adminFee->maximum_amount, 0, ',', '.') : 'Tidak terbatas' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Efektif Dari</label>
                                <p class="form-control-plaintext">
                                    {{ $adminFee->effective_from ? $adminFee->effective_from->format('d/m/Y H:i') : 'Sekarang' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Efektif Sampai</label>
                                <p class="form-control-plaintext">
                                    {{ $adminFee->effective_until ? $adminFee->effective_until->format('d/m/Y H:i') : 'Tidak ada batas waktu' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Dibuat Oleh</label>
                                <p class="form-control-plaintext">{{ $adminFee->createdBy->name ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Diperbarui Oleh</label>
                                <p class="form-control-plaintext">{{ $adminFee->updatedBy->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Dibuat Pada</label>
                                <p class="form-control-plaintext">{{ $adminFee->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Diperbarui Pada</label>
                                <p class="form-control-plaintext">{{ $adminFee->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    @if ($adminFee->conditions)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kondisi Tambahan</label>
                            <div class="form-control-plaintext">
                                <pre class="bg-light p-3 rounded">{{ json_encode($adminFee->conditions, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
