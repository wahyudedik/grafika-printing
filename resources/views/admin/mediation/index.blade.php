@extends('dev.layouts.app')

@section('title', 'Manajemen Mediasi')

@section('content')
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col-auto">
            <div class="page-pretitle">Admin Panel</div>
            <h2 class="page-title">Manajemen Mediasi</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('admin.mediation.statistics') }}" class="btn btn-outline-primary">
                <i class="ti ti-chart-bar"></i> Statistik
            </a>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        {{-- Filter --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.mediation.index') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_review" {{ request('status') === 'in_review' ? 'selected' : '' }}>In Review</option>
                                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Permintaan Mediasi</h3>
                <div class="card-actions">
                    <span class="text-muted">{{ $mediationRequests->total() }} permintaan</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Lelang</th>
                                <th>Vendor</th>
                                <th>Pengguna</th>
                                <th>Alasan</th>
                                <th>Status</th>
                                <th>Keputusan</th>
                                <th>Tanggal</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mediationRequests as $request)
                            <tr>
                                <td>
                                    <span class="badge bg-blue-lt">#{{ $request->id }}</span>
                                </td>
                                <td>
                                    @if($request->auction)
                                        <a href="{{ route('admin.auctions.show', $request->auction) }}" class="text-decoration-none">
                                            {{ Str::limit($request->auction->title ?? 'N/A', 30) }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($request->vendor)
                                        {{ $request->vendor->name ?? 'N/A' }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($request->user)
                                        {{ $request->user->name ?? 'N/A' }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted">{{ Str::limit($request->reason, 40) }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-lt text-yellow-fg',
                                            'in_review' => 'bg-blue-lt text-blue-fg',
                                            'resolved' => 'bg-green-lt text-green-fg',
                                            'closed' => 'bg-gray-lt text-gray-fg',
                                        ];
                                        $statusColor = $statusColors[$request->status] ?? 'bg-gray-lt text-gray-fg';
                                    @endphp
                                    <span class="badge {{ $statusColor }}">{{ $request->status_label }}</span>
                                </td>
                                <td>
                                    @if($request->admin_decision)
                                        @php
                                            $decisionColors = [
                                                'favor_user' => 'bg-green-lt text-green-fg',
                                                'favor_vendor' => 'bg-blue-lt text-blue-fg',
                                                'compromise' => 'bg-yellow-lt text-yellow-fg',
                                                'no_fault' => 'bg-gray-lt text-gray-fg',
                                            ];
                                            $decisionColor = $decisionColors[$request->admin_decision] ?? 'bg-gray-lt text-gray-fg';
                                        @endphp
                                        <span class="badge {{ $decisionColor }}">{{ $request->decision_label }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted">{{ $request->created_at->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.mediation.show', $request) }}" class="btn btn-sm btn-ghost-primary">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="ti ti-message-off icon-lg mb-2"></i>
                                    <br>
                                    Tidak ada permintaan mediasi
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($mediationRequests->hasPages())
            <div class="card-footer d-flex justify-content-center">
                {{ $mediationRequests->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
