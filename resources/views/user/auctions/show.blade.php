@extends('layouts.user')

@section('title', 'Detail Lelang')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">{{ $auction->title }}</h2>
                    <p class="text-muted">Oleh {{ $auction->user->name }} • {{ $auction->created_at->diffForHumans() }}</p>
                </div>
                <div class="d-flex gap-2">
                    @if ($auction->user_id === auth()->id())
                        <a href="{{ route('auctions.edit', $auction) }}" class="btn btn-outline-primary">
                            Edit
                        </a>
                    @endif
                    <a href="{{ route('auctions.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="card-title">Detail Permintaan</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6>Deskripsi</h6>
                                <p>{{ $auction->description }}</p>
                            </div>

                            @if ($auction->specifications)
                                <div class="mb-3">
                                    <h6>Spesifikasi Teknis</h6>
                                    <p>{{ $auction->specifications }}</p>
                                </div>
                            @endif

                            @if ($auction->file_path)
                                <div class="mb-3">
                                    <h6>File Desain/Referensi</h6>
                                    <a href="{{ asset('storage/auction_files/' . $auction->file_path) }}" target="_blank"
                                        class="btn btn-outline-primary btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                            <path
                                                d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                            <path d="M9 9l1 1l3 -3" />
                                        </svg>
                                        Download File
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($auction->user_id === auth()->id() && $auction->status === 'active' && $auction->bids->count() > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4 class="card-title">Penawaran dari Vendor</h4>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('auctions.close', $auction) }}">
                                    @csrf
                                    <div class="row">
                                        @foreach ($auction->bids->where('status', 'pending') as $bid)
                                            <div class="col-md-6 mb-3">
                                                <div class="card border {{ $loop->first ? 'border-primary' : '' }}">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <h6 class="card-title mb-0">{{ $bid->vendor->name }}</h6>
                                                            <span class="badge bg-green-lt">Rp
                                                                {{ number_format($bid->bid_amount) }}</span>
                                                        </div>
                                                        <p class="text-muted small mb-2">{{ $bid->vendor->email }}</p>
                                                        @if ($bid->message)
                                                            <p class="small mb-2">{{ $bid->message }}</p>
                                                        @endif
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="winner_bid_id" id="bid_{{ $bid->id }}"
                                                                value="{{ $bid->id }}">
                                                            <label class="form-check-label" for="bid_{{ $bid->id }}">
                                                                Pilih sebagai pemenang
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if ($auction->bids->where('status', 'pending')->count() > 0)
                                        <div class="d-flex justify-content-end mt-3">
                                            <button type="submit" class="btn btn-success">
                                                Tutup Lelang & Pilih Pemenang
                                            </button>
                                        </div>
                                    @endif
                                </form>
                            </div>
                        </div>
                    @endif

                    @if ($auction->status === 'closed' && $auction->winnerVendor)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4 class="card-title">Pemenang Lelang</h4>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success">
                                    <h6 class="alert-heading">Lelang telah ditutup!</h6>
                                    <p class="mb-0">
                                        <strong>Pemenang:</strong> {{ $auction->winnerVendor->name }}<br>
                                        <strong>Harga Menang:</strong> Rp {{ number_format($auction->winning_bid) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="card-title">Informasi Lelang</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Kategori:</span>
                                        <span class="fw-bold">{{ $auction->category }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Jumlah:</span>
                                        <span class="fw-bold">{{ number_format($auction->quantity) }} pcs</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Budget:</span>
                                        <span class="fw-bold">Rp {{ number_format($auction->budget) }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Deadline:</span>
                                        <span class="fw-bold">{{ $auction->deadline->format('d M Y') }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Status:</span>
                                        <span
                                            class="badge bg-{{ $auction->status === 'active' ? 'green' : ($auction->status === 'closed' ? 'blue' : 'red') }}-lt">
                                            {{ ucfirst($auction->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Penawaran:</span>
                                        <span class="fw-bold">{{ $auction->getBidCount() }} vendor</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($auction->isActive())
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>Waktu Tersisa</h6>
                                <div class="h4 text-primary">
                                    {{ $auction->deadline->diffInDays(now()) }} hari
                                </div>
                                <small class="text-muted">
                                    Berakhir pada {{ $auction->deadline->format('d M Y H:i') }}
                                </small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
