@extends('layouts.user')

@section('title', 'Tracking Pesanan')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col-auto">
            <h2 class="page-title">Tracking Pesanan</h2>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Pesanan</h3>
            </div>
            <div class="card-body">
                @if($orderTrackings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Kode Pesanan</th>
                                <th>Lelang</th>
                                <th>Vendor</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orderTrackings as $tracking)
                            <tr>
                                <td>
                                    <span class="font-weight-medium">{{ $tracking->order_code ?? $tracking->id }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('user.auctions.show', $tracking->auction) }}">
                                        {{ Str::limit($tracking->auction->title ?? '-', 30) }}
                                    </a>
                                </td>
                                <td>
                                    {{ $tracking->vendor->name ?? '-' }}
                                </td>
                                <td>
                                    @if($tracking->status === 'pending')
                                        <span class="badge bg-warning-lt">Menunggu</span>
                                    @elseif($tracking->status === 'confirmed')
                                        <span class="badge bg-info-lt">Dikonfirmasi</span>
                                    @elseif($tracking->status === 'processing')
                                        <span class="badge bg-blue-lt">Diproses</span>
                                    @elseif($tracking->status === 'shipped')
                                        <span class="badge bg-purple-lt">Dikirim</span>
                                    @elseif($tracking->status === 'delivered')
                                        <span class="badge bg-success-lt">Diterima</span>
                                    @elseif($tracking->status === 'completed')
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif($tracking->status === 'cancelled')
                                        <span class="badge bg-danger-lt">Dibatalkan</span>
                                    @else
                                        <span class="badge bg-secondary-lt">{{ $tracking->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $tracking->created_at->format('d M Y') }}
                                </td>
                                <td>
                                    <a href="{{ route('user.order-tracking.show', $tracking) }}" class="btn btn-sm btn-outline-primary">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $orderTrackings->links() }}
                </div>
                @else
                <div class="empty">
                    <p class="empty-title">Belum ada pesanan</p>
                    <p class="empty-subtitle text-muted">Pesanan Anda akan muncul di sini setelah pembayaran lelang berhasil</p>
                    <div class="empty-action">
                        <a href="{{ route('user.auctions.index') }}" class="btn btn-primary">
                            Lihat Lelang
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
