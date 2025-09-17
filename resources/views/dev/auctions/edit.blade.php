@extends('dev.layouts.app')

@section('title', 'Edit Lelang')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Edit Lelang</h2>
                    <p class="text-muted">Edit data lelang {{ $auction->title }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.auctions.show', $auction) }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 6l6 6l-6 6" />
                        </svg>
                        Kembali ke Detail
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Form Edit Lelang</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.auctions.update', $auction) }}" method="POST"
                                enctype="multipart/form-data" data-loading>
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Judul Lelang <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                                            name="title" value="{{ old('title', $auction->title) }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                        <select class="form-select @error('category') is-invalid @enderror" name="category"
                                            required>
                                            <option value="">Pilih Kategori</option>
                                            <option value="Stiker"
                                                {{ old('category', $auction->category) === 'Stiker' ? 'selected' : '' }}>
                                                Stiker</option>
                                            <option value="Banner"
                                                {{ old('category', $auction->category) === 'Banner' ? 'selected' : '' }}>
                                                Banner</option>
                                            <option value="Flyer"
                                                {{ old('category', $auction->category) === 'Flyer' ? 'selected' : '' }}>
                                                Flyer</option>
                                            <option value="Brochure"
                                                {{ old('category', $auction->category) === 'Brochure' ? 'selected' : '' }}>
                                                Brochure</option>
                                            <option value="Poster"
                                                {{ old('category', $auction->category) === 'Poster' ? 'selected' : '' }}>
                                                Poster</option>
                                            <option value="Kartu Nama"
                                                {{ old('category', $auction->category) === 'Kartu Nama' ? 'selected' : '' }}>
                                                Kartu Nama</option>
                                            <option value="Undangan"
                                                {{ old('category', $auction->category) === 'Undangan' ? 'selected' : '' }}>
                                                Undangan</option>
                                            <option value="Buku"
                                                {{ old('category', $auction->category) === 'Buku' ? 'selected' : '' }}>Buku
                                            </option>
                                            <option value="Lainnya"
                                                {{ old('category', $auction->category) === 'Lainnya' ? 'selected' : '' }}>
                                                Lainnya</option>
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Jumlah Produksi <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                            name="quantity" value="{{ old('quantity', $auction->quantity) }}"
                                            min="1" required>
                                        @error('quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Budget Maksimal (Rp) <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('budget') is-invalid @enderror"
                                            name="budget" value="{{ old('budget', $auction->budget) }}" min="0"
                                            step="1000" required>
                                        @error('budget')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Deadline <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('deadline') is-invalid @enderror"
                                            name="deadline"
                                            value="{{ old('deadline', $auction->deadline->format('Y-m-d')) }}"
                                            min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                        @error('deadline')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="4" required>{{ old('description', $auction->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Spesifikasi <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('specifications') is-invalid @enderror" name="specifications" rows="4"
                                            required>{{ old('specifications', $auction->specifications) }}</textarea>
                                        @error('specifications')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">File Pendukung</label>
                                        <input type="file" class="form-control @error('file') is-invalid @enderror"
                                            name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                        @error('file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            Format yang diperbolehkan: PDF, DOC, DOCX, JPG, JPEG, PNG (Maksimal 10MB)
                                        </div>
                                        @if ($auction->file_path)
                                            <div class="mt-2">
                                                <div class="text-muted small">File saat ini:</div>
                                                <a href="{{ asset('storage/' . $auction->file_path) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16"
                                                        height="16" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                        <path
                                                            d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                        <path d="M9 9l1 1l3 -3" />
                                                    </svg>
                                                    Lihat File
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary" data-loading>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M5 12l5 5l10 -10" />
                                        </svg>
                                        Simpan Perubahan
                                    </button>
                                    <a href="{{ route('admin.auctions.show', $auction) }}"
                                        class="btn btn-outline-secondary ms-2">
                                        Batal
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Lelang</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="text-muted small">ID Lelang</div>
                                    <div class="fw-bold">#{{ $auction->id }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="text-muted small">Status Saat Ini</div>
                                    <div>
                                        <span
                                            class="badge 
                                            @if ($auction->status === 'pending') bg-warning
                                            @elseif($auction->status === 'active') bg-success
                                            @elseif($auction->status === 'closed') bg-info
                                            @elseif($auction->status === 'rejected') bg-danger
                                            @else bg-secondary @endif fs-6">
                                            {{ ucfirst($auction->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="text-muted small">Dibuat Oleh</div>
                                    <div class="fw-bold">{{ $auction->user->name }}</div>
                                    <div class="text-muted small">{{ $auction->user->email }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="text-muted small">Dibuat</div>
                                    <div class="fw-bold">{{ $auction->created_at->format('d M Y H:i') }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="text-muted small">Terakhir Update</div>
                                    <div class="fw-bold">{{ $auction->updated_at->format('d M Y H:i') }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="text-muted small">Total Penawaran</div>
                                    <div class="fw-bold text-primary">{{ $auction->bids->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Aksi Cepat</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.auctions.show', $auction) }}" class="btn btn-outline-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                        <path
                                            d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                    </svg>
                                    Lihat Detail
                                </a>

                                @if ($auction->status === 'active')
                                    <form action="{{ route('admin.auctions.close', $auction) }}" method="POST"
                                        onsubmit="return confirm('Tutup lelang ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-warning w-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16"
                                                height="16" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M18 6l-12 12" />
                                                <path d="M6 6l12 12" />
                                            </svg>
                                            Tutup Lelang
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.auctions.destroy', $auction) }}" method="POST"
                                    onsubmit="return confirm('Hapus lelang ini? Tindakan ini tidak dapat dibatalkan!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16"
                                            height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M4 7l16 0" />
                                            <path d="M10 11l0 6" />
                                            <path d="M14 11l0 6" />
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                        </svg>
                                        Hapus Lelang
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
