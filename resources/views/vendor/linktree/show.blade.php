@extends('layouts.vendor')

@section('content')
<div class="page-wrapper">
    <div class="container-xl">
        {{-- Page Header --}}
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-link" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M9 12m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0"/>
                            <path d="M12 7l5 5"/>
                            <path d="M12 12l5 -5"/>
                            <path d="M17 12h4"/>
                        </svg>
                        {{ $linktree->title }}
                    </h2>
                    <div class="page-pretitle">
                        <a href="{{ route('vendor.linktree.index') }}">Linktree</a> / Detail
                    </div>
                </div>
                <div class="col-auto">
                    <div class="btn-list">
                        @if($linktree->is_active)
                        <a href="{{ route('linktree.public', $linktree->custom_url) }}" target="_blank" class="btn btn-info">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/></svg>
                            Lihat Publik
                        </a>
                        @endif
                        <a href="{{ route('vendor.linktree.edit', $linktree) }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"/><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"/></svg>
                            Edit
                        </a>
                        <a href="{{ route('vendor.linktree.template.index', $linktree) }}" class="btn btn-azure">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"/><path d="M4 12l5 5"/><path d="M12 4l5 5"/></svg>
                            Template
                        </a>
                        <a href="{{ route('vendor.linktree.analytics', $linktree) }}" class="btn btn-purple">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/></svg>
                            Analytics
                        </a>
                        <a href="{{ route('vendor.linktree.products', $linktree) }}" class="btn btn-indigo">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                            Produk
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/></svg>
                                Link Tools
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('vendor.linktree.export-links', $linktree) }}" class="dropdown-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/><path d="M12 11v6"/><path d="M9 14l3 3l3 -3"/></svg>
                                    Export ke CSV
                                </a>
                                <a href="{{ route('vendor.linktree.import-links-form', $linktree) }}" class="dropdown-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/><path d="M9 15l3 3l3 -3"/><path d="M12 18v-6"/></svg>
                                    Import dari CSV
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('vendor.linktree.ab-test.index', $linktree) }}" class="dropdown-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16 21v-2a4 4 0 0 0 -4 -4h-4a4 4 0 0 0 -4 4v2"/><path d="M8.5 4l3.5 16"/><path d="M13 4l-3.5 16"/></svg>
                                    A/B Testing
                                </a>
                            </div>
                        </div>
                        <form action="{{ route('vendor.linktree.toggle-active', $linktree) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn {{ $linktree->is_active ? 'btn-warning' : 'btn-success' }}">
                                {{ $linktree->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
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
            {{-- Main Content --}}
            <div class="col-lg-8">
                {{-- Linktree Info Card --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 7l0 10"/><path d="M12 7l5 5"/><path d="M12 7l-5 5"/><path d="M17 12l-10 0"/></svg>
                            Informasi Linktree
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Judul</label>
                                    <div class="fw-bold">{{ $linktree->title }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small">URL Publik</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ config('app.url') }}/l/</span>
                                        <input type="text" class="form-control" value="{{ $linktree->custom_url }}" readonly id="publicUrl">
                                        <button class="btn btn-outline-primary" type="button" onclick="copyToClipboard('{{ config('app.url') }}/l/{{ $linktree->custom_url }}')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z"/><path d="M16 8m0 -2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2z"/></svg>
                                            Salin
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Status</label>
                                    <div>
                                        <span class="badge {{ $linktree->is_active ? 'bg-success-lt' : 'bg-secondary-lt' }}">
                                            {{ $linktree->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Template</label>
                                    <div>
                                        <span class="badge bg-blue-lt">{{ ucfirst($linktree->template) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($linktree->bio)
                        <div class="mb-3">
                            <label class="form-label text-muted small">Bio</label>
                            <div class="text-muted">{{ $linktree->bio }}</div>
                        </div>
                        @endif

                        {{-- Color Preview --}}
                        <div class="mb-3">
                            <label class="form-label text-muted small">Warna Tema</label>
                            <div class="d-flex gap-2">
                                <div class="d-flex align-items-center gap-1">
                                    <div class="rounded" style="width: 24px; height: 24px; background-color: {{ $linktree->primary_color }}; border: 1px solid #ccc;"></div>
                                    <span class="small text-muted">Primary</span>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <div class="rounded" style="width: 24px; height: 24px; background-color: {{ $linktree->secondary_color }}; border: 1px solid #ccc;"></div>
                                    <span class="small text-muted">Secondary</span>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <div class="rounded" style="width: 24px; height: 24px; background-color: {{ $linktree->bg_color }}; border: 1px solid #ccc;"></div>
                                    <span class="small text-muted">Background</span>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <div class="rounded" style="width: 24px; height: 24px; background-color: {{ $linktree->text_color }}; border: 1px solid #ccc;"></div>
                                    <span class="small text-muted">Text</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label text-muted small">Gaya Tombol</label>
                            <div>
                                <span class="badge bg-info-lt">{{ ucfirst($linktree->button_style) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Links List --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 12m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0"/><path d="M12 7l5 5"/><path d="M12 12l5 -5"/><path d="M17 12h4"/></svg>
                            Links ({{ $linktree->links->count() }})
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        @if($linktree->links->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-link mb-2" width="32" height="32" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M9 12m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0"/>
                                <path d="M12 7l5 5"/>
                                <path d="M12 12l5 -5"/>
                                <path d="M17 12h4"/>
                            </svg>
                            <p>Belum ada link. <a href="{{ route('vendor.linktree.edit', $linktree) }}">Tambahkan link sekarang.</a></p>
                        </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th>Judul</th>
                                        <th>Tipe</th>
                                        <th>URL</th>
                                        <th style="width: 80px;">Clicks</th>
                                        <th style="width: 80px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($linktree->links as $index => $link)
                                    <tr>
                                        <td class="text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="me-2">{!! $link->icon_html !!}</span>
                                                <span class="fw-bold">{{ $link->title }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $link->type === 'link' ? 'blue' : ($link->type === 'whatsapp' ? 'green' : ($link->type === 'qris' ? 'yellow' : 'secondary')) }}-lt">
                                                {{ ucfirst($link->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ $link->url }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $link->url }}">
                                                {{ $link->url }}
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold">{{ number_format($link->clicks_count) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $link->is_active ? 'bg-success-lt' : 'bg-secondary-lt' }}">
                                                {{ $link->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Social Media List --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 4a4 4 0 0 1 4 -4h8a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-8a4 4 0 0 1 -4 -4z"/><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M16.5 7.5l.01 0"/></svg>
                            Social Media ({{ $linktree->socials->count() }})
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        @if($linktree->socials->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-instagram mb-2" width="32" height="32" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M4 4m0 4a4 4 0 0 1 4 -4h8a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-8a4 4 0 0 1 -4 -4z"/>
                                <path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/>
                                <path d="M16.5 7.5l.01 0"/>
                            </svg>
                            <p>Belum ada social media. <a href="{{ route('vendor.linktree.edit', $linktree) }}">Tambahkan sekarang.</a></p>
                        </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th>Platform</th>
                                        <th>URL</th>
                                        <th style="width: 80px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($linktree->socials as $index => $social)
                                    <tr>
                                        <td class="text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="me-2" style="color: {{ $social->platform_color }};">
                                                    {!! $social->icon_html !!}
                                                </span>
                                                <span class="fw-bold">{{ ucfirst($social->platform) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ $social->url }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 300px;" title="{{ $social->url }}">
                                                {{ $social->url }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge {{ $social->is_active ? 'bg-success-lt' : 'bg-secondary-lt' }}">
                                                {{ $social->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                {{-- Statistics Card --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/><path d="M3.6 9h16.8"/><path d="M3.6 15h16.8"/><path d="M12 3a17 17 0 0 1 0 18"/><path d="M12 3a17 17 0 0 0 0 18"/></svg>
                            Statistik
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="display-6 fw-bold text-blue">{{ number_format($linktree->views_count) }}</div>
                                <div class="text-muted small">Total Views</div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="display-6 fw-bold text-green">{{ number_format($linktree->clicks_count) }}</div>
                                <div class="text-muted small">Total Clicks</div>
                            </div>
                            <div class="col-6">
                                <div class="display-6 fw-bold text-orange">{{ $linktree->links->count() }}</div>
                                <div class="text-muted small">Links</div>
                            </div>
                            <div class="col-6">
                                <div class="display-6 fw-bold text-purple">{{ $linktree->socials->count() }}</div>
                                <div class="text-muted small">Socials</div>
                            </div>
                        </div>

                        @if($linktree->views_count > 0)
                        <hr>
                        <div class="text-center">
                            <span class="text-muted small">Click Rate:</span>
                            <span class="fw-bold text-primary">
                                {{ number_format(($linktree->clicks_count / max($linktree->views_count, 1)) * 100, 1) }}%
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Media Card --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 3l6 6l-6 6"/><path d="M9 3l-6 6l6 6"/><path d="M3 12h18"/></svg>
                            Media
                        </h3>
                    </div>
                    <div class="card-body">
                        {{-- Avatar --}}
                        <div class="mb-3">
                            <label class="form-label text-muted small">Avatar</label>
                            <div class="text-center">
                                @if($linktree->avatar)
                                <img src="{{ asset('linktree/avatars/' . $linktree->avatar) }}" alt="Avatar" class="rounded-circle" width="80" height="80" style="object-fit: cover;">
                                @else
                                <div class="avatar avatar-xl bg-primary text-white d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; font-size: 2rem;">
                                    {{ strtoupper(substr($linktree->title, 0, 1)) }}
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Banner --}}
                        <div class="mb-3">
                            <label class="form-label text-muted small">Banner</label>
                            @if($linktree->banner)
                            <img src="{{ asset('linktree/banners/' . $linktree->banner) }}" alt="Banner" class="rounded w-100" style="object-fit: cover; max-height: 120px;">
                            @else
                            <div class="bg-muted rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                <span class="text-muted small">Tidak ada banner</span>
                            </div>
                            @endif
                        </div>

                        {{-- QRIS --}}
                        @if($linktree->show_qris && $linktree->qris_image)
                        <div class="mb-0">
                            <label class="form-label text-muted small">QRIS Image</label>
                            <div class="text-center">
                                <img src="{{ asset('linktree/qris/' . $linktree->qris_image) }}" alt="QRIS" class="rounded" style="max-width: 150px;">
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- SEO Settings Card --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"/><path d="M21 21l-6 -6"/></svg>
                            Pengaturan SEO
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small">Meta Title</label>
                            <div class="text-muted small">{{ $linktree->meta_title ?: '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Meta Description</label>
                            <div class="text-muted small">{{ $linktree->meta_description ?: '-' }}</div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label text-muted small">QRIS Ditampilkan</label>
                            <div>
                                <span class="badge {{ $linktree->show_qris ? 'bg-success-lt' : 'bg-secondary-lt' }}">
                                    {{ $linktree->show_qris ? 'Ya' : 'Tidak' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions Card --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M13 10l7.5 -7.5l.5 .5a3.5 3.5 0 0 1 -5 5l-5 5l-2.5 2.5l-3 1l1 -3l2.5 -2.5l5 -5a3.5 3.5 0 0 1 5 -5l-.5 .5l-7.5 7.5"/></svg>
                            Aksi Cepat
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="btn-list d-grid gap-2">
                            <a href="{{ route('vendor.linktree.edit', $linktree) }}" class="btn btn-outline-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"/><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"/></svg>
                                Edit Linktree
                            </a>
                            @if($linktree->is_active)
                            <button class="btn btn-outline-info" onclick="generateQRCode('{{ config('app.url') }}/l/{{ $linktree->custom_url }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M12 3v1m0 16v1m-8 -10h1m16 0h1m-2.636 -5.364l-.707 .707m-9.9 9.9l-.707 .707m-5.657 -5.657l-.707 .707m14.142 0l-.707 .707m-9.9 -9.9l-.707 .707"/></svg>
                                Generate QR Code
                            </button>
                            @endif
                            <form action="{{ route('vendor.linktree.destroy', $linktree) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus linktree ini? Semua link dan social media akan ikut terhapus.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7l0 -3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1l0 3"/></svg>
                                    Hapus Linktree
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- QR Code Modal --}}
<div class="modal modal-blur fade" id="qrModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">QR Code Linktree</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrCodeContainer" class="mb-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <p class="text-muted small" id="qrUrl"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" onclick="downloadQRCode()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><path d="M7 11l5 5l5 -5"/><path d="M12 4l0 12"/></svg>
                    Download
                </button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Show success toast
            showToast('URL berhasil disalin ke clipboard!');
        }).catch(function(err) {
            console.error('Failed to copy: ', err);
            // Fallback
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showToast('URL berhasil disalin ke clipboard!');
        });
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 p-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-success text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                    <strong class="me-auto">Berhasil</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">${message}</div>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    function generateQRCode(url) {
        document.getElementById('qrUrl').textContent = url;
        document.getElementById('qrCodeContainer').innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        `;

        // Use free QR code API
        const qrApiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(url)}`;

        const img = new Image();
        img.onload = function() {
            document.getElementById('qrCodeContainer').innerHTML = `<img src="${qrApiUrl}" alt="QR Code" class="img-fluid rounded" style="max-width: 200px;">`;
        };
        img.onerror = function() {
            document.getElementById('qrCodeContainer').innerHTML = `
                <div class="text-danger">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 8l0 4"/><path d="M12 16l.01 0"/></svg>
                    <p class="small">Gagal memuat QR Code</p>
                </div>
            `;
        };
        img.src = qrApiUrl;

        const modal = new bootstrap.Modal(document.getElementById('qrModal'));
        modal.show();
    }

    function downloadQRCode() {
        const img = document.querySelector('#qrCodeContainer img');
        if (img) {
            const link = document.createElement('a');
            link.href = img.src;
            link.download = 'linktree-qr-{{ $linktree->custom_url }}.png';
            link.click();
        }
    }
</script>
@endpush
