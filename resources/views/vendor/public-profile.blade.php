@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">{{ $vendor->name }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                @if ($vendor->logo)
                                    <img src="{{ Storage::url($vendor->logo) }}" alt="{{ $vendor->name }}"
                                        class="img-fluid rounded">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                        style="height: 200px;">
                                        <i class="fas fa-building fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-8">
                                <h5>Informasi Vendor</h5>
                                <p><strong>Email:</strong> {{ $vendor->email }}</p>
                                @if ($vendor->phone)
                                    <p><strong>Telepon:</strong> {{ $vendor->phone }}</p>
                                @endif
                                @if ($vendor->address)
                                    <p><strong>Alamat:</strong> {{ $vendor->address }}</p>
                                @endif
                                @if ($vendor->website)
                                    <p><strong>Website:</strong> <a href="{{ $vendor->website }}"
                                            target="_blank">{{ $vendor->website }}</a></p>
                                @endif

                                <div class="mt-3">
                                    <span class="badge {{ $vendor->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $vendor->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Statistik</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="text-primary">{{ $vendor->ratings->count() }}</h4>
                                <small class="text-muted">Rating</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success">{{ $vendor->ratings->avg('rating') ?? 0 }}</h4>
                                <small class="text-muted">Rata-rata</small>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($vendor->ratings->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="mb-0">Rating Terbaru</h5>
                        </div>
                        <div class="card-body">
                            @foreach ($vendor->ratings->take(3) as $rating)
                                <div class="d-flex align-items-center mb-2">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i
                                                    class="fas fa-star {{ $i <= $rating->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        </div>
                                        <small class="text-muted">{{ $rating->comment }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
