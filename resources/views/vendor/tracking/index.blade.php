@extends('layouts.vendor')

@section('title', 'Kelola Tracking Pesanan')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kelola Tracking Pesanan</h3>
                    <div class="card-subtitle">Update status pesanan dari lelang</div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Kode Transaksi</th>
                                    <th>Lelang</th>
                                    <th>Pelanggan</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Ongkir</th>
                                    <th>No. Resi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksis as $transaksi)
                                    <tr>
                                        <td>{{ $transaksi->kode }}</td>
                                        <td>
                                            <div>
                                                <strong>{{ $transaksi->auction->title ?? 'N/A' }}</strong><br>
                                                <small class="text-muted">Kode:
                                                    {{ $transaksi->auction->kode ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $transaksi->pelanggan->nama }}</strong><br>
                                                <small class="text-muted">{{ $transaksi->pelanggan->email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $this->getStatusColor($transaksi->tracking_status) }}">
                                                {{ ucfirst($transaksi->tracking_status) }}
                                            </span>
                                        </td>
                                        <td>Rp {{ number_format($transaksi->total_harga) }}</td>
                                        <td>
                                            @if ($transaksi->ongkir > 0)
                                                Rp {{ number_format($transaksi->ongkir) }}
                                                @if ($transaksi->is_cod)
                                                    <span class="badge bg-info ms-1">COD</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $transaksi->no_resi ?? '-' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary"
                                                onclick="updateTracking({{ $transaksi->id }}, '{{ $transaksi->tracking_status }}', '{{ $transaksi->no_resi }}', '{{ $transaksi->kurir }}', {{ $transaksi->ongkir }}, {{ $transaksi->is_cod ? 'true' : 'false' }})">
                                                Update Status
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="empty">
                                                <div class="empty-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="64"
                                                        height="64" viewBox="0 0 24 24" stroke-width="1"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                                        <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                                    </svg>
                                                </div>
                                                <p class="empty-title">Belum ada pesanan dari lelang</p>
                                                <p class="empty-subtitle text-muted">
                                                    Pesanan akan muncul di sini setelah ada lelang yang dimenangkan.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Tracking Modal -->
    <div class="modal fade" id="updateTrackingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Status Tracking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="updateTrackingForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status Tracking</label>
                                    <select name="tracking_status" class="form-control" required>
                                        <option value="menunggu">Menunggu</option>
                                        <option value="diproses">Diproses</option>
                                        <option value="dicetak">Dicetak</option>
                                        <option value="dikirim">Dikirim</option>
                                        <option value="selesai">Selesai</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kurir</label>
                                    <select name="kurir" class="form-control">
                                        <option value="">Pilih Kurir</option>
                                        <option value="jne">JNE</option>
                                        <option value="tiki">TIKI</option>
                                        <option value="pos">POS Indonesia</option>
                                        <option value="jnt">J&T</option>
                                        <option value="sicepat">SiCepat</option>
                                        <option value="ninja">Ninja Xpress</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">No. Resi</label>
                                    <input type="text" name="no_resi" class="form-control"
                                        placeholder="Masukkan nomor resi">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Ongkir (Rp)</label>
                                    <input type="number" name="ongkir" class="form-control" min="0" step="0.01"
                                        placeholder="0">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_cod" class="form-check-input" id="is_cod">
                                <label class="form-check-label" for="is_cod">
                                    COD (Cash on Delivery)
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat Pengiriman</label>
                            <textarea name="alamat_pengiriman" class="form-control" rows="3" placeholder="Alamat lengkap pengiriman"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateTracking(transaksiId, currentStatus, currentResi, currentKurir, currentOngkir, currentCod) {
            const modal = new bootstrap.Modal(document.getElementById('updateTrackingModal'));
            const form = document.getElementById('updateTrackingForm');

            // Set form action
            form.action = `/dashboard/tracking/${transaksiId}`;

            // Set current values
            form.querySelector('select[name="tracking_status"]').value = currentStatus;
            form.querySelector('input[name="no_resi"]').value = currentResi || '';
            form.querySelector('select[name="kurir"]').value = currentKurir || '';
            form.querySelector('input[name="ongkir"]').value = currentOngkir || '';
            form.querySelector('input[name="is_cod"]').checked = currentCod;

            modal.show();
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
