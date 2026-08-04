@extends('layouts.app')

@section('title', 'Detail Konfirmasi Pengiriman')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">Konfirmasi Pengiriman</h4>
                    <span class="text-muted">Detail status pengiriman pesanan Anda</span>
                </div>
                <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-sm">
                    <i class="ti ti-arrow-left"></i> Kembali
                </a>
            </div>

            {{-- Status Badge --}}
            <div class="card mb-3">
                <div class="card-body text-center py-4">
                    @php
                        $statusConfig = [
                            'pending' => ['icon' => 'ti-clock', 'color' => 'warning', 'label' => 'Menunggu Konfirmasi'],
                            'delivered' => ['icon' => 'ti-package', 'color' => 'info', 'label' => 'Barang Diterima'],
                            'confirmed' => ['icon' => 'ti-check', 'color' => 'success', 'label' => 'Dikonfirmasi'],
                            'disputed' => ['icon' => 'ti-alert-triangle', 'color' => 'danger', 'label' => 'Ada Masalah'],
                            'resolved' => ['icon' => 'ti-check-circle', 'color' => 'success', 'label' => 'Selesai'],
                        ];
                        $config = $statusConfig[$confirmation->delivery_status] ?? ['icon' => 'ti-help', 'color' => 'secondary', 'label' => 'Unknown'];
                    @endphp
                    <i class="ti {{ $config['icon'] }} icon-lg text-{{ $config['color'] }} mb-2" style="font-size: 2.5rem;"></i>
                    <h5 class="text-{{ $config['color'] }}">{{ $config['label'] }}</h5>
                    @if($confirmation->delivery_date)
                        <span class="text-muted">{{ $confirmation->delivery_date->format('d M Y H:i') }}</span>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    {{-- Info Pengiriman --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Info Pengiriman</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <label class="text-muted small">Tanggal Pengiriman</label>
                                <p class="fw-bold mb-0">{{ $confirmation->delivery_date ? $confirmation->delivery_date->format('d M Y H:i') : '-' }}</p>
                            </div>
                            @if($confirmation->delivery_notes)
                            <div class="mb-2">
                                <label class="text-muted small">Catatan</label>
                                <p class="mb-0">{{ $confirmation->delivery_notes }}</p>
                            </div>
                            @endif
                            @if($confirmation->confirmed_at)
                            <div class="mb-2">
                                <label class="text-muted small">Dikonfirmasi Pada</label>
                                <p class="fw-bold mb-0">{{ $confirmation->confirmed_at->format('d M Y H:i') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    {{-- Info Vendor --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Vendor</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <label class="text-muted small">Nama Vendor</label>
                                <p class="fw-bold mb-0">{{ $confirmation->vendor->name ?? 'N/A' }}</p>
                            </div>
                            @if($confirmation->auction)
                            <div class="mb-2">
                                <label class="text-muted small">Lelang</label>
                                <p class="mb-0">{{ $confirmation->auction->title ?? 'N/A' }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rating & Feedback --}}
            @if($confirmation->user_rating || $confirmation->user_feedback)
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Rating & Feedback</h3>
                </div>
                <div class="card-body">
                    @if($confirmation->user_rating)
                    <div class="mb-3">
                        <label class="text-muted small">Rating</label>
                        <div class="d-flex align-items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="ti ti-star {{ $i <= $confirmation->user_rating ? 'text-yellow' : 'text-muted' }}" style="font-size: 1.5rem;"></i>
                            @endfor
                            <span class="ms-2 fw-bold">{{ $confirmation->user_rating }}/5</span>
                        </div>
                    </div>
                    @endif
                    @if($confirmation->user_feedback)
                    <div class="mb-0">
                        <label class="text-muted small">Feedback</label>
                        <p class="mb-0">{{ $confirmation->user_feedback }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Foto Bukti --}}
            @if($confirmation->photos && count($confirmation->photos) > 0)
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Foto Bukti</h3>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($confirmation->photos as $photo)
                        <div class="col-4">
                            <a href="{{ Storage::url($photo) }}" target="_blank">
                                <img src="{{ Storage::url($photo) }}" alt="Bukti {{ $loop->iteration }}" class="img-fluid rounded border" style="height: 150px; object-fit: cover; width: 100%;">
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Dispute Info --}}
            @if($confirmation->hasDispute())
            <div class="card mb-3 border-danger">
                <div class="card-header bg-red-lt">
                    <h3 class="card-title text-red">
                        <i class="ti ti-alert-triangle"></i> Sengketa
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="text-muted small">Alasan Sengketa</label>
                        <p class="fw-bold mb-0">{{ $confirmation->dispute_reason ?? '-' }}</p>
                    </div>
                    @if($confirmation->dispute_resolved_at)
                    <div class="mb-0">
                        <label class="text-muted small">Diselesaikan Pada</label>
                        <p class="mb-0">{{ $confirmation->dispute_resolved_at->format('d M Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Action: Request Dispute --}}
            @if(in_array($confirmation->delivery_status, ['delivered', 'confirmed']) && !$confirmation->hasDispute())
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Ada Masalah?</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Jika barang yang diterima tidak sesuai atau ada masalah lainnya, Anda bisa mengajukan sengketa.</p>
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#disputeModal">
                        <i class="ti ti-alert-triangle"></i> Ajukan Sengketa
                    </button>
                </div>
            </div>

            {{-- Dispute Modal --}}
            <div class="modal fade" id="disputeModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('user.delivery-confirmation.resolve-dispute', $confirmation) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Ajukan Sengketa</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Alasan Sengketa <span class="text-danger">*</span></label>
                                    <textarea name="dispute_reason" class="form-control" rows="4" required placeholder="Jelaskan masalah yang Anda hadapi..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">Kirim Sengketa</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
