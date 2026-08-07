@extends('layouts.vendor')

@section('title', 'Order Tracking - Vendor')

@section('content')
<x-ui.breadcrumb :items="[['label' => 'Order Tracking']]" />

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="text-sm text-gray-500 font-medium">Vendor Panel</div>
        <h2 class="text-2xl font-bold text-gray-900">Order Tracking</h2>
    </div>
</div>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Pesanan</h3>
            </div>
            <div class="p-5">
                @if($orderTrackings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Kode Pesanan</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Lelang</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Pembeli</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Status</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Resi</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600">Tanggal</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-600"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orderTrackings as $tracking)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors" x-data="{ open: false }">
                                <td class="py-3 px-4 font-medium">{{ $tracking->order_code ?? $tracking->id }}</td>
                                <td class="py-3 px-4">{{ Str::limit($tracking->auction->title ?? '-', 30) }}</td>
                                <td class="py-3 px-4">{{ $tracking->user->name ?? '-' }}</td>
                                <td class="py-3 px-4">
                                    @php
                                        $statusMap = [
                                            'pending' => ['Menunggu', 'bg-amber-100 text-amber-800'],
                                            'confirmed' => ['Dikonfirmasi', 'bg-blue-100 text-blue-800'],
                                            'processing' => ['Diproses', 'bg-blue-100 text-blue-800'],
                                            'shipped' => ['Dikirim', 'bg-purple-100 text-purple-800'],
                                            'delivered' => ['Diterima', 'bg-green-100 text-green-800'],
                                            'completed' => ['Selesai', 'bg-green-500 text-white'],
                                            'cancelled' => ['Dibatalkan', 'bg-red-100 text-red-800'],
                                        ];
                                        $s = $statusMap[$tracking->status] ?? [$tracking->status, 'bg-gray-100 text-gray-800'];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $s[1] }}">{{ $s[0] }}</span>
                                </td>
                                <td class="py-3 px-4">{{ $tracking->tracking_number ?? '-' }}</td>
                                <td class="py-3 px-4 text-gray-500">{{ $tracking->created_at->format('d M Y') }}</td>
                                <td class="py-3 px-4">
                                    <x.ui.button @click="open = !open" variant="primary" size="sm">Update</x.ui.button>
                                </td>
                            </tr>

                            {{-- Update Status Modal --}}
                            <tr x-show="open" x-transition @keydown.escape.window="open = false">
                                <td colspan="7" class="p-4 bg-gray-50">
                                    <form action="{{ route('vendor.tracking.update', $tracking) }}" method="POST" class="space-y-3">
                                        @csrf
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                                                <select class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" name="status" required>
                                                    <option value="pending" {{ $tracking->status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                                                    <option value="confirmed" {{ $tracking->status === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                                                    <option value="processing" {{ $tracking->status === 'processing' ? 'selected' : '' }}>Diproses</option>
                                                    <option value="shipped" {{ $tracking->status === 'shipped' ? 'selected' : '' }}>Dikirim</option>
                                                    <option value="delivered" {{ $tracking->status === 'delivered' ? 'selected' : '' }}>Diterima</option>
                                                    <option value="completed" {{ $tracking->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                                                    <option value="cancelled" {{ $tracking->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Resi</label>
                                                <input type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" name="tracking_number" value="{{ $tracking->tracking_number }}" placeholder="Nomor resi">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Estimasi</label>
                                                <input type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" name="estimated_delivery" value="{{ $tracking->estimated_delivery?->format('Y-m-d') }}">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Deskripsi</label>
                                                <input type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" name="status_description" placeholder="Deskripsi singkat">
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <input type="text" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm" name="notes" placeholder="Catatan tambahan" value="{{ $tracking->notes }}">
                                            <x.ui.button type="submit" variant="primary" size="sm">Simpan</x.ui.button>
                                            <x.ui.button @click="open = false" variant="outline" size="sm">Batal</x.ui.button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-center">{{ $orderTrackings->links() }}</div>
                @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-lg font-medium text-gray-900">Belum ada pesanan</p>
                    <p class="text-sm text-gray-500 mt-1">Pesanan dari lelang akan muncul di sini</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
