@extends('layouts.vendor')

@section('content')
<div class="page-wrapper">
    <div class="container-xl">
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
                        Linktree Management
                    </h2>
                    <div class="page-pretitle">Kelola halaman linktree toko Anda</div>
                </div>
                <div class="col-auto">
                    <a href="{{ route('vendor.linktree.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 5l0 14"/>
                            <path d="M5 12l14 0"/>
                        </svg>
                        Buat Linktree
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <div class="d-flex">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 12l5 5l10 -10"/>
                    </svg>
                </div>
                <div>{{ session('success') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        @endif

        @if($linktrees->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-link text-muted mb-3" width="48" height="48" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M9 12m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0"/>
                    <path d="M12 7l5 5"/>
                    <path d="M12 12l5 -5"/>
                    <path d="M17 12h4"/>
                </svg>
                <h3 class="text-muted">Belum Ada Linktree</h3>
                <p class="text-muted">Buat linktree pertama Anda untuk berbagi tautan penting toko Anda.</p>
                <a href="{{ route('vendor.linktree.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14"/>
                        <path d="M5 12l14 0"/>
                    </svg>
                    Buat Linktree Sekarang
                </a>
            </div>
        </div>
        @else
        <div class="row">
            @foreach($linktrees as $linktree)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card {{ $linktree->is_active ? 'border-success' : 'border-muted' }}">
                    <div class="card-status-top {{ $linktree->is_active ? 'bg-green' : 'bg-muted' }}"></div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            @if($linktree->avatar)
                            <img src="{{ asset('linktree/avatars/' . $linktree->avatar) }}" alt="Avatar" class="rounded-circle me-3" width="48" height="48" style="object-fit: cover;">
                            @else
                            <div class="avatar avatar-lg me-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-circle">
                                {{ strtoupper(substr($linktree->title, 0, 1)) }}
                            </div>
                            @endif
                            <div>
                                <h3 class="card-title mb-0">{{ $linktree->title }}</h3>
                                <span class="text-muted small">/l/{{ $linktree->custom_url }}</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <span class="badge {{ $linktree->is_active ? 'bg-success-lt' : 'bg-secondary-lt' }}">
                                {{ $linktree->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                            <span class="badge bg-blue-lt">{{ ucfirst($linktree->template) }}</span>
                        </div>

                        @if($linktree->bio)
                        <p class="text-muted small text-truncate">{{ $linktree->bio }}</p>
                        @endif

                        <div class="row text-center mb-3">
                            <div class="col">
                                <div class="font-weight-bold">{{ $linktree->active_links_count ?? 0 }}</div>
                                <div class="text-muted small">Links</div>
                            </div>
                            <div class="col">
                                <div class="font-weight-bold">{{ $linktree->active_socials_count ?? 0 }}</div>
                                <div class="text-muted small">Social</div>
                            </div>
                            <div class="col">
                                <div class="font-weight-bold">{{ number_format($linktree->views_count) }}</div>
                                <div class="text-muted small">Views</div>
                            </div>
                            <div class="col">
                                <div class="font-weight-bold">{{ number_format($linktree->clicks_count) }}</div>
                                <div class="text-muted small">Clicks</div>
                            </div>
                        </div>

                        <div class="btn-list">
                            <a href="{{ route('vendor.linktree.edit', $linktree) }}" class="btn btn-sm btn-outline-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"/>
                                    <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"/>
                                </svg>
                                Edit
                            </a>
                            <form action="{{ route('vendor.linktree.toggle-active', $linktree) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $linktree->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                    {{ $linktree->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            @if($linktree->is_active)
                            <a href="{{ route('linktree.public', $linktree->custom_url) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                                </svg>
                                Lihat
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
