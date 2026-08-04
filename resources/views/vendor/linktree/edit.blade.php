@extends('layouts.vendor')

@section('content')
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">Edit Linktree: {{ $linktree->title }}</h2>
                    <div class="page-pretitle">
                        <a href="{{ route('vendor.linktree.index') }}">Linktree</a> / Edit
                    </div>
                </div>
                <div class="col-auto">
                    @if($linktree->is_active)
                    <a href="{{ route('linktree.public', $linktree->custom_url) }}" target="_blank" class="btn btn-info">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/></svg>
                        Lihat Publik
                    </a>
                    @endif
                    <form action="{{ route('vendor.linktree.toggle-active', $linktree) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn {{ $linktree->is_active ? 'btn-warning' : 'btn-success' }}">
                            {{ $linktree->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <div class="d-flex">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                </div>
                <div>{{ session('success') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex">
                <div>{{ session('error') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        @endif

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Settings Tab -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"/><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"/></svg>
                            Pengaturan
                        </h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('vendor.linktree.update', $linktree) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="title" class="form-label required">Judul</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title', $linktree->title) }}" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="custom_url" class="form-label required">URL Kustom</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ config('app.url') }}/l/</span>
                                    <input type="text" class="form-control @error('custom_url') is-invalid @enderror"
                                        id="custom_url" name="custom_url" value="{{ old('custom_url', $linktree->custom_url) }}"
                                        pattern="[a-z0-9\-]+" required>
                                    @error('custom_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror"
                                    id="bio" name="bio" rows="2">{{ old('bio', $linktree->bio) }}</textarea>
                                @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="template" class="form-label">Template</label>
                                    <select class="form-select" id="template" name="template">
                                        @foreach(['minimal' => 'Minimal', 'colorful' => 'Colorful', 'dark' => 'Dark', 'professional' => 'Professional'] as $val => $lbl)
                                        <option value="{{ $val }}" {{ old('template', $linktree->template) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="button_style" class="form-label">Gaya Tombol</label>
                                    <select class="form-select" id="button_style" name="button_style">
                                        @foreach(['rounded' => 'Rounded', 'square' => 'Square', 'pill' => 'Pill'] as $val => $lbl)
                                        <option value="{{ $val }}" {{ old('button_style', $linktree->button_style) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Color Settings -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="primary_color" class="form-label">Warna Utama</label>
                                    <input type="color" class="form-control form-control-color w-100" id="primary_color"
                                        name="primary_color" value="{{ old('primary_color', $linktree->primary_color) }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="secondary_color" class="form-label">Warna Sekunder</label>
                                    <input type="color" class="form-control form-control-color w-100" id="secondary_color"
                                        name="secondary_color" value="{{ old('secondary_color', $linktree->secondary_color) }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="bg_color" class="form-label">Warna Background</label>
                                    <input type="color" class="form-control form-control-color w-100" id="bg_color"
                                        name="bg_color" value="{{ old('bg_color', $linktree->bg_color) }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="text_color" class="form-label">Warna Teks</label>
                                    <input type="color" class="form-control form-control-color w-100" id="text_color"
                                        name="text_color" value="{{ old('text_color', $linktree->text_color) }}">
                                </div>
                            </div>

                            <!-- Meta -->
                            <div class="mb-3">
                                <label for="meta_title" class="form-label">Meta Title (SEO)</label>
                                <input type="text" class="form-control" id="meta_title" name="meta_title"
                                    value="{{ old('meta_title', $linktree->meta_title) }}" placeholder="Judul untuk SEO">
                            </div>
                            <div class="mb-3">
                                <label for="meta_description" class="form-label">Meta Description (SEO)</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" rows="2"
                                    placeholder="Deskripsi untuk SEO">{{ old('meta_description', $linktree->meta_description) }}</textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">Hapus Linktree</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Links Management -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 12m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0"/><path d="M12 7l5 5"/><path d="M12 12l5 -5"/><path d="M17 12h4"/></svg>
                            Links ({{ $linktree->links->count() }})
                        </h3>
                        <div class="card-actions">
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addLinkModal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                                Tambah Link
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($linktree->links->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <p>Belum ada link. Klik "Tambah Link" untuk menambahkan.</p>
                        </div>
                        @else
                        <div class="list-group list-group-flush" id="links-list">
                            @foreach($linktree->links as $link)
                            <div class="list-group-item" data-id="{{ $link->id }}">
                                <div class="d-flex align-items-center">
                                    <span class="text-muted me-2 cursor-move" title="Drag untuk mengurutkan">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M4 8l4 0"/><path d="M4 16l4 0"/><path d="M10 8l4 0"/><path d="M10 16l4 0"/><path d="M16 8l4 0"/><path d="M16 16l4 0"/></svg>
                                    </span>
                                    <div class="flex-fill">
                                        <div class="fw-bold">{{ $link->title }}</div>
                                        <div class="text-muted small text-truncate" style="max-width: 400px;">{{ $link->url }}</div>
                                    </div>
                                    <span class="badge {{ $link->is_active ? 'bg-success-lt' : 'bg-secondary-lt' }} me-2">
                                        {{ $link->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                    <span class="text-muted small me-2">{{ number_format($link->clicks_count) }} clicks</span>
                                    <div class="btn-list">
                                        <button class="btn btn-sm btn-outline-primary edit-link-btn"
                                            data-id="{{ $link->id }}"
                                            data-title="{{ $link->title }}"
                                            data-url="{{ $link->url }}"
                                            data-type="{{ $link->type }}"
                                            data-active="{{ $link->is_active ? '1' : '0' }}">
                                            Edit
                                        </button>
                                        <form action="{{ route('vendor.linktree.links.destroy', [$linktree, $link]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus link ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Social Media Management -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><path d="M6 12a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/></svg>
                            Social Media ({{ $linktree->socials->count() }})
                        </h3>
                        <div class="card-actions">
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSocialModal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                                Tambah Social
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($linktree->socials->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <p>Belum ada social media.</p>
                        </div>
                        @else
                        <div class="list-group list-group-flush">
                            @foreach($linktree->socials as $social)
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <span class="me-2">{!! $social->icon_html !!}</span>
                                    <div class="flex-fill">
                                        <div class="fw-bold text-capitalize">{{ $social->platform }}</div>
                                        <div class="text-muted small text-truncate" style="max-width: 400px;">{{ $social->url }}</div>
                                    </div>
                                    <span class="badge {{ $social->is_active ? 'bg-success-lt' : 'bg-secondary-lt' }} me-2">
                                        {{ $social->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                    <div class="btn-list">
                                        <button class="btn btn-sm btn-outline-primary edit-social-btn"
                                            data-id="{{ $social->id }}"
                                            data-platform="{{ $social->platform }}"
                                            data-url="{{ $social->url }}"
                                            data-active="{{ $social->is_active ? '1' : '0' }}">
                                            Edit
                                        </button>
                                        <form action="{{ route('vendor.linktree.socials.destroy', [$linktree, $social]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus social media ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Media Upload -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 3l6 6l-6 6"/><path d="M21 9l-9 0"/><path d="M9 21l-6 0l6 -6"/></svg>
                            Media
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Avatar -->
                            <div class="col-md-4 text-center mb-3">
                                <label class="form-label">Avatar</label>
                                <div class="mb-2">
                                    @if($linktree->avatar)
                                    <img src="{{ asset('linktree/avatars/' . $linktree->avatar) }}" alt="Avatar" class="rounded-circle" width="80" height="80" style="object-fit: cover;">
                                    @else
                                    <div class="avatar avatar-xl bg-primary text-white d-flex align-items-center justify-content-center rounded-circle mx-auto" style="width: 80px; height: 80px; font-size: 32px;">
                                        {{ strtoupper(substr($linktree->title, 0, 1)) }}
                                    </div>
                                    @endif
                                </div>
                                <form action="{{ route('vendor.linktree.upload-avatar', $linktree) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="avatar" accept="image/*" class="d-none" id="avatar-input" onchange="this.form.submit()">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="document.getElementById('avatar-input').click()">Upload Avatar</button>
                                </form>
                            </div>

                            <!-- Banner -->
                            <div class="col-md-4 text-center mb-3">
                                <label class="form-label">Banner</label>
                                <div class="mb-2">
                                    @if($linktree->banner)
                                    <img src="{{ asset('linktree/banners/' . $linktree->banner) }}" alt="Banner" class="rounded" width="120" height="60" style="object-fit: cover;">
                                    @else
                                    <div class="bg-muted rounded d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 60px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6l18 0"/><path d="M5 6l0 -1"/><path d="M19 6l0 -1"/></svg>
                                    </div>
                                    @endif
                                </div>
                                <form action="{{ route('vendor.linktree.upload-banner', $linktree) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="banner" accept="image/*" class="d-none" id="banner-input" onchange="this.form.submit()">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="document.getElementById('banner-input').click()">Upload Banner</button>
                                </form>
                            </div>

                            <!-- QRIS -->
                            <div class="col-md-4 text-center mb-3">
                                <label class="form-label">Gambar QRIS</label>
                                <div class="mb-2">
                                    @if($linktree->qris_image)
                                    <img src="{{ asset('linktree/qris/' . $linktree->qris_image) }}" alt="QRIS" class="rounded" width="80" height="80" style="object-fit: cover;">
                                    @else
                                    <div class="bg-muted rounded d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                                        <span class="text-muted small">QRIS</span>
                                    </div>
                                    @endif
                                </div>
                                <form action="{{ route('vendor.linktree.upload-qris', $linktree) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="qris_image" accept="image/*" class="d-none" id="qris-input" onchange="this.form.submit()">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="document.getElementById('qris-input').click()">Upload QRIS</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Live Preview -->
            <div class="col-lg-4">
                <div class="card" style="position: sticky; top: 1rem;">
                    <div class="card-header">
                        <h3 class="card-title">Live Preview</h3>
                    </div>
                    <div class="card-body">
                        <div class="phone-frame mx-auto" style="max-width: 280px; border: 2px solid #e5e7eb; border-radius: 16px; overflow: hidden; background: {{ $linktree->bg_color }};">
                            @if($linktree->banner)
                            <img src="{{ asset('linktree/banners/' . $linktree->banner) }}" alt="Banner" style="width: 100%; height: 80px; object-fit: cover;">
                            @else
                            <div style="height: 40px; background: {{ $linktree->primary_color }}30;"></div>
                            @endif
                            <div class="text-center p-3">
                                @if($linktree->avatar)
                                <img src="{{ asset('linktree/avatars/' . $linktree->avatar) }}" alt="Avatar" class="rounded-circle mb-2" width="64" height="64" style="object-fit: cover;">
                                @else
                                <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; background: {{ $linktree->primary_color }}; color: white; font-size: 24px;">
                                    {{ strtoupper(substr($linktree->title, 0, 1)) }}
                                </div>
                                @endif
                                <h5 style="color: {{ $linktree->text_color }}; margin-bottom: 4px;">{{ $linktree->title }}</h5>
                                <p class="small" style="color: {{ $linktree->text_color }}80;">{{ Str::limit($linktree->bio, 60) }}</p>
                            </div>
                            <div class="px-3 pb-3">
                                @foreach($linktree->links->where('is_active', true) as $link)
                                <div class="mb-2 p-2 text-center" style="background: {{ $linktree->primary_color }}; color: white; border-radius: {{ $linktree->button_style === 'pill' ? '50px' : ($linktree->button_style === 'square' ? '4px' : '8px') }};">
                                    <small>{{ $link->title }}</small>
                                </div>
                                @endforeach
                                @if($linktree->show_qris && $linktree->qris_image)
                                <div class="text-center mt-2">
                                    <img src="{{ asset('linktree/qris/' . $linktree->qris_image) }}" alt="QRIS" style="max-width: 120px;" class="rounded">
                                </div>
                                @endif
                            </div>
                            @if($linktree->socials->where('is_active', true)->count() > 0)
                            <div class="text-center pb-3">
                                @foreach($linktree->socials->where('is_active', true) as $social)
                                <span class="mx-1">{!! $social->icon_html !!}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Link Modal -->
<div class="modal fade" id="addLinkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('vendor.linktree.links.store', $linktree) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Link Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Judul Link</label>
                        <input type="text" class="form-control" name="title" required placeholder="Contoh: Website Toko">
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">URL</label>
                        <input type="url" class="form-control" name="url" required placeholder="https://...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe Link</label>
                        <select class="form-select" name="type">
                            <option value="link">🔗 Link Umum</option>
                            <option value="whatsapp">📱 WhatsApp</option>
                            <option value="qris">💳 QRIS</option>
                            <option value="phone">📞 Telepon</option>
                            <option value="email">📧 Email</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah Link</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Link Modal -->
<div class="modal fade" id="editLinkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editLinkForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Judul Link</label>
                        <input type="text" class="form-control" name="title" id="edit-link-title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">URL</label>
                        <input type="url" class="form-control" name="url" id="edit-link-url" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe Link</label>
                        <select class="form-select" name="type" id="edit-link-type">
                            <option value="link">🔗 Link Umum</option>
                            <option value="whatsapp">📱 WhatsApp</option>
                            <option value="qris">💳 QRIS</option>
                            <option value="phone">📞 Telepon</option>
                            <option value="email">📧 Email</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="edit-link-active" value="1">
                            <label class="form-check-label" for="edit-link-active">Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Social Modal -->
<div class="modal fade" id="addSocialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('vendor.linktree.socials.store', $linktree) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Social Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Platform</label>
                        <select class="form-select" name="platform" required>
                            <option value="">Pilih Platform</option>
                            <option value="instagram">📷 Instagram</option>
                            <option value="facebook">📘 Facebook</option>
                            <option value="twitter">🐦 Twitter/X</option>
                            <option value="tiktok">🎵 TikTok</option>
                            <option value="youtube">📺 YouTube</option>
                            <option value="whatsapp">💬 WhatsApp</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">URL Profil</label>
                        <input type="url" class="form-control" name="url" required placeholder="https://...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Social Modal -->
<div class="modal fade" id="editSocialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editSocialForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Social Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Platform</label>
                        <select class="form-select" name="platform" id="edit-social-platform" required>
                            <option value="instagram">📷 Instagram</option>
                            <option value="facebook">📘 Facebook</option>
                            <option value="twitter">🐦 Twitter/X</option>
                            <option value="tiktok">🎵 TikTok</option>
                            <option value="youtube">📺 YouTube</option>
                            <option value="whatsapp">💬 WhatsApp</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">URL Profil</label>
                        <input type="url" class="form-control" name="url" id="edit-social-url" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="edit-social-active" value="1">
                            <label class="form-check-label" for="edit-social-active">Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('vendor.linktree.destroy', $linktree) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Hapus Linktree</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus linktree <strong>{{ $linktree->title }}</strong>?</p>
                    <p class="text-muted small">Semua link dan social media yang terkait juga akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Edit Link Modal
    document.querySelectorAll('.edit-link-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const title = this.dataset.title;
            const url = this.dataset.url;
            const type = this.dataset.type;
            const active = this.dataset.active;

            document.getElementById('edit-link-title').value = title;
            document.getElementById('edit-link-url').value = url;
            document.getElementById('edit-link-type').value = type;
            document.getElementById('edit-link-active').checked = active === '1';
            document.getElementById('editLinkForm').action = '{{ route("vendor.linktree.links.index", $linktree) }}/' + id;

            new bootstrap.Modal(document.getElementById('editLinkModal')).show();
        });
    });

    // Edit Social Modal
    document.querySelectorAll('.edit-social-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const platform = this.dataset.platform;
            const url = this.dataset.url;
            const active = this.dataset.active;

            document.getElementById('edit-social-platform').value = platform;
            document.getElementById('edit-social-url').value = url;
            document.getElementById('edit-social-active').checked = active === '1';
            document.getElementById('editSocialForm').action = '{{ route("vendor.linktree.socials.update", [$linktree, "__ID__"]) }}'.replace('__ID__', id);

            new bootstrap.Modal(document.getElementById('editSocialModal')).show();
        });
    });
</script>
@endpush
@endsection
