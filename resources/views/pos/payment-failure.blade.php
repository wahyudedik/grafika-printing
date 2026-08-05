@extends('layouts.vendor')

@section('title', 'Pembayaran Gagal - ' . $transaksi->kode)

@section('content')
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white text-center">
                        <h3 class="mb-0">❌ Pembayaran Gagal</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <svg class="text-danger" width="64" height="64" fill="currentColor" viewBox="0 0 16 16">
                                <path
                                    d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z" />
                            </svg>
                        </div>

                        <h4 class="text-danger mb-3">Pembayaran Gagal</h4>

                        <div class="alert alert-danger">
                            <h5>Detail Transaksi</h5>
                            <p><strong>Faktur:</strong> {{ $transaksi->kode }}</p>
                            <p><strong>Pelanggan:</strong> {{ $transaksi->pelanggan->nama }}</p>
                            <p><strong>Jumlah:</strong> Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>
                            <p><strong>Status:</strong> {{ ucfirst($transaksi->status ?? 'Gagal') }}</p>
                        </div>

                        <div class="alert alert-warning">
                            <h6>Apa yang harus dilakukan?</h6>
                            <ul class="list-unstyled mb-0">
                                <li>• Periksa status pembayaran dengan pelanggan</li>
                                <li>• Coba metode pembayaran lain</li>
                                <li>• Proses pembayaran tunai jika pelanggan hadir</li>
                                <li>• Hubungi pelanggan untuk kelanjutan pembayaran</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('vendor.pos.payment.options', $transaksi->id) }}" class="btn btn-primary">
                                💳 Coba Metode Lain
                            </a>
                            <a href="{{ route('vendor.pos.invoice.show', $transaksi->id) }}"
                                class="btn btn-outline-secondary">
                                📄 Lihat Faktur
                            </a>
                            <a href="{{ route('vendor.pos.index') }}" class="btn btn-outline-primary">
                                🏪 Kembali ke POS
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
