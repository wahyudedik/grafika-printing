@extends('layouts.user')

@section('title', 'Detail Tracking - ' . $auction->title)

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-1">{{ $auction->title }}</h3>
                            <div class="card-subtitle">Kode: {{ $auction->kode }} • Transaksi:
                                {{ $auction->transaksi->kode }}</div>
                        </div>
                        <span class="badge bg-{{ $this->getStatusColor($auction->transaksi->tracking_status) }} fs-6">
                            {{ ucfirst($auction->transaksi->tracking_status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Order Info -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Informasi Pesanan</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Vendor:</strong><br>
                                        {{ $auction->winnerVendor->name }}<br>
                                        <small class="text-muted">{{ $auction->winnerVendor->email }}</small>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Total Harga:</strong><br>
                                        Rp {{ number_format($auction->transaksi->total_harga) }}
                                    </div>
                                    @if ($auction->transaksi->ongkir > 0)
                                        <div class="mb-3">
                                            <strong>Ongkir:</strong><br>
                                            Rp {{ number_format($auction->transaksi->ongkir) }}
                                            @if ($auction->transaksi->is_cod)
                                                <span class="badge bg-info ms-2">COD</span>
                                            @endif
                                        </div>
                                    @endif
                                    @if ($auction->transaksi->no_resi)
                                        <div class="mb-3">
                                            <strong>No. Resi:</strong><br>
                                            {{ $auction->transaksi->no_resi }}
                                            @if ($auction->transaksi->kurir)
                                                <small class="text-muted">({{ $auction->transaksi->kurir }})</small>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="mb-3">
                                        <strong>Estimasi Selesai:</strong><br>
                                        {{ $auction->transaksi->estimasi_selesai ? $auction->transaksi->estimasi_selesai->format('d M Y H:i') : 'Belum ditentukan' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tracking Timeline -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Timeline Tracking</h5>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">
                                        <!-- Menunggu -->
                                        <div
                                            class="timeline-item {{ $auction->transaksi->tracking_status === 'menunggu' ? 'active' : '' }}">
                                            <div class="timeline-marker bg-secondary"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Menunggu Konfirmasi</h6>
                                                <p class="timeline-text">Pesanan diterima, menunggu konfirmasi vendor</p>
                                                <small
                                                    class="text-muted">{{ $auction->transaksi->created_at->format('d M Y H:i') }}</small>
                                            </div>
                                        </div>

                                        <!-- Diproses -->
                                        <div
                                            class="timeline-item {{ $auction->transaksi->tracking_status === 'diproses' ? 'active' : '' }}">
                                            <div class="timeline-marker bg-info"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Sedang Diproses</h6>
                                                <p class="timeline-text">Vendor sedang memproses pesanan Anda</p>
                                                @if ($auction->transaksi->diproses_at)
                                                    <small
                                                        class="text-muted">{{ $auction->transaksi->diproses_at->format('d M Y H:i') }}</small>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Dicetak -->
                                        <div
                                            class="timeline-item {{ $auction->transaksi->tracking_status === 'dicetak' ? 'active' : '' }}">
                                            <div class="timeline-marker bg-warning"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Sedang Dicetak</h6>
                                                <p class="timeline-text">Pesanan sedang dalam proses pencetakan</p>
                                                @if ($auction->transaksi->dicetak_at)
                                                    <small
                                                        class="text-muted">{{ $auction->transaksi->dicetak_at->format('d M Y H:i') }}</small>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Dikirim -->
                                        <div
                                            class="timeline-item {{ $auction->transaksi->tracking_status === 'dikirim' ? 'active' : '' }}">
                                            <div class="timeline-marker bg-primary"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Sedang Dikirim</h6>
                                                <p class="timeline-text">Pesanan sedang dalam perjalanan</p>
                                                @if ($auction->transaksi->dikirim_at)
                                                    <small
                                                        class="text-muted">{{ $auction->transaksi->dikirim_at->format('d M Y H:i') }}</small>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Selesai -->
                                        <div
                                            class="timeline-item {{ $auction->transaksi->tracking_status === 'selesai' ? 'active' : '' }}">
                                            <div class="timeline-marker bg-success"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Selesai</h6>
                                                <p class="timeline-text">Pesanan telah selesai dan diterima</p>
                                                @if ($auction->transaksi->selesai_at)
                                                    <small
                                                        class="text-muted">{{ $auction->transaksi->selesai_at->format('d M Y H:i') }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <a href="{{ route('user.tracking.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali
                                </a>
                                @if ($auction->transaksi->tracking_status === 'selesai')
                                    <a href="{{ route('vendor.ratings.create', $auction) }}" class="btn btn-warning">
                                        <i class="fas fa-star me-1"></i> Beri Rating
                                    </a>
                                @endif
                                @if ($auction->transaksi->no_resi && $auction->transaksi->kurir)
                                    <button class="btn btn-info"
                                        onclick="trackShipment('{{ $auction->transaksi->no_resi }}', '{{ $auction->transaksi->kurir }}')">
                                        <i class="fas fa-truck me-1"></i> Lacak Pengiriman
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tracking Modal -->
    <div class="modal fade" id="trackingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Pengiriman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="trackingContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 30px;
        }

        .timeline-item.active .timeline-marker {
            box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.25);
        }

        .timeline-marker {
            position: absolute;
            left: -22px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .timeline-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 3px solid #e9ecef;
        }

        .timeline-item.active .timeline-content {
            background: #e3f2fd;
            border-left-color: #2196f3;
        }

        .timeline-title {
            margin-bottom: 5px;
            font-weight: 600;
        }

        .timeline-text {
            margin-bottom: 5px;
            color: #6c757d;
        }
    </style>

    <script>
        function trackShipment(awb, courier) {
            const modal = new bootstrap.Modal(document.getElementById('trackingModal'));
            modal.show();

            // Show loading
            document.getElementById('trackingContent').innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

            // Make API call
            fetch('/api/track-shipment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        awb: awb,
                        courier: courier
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('trackingContent').innerHTML = `
                <div class="alert alert-success">
                    <h6>Status Pengiriman</h6>
                    <p>${data.data.summary || 'Data pengiriman berhasil diambil'}</p>
                </div>
            `;
                    } else {
                        document.getElementById('trackingContent').innerHTML = `
                <div class="alert alert-danger">
                    <h6>Error</h6>
                    <p>${data.message}</p>
                </div>
            `;
                    }
                })
                .catch(error => {
                    document.getElementById('trackingContent').innerHTML = `
            <div class="alert alert-danger">
                <h6>Error</h6>
                <p>Terjadi kesalahan saat melacak pengiriman</p>
            </div>
        `;
                });
        }
    </script>
@endsection

@php
    function getStatusColor($status)
    {
        switch ($status) {
            case 'menunggu':
                return 'secondary';
            case 'diproses':
                return 'info';
            case 'dicetak':
                return 'warning';
            case 'dikirim':
                return 'primary';
            case 'selesai':
                return 'success';
            default:
                return 'secondary';
        }
    }
@endphp
