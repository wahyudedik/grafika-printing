@extends('layouts.user')

@section('title', 'Buat Permintaan Cetak')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Buat Permintaan Cetak Baru</h3><br>
                    <div class="card-subtitle">Buat permintaan cetak dan biarkan vendor memberikan penawaran terbaik</div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('auctions.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Judul Permintaan <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi Detail <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        rows="4" required>{{ old('description') }}</textarea>
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
                                                <option value="Banner" {{ old('category') == 'Banner' ? 'selected' : '' }}>
                                                    Banner</option>
                                                <option value="Stiker" {{ old('category') == 'Stiker' ? 'selected' : '' }}>
                                                    Stiker</option>
                                                <option value="Kartu Nama"
                                                    {{ old('category') == 'Kartu Nama' ? 'selected' : '' }}>Kartu Nama
                                                </option>
                                                <option value="Flyer" {{ old('category') == 'Flyer' ? 'selected' : '' }}>
                                                    Flyer</option>
                                                <option value="Poster" {{ old('category') == 'Poster' ? 'selected' : '' }}>
                                                    Poster</option>
                                                <option value="Brosur" {{ old('category') == 'Brosur' ? 'selected' : '' }}>
                                                    Brosur</option>
                                                <option value="Kalender"
                                                    {{ old('category') == 'Kalender' ? 'selected' : '' }}>Kalender</option>
                                                <option value="Buku" {{ old('category') == 'Buku' ? 'selected' : '' }}>
                                                    Buku</option>
                                                <option value="Kaos" {{ old('category') == 'Kaos' ? 'selected' : '' }}>
                                                    Kaos</option>
                                                <option value="Lainnya"
                                                    {{ old('category') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
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
                                                name="quantity" value="{{ old('quantity') }}" min="1" required>
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
                                                    id="budget" name="budget" value="{{ old('budget') }}"
                                                    min="0" step="0.01" required>
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
                                                name="deadline" value="{{ old('deadline') }}" required>
                                            @error('deadline')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="specifications" class="form-label">Spesifikasi Teknis</label>
                                    <textarea class="form-control @error('specifications') is-invalid @enderror" id="specifications" name="specifications"
                                        rows="3" placeholder="Contoh: Ukuran A4, Kertas 80gsm, Full Color, Finishing Laminating">{{ old('specifications') }}</textarea>
                                    @error('specifications')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="file" class="form-label">File Desain/Referensi</label>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror"
                                        id="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <div class="form-text">Format yang didukung: PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 10MB)
                                    </div>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Informasi Pengiriman -->
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Informasi Pengiriman</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="alamat_pengiriman" class="form-label">Alamat Pengiriman <span
                                                    class="text-danger">*</span></label>
                                            <textarea class="form-control @error('alamat_pengiriman') is-invalid @enderror" id="alamat_pengiriman"
                                                name="alamat_pengiriman" rows="3" placeholder="Masukkan alamat lengkap untuk pengiriman" required>{{ old('alamat_pengiriman') }}</textarea>
                                            @error('alamat_pengiriman')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="no_telp" class="form-label">No. Telepon <span
                                                            class="text-danger">*</span></label>
                                                    <input type="tel"
                                                        class="form-control @error('no_telp') is-invalid @enderror"
                                                        id="no_telp" name="no_telp" value="{{ old('no_telp') }}"
                                                        placeholder="08123456789, +628123456789, atau (0812) 345-6789"
                                                        required>
                                                    <div class="form-text">Format: 08123456789, +628123456789, atau (0812)
                                                        345-6789</div>
                                                    @error('no_telp')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="email_pengiriman" class="form-label">Email untuk
                                                        Notifikasi</label>
                                                    <input type="email"
                                                        class="form-control @error('email_pengiriman') is-invalid @enderror"
                                                        id="email_pengiriman" name="email_pengiriman"
                                                        value="{{ old('email_pengiriman', auth()->user()->email) }}"
                                                        placeholder="email@example.com">
                                                    @error('email_pengiriman')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="catatan_khusus" class="form-label">Catatan Khusus</label>
                                            <textarea class="form-control @error('catatan_khusus') is-invalid @enderror" id="catatan_khusus"
                                                name="catatan_khusus" rows="2" placeholder="Catatan khusus untuk vendor (opsional)">{{ old('catatan_khusus') }}</textarea>
                                            @error('catatan_khusus')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Tips Membuat Permintaan</h5>
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2">• Jelaskan kebutuhan dengan detail</li>
                                            <li class="mb-2">• Sertakan spesifikasi yang jelas</li>
                                            <li class="mb-2">• Upload file desain jika ada</li>
                                            <li class="mb-2">• Set budget yang realistis</li>
                                            <li class="mb-2">• Berikan deadline yang wajar</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Alur Kerja</h6>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-primary text-white me-2">1</span>
                                            <small>Buat permintaan</small>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-primary text-white me-2">2</span>
                                            <small>Vendor memberikan penawaran</small>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-primary text-white me-2">3</span>
                                            <small>Pilih vendor terbaik</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary text-white me-2">4</span>
                                            <small>Proses pengerjaan</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('auctions.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Buat Permintaan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
