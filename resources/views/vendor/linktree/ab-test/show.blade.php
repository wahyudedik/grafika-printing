@extends('layouts.vendor')

@section('content')
<div class="page-wrapper">
    <div class="container-xl">
        {{-- Page Header --}}
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-a-b" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M16 21v-2a4 4 0 0 0 -4 -4h-4a4 4 0 0 0 -4 4v2"/>
                            <path d="M8.5 4l3.5 16"/>
                            <path d="M13 4l-3.5 16"/>
                        </svg>
                        {{ $abTest->name }}
                    </h2>
                    <div class="page-pretitle">
                        <a href="{{ route('vendor.linktree.show', $linktree) }}">{{ $linktree->title }}</a> /
                        <a href="{{ route('vendor.linktree.ab-test.index', $linktree) }}">A/B Testing</a> / Detail
                    </div>
                </div>
                <div class="col-auto">
                    <div class="btn-list">
                        @if($abTest->status === 'draft')
                        <form action="{{ route('vendor.linktree.ab-test.start', [$linktree, $abTest]) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 4v16l13 -8z"/></svg>
                                Mulai Test
                            </button>
                        </form>
                        @endif
                        @if($abTest->status === 'running')
                        <form action="{{ route('vendor.linktree.ab-test.pause', [$linktree, $abTest]) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning">Jeda</button>
                        </form>
                        <form action="{{ route('vendor.linktree.ab-test.stop', [$linktree, $abTest]) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger">Hentikan & Evaluasi</button>
                        </form>
                        @endif
                        @if($abTest->status === 'paused')
                        <form action="{{ route('vendor.linktree.ab-test.start', [$linktree, $abTest]) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">Lanjutkan</button>
                        </form>
                        <form action="{{ route('vendor.linktree.ab-test.stop', [$linktree, $abTest]) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger">Hentikan & Evaluasi</button>
                        </form>
                        @endif
                        @if($abTest->status === 'completed' && $winner)
                        <form action="{{ route('vendor.linktree.ab-test.apply-winner', [$linktree, $abTest]) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('Terapkan template pemenang sebagai template utama?')">
                                Terapkan Pemenang
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('vendor.linktree.ab-test.index', $linktree) }}" class="btn btn-ghost">Kembali</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-body">
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

            {{-- Status & Meta --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="text-muted small">Status</div>
                            <div class="fw-bold">
                                <span class="badge bg-{{ $abTest->status_color }}">{{ $abTest->status_label }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="text-muted small">Traffic Split</div>
                            <div class="fw-bold">{{ $abTest->traffic_split }}% / {{ 100 - $abTest->traffic_split }}%</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="text-muted small">Dimulai</div>
                            <div class="fw-bold">{{ $abTest->started_at ? $abTest->started_at->format('d M Y H:i') : '-' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="text-muted small">Berakhir</div>
                            <div class="fw-bold">{{ $abTest->ended_at ? $abTest->ended_at->format('d M Y H:i') : '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Variant Comparison --}}
            <div class="row mb-4">
                {{-- Variant A --}}
                <div class="col-md-6">
                    <div class="card {{ $winner === 'variant_a' ? 'border-success border-2' : '' }}">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <h3 class="card-title">
                                    Variant A: <strong>{{ ucfirst($abTest->variant_a) }}</strong>
                                </h3>
                                @if($winner === 'variant_a')
                                <span class="badge bg-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="12" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6l-4 6h8z"/><path d="M5 18h14"/></svg>
                                    PEMENANG
                                </span>
                                @endif
                            </div>
                            <div class="card-subtitle">{{ $abTest->traffic_split }}% traffic</div>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col">
                                    <div class="h1 mb-0">{{ number_format($statsA['impressions']) }}</div>
                                    <div class="text-muted">Impressions</div>
                                </div>
                                <div class="col">
                                    <div class="h1 mb-0">{{ number_format($statsA['clicks']) }}</div>
                                    <div class="text-muted">Klik</div>
                                </div>
                                <div class="col">
                                    <div class="h1 mb-0 {{ $statsA['conversion_rate'] > $statsB['conversion_rate'] ? 'text-success' : '' }}">
                                        {{ $statsA['conversion_rate'] }}%
                                    </div>
                                    <div class="text-muted">Conversion</div>
                                </div>
                            </div>

                            {{-- Conversion Bar --}}
                            <div class="mt-3">
                                <div class="d-flex justify-content-between small">
                                    <span>Conversion Rate</span>
                                    <span class="fw-bold">{{ $statsA['conversion_rate'] }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-azure" style="width: {{ min(100, $statsA['conversion_rate'] * 10) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Variant B --}}
                <div class="col-md-6">
                    <div class="card {{ $winner === 'variant_b' ? 'border-success border-2' : '' }}">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <h3 class="card-title">
                                    Variant B: <strong>{{ ucfirst($abTest->variant_b) }}</strong>
                                </h3>
                                @if($winner === 'variant_b')
                                <span class="badge bg-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="12" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6l-4 6h8z"/><path d="M5 18h14"/></svg>
                                    PEMENANG
                                </span>
                                @endif
                            </div>
                            <div class="card-subtitle">{{ 100 - $abTest->traffic_split }}% traffic</div>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col">
                                    <div class="h1 mb-0">{{ number_format($statsB['impressions']) }}</div>
                                    <div class="text-muted">Impressions</div>
                                </div>
                                <div class="col">
                                    <div class="h1 mb-0">{{ number_format($statsB['clicks']) }}</div>
                                    <div class="text-muted">Klik</div>
                                </div>
                                <div class="col">
                                    <div class="h1 mb-0 {{ $statsB['conversion_rate'] > $statsA['conversion_rate'] ? 'text-success' : '' }}">
                                        {{ $statsB['conversion_rate'] }}%
                                    </div>
                                    <div class="text-muted">Conversion</div>
                                </div>
                            </div>

                            {{-- Conversion Bar --}}
                            <div class="mt-3">
                                <div class="d-flex justify-content-between small">
                                    <span>Conversion Rate</span>
                                    <span class="fw-bold">{{ $statsB['conversion_rate'] }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-pink" style="width: {{ min(100, $statsB['conversion_rate'] * 10) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistical Significance --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M3 21v-2a4 4 0 0 1 4 -4h4"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/></svg>
                        Signifikansi Statistik
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-{{ $significance['color'] }}">
                        <div class="d-flex">
                            <div>
                                @if($significance['level'] === 'high')
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                                @elseif($significance['level'] === 'medium')
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M12 11v2"/><path d="M12 15v.01"/></svg>
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 8l0 4"/><path d="M12 16l.01 0"/></svg>
                                @endif
                            </div>
                            <div>
                                <strong>{{ $significance['label'] }}</strong>
                                <br>{{ $significance['message'] }}
                            </div>
                        </div>
                    </div>

                    {{-- Minimum Sample Progress --}}
                    <div class="mt-3">
                        @php
                            $totalImpressions = $statsA['impressions'] + $statsB['impressions'];
                            $minTotal = $abTest->min_samples * 2;
                            $sampleProgress = min(100, ($totalImpressions / $minTotal) * 100);
                        @endphp
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Progres Minimum Sampel</span>
                            <span>{{ number_format($totalImpressions) }} / {{ number_format($minTotal) }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar {{ $sampleProgress >= 100 ? 'bg-success' : 'bg-blue' }}"
                                 style="width: {{ $sampleProgress }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Winner Result --}}
            @if($abTest->status === 'completed')
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Hasil Evaluasi</h3>
                </div>
                <div class="card-body text-center py-4">
                    @if($winner)
                    <div class="text-success mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-glyph" width="48" height="48" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6l-4 6h8z"/><path d="M5 18h14"/></svg>
                    </div>
                    <h2>Pemenang: <strong>{{ ucfirst($winner === 'variant_a' ? $abTest->variant_a : $abTest->variant_b) }}</strong></h2>
                    <p class="text-muted">
                        Variant {{ strtoupper($winner) }} menghasilkan conversion rate
                        <strong>{{ $winner === 'variant_a' ? $statsA['conversion_rate'] : $statsB['conversion_rate'] }}%</strong>
                        vs
                        <strong>{{ $winner === 'variant_a' ? $statsB['conversion_rate'] : $statsA['conversion_rate'] }}%</strong>
                    </p>
                    @if($abTest->winner)
                    <div class="mt-3">
                        <form action="{{ route('vendor.linktree.ab-test.apply-winner', [$linktree, $abTest]) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Terapkan template pemenang?')">
                                Terapkan Template {{ ucfirst($winner === 'variant_a' ? $abTest->variant_a : $abTest->variant_b) }}
                            </button>
                        </form>
                    </div>
                    @endif
                    @else
                    <div class="text-warning mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-glyph" width="48" height="48" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 8l0 4"/><path d="M12 16l.01 0"/></svg>
                    </div>
                    <h2>Tidak Ada Pemenang</h2>
                    <p class="text-muted">Perbedaan conversion rate tidak cukup signifikan untuk menentukan pemenang.</p>
                    @endif
                </div>
            </div>
            @endif

            {{-- Test Info --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Test</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted" style="width: 40%">Nama Test</td>
                                    <td class="fw-bold">{{ $abTest->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Variant A</td>
                                    <td><span class="badge bg-azure">{{ ucfirst($abTest->variant_a) }}</span> ({{ $abTest->traffic_split }}%)</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Variant B</td>
                                    <td><span class="badge bg-pink">{{ ucfirst($abTest->variant_b) }}</span> ({{ 100 - $abTest->traffic_split }}%)</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted" style="width: 40%">Minimum Sampel</td>
                                    <td>{{ number_format($abTest->min_samples) }} per varian</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Total Impressions</td>
                                    <td>{{ number_format($totalImpressions ?? 0) }}</td>
                                </tr>
                                @if($abTest->notes)
                                <tr>
                                    <td class="text-muted">Catatan</td>
                                    <td>{{ $abTest->notes }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($abTest->status !== 'running' && $abTest->status !== 'completed')
                    <hr>
                    <form action="{{ route('vendor.linktree.ab-test.destroy', [$linktree, $abTest]) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                onclick="return confirm('Hapus A/B test ini? Data tidak bisa dikembalikan.')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                            Hapus Test
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
