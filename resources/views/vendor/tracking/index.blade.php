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
                                @forelse($auctions as $auction)
                                    <tr>
                                        <td>{{ $auction->transaksi->kode ?? $auction->kode }}</td>
                                        <td>
                                            <div>
                                                <strong>{{ $auction->title }}</strong><br>
                                                <small class="text-muted">Kode: {{ $auction->kode }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $auction->user->name }}</strong><br>
                                                <small class="text-muted">{{ $auction->user->email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ getStatusColor($auction->transaksi->tracking_status ?? 'menunggu') }}">
                                                {{ ucfirst($auction->transaksi->tracking_status ?? 'menunggu') }}
                                            </span>
                                        </td>
                                        <td>Rp {{ number_format($auction->winning_bid) }}</td>
                                        <td>
                                            @if ($auction->shippingInvoice)
                                                Rp {{ number_format($auction->shippingInvoice->shipping_cost) }}
                                                @if ($auction->shippingInvoice->payment_status === 'pending')
                                                    <span class="badge bg-warning ms-1">Belum Bayar</span>
                                                @elseif($auction->shippingInvoice->payment_status === 'paid')
                                                    <span class="badge bg-success ms-1">Lunas</span>
                                                @endif
                                            @else
                                                <button class="btn btn-sm btn-outline-primary"
                                                    onclick="createShippingInvoice({{ $auction->id }})">
                                                    Buat Invoice
                                                </button>
                                            @endif
                                        </td>
                                        <td>{{ $auction->shippingInvoice->waybill_number ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group">
                                                @if ($auction->shippingInvoice)
                                                    <button class="btn btn-sm btn-primary"
                                                        onclick="updateShippingStatus({{ $auction->id }})">
                                                        Update Status
                                                    </button>
                                                    <button class="btn btn-sm btn-info"
                                                        onclick="trackShipment({{ $auction->id }})">
                                                        Track
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-success"
                                                        onclick="createShippingInvoice({{ $auction->id }})">
                                                        Setup Shipping
                                                    </button>
                                                @endif
                                            </div>
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

    <!-- Create Shipping Invoice Modal -->
    <div class="modal fade" id="createShippingInvoiceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buat Shipping Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createShippingInvoiceForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kurir</label>
                                    <select name="courier" class="form-control" required>
                                        <option value="">Pilih Kurir</option>
                                        <option value="jne">JNE</option>
                                        <option value="tiki">TIKI</option>
                                        <option value="pos">POS Indonesia</option>
                                        <option value="jnt">J&T Express</option>
                                        <option value="sicepat">SiCepat</option>
                                        <option value="ninja">Ninja Xpress</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Layanan</label>
                                    <select name="service" class="form-control" required>
                                        <option value="">Pilih Layanan</option>
                                        <option value="reg">Regular</option>
                                        <option value="eco">Economy</option>
                                        <option value="ons">Overnight</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Berat (gram)</label>
                                    <input type="number" name="weight" class="form-control" min="1" required
                                        placeholder="1000">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kota Asal</label>
                                    <input type="text" name="origin_city" class="form-control" required
                                        placeholder="Jakarta">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kota Tujuan</label>
                                    <input type="text" name="destination_city" class="form-control" required
                                        placeholder="Bandung">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Alamat Asal</label>
                                    <textarea name="origin_address" class="form-control" rows="2" required placeholder="Alamat vendor"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat Tujuan</label>
                            <textarea name="destination_address" class="form-control" rows="3" required
                                placeholder="Alamat pengiriman user"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Catatan pengiriman (opsional)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Buat Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Shipping Status Modal -->
    <div class="modal fade" id="updateShippingStatusModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Status Pengiriman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="updateShippingStatusForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status Pengiriman</label>
                                    <select name="shipping_status" class="form-control" required>
                                        <option value="pending">Pending</option>
                                        <option value="processing">Processing</option>
                                        <option value="shipped">Shipped</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="failed">Failed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">No. Resi</label>
                                    <input type="text" name="waybill_number" class="form-control"
                                        placeholder="Masukkan nomor resi">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Catatan status pengiriman"></textarea>
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
        function createShippingInvoice(auctionId) {
            const modal = new bootstrap.Modal(document.getElementById('createShippingInvoiceModal'));
            const form = document.getElementById('createShippingInvoiceForm');

            // Set form action
            form.action = `/dashboard/tracking/${auctionId}/shipping-invoice`;

            modal.show();
        }

        function updateShippingStatus(auctionId) {
            const modal = new bootstrap.Modal(document.getElementById('updateShippingStatusModal'));
            const form = document.getElementById('updateShippingStatusForm');

            // Set form action
            form.action = `/dashboard/tracking/${auctionId}/shipping-status`;

            modal.show();
        }

        function trackShipment(auctionId) {
            // Redirect to tracking page or open tracking modal
            window.open(`/dashboard/tracking/${auctionId}/track`, '_blank');
        }

        // Handle form submissions with AJAX
        document.addEventListener('DOMContentLoaded', function() {
            // Create Shipping Invoice Form
            const createForm = document.getElementById('createShippingInvoiceForm');
            if (createForm) {
                createForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.textContent;

                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Membuat Invoice...';

                    fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Shipping invoice berhasil dibuat!');
                                location.reload();
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan saat membuat invoice');
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                            submitBtn.textContent = originalText;
                        });
                });
            }

            // Update Shipping Status Form
            const updateForm = document.getElementById('updateShippingStatusForm');
            if (updateForm) {
                updateForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.textContent;

                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Memperbarui Status...';

                    fetch(this.action, {
                            method: 'PUT',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Status pengiriman berhasil diperbarui!');
                                location.reload();
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan saat memperbarui status');
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                            submitBtn.textContent = originalText;
                        });
                });
            }
        });
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
