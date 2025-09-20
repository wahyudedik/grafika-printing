@extends('layouts.user')

@section('title', 'Tracking Pesanan')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tracking Pesanan Lelang</h3>
                    <div class="card-subtitle">Lacak status pesanan dari lelang yang Anda menangkan</div>
                </div>
                <div class="card-body">
                    @if ($auctions->count() > 0)
                        <div class="row">
                            @foreach ($auctions as $auction)
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="card-title mb-0">{{ $auction->title }}</h5>
                                                <span
                                                    class="badge bg-{{ getStatusColor($auction->transaksi->tracking_status ?? 'menunggu') }}">
                                                    {{ ucfirst($auction->transaksi->tracking_status ?? 'menunggu') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mb-2"><strong>Vendor:</strong>
                                                        {{ $auction->winnerVendor->name }}</p>
                                                    <p class="mb-2"><strong>Kode Lelang:</strong> {{ $auction->kode }}</p>
                                                    <p class="mb-2"><strong>Kode Transaksi:</strong>
                                                        {{ $auction->transaksi->kode }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-2"><strong>Total Harga:</strong> Rp
                                                        {{ number_format($auction->transaksi->total_harga) }}</p>
                                                    @if ($auction->transaksi->ongkir > 0)
                                                        <p class="mb-2"><strong>Ongkir:</strong> Rp
                                                            {{ number_format($auction->transaksi->ongkir) }}</p>
                                                    @endif
                                                    @if ($auction->transaksi->no_resi)
                                                        <p class="mb-2"><strong>No. Resi:</strong>
                                                            {{ $auction->transaksi->no_resi }}</p>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Progress Bar -->
                                            <div class="mt-3">
                                                <div class="progress mb-2" style="height: 8px;">
                                                    <div class="progress-bar bg-{{ getStatusColor($auction->transaksi->tracking_status ?? 'menunggu') }}"
                                                        style="width: {{ getProgressPercentage($auction->transaksi->tracking_status ?? 'menunggu') }}%">
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between small text-muted">
                                                    <span>Menunggu</span>
                                                    <span>Diproses</span>
                                                    <span>Dicetak</span>
                                                    <span>Dikirim</span>
                                                    <span>Selesai</span>
                                                </div>
                                            </div>

                                            <div class="mt-3">
                                                <a href="{{ route('user.tracking.show', $auction) }}"
                                                    class="btn btn-primary btn-sm">
                                                    Detail Tracking
                                                </a>
                                                @if ($auction->transaksi->tracking_status === 'selesai')
                                                    <a href="{{ route('vendor.ratings.create', $auction) }}"
                                                        class="btn btn-warning btn-sm">
                                                        Beri Rating
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="empty">
                                <div class="empty-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="128" height="128"
                                        viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                        <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                    </svg>
                                </div>
                                <p class="empty-title">Belum ada pesanan untuk dilacak</p>
                                <p class="empty-subtitle text-muted">
                                    Pesanan akan muncul di sini setelah lelang Anda dimenangkan oleh vendor.
                                </p>
                                <div class="empty-action">
                                    <a href="{{ route('user.auctions.index') }}" class="btn btn-primary">Lihat Lelang</a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
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

    function getProgressPercentage($status)
    {
        switch ($status) {
            case 'menunggu':
                return 20;
            case 'diproses':
                return 40;
            case 'dicetak':
                return 60;
            case 'dikirim':
                return 80;
            case 'selesai':
                return 100;
            default:
                return 0;
        }
    }
@endphp
