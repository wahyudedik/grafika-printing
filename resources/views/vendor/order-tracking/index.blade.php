@extends('layouts.vendor')

@section('title', 'Order Tracking - Vendor')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col-auto">
            <div class="page-pretitle">Vendor Panel</div>
            <h2 class="page-title">Order Tracking</h2>
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
                                <th>Pembeli</th>
                                <th>Status</th>
                                <th>Resi</th>
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
                                    {{ Str::limit($tracking->auction->title ?? '-', 30) }}
                                </td>
                                <td>
                                    {{ $tracking->user->name ?? '-' }}
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
                                    {{ $tracking->tracking_number ?? '-' }}
                                </td>
                                <td>
                                    {{ $tracking->created_at->format('d M Y') }}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#updateStatusModal{{ $tracking->id }}">
                                        Update
                                    </button>
                                </td>
                            </tr>

                            <!-- Update Status Modal -->
                            <div class="modal modal-blur fade" id="updateStatusModal{{ $tracking->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="{{ route('vendor.tracking.update', $tracking) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Update Status Pesanan #{{ $tracking->order_code ?? $tracking->id }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select class="form-select" name="status" required>
                                                        <option value="pending" {{ $tracking->status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                                                        <option value="confirmed" {{ $tracking->status === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                                                        <option value="processing" {{ $tracking->status === 'processing' ? 'selected' : '' }}>Diproses</option>
                                                        <option value="shipped" {{ $tracking->status === 'shipped' ? 'selected' : '' }}>Dikirim</option>
                                                        <option value="delivered" {{ $tracking->status === 'delivered' ? 'selected' : '' }}>Diterima</option>
                                                        <option value="completed" {{ $tracking->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                                                        <option value="cancelled" {{ $tracking->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Nomor Resi</label>
                                                    <input type="text" class="form-control" name="tracking_number"
                                                           value="{{ $tracking->tracking_number }}"
                                                           placeholder="Masukkan nomor resi">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Estimasi Pengiriman</label>
                                                    <input type="date" class="form-control" name="estimated_delivery"
                                                           value="{{ $tracking->estimated_delivery?->format('Y-m-d') }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Deskripsi Status</label>
                                                    <input type="text" class="form-control" name="status_description"
                                                           placeholder="Deskripsi singkat status">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Catatan</label>
                                                    <textarea class="form-control" name="notes" rows="3"
                                                              placeholder="Catatan tambahan">{{ $tracking->notes }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
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
                    <p class="empty-subtitle text-muted">Pesanan dari lelang akan muncul di sini</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
