@extends('layouts.vendor')

@section('content')
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">Buat Linktree Baru</h2>
                    <div class="page-pretitle">
                        <a href="{{ route('vendor.linktree.index') }}">Linktree</a> / Buat Baru
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <form action="{{ route('vendor.linktree.store') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Dasar</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="title" class="form-label required">Judul Linktree</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title', $vendor->name) }}"
                                    placeholder="Nama toko atau brand Anda" required>
                                @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-hint">Judul yang ditampilkan di halaman linktree Anda.</div>
                            </div>

                            <div class="mb-3">
                                <label for="custom_url" class="form-label required">URL Kustom</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ config('app.url', 'https://grafika.noteds.com') }}/l/</span>
                                    <input type="text"
                                        class="form-control @error('custom_url') is-invalid @enderror"
                                        id="custom_url" name="custom_url"
                                        value="{{ old('custom_url') }}"
                                        placeholder="nama-toko-anda"
                                        pattern="[a-z0-9\-]+" required>
                                    @error('custom_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-hint">Hanya huruf kecil, angka, dan tanda hubung (-). Contoh: <code>my-print-shop</code></div>
                            </div>

                            <div class="mb-3">
                                <label for="bio" class="form-label">Bio / Deskripsi</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror"
                                    id="bio" name="bio" rows="3"
                                    placeholder="Deskripsi singkat tentang toko Anda">{{ old('bio') }}</textarea>
                                @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-hint">Maksimal 500 karakter.</div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Pilih Template</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @php
                                $templates = [
                                    'minimal' => ['name' => 'Minimal', 'desc' => 'Bersih dan simpel', 'preview' => 'bg-white border'],
                                    'colorful' => ['name' => 'Colorful', 'desc' => 'Ceriah dan menarik', 'preview' => 'bg-gradient-to-r from-purple-500 to-pink-500'],
                                    'dark' => ['name' => 'Dark', 'desc' => 'Gelap dan elegan', 'preview' => 'bg-gray-900'],
                                    'professional' => ['name' => 'Professional', 'desc' => 'Formal dan terpercaya', 'preview' => 'bg-slate-800'],
                                ];
                                @endphp

                                @foreach($templates as $key => $template)
                                <div class="col-sm-6 col-lg-3">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="template" value="{{ $key }}"
                                            class="d-none" {{ old('template', 'minimal') === $key ? 'checked' : '' }}
                                            onchange="selectTemplate('{{ $key }}')">
                                        <div class="card template-card {{ old('template', 'minimal') === $key ? 'border-primary border-2 shadow' : '' }}"
                                            id="template-{{ $key }}">
                                            <div class="card-body text-center p-3">
                                                <div class="{{ $template['preview'] }} rounded mb-2" style="height: 60px; border-radius: 8px;"></div>
                                                <div class="fw-bold">{{ $template['name'] }}</div>
                                                <div class="text-muted small">{{ $template['desc'] }}</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @error('template')
                            <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Pengaturan Tombol</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Gaya Tombol</label>
                                <div class="row g-2">
                                    @foreach(['rounded' => 'Rounded (Bulat)', 'square' => 'Square (Kotak)', 'pill' => 'Pill (Pill)'] as $style => $label)
                                    <div class="col-sm-4">
                                        <label class="btn btn-outline {{ old('button_style', 'rounded') === $style ? 'active border-primary' : '' }} w-100" style="border-radius: {{ $style === 'rounded' ? '8px' : ($style === 'square' ? '2px' : '50px') }}">
                                            <input type="radio" name="button_style" value="{{ $style }}"
                                                class="d-none" {{ old('button_style', 'rounded') === $style ? 'checked' : '' }}>
                                            {{ $label }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                @error('button_style')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 5l0 14"/>
                                <path d="M5 12l14 0"/>
                            </svg>
                            Buat Linktree
                        </button>
                        <a href="{{ route('vendor.linktree.index') }}" class="btn btn-ghost btn-lg">Batal</a>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Preview</h3>
                    </div>
                    <div class="card-body">
                        <div id="preview-container" class="rounded p-3 text-center" style="min-height: 200px; background: #ffffff; border: 1px solid #e5e7eb;">
                            <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center bg-primary text-white" style="width: 48px; height: 48px; font-size: 20px;">
                                T
                            </div>
                            <h5 id="preview-title" class="mb-1">Nama Toko</h5>
                            <p id="preview-bio" class="text-muted small mb-3">Deskripsi toko Anda</p>
                            <div class="d-grid gap-2">
                                <div id="preview-btn" class="btn" style="border: 2px solid #e5e7eb; border-radius: 8px;">Link 1</div>
                                <div id="preview-btn2" class="btn" style="border: 2px solid #e5e7eb; border-radius: 8px;">Link 2</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Tips</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M5 12l5 5l10 -10"/></svg>
                                Pilih URL yang mudah diingat
                            </li>
                            <li class="mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M5 12l5 5l10 -10"/></svg>
                                Gunakan huruf kecil saja
                            </li>
                            <li class="mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M5 12l5 5l10 -10"/></svg>
                                Tanda hubung sebagai pengganti spasi
                            </li>
                            <li>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M5 12l5 5l10 -10"/></svg>
                                Template bisa diubah kapan saja
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function selectTemplate(template) {
        document.querySelectorAll('.template-card').forEach(card => {
            card.classList.remove('border-primary', 'border-2', 'shadow');
        });
        document.getElementById('template-' + template).classList.add('border-primary', 'border-2', 'shadow');

        const colors = {
            minimal: { bg: '#ffffff', btn: '#374151', text: '#1F2937' },
            colorful: { bg: '#F5F3FF', btn: '#8B5CF6', text: '#1F2937' },
            dark: { bg: '#111827', btn: '#6366F1', text: '#F9FAFB' },
            professional: { bg: '#F1F5F9', btn: '#1E3A5F', text: '#0F172A' },
        };

        const c = colors[template];
        const preview = document.getElementById('preview-container');
        preview.style.background = c.bg;
        preview.style.borderColor = template === 'minimal' ? '#e5e7eb' : 'transparent';

        const title = document.getElementById('preview-title');
        title.style.color = c.text;

        const bio = document.getElementById('preview-bio');
        bio.style.color = template === 'minimal' ? '#6B7280' : c.text + '99';

        ['preview-btn', 'preview-btn2'].forEach(id => {
            const btn = document.getElementById(id);
            if (template === 'colorful') {
                btn.style.background = 'linear-gradient(to right, #8B5CF6, #EC4899)';
                btn.style.border = 'none';
                btn.style.color = '#fff';
            } else if (template === 'dark') {
                btn.style.background = '#374151';
                btn.style.border = 'none';
                btn.style.color = '#fff';
            } else if (template === 'professional') {
                btn.style.background = '#1E3A5F';
                btn.style.border = 'none';
                btn.style.color = '#fff';
            } else {
                btn.style.background = 'transparent';
                btn.style.border = '2px solid #e5e7eb';
                btn.style.color = '#374151';
            }
        });
    }

    // Live preview for title
    document.getElementById('title').addEventListener('input', function() {
        document.getElementById('preview-title').textContent = this.value || 'Nama Toko';
    });

    document.getElementById('bio').addEventListener('input', function() {
        document.getElementById('preview-bio').textContent = this.value || 'Deskripsi toko Anda';
    });

    // Initialize preview
    selectTemplate('{{ old('template', 'minimal') }}');
</script>
@endpush
@endsection
