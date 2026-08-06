@extends('layouts.vendor')

@section('title', 'Detail Order ' . $order->order_number)

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('vendor.manual-transfers.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition-colors"><i class="fas fa-arrow-left text-sm"></i></a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Order {{ $order->order_number }}</h2>
        </div>
    </div>
    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">{{ $order->status_label }}</span>
</div>

@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
        <div class="flex items-center gap-2 text-green-800"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span></div>
        <button @click="show = false" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
    </div>
@endif

@if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center justify-between">
        <div class="flex items-center gap-2 text-red-800"><i class="fas fa-exclamation-circle"></i><span>{{ session('error') }}</span></div>
        <button @click="show = false" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main Content --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Customer Info --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-lg font-semibold text-gray-900">Informasi Pelanggan</h3></div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><div class="text-sm text-gray-500">Nama</div><div class="font-bold">{{ $order->customer_name }}</div></div>
                    <div><div class="text-sm text-gray-500">Telepon</div><div>{{ $order->customer_phone ?? '-' }}</div></div>
                    <div><div class="text-sm text-gray-500">Email</div><div>{{ $order->customer_email ?? '-' }}</div></div>
                    <div><div class="text-sm text-gray-500">Tanggal Order</div><div>{{ $order->created_at->format('d/m/Y H:i') }}</div></div>
                </div>
            </div>
        </div>

        {{-- Items --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-lg font-semibold text-gray-900">Items</h3></div>
            <div class="p-0">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Item</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Harga</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Qty</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-600">Subtotal</th>
                    </tr></thead>
                    <tbody>
                        @if($order->items && is_array($order->items))
                            @foreach($order->items as $item)
                                <tr class="border-b border-gray-100">
                                    <td class="py-3 px-4">{{ $item['name'] ?? '-' }}</td>
                                    <td class="py-3 px-4">Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4">{{ $item['quantity'] ?? 1 }}</td>
                                    <td class="py-3 px-4 text-right">Rp {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot><tr class="border-t border-gray-200">
                        <td colspan="3" class="py-3 px-4 text-right font-bold">Total</td>
                        <td class="py-3 px-4 text-right font-bold text-lg">{{ $order->formatted_total }}</td>
                    </tr></tfoot>
                </table>
            </div>
        </div>

        {{-- Transfer Proof --}}
        @if($order->transfer_proof)
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-lg font-semibold text-gray-900">Bukti Transfer</h3></div>
                <div class="p-5 text-center">
                    <img src="{{ asset('storage/manual_transfer_proofs/' . $order->transfer_proof) }}" alt="Bukti Transfer" class="max-h-96 mx-auto rounded-lg">
                </div>
            </div>
        @endif

        {{-- Notes --}}
        @if($order->notes)
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-lg font-semibold text-gray-900">Catatan</h3></div>
                <div class="p-5"><p class="mb-0">{{ $order->notes }}</p></div>
            </div>
        @endif

        {{-- Rejection Reason --}}
        @if($order->rejection_reason)
            <div class="bg-white rounded-xl border border-red-200">
                <div class="px-5 py-4 border-b border-red-200 bg-red-50"><h3 class="text-lg font-semibold text-red-700">Alasan Penolakan</h3></div>
                <div class="p-5"><p class="mb-0">{{ $order->rejection_reason }}</p></div>
            </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        {{-- Payment Info --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-lg font-semibold text-gray-900">Info Pembayaran</h3></div>
            <div class="p-5 space-y-3">
                <div><div class="text-sm text-gray-500">Bank</div><div class="font-bold">{{ $order->bank_name ?? '-' }}</div></div>
                <div><div class="text-sm text-gray-500">No. Rekening</div><div class="font-bold font-mono">{{ $order->account_number ?? '-' }}</div></div>
                <div><div class="text-sm text-gray-500">Atas Nama</div><div class="font-bold">{{ $order->account_name ?? '-' }}</div></div>
                @if($order->paid_at)
                    <div><div class="text-sm text-gray-500">Dibayar Pada</div><div>{{ $order->paid_at->format('d/m/Y H:i') }}</div></div>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-lg font-semibold text-gray-900">Aksi</h3></div>
            <div class="p-5 space-y-3">
                @if($order->isPaid())
                    <form action="{{ route('vendor.manual-transfers.confirm', $order) }}" method="POST" @submit="return confirm('Konfirmasi order ini sebagai selesai?')">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium text-sm">✅ Konfirmasi Selesai</button>
                    </form>
                @endif

                @if($order->status !== 'completed' && $order->status !== 'rejected')
                    <div x-data="{ open: false }">
                        <button @click="open = true" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium text-sm">❌ Tolak Order</button>
                        <div x-show="open" x-transition @keydown.escape.window="open = false" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="open = false"></div>
                            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6 mx-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tolak Order</h3>
                                <form action="{{ route('vendor.manual-transfers.reject', $order) }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan <span class="text-red-500">*</span></label>
                                        <textarea name="rejection_reason" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" rows="3" required placeholder="Masukkan alasan penolakan..."></textarea>
                                    </div>
                                    <div class="flex justify-end gap-3">
                                        <button type="button" @click="open = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm">Batal</button>
                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium text-sm">Tolak Order</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                @if($order->status === 'pending')
                    <div class="text-center text-sm text-gray-500 mt-3">Menunggu bukti transfer dari pelanggan</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
