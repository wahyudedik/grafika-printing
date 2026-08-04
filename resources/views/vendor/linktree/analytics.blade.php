@extends('layouts.vendor')

@section('content')
<div class="page-wrapper">
    <div class="container-xl">
        <!-- Page Header -->
        <div class="page-pretitle">
            <a href="{{ route('vendor.linktree.index') }}" class="text-decoration-none">Linktree</a> / Analytics
        </div>
        <h2 class="page-title">
            📊 Analytics: {{ $linktree->title }}
        </h2>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Quick Stats -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Views</div>
                            </div>
                            <div class="h1 mb-0">{{ number_format($linktree->views_count) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Clicks</div>
                            </div>
                            <div class="h1 mb-0">{{ number_format($linktree->clicks_count) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Conversion Rate</div>
                            </div>
                            <div class="h1 mb-0 text-{{ $conversionRate > 50 ? 'success' : ($conversionRate > 20 ? 'warning' : 'danger') }}">
                                {{ $conversionRate }}%
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Active Links</div>
                            </div>
                            <div class="h1 mb-0">{{ $linktree->links->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <!-- Views vs Clicks Chart -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">📈 Performa Linktree</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <div class="flex-fill">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Views</span>
                                        <span class="fw-bold">{{ number_format($linktree->views_count) }}</span>
                                    </div>
                                    <div class="progress" style="height: 24px;">
                                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div class="flex-fill">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Clicks</span>
                                        <span class="fw-bold">{{ number_format($linktree->clicks_count) }}</span>
                                    </div>
                                    <div class="progress" style="height: 24px;">
                                        <div class="progress-bar bg-success" style="width: {{ $linktree->views_count > 0 ? min(($linktree->clicks_count / $linktree->views_count) * 100, 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Engagement Summary -->
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="fs-1">💡</span>
                                    </div>
                                    <div>
                                        <strong>Insight:</strong>
                                        @if($conversionRate > 50)
                                            Konversi sangat baik! Pengunjung aktif mengklik link Anda.
                                        @elseif($conversionRate > 20)
                                            Konversi cukup baik. Pertimbangkan untuk menambah CTA yang lebih menarik.
                                        @elseif($linktree->views_count > 0)
                                            Konversi masih rendah. Coba perbarui link dan deskripsi untuk lebih menarik.
                                        @else
                                            Belum ada data kunjungan. Bagikan linktree Anda!
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Info -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">ℹ️ Info</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="text-muted">URL Publik</td>
                                    <td>
                                        <a href="{{ url('/l/' . $linktree->custom_url) }}" target="_blank" class="text-decoration-none">
                                            /l/{{ $linktree->custom_url }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Template</td>
                                    <td><span class="badge bg-azure">{{ ucfirst($linktree->template) }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status</td>
                                    <td>
                                        @if($linktree->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Social Links</td>
                                    <td>{{ $socialCount }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">QRIS</td>
                                    <td>
                                        @if($linktree->show_qris)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h3 class="card-title mb-3">⚡ Aksi</h3>
                            <div class="btn-group-vertical w-100">
                                <a href="{{ route('vendor.linktree.show', $linktree) }}" class="btn btn-outline-primary">
                                    🔗 Lihat Linktree
                                </a>
                                <a href="{{ url('/l/' . $linktree->custom_url) }}" target="_blank" class="btn btn-outline-success">
                                    🌐 Buka Halaman Publik
                                </a>
                                <a href="{{ route('vendor.linktree.template.index', $linktree) }}" class="btn btn-outline-azure">
                                    🎨 Template Builder
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Links Table -->
            <div class="row g-3 mt-2">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">🔗 Top Links by Clicks</h3>
                        </div>
                        <div class="card-body p-0">
                            @if($topLinks->count() > 0)
                                <table class="table table-hover table-vcenter">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;">#</th>
                                            <th>Nama Link</th>
                                            <th>URL</th>
                                            <th style="width: 100px;" class="text-center">Clicks</th>
                                            <th style="width: 150px;">Performa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($topLinks as $index => $link)
                                            @php
                                                $maxClicks = $topLinks->first()->clicks_count ?? 1;
                                                $percentage = $maxClicks > 0 ? round(($link->clicks_count / $maxClicks) * 100) : 0;
                                            @endphp
                                            <tr>
                                                <td class="text-muted">{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $link->title }}</strong>
                                                    @if(!$link->is_active)
                                                        <span class="badge bg-secondary ms-1">Nonaktif</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ $link->url }}" target="_blank" class="text-muted text-decoration-none" style="max-width: 250px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                        {{ $link->url }}
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary">{{ number_format($link->clicks_count) }}</span>
                                                </td>
                                                <td>
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="empty py-5">
                                    <div class="empty-icon">📭</div>
                                    <p class="empty-title">Belum ada data clicks</p>
                                    <p class="empty-subtitle text-muted">Link clicks akan muncul di sini setelah pengunjung mulai mengklik link Anda.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
