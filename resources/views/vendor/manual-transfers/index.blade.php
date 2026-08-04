@extends('layouts.vendor')

@section('title', 'Manual Transfer Orders')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Manual Transfer Orders</h2>
            <div class="page-pretitle">Pesanan transfer bank manual</div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible">
        {{ session('success') }}
        <a class="btn-close" data-bs-dismiss="alert"></a>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible">
        {{ session('error') }}
        <a class="btn-close" data-bs-dismiss="alert"></a>
    </div>
@endif

{{-- Statistics --}}
<div class="row row-cards mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Total</div>
                <div class="h1 mb-0">{{ $statistics['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Menunggu</div>
                <div class="h1 mb-0 text-yellow">{{ $statistics['pending'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Dibayar</div>
                <div class="h1 mb-0 text-blue">{{ $statistics['paid'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Selesai</div>
                <div class="h1 mb-0 text-green">{{ $statistics['completed'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col">
                <input type="text" name="search" class="form-control" placeholder="Cari nomor order, nama, HP..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Dibayar</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('vendor.manual-transfers.index') }}" class="btn btn-ghost">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Orders Table --}}
<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>No. Order</th>
                    <th>Pelanggan</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th class="w-1">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            <code>{{ $order->order_number }}</code>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $order->customer_name }}</div>
                            @if($order->customer_phone)
                                <div class="text-muted small">{{ $order->customer_phone }}</div>
                            @endif
                        </td>
                        <td>{{ $order->items_summary }}</td>
                        <td class="fw-bold">{{ $order->formatted_total }}</td>
                        <td>
                            <span class="badge bg-{{ $order->status_color }}-lt">{{ $order->status_label }}</span>
                        </td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('vendor.manual-transfers.show', $order) }}" class="btn btn-sm btn-ghost">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Belum ada order manual transfer.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex align-items-center justify-content-between">
        {{ $orders->links() }}
    </div>
</div>
@endsection
