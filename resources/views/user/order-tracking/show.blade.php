@extends('layouts.user')

@section('title', 'Detail Tracking Pesanan')

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Detail Tracking Pesanan</h1>
        <a href="{{ route('user.orders.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Pesanan #{{ $orderTracking->order_code ?? $orderTracking->id }}</h2>
                </div>
                <div class="px-6 py-5">
                    {{-- Status Timeline --}}
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Status Pesanan</h3>
                        @php
                            $statuses = [
                                'pending' => 'Menunggu Konfirmasi',
                                'confirmed' => 'Dikonfirmasi Vendor',
                                'processing' => 'Sedang Diproses',
                                'shipped' => 'Dikirim',
                                'delivered' => 'Diterima',
                                'completed' => 'Selesai'
                            ];
                            $currentStatusIndex = array_search($orderTracking->status, array_keys($statuses));
                        @endphp

                        <div class="relative pl-8">
                            {{-- Garis Timeline --}}
                            <div class="absolute left-3 top-1 bottom-1 w-0.5 bg-gray-200"></div>

                            @foreach($statuses as $key => $label)
                                @php
                                    $statusIndex = array_search($key, array_keys($statuses));
                                    $isActive = $orderTracking->status === $key;
                                    $isCompleted = $currentStatusIndex > $statusIndex;
                                    $isPending = $currentStatusIndex < $statusIndex;
                                @endphp
                                <div class="relative pb-6 last:pb-0">
                                    {{-- Titik Status --}}
                                    <div class="absolute -left-8 top-0.5 w-6 h-6 rounded-full border-2 flex items-center justify-center z-10
                                        {{ $isActive ? 'border-primary-500 bg-primary-500' : ($isCompleted ? 'border-green-500 bg-green-500' : 'border-gray-300 bg-white') }}">
                                        @if($isCompleted)
                                            <i class="fas fa-check text-white text-[10px]"></i>
                                        @elseif($isActive)
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                        @endif
                                    </div>
                                    {{-- Label --}}
                                    <div class="{{ $isActive ? '' : ($isPending ? 'opacity-50' : '') }}">
                                        <p class="text-sm font-medium text-gray-900 {{ $isActive ? 'text-primary-600' : '' }}">{{ $label }}</p>
                                        @if($isActive)
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $orderTracking->updated_at->format('d M Y H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <hr class="border-gray-100 my-5">

                    {{-- Order Details --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Lelang</p>
                            <a href="{{ route('user.auctions.show', $orderTracking->auction) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">
                                {{ $orderTracking->auction->title ?? '-' }}
                            </a>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Vendor</p>
                            <p class="text-sm font-medium text-gray-900">{{ $orderTracking->vendor->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Nomor Resi</p>
                            <p class="text-sm font-medium text-gray-900">{{ $orderTracking->tracking_number ?? 'Belum tersedia' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Estimasi Pengiriman</p>
                            <p class="text-sm font-medium text-gray-900">{{ $orderTracking->estimated_delivery ? $orderTracking->estimated_delivery->format('d M Y') : 'Belum tersedia' }}</p>
                        </div>
                        @if($orderTracking->notes)
                        <div class="sm:col-span-2">
                            <p class="text-xs text-gray-500 mb-0.5">Catatan</p>
                            <p class="text-sm text-gray-900">{{ $orderTracking->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Action Card --}}
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Aksi</h3>
                </div>
                <div class="px-5 py-4">
                    @if($orderTracking->status === 'shipped' || $orderTracking->status === 'delivered')
                        <form action="{{ route('user.orders.confirm-delivery', $orderTracking) }}" method="POST" enctype="multipart/form-data" x-data="{ rating: 5 }">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Bukti Terima</label>
                                <input type="file" name="delivery_photo" accept="image/*" required
                                    class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 file:cursor-pointer">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" @click="rating = {{ $i }}" class="text-2xl transition-colors"
                                            :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'">
                                            &#9733;
                                        </button>
                                    @endfor
                                    <input type="hidden" name="rating" :value="rating">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Feedback (Opsional)</label>
                                <textarea name="feedback" rows="3" placeholder="Berikan feedback tentang pesanan Anda"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                            </div>
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-check-circle mr-2"></i> Konfirmasi Penerimaan
                            </button>
                        </form>
                    @else
                        <div class="text-center py-4">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-clock text-gray-400"></i>
                            </div>
                            <p class="text-sm text-gray-500">Aksi akan tersedia setelah pesanan dikirim</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Mediation Request --}}
            @if(in_array($orderTracking->status, ['shipped', 'delivered', 'completed']))
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Ajukan Mediasi</h3>
                </div>
                <div class="px-5 py-4">
                    <form action="{{ route('user.orders.mediation', $orderTracking) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alasan</label>
                            <input type="text" name="reason" required placeholder="Contoh: Barang tidak sesuai"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="description" rows="3" required placeholder="Jelaskan masalah Anda"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bukti (Opsional, max 5 file)</label>
                            <input type="file" name="evidence_files[]" multiple accept="image/*,.pdf"
                                class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100 file:cursor-pointer">
                        </div>
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 transition-colors">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Ajukan Mediasi
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
