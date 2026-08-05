@extends('layouts.user')

@section('title', 'Detail Tracking Pesanan')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col-auto">
            <h2 class="page-title">Detail Tracking Pesanan</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('user.orders.index') }}" class="btn btn-outline-primary">
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
                        <h3 class="card-title">Pesanan #{{ $orderTracking->order_code ?? $orderTracking->id }}</h3>
                    </div>
                    <div class="card-body">
                        <!-- Status Timeline -->
                        <div class="mb-4">
                            <h6>Status Pesanan</h6>
                            <div class="timeline">
                                @php
                                    $statuses = [
                                        'pending' => 'Menunggu Konfirmasi',
                                        'confirmed' => 'Dikonfirmasi Vendor',
                                        'processing' => 'Sedang Diproses',
                                        'shipped' => 'Dikirim',
                                        'delivered' => 'Diterima',
                                        'completed' => 'Selesai'
                                    ];
                                    $currentStatusIndex = array_search($orderTracking->status, array_keys($statuses));
                                @endphp

                                @foreach($statuses as $key => $label)
                                <div class="timeline-item {{ $currentStatusIndex >= array_search($key, array_keys($statuses)) ? 'active' : '' }}">
                                    <div class="timeline-point {{ $orderTracking->status === $key ? 'bg-primary' : ($currentStatusIndex > array_search($key, array_keys($statuses)) ? 'bg-success' : 'bg-secondary') }}"></div>
                                    <div class="timeline-content">
                                        <div class="fw-bold {{ $orderTracking->status === $key ? 'text-primary' : '' }}">{{ $label }}</div>
                                        @if($orderTracking->status === $key)
                                            <div class="text-muted small">{{ $orderTracking->updated_at->format('d M Y H:i') }}</div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <hr>

                        <!-- Order Details -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="text-muted small">Lelang</div>
                                <div>
                                    <a href="{{ route('user.auctions.show', $orderTracking->auction) }}">
                                        {{ $orderTracking->auction->title ?? '-' }}
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Vendor</div>
                                <div>{{ $orderTracking->vendor->name ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="text-muted small">Nomor Resi</div>
                                <div>{{ $orderTracking->tracking_number ?? 'Belum tersedia' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Estimasi Pengiriman</div>
                                <div>{{ $orderTracking->estimated_delivery ? $orderTracking->estimated_delivery->format('d M Y') : 'Belum tersedia' }}</div>
                            </div>
                        </div>

                        @if($orderTracking->notes)
                        <div class="mb-3">
                            <div class="text-muted small">Catatan</div>
                            <div>{{ $orderTracking->notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Action Card -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Aksi</h3>
                    </div>
                    <div class="card-body">
                        @if($orderTracking->status === 'shipped' || $orderTracking->status === 'delivered')
                        <form action="{{ route('user.orders.confirm-delivery', $orderTracking) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Foto Bukti Terima</label>
                                <input type="file" class="form-control" name="delivery_photo" accept="image/*" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <div class="rating">
                                    @for($i = 1; $i <= 5; $i++)
                                    <label class="rating-star">
                                        <input type="radio" name="rating" value="{{ $i }}" {{ $i === 5 ? 'checked' : '' }}>
                                        <span class="star">&#9733;</span>
                                    </label>
                                    @endfor
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Feedback (Opsional)</label>
                                <textarea class="form-control" name="feedback" rows="3" placeholder="Berikan feedback tentang pesanan Anda"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                Konfirmasi Penerimaan
                            </button>
                        </form>
                        @else
                        <div class="text-muted text-center">
                            <p>Aksi akan tersedia setelah pesanan dikirim</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Mediation Request -->
                @if(in_array($orderTracking->status, ['shipped', 'delivered', 'completed']))
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ajukan Mediasi</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('user.orders.mediation', $orderTracking) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Alasan</label>
                                <input type="text" class="form-control" name="reason" required placeholder="Contoh: Barang tidak sesuai">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="description" rows="3" required placeholder="Jelaskan masalah Anda"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Bukti (Opsional, max 5 file)</label>
                                <input type="file" class="form-control" name="evidence_files[]" multiple accept="image/*,.pdf">
                            </div>
                            <button type="submit" class="btn btn-warning w-100">
                                Ajukan Mediasi
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 2rem;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    .timeline-point {
        position: absolute;
        left: -2rem;
        top: 0.25rem;
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
    .timeline-item.active .timeline-point {
        width: 16px;
        height: 16px;
        left: -2.125rem;
    }
    .rating-star {
        cursor: pointer;
        font-size: 1.5rem;
        color: #ccc;
    }
    .rating-star input {
        display: none;
    }
    .rating-star:hover,
    .rating-star:hover ~ .rating-star,
    .rating-star input:checked ~ .rating-star {
        color: #ffc107;
    }
    .rating-star:has(input:checked) {
        color: #ffc107;
    }
</style>
@endpush
@endsection
