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
                        A/B Testing
                    </h2>
                    <div class="page-pretitle">
                        <a href="{{ route('vendor.linktree.show', $linktree) }}">{{ $linktree->title }}</a> / A/B Testing
                    </div>
                </div>
                <div class="col-auto">
                    <div class="btn-list">
                        <a href="{{ route('vendor.linktree.show', $linktree) }}" class="btn btn-ghost">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0"/><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/></svg>
                            Kembali
                        </a>
                        <a href="{{ route('vendor.linktree.ab-test.create', $linktree) }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                            Buat A/B Test Baru
                        </a>
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

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M12 11v2"/><path d="M12 15v.01"/></svg>
                    </div>
                    <div>{{ session('error') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
            @endif

            {{-- Info Alert --}}
            <div class="alert alert-info mb-4">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M12 11v2"/><path d="M12 15v.01"/></svg>
                    </div>
                    <div>
                        <strong>A/B Testing</strong> memungkinkan Anda menguji dua template berbeda secara bersamaan.
                        Pengunjung akan dilihatkan salah satu varian secara acak, dan sistem akan melacak mana yang menghasilkan lebih banyak klik.
                        <br><strong>Catatan:</strong> Hanya satu A/B test yang bisa berjalan per linktree.
                    </div>
                </div>
            </div>

            @if($abTests->isEmpty())
            {{-- Empty State --}}
            <div class="card">
                <div class="card-body text-center py-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-glyph" width="48" height="48" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16 21v-2a4 4 0 0 0 -4 -4h-4a4 4 0 0 0 -4 4v2"/><path d="M8.5 4l3.5 16"/><path d="M13 4l-3.5 16"/></svg>
                    <h3 class="mt-3">Belum Ada A/B Test</h3>
                    <p class="text-muted">Buat A/B test pertama untuk membandingkan performa dua template berbeda.</p>
                    <a href="{{ route('vendor.linktree.ab-test.create', $linktree) }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                        Buat A/B Test Baru
                    </a>
                </div>
            </div>
            @else
            {{-- A/B Tests List --}}
            <div class="row row-cards">
                @foreach($abTests as $test)
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <h3 class="card-title">{{ $test->name }}</h3>
                                <span class="badge bg-{{ $test->status_color }}">{{ $test->status_label }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Variant Info --}}
                            <div class="mb-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="text-center p-2 rounded {{ $test->winner === 'variant_a' ? 'bg-success-lt' : '' }}">
                                            <div class="text-muted small">Variant A</div>
                                            <div class="fw-bold">{{ ucfirst($test->variant_a) }}</div>
                                            <div class="text-muted small">{{ $test->traffic_split }}% traffic</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center p-2 rounded {{ $test->winner === 'variant_b' ? 'bg-success-lt' : '' }}">
                                            <div class="text-muted small">Variant B</div>
                                            <div class="fw-bold">{{ ucfirst($test->variant_b) }}</div>
                                            <div class="text-muted small">{{ 100 - $test->traffic_split }}% traffic</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Stats --}}
                            <div class="row text-center mb-3">
                                <div class="col">
                                    <div class="text-muted small">Impressions</div>
                                    <div class="fw-bold">{{ number_format($test->results_count) }}</div>
                                </div>
                                <div class="col">
                                    <div class="text-muted small">Klik</div>
                                    <div class="fw-bold">{{ number_format($test->clicks_count) }}</div>
                                </div>
                                <div class="col">
                                    <div class="text-muted small">Min. Sampel</div>
                                    <div class="fw-bold">{{ number_format($test->min_samples) }}</div>
                                </div>
                            </div>

                            {{-- Progress --}}
                            @php $progress = min(100, ($test->results_count / $test->min_samples) * 100); @endphp
                            <div class="progress progress-sm mb-2">
                                <div class="progress-bar bg-blue" style="width: {{ $progress }}%"></div>
                            </div>
                            <div class="text-muted small text-center">
                                {{ round($progress) }}% dari minimum sampel tercapai
                            </div>

                            {{-- Winner Badge --}}
                            @if($test->winner)
                            <div class="mt-3 p-2 rounded bg-success-lt text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6l-4 6h8z"/><path d="M5 18h14"/></svg>
                                <strong>Pemenang: {{ ucfirst($test->winner === 'variant_a' ? $test->variant_a : $test->variant_b) }}</strong>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <div class="btn-list">
                                <a href="{{ route('vendor.linktree.ab-test.show', [$linktree, $test]) }}" class="btn btn-sm btn-primary">
                                    Detail
                                </a>
                                @if($test->status === 'draft')
                                <form action="{{ route('vendor.linktree.ab-test.start', [$linktree, $test]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Mulai</button>
                                </form>
                                @endif
                                @if($test->status === 'running')
                                <form action="{{ route('vendor.linktree.ab-test.pause', [$linktree, $test]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning">Jeda</button>
                                </form>
                                @endif
                                @if($test->status === 'paused')
                                <form action="{{ route('vendor.linktree.ab-test.start', [$linktree, $test]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Lanjut</button>
                                </form>
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
</div>
@endsection
