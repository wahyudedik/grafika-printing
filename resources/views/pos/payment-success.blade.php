@extends('layouts.vendor')

@section('title', 'Pembayaran Berhasil - ' . $transaksi->kode)

@section('content')
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-header bg-success text-white text-center">
                        <h3 class="mb-0">✅ Pembayaran Berhasil</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <svg class="text-success" width="64" height="64" fill="currentColor" viewBox="0 0 16 16">
                                <path
                                    d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.061L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                            </svg>
                        </div>

                        <h4 class="text-success mb-3">Pembayaran Berhasil Diselesaikan!</h4>

                        <div class="alert alert-success">
                            <h5>Detail Transaksi</h5>
                            <p><strong>Faktur:</strong> {{ $transaksi->kode }}</p>
                            <p><strong>Pelanggan:</strong> {{ $transaksi->pelanggan->nama }}</p>
                            <p><strong>Jumlah:</strong> Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>
                            <p><strong>Metode Pembayaran:</strong> {{ ucfirst($transaksi->payment_method ?? 'Online') }}</p>
                            <p><strong>Status:</strong> {{ ucfirst($transaksi->status ?? 'Selesai') }}</p>
                            @if ($transaksi->xendit_payment_id)
                                <p><strong>Payment ID:</strong> {{ $transaksi->xendit_payment_id }}</p>
                            @endif
                        </div>

                        <div class="alert alert-info">
                            <h6>Langkah Selanjutnya</h6>
                            <ul class="list-unstyled mb-0">
                                <li>• Cetak struk untuk pelanggan</li>
                                <li>• Proses pemenuhan pesanan</li>
                                <li>• Perbarui inventaris jika diperlukan</li>
                                <li>• Kirim konfirmasi ke pelanggan</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('vendor.pos.invoice.print', $transaksi->id) }}" class="btn btn-success">
                                🖨️ Cetak Struk
                            </a>
                            <a href="{{ route('vendor.pos.invoice.show', $transaksi->id) }}"
                                class="btn btn-outline-primary">
                                📄 Lihat Faktur
                            </a>
                            <a href="{{ route('vendor.pos.index') }}" class="btn btn-outline-secondary">
                                🏪 Transaksi Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
