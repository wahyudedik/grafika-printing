@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Konfirmasi Barang Diterima</h4>
                        <small class="text-muted">Auction #{{ $auction->id }} - {{ $auction->title }}</small>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('delivery-confirmation.store', $auction) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- Auction Info -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6>Informasi Lelang</h6>
                                    <p><strong>Judul:</strong> {{ $auction->title }}</p>
                                    <p><strong>Vendor:</strong> {{ $auction->winnerVendor->name ?? 'N/A' }}</p>
                                    <p><strong>Jumlah Lelang:</strong> Rp
                                        {{ number_format($auction->winning_bid, 0, ',', '.') }}</p>
                                    @if ($auction->admin_fee_amount > 0)
                                        <p><strong>Admin Fee:</strong> Rp
                                            {{ number_format($auction->admin_fee_amount, 0, ',', '.') }}</p>
                                        <p><strong>Vendor Akan Dapat:</strong> Rp
                                            {{ number_format($auction->winning_bid - $auction->admin_fee_amount, 0, ',', '.') }}
                                        </p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6>Status Pembayaran</h6>
                                    <span class="badge bg-success">Sudah Dibayar</span>
                                    <p class="mt-2"><strong>Tanggal Bayar:</strong>
                                        {{ $auction->updated_at->format('d M Y H:i') }}</p>
                                    <div class="alert alert-info mt-2">
                                        <small>
                                            <strong>Catatan:</strong> Vendor sudah mencetak dan mengirim barang.
                                            Ongkir sudah dibayar CASH saat barang diterima.
                                            Vendor akan dapat bayar setelah Anda konfirmasi barang diterima.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery Status -->
                            <div class="mb-4">
                                <label class="form-label required">Status Barang</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delivery_status" id="delivered"
                                        value="delivered" required>
                                    <label class="form-check-label" for="delivered">
                                        <strong>✅ Barang Sudah Diterima dengan Baik</strong>
                                        <small class="d-block text-muted">Barang sudah sampai dan sesuai dengan
                                            pesanan</small>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delivery_status" id="disputed"
                                        value="disputed" required>
                                    <label class="form-check-label" for="disputed">
                                        <strong>❌ Ada Masalah dengan Barang</strong>
                                        <small class="d-block text-muted">Barang rusak, tidak sesuai, atau ada masalah
                                            lainnya</small>
                                    </label>
                                </div>
                            </div>

                            <!-- Delivery Notes -->
                            <div class="mb-4">
                                <label for="delivery_notes" class="form-label">Catatan Pengiriman</label>
                                <textarea class="form-control" id="delivery_notes" name="delivery_notes" rows="3"
                                    placeholder="Berikan detail tentang kondisi barang yang diterima..."></textarea>
                            </div>

                            <!-- Rating (only if delivered) -->
                            <div class="mb-4" id="rating-section" style="display: none;">
                                <label class="form-label">Rating Vendor</label>
                                <div class="rating">
                                    <input type="radio" name="user_rating" id="star5" value="5">
                                    <label for="star5">★</label>
                                    <input type="radio" name="user_rating" id="star4" value="4">
                                    <label for="star4">★</label>
                                    <input type="radio" name="user_rating" id="star3" value="3">
                                    <label for="star3">★</label>
                                    <input type="radio" name="user_rating" id="star2" value="2">
                                    <label for="star2">★</label>
                                    <input type="radio" name="user_rating" id="star1" value="1">
                                    <label for="star1">★</label>
                                </div>
                            </div>

                            <!-- Feedback (only if delivered) -->
                            <div class="mb-4" id="feedback-section" style="display: none;">
                                <label for="user_feedback" class="form-label">Feedback untuk Vendor</label>
                                <textarea class="form-control" id="user_feedback" name="user_feedback" rows="3"
                                    placeholder="Bagikan pengalaman Anda dengan vendor ini..."></textarea>
                            </div>

                            <!-- Photos -->
                            <div class="mb-4">
                                <label for="photos" class="form-label">Foto Barang (Opsional)</label>
                                <input type="file" class="form-control" id="photos" name="photos[]" multiple
                                    accept="image/*">
                                <div class="form-text">Upload foto barang yang diterima (maksimal 5 foto)</div>
                            </div>

                            <!-- Dispute Reason (only if disputed) -->
                            <div class="mb-4" id="dispute-section" style="display: none;">
                                <label for="dispute_reason" class="form-label required">Alasan Masalah</label>
                                <textarea class="form-control" id="dispute_reason" name="dispute_reason" rows="3"
                                    placeholder="Jelaskan masalah yang ditemukan..."></textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('user.auctions.show', $auction) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check"></i> Konfirmasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .rating input {
            display: none;
        }

        .rating label {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }

        .rating label:hover,
        .rating label:hover~label,
        .rating input:checked~label {
            color: #ffc107;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deliveredRadio = document.getElementById('delivered');
            const disputedRadio = document.getElementById('disputed');
            const ratingSection = document.getElementById('rating-section');
            const feedbackSection = document.getElementById('feedback-section');
            const disputeSection = document.getElementById('dispute-section');

            function toggleSections() {
                if (deliveredRadio.checked) {
                    ratingSection.style.display = 'block';
                    feedbackSection.style.display = 'block';
                    disputeSection.style.display = 'none';
                } else if (disputedRadio.checked) {
                    ratingSection.style.display = 'none';
                    feedbackSection.style.display = 'none';
                    disputeSection.style.display = 'block';
                } else {
                    ratingSection.style.display = 'none';
                    feedbackSection.style.display = 'none';
                    disputeSection.style.display = 'none';
                }
            }

            deliveredRadio.addEventListener('change', toggleSections);
            disputedRadio.addEventListener('change', toggleSections);
        });
    </script>
@endsection
