@extends('dev.layouts.app')

@section('title', 'Detail Mediasi #' . $mediationRequest->id)

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col-auto">
            <div class="page-pretitle">Admin Panel</div>
            <h2 class="page-title">Detail Mediasi #{{ $mediationRequest->id }}</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('admin.mediation.index') }}" class="btn btn-outline-primary">
                Kembali
            </a>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Mediasi</h3>
                        <div class="card-actions">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-lt text-yellow-fg',
                                    'in_review' => 'bg-blue-lt text-blue-fg',
                                    'resolved' => 'bg-green-lt text-green-fg',
                                    'closed' => 'bg-gray-lt text-gray-fg',
                                ];
                            @endphp
                            <span class="badge {{ $statusColors[$mediationRequest->status] ?? 'bg-gray-lt text-gray-fg' }} me-2">
                                {{ $mediationRequest->status_label }}
                            </span>
                            @if($mediationRequest->admin_decision)
                                @php
                                    $decisionColors = [
                                        'favor_user' => 'bg-green-lt text-green-fg',
                                        'favor_vendor' => 'bg-blue-lt text-blue-fg',
                                        'compromise' => 'bg-yellow-lt text-yellow-fg',
                                        'no_fault' => 'bg-gray-lt text-gray-fg',
                                    ];
                                @endphp
                                <span class="badge {{ $decisionColors[$mediationRequest->admin_decision] ?? 'bg-gray-lt text-gray-fg' }}">
                                    {{ $mediationRequest->decision_label }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alasan</label>
                            <p>{{ $mediationRequest->reason }}</p>
                        </div>
                        @if($mediationRequest->description)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <p>{{ $mediationRequest->description }}</p>
                        </div>
                        @endif
                        @if($mediationRequest->evidence_files && count($mediationRequest->evidence_files) > 0)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Bukti</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($mediationRequest->evidence_files as $file)
                                <a href="{{ Storage::url($file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-file"></i> Bukti {{ $loop->iteration }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if($mediationRequest->resolution)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Resolusi</label>
                            <p>{{ $mediationRequest->resolution }}</p>
                        </div>
                        @endif
                        @if($mediationRequest->admin_notes)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Catatan Admin</label>
                            <p class="text-muted">{{ $mediationRequest->admin_notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Info Pelaku --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Pelaku</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label text-muted small">Pengguna</label>
                            <p class="fw-bold mb-0">{{ $mediationRequest->user->name ?? 'N/A' }}</p>
                        </div>
                        <div class="mb-2">
                            <label class="form-label text-muted small">Vendor</label>
                            <p class="fw-bold mb-0">{{ $mediationRequest->vendor->name ?? 'N/A' }}</p>
                        </div>
                        <div class="mb-2">
                            <label class="form-label text-muted small">Diminta Oleh</label>
                            <p class="fw-bold mb-0">{{ $mediationRequest->requestedBy->name ?? 'N/A' }}</p>
                        </div>
                        @if($mediationRequest->resolvedBy)
                        <div class="mb-2">
                            <label class="form-label text-muted small">Diselesaikan Oleh</label>
                            <p class="fw-bold mb-0">{{ $mediationRequest->resolvedBy->name }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Info Lelang --}}
                @if($mediationRequest->auction)
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Lelang</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label text-muted small">Judul</label>
                            <p class="fw-bold mb-0">{{ $mediationRequest->auction->title ?? 'N/A' }}</p>
                        </div>
                        <div class="mb-2">
                            <label class="form-label text-muted small">Status</label>
                            <p class="mb-0">{{ $mediationRequest->auction->status ?? 'N/A' }}</p>
                        </div>
                        <a href="{{ route('admin.auctions.show', $mediationRequest->auction) }}" class="btn btn-sm btn-outline-primary w-100 mt-2">
                            Lihat Lelang
                        </a>
                    </div>
                </div>
                @endif

                {{-- Info Keputusan --}}
                @if($mediationRequest->compensation_amount || $mediationRequest->penalty_amount)
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Keuangan</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label text-muted small">Kompensasi</label>
                            <p class="fw-bold mb-0">Rp {{ number_format($mediationRequest->compensation_amount ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="mb-2">
                            <label class="form-label text-muted small">Denda</label>
                            <p class="fw-bold mb-0">Rp {{ number_format($mediationRequest->penalty_amount ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Timeline --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Timeline</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-start mb-3">
                                <div class="badge bg-yellow-lt text-yellow-fg me-3 mt-1">
                                    <i class="ti ti-plus"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">Dibuat</div>
                                    <div class="text-muted small">{{ $mediationRequest->created_at->format('d M Y H:i') }}</div>
                                </div>
                            </div>
                            @if($mediationRequest->status !== 'pending')
                            <div class="d-flex align-items-start mb-3">
                                <div class="badge bg-blue-lt text-blue-fg me-3 mt-1">
                                    <i class="ti ti-eye"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">Review Dimulai</div>
                                    <div class="text-muted small">-</div>
                                </div>
                            </div>
                            @endif
                            @if($mediationRequest->resolved_at)
                            <div class="d-flex align-items-start">
                                <div class="badge bg-green-lt text-green-fg me-3 mt-1">
                                    <i class="ti ti-check"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">Diselesaikan</div>
                                    <div class="text-muted small">{{ $mediationRequest->resolved_at->format('d M Y H:i') }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        @if($mediationRequest->status === 'pending')
        <div class="mt-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Aksi</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <form action="{{ route('admin.mediation.start-review', $mediationRequest) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-eye"></i> Mulai Review
                            </button>
                        </form>
                        <form action="{{ route('admin.mediation.close', $mediationRequest) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Tutup mediasi ini?')">
                                <i class="ti ti-x"></i> Tutup
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($mediationRequest->status === 'in_review')
        <div class="mt-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Keputusan Mediasi</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.mediation.resolve', $mediationRequest) }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Keputusan <span class="text-danger">*</span></label>
                                <select name="admin_decision" class="form-select" required>
                                    <option value="">Pilih Keputusan</option>
                                    <option value="favor_user">Favor Pengguna</option>
                                    <option value="favor_vendor">Favor Vendor</option>
                                    <option value="compromise">Kompromi</option>
                                    <option value="no_fault">Tanpa Kesalahan</option>
                                </select>
                                @error('admin_decision') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kompensasi (Rp)</label>
                                <input type="number" name="compensation_amount" class="form-control" min="0" step="1000" value="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Denda (Rp)</label>
                                <input type="number" name="penalty_amount" class="form-control" min="0" step="1000" value="0">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Resolusi <span class="text-danger">*</span></label>
                                <textarea name="resolution" class="form-control" rows="3" required placeholder="Jelaskan resolusi mediasi..."></textarea>
                                @error('resolution') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Catatan Admin</label>
                                <textarea name="admin_notes" class="form-control" rows="2" placeholder="Catatan internal..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success" onclick="return confirm('Selesaikan mediasi dengan keputusan ini?')">
                                    <i class="ti ti-check"></i> Selesaikan Mediasi
                                </button>
                                <button type="button" class="btn btn-outline-danger ms-2" onclick="document.querySelector('[name=admin_decision]').value=''; this.form.action='{{ route('admin.mediation.close', $mediationRequest) }}'; this.form.submit();">
                                    <i class="ti ti-x"></i> Tutup
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
