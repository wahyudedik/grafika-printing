@extends('layouts.user')

@section('title', 'Edit Permintaan Cetak')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Permintaan Cetak</h3>
                    <div class="card-subtitle">Perbarui informasi permintaan cetak Anda</div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('auctions.update', $auction) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Judul Permintaan <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        id="title" name="title" value="{{ old('title', $auction->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi Detail <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        rows="4" required>{{ old('description', $auction->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="category" class="form-label">Kategori <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control @error('category') is-invalid @enderror"
                                                id="category" name="category" required>
                                                <option value="">Pilih Kategori</option>
                                                <option value="Banner"
                                                    {{ old('category', $auction->category) == 'Banner' ? 'selected' : '' }}>
                                                    Banner</option>
                                                <option value="Stiker"
                                                    {{ old('category', $auction->category) == 'Stiker' ? 'selected' : '' }}>
                                                    Stiker</option>
                                                <option value="Kartu Nama"
                                                    {{ old('category', $auction->category) == 'Kartu Nama' ? 'selected' : '' }}>
                                                    Kartu Nama</option>
                                                <option value="Flyer"
                                                    {{ old('category', $auction->category) == 'Flyer' ? 'selected' : '' }}>
                                                    Flyer</option>
                                                <option value="Poster"
                                                    {{ old('category', $auction->category) == 'Poster' ? 'selected' : '' }}>
                                                    Poster</option>
                                                <option value="Brosur"
                                                    {{ old('category', $auction->category) == 'Brosur' ? 'selected' : '' }}>
                                                    Brosur</option>
                                                <option value="Kalender"
                                                    {{ old('category', $auction->category) == 'Kalender' ? 'selected' : '' }}>
                                                    Kalender</option>
                                                <option value="Buku"
                                                    {{ old('category', $auction->category) == 'Buku' ? 'selected' : '' }}>
                                                    Buku</option>
                                                <option value="Kaos"
                                                    {{ old('category', $auction->category) == 'Kaos' ? 'selected' : '' }}>
                                                    Kaos</option>
                                                <option value="Lainnya"
                                                    {{ old('category', $auction->category) == 'Lainnya' ? 'selected' : '' }}>
                                                    Lainnya</option>
                                            </select>
                                            @error('category')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="quantity" class="form-label">Jumlah Produksi <span
                                                    class="text-danger">*</span></label>
                                            <input type="number"
                                                class="form-control @error('quantity') is-invalid @enderror" id="quantity"
                                                name="quantity" value="{{ old('quantity', $auction->quantity) }}"
                                                min="1" required>
                                            @error('quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="budget" class="form-label">Budget Maksimal <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number"
                                                    class="form-control @error('budget') is-invalid @enderror"
                                                    id="budget" name="budget"
                                                    value="{{ old('budget', $auction->budget) }}" min="0"
                                                    step="0.01" required>
                                            </div>
                                            @error('budget')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="deadline" class="form-label">Deadline Pengerjaan <span
                                                    class="text-danger">*</span></label>
                                            <input type="date"
                                                class="form-control @error('deadline') is-invalid @enderror" id="deadline"
                                                name="deadline"
                                                value="{{ old('deadline', $auction->deadline->format('Y-m-d')) }}"
                                                required>
                                            @error('deadline')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="specifications" class="form-label">Spesifikasi Teknis</label>
                                    <textarea class="form-control @error('specifications') is-invalid @enderror" id="specifications" name="specifications"
                                        rows="3" placeholder="Contoh: Ukuran A4, Kertas 80gsm, Full Color, Finishing Laminating">{{ old('specifications', $auction->specifications) }}</textarea>
                                    @error('specifications')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="file" class="form-label">File Desain/Referensi</label>
                                    @if ($auction->file_path)
                                        <div class="mb-2">
                                            <small class="text-muted">File saat ini:</small>
                                            <a href="{{ asset('storage/auction_files/' . $auction->file_path) }}"
                                                target="_blank" class="btn btn-outline-primary btn-sm ms-2">
                                                Lihat File
                                            </a>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control @error('file') is-invalid @enderror"
                                        id="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <div class="form-text">Format yang didukung: PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 10MB)
                                    </div>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Status Lelang</h5>
                                        <div class="mb-2">
                                            <span
                                                class="badge bg-{{ $auction->status === 'active' ? 'green' : ($auction->status === 'closed' ? 'blue' : 'red') }}-lt">
                                                {{ ucfirst($auction->status) }}
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Penawaran:</small>
                                            <div class="fw-bold">{{ $auction->getBidCount() }} vendor</div>
                                        </div>
                                        @if ($auction->getLowestBid())
                                            <div class="mb-2">
                                                <small class="text-muted">Penawaran Terendah:</small>
                                                <div class="fw-bold">Rp {{ number_format($auction->getLowestBid()) }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Catatan</h6>
                                        <ul class="list-unstyled mb-0 small">
                                            <li class="mb-1">• Edit hanya bisa dilakukan jika lelang masih aktif</li>
                                            <li class="mb-1">• Perubahan akan terlihat oleh vendor</li>
                                            <li class="mb-1">• File baru akan menggantikan file lama</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('auctions.show', $auction) }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
