@extends('dev.layouts.app')

@section('title', 'Statistik Mediasi')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col-auto">
            <div class="page-pretitle">Admin Panel</div>
            <h2 class="page-title">Statistik Mediasi</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('admin.mediation.index') }}" class="btn btn-outline-primary">
                Kembali
            </a>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        {{-- Ringkasan --}}
        <div class="row row-deck row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="subheader">Total Permintaan</div>
                        </div>
                        <div class="h1 mb-0">{{ $stats['total_requests'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="subheader">Pending</div>
                        </div>
                        <div class="h1 mb-0 text-yellow">{{ $stats['pending_requests'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="subheader">Dalam Review</div>
                        </div>
                        <div class="h1 mb-0 text-blue">{{ $stats['in_review'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="subheader">Selesai</div>
                        </div>
                        <div class="h1 mb-0 text-green">{{ $stats['resolved'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Keputusan --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ringkasan Keputusan</h3>
            </div>
            <div class="card-body">
                <div class="row row-deck row-cards">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader text-green">Favor Pengguna</div>
                                </div>
                                <div class="h2 mb-0 text-green mt-2">{{ $stats['favor_user'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader text-blue">Favor Vendor</div>
                                </div>
                                <div class="h2 mb-0 text-blue mt-2">{{ $stats['favor_vendor'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader text-yellow">Kompromi</div>
                                </div>
                                <div class="h2 mb-0 text-yellow mt-2">{{ $stats['compromise'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader text-gray">Tanpa Kesalahan</div>
                                </div>
                                <div class="h2 mb-0 text-gray mt-2">{{ $stats['no_fault'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Persentase --}}
        @if($stats['resolved'] > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Persentase Keputusan (dari {{ $stats['resolved'] }} yang diselesaikan)</h3>
            </div>
            <div class="card-body">
                @php
                    $totalDecisions = $stats['favor_user'] + $stats['favor_vendor'] + $stats['compromise'] + $stats['no_fault'];
                @endphp
                @if($totalDecisions > 0)
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Favor Pengguna</span>
                        <span class="text-green">{{ round(($stats['favor_user'] / $totalDecisions) * 100, 1) }}%</span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-green" style="width: {{ ($stats['favor_user'] / $totalDecisions) * 100 }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Favor Vendor</span>
                        <span class="text-blue">{{ round(($stats['favor_vendor'] / $totalDecisions) * 100, 1) }}%</span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-blue" style="width: {{ ($stats['favor_vendor'] / $totalDecisions) * 100 }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Kompromi</span>
                        <span class="text-yellow">{{ round(($stats['compromise'] / $totalDecisions) * 100, 1) }}%</span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-yellow" style="width: {{ ($stats['compromise'] / $totalDecisions) * 100 }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Tanpa Kesalahan</span>
                        <span class="text-gray">{{ round(($stats['no_fault'] / $totalDecisions) * 100, 1) }}%</span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-gray" style="width: {{ ($stats['no_fault'] / $totalDecisions) * 100 }}%"></div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
