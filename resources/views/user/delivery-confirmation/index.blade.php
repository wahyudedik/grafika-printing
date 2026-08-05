@extends('layouts.user')

@section('title', 'Konfirmasi Pengiriman')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">Konfirmasi Pengiriman</h4>
                    <span class="text-muted">Daftar semua konfirmasi pengiriman pesanan Anda</span>
                </div>
            </div>

            {{-- Stats Summary --}}
            @php
                $totalConfirmations = $confirmations->total();
                $pendingCount = $confirmations->getCollection()->where('delivery_status', 'pending')->count();
                $deliveredCount = $confirmations->getCollection()->where('delivery_status', 'delivered')->count();
                $confirmedCount = $confirmations->getCollection()->where('delivery_status', 'confirmed')->count();
            @endphp
            <div class="row mb-4">
                <div class="col-6 col-md-3">
                    <div class="card card-sm">
                        <div class="card-body text-center">
                            <div class="h3 mb-0">{{ $totalConfirmations }}</div>
                            <div class="text-muted small">Total</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card card-sm">
                        <div class="card-body text-center">
                            <div class="h3 mb-0 text-warning">{{ $pendingCount }}</div>
                            <div class="text-muted small">Menunggu</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card card-sm">
                        <div class="card-body text-center">
                            <div class="h3 mb-0 text-info">{{ $deliveredCount }}</div>
                            <div class="text-muted small">Diterima</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card card-sm">
                        <div class="card-body text-center">
                            <div class="h3 mb-0 text-success">{{ $confirmedCount }}</div>
                            <div class="text-muted small">Dikonfirmasi</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Confirmation List --}}
            @if($confirmations->count() > 0)
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Lelang</th>
                                        <th>Vendor</th>
                                        <th class="d-none d-md-table-cell">Tanggal</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($confirmations as $confirmation)
                                        @php
                                            $statusConfig = [
                                                'pending' => ['color' => 'warning', 'label' => 'Menunggu'],
                                                'delivered' => ['color' => 'info', 'label' => 'Diterima'],
                                                'confirmed' => ['color' => 'success', 'label' => 'Dikonfirmasi'],
                                                'disputed' => ['color' => 'danger', 'label' => 'Masalah'],
                                                'resolved' => ['color' => 'success', 'label' => 'Selesai'],
                                            ];
                                            $config = $statusConfig[$confirmation->delivery_status] ?? ['color' => 'secondary', 'label' => 'Unknown'];
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $confirmation->auction->title ?? 'N/A' }}</div>
                                                <small class="text-muted d-md-none">{{ $confirmation->created_at->format('d M Y') }}</small>
                                            </td>
                                            <td>{{ $confirmation->vendor->name ?? 'N/A' }}</td>
                                            <td class="d-none d-md-table-cell">
                                                <span class="text-muted">{{ $confirmation->delivery_date ? $confirmation->delivery_date->format('d M Y H:i') : '-' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $config['color'] }}">{{ $config['label'] }}</span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('user.delivery-confirmation.show', $confirmation) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="ti ti-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $confirmations->links() }}
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="ti ti-package text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5 class="text-muted">Belum ada konfirmasi pengiriman</h5>
                        <p class="text-muted">Konfirmasi pengiriman akan muncul di sini setelah Anda menerima barang dari lelang yang sudah dibayar.</p>
                        <a href="{{ route('user.auctions.index') }}" class="btn btn-primary mt-2">
                            <i class="ti ti-list"></i> Lihat Lelang Saya
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
