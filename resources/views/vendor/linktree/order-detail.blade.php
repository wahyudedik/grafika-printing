@extends('layouts.vendor')

@section('title', 'Detail Pesanan #' . substr($order->uuid, 0, 8))

@section('content')
<div class="space-y-6" x-data="{ showStatusModal: false, showPaymentModal: false }">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('vendor.linktree.orders') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h2 class="text-2xl font-bold text-gray-900">Detail Pesanan</h2>
            </div>
            <p class="text-sm text-gray-500 ml-7">No. Order: <span class="font-mono text-gray-700">{{ $order->uuid }}</span></p>
        </div>
        <a href="{{ route('vendor.linktree.orders') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Orders
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Order Details --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Customer Info --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-900">
                        <i class="fas fa-user mr-2 text-gray-400"></i>Informasi Pelanggan
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Nama</div>
                            <div class="text-sm font-medium text-gray-900">{{ $order->customer_name }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 mb-1">No. WhatsApp</div>
                            <div class="text-sm font-medium text-gray-900">
                                {{ $order->customer_phone }}
                                @if($order->customer_phone)
                                @php
                                    $phone = preg_replace('/[^0-9]/', '', $order->customer_phone);
                                    if (substr($phone, 0, 1) === '0') $phone = '62' . substr($phone, 1);
                                @endphp
                                <a href="https://wa.me/{{ $phone }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1 ml-2 text-xs text-green-600 hover:text-green-700">
                                    <i class="fab fa-whatsapp"></i> Chat
                                </a>
                                @endif
                            </div>
                        </div>
                        @if($order->customer_email)
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Email</div>
                            <div class="text-sm text-gray-900">{{ $order->customer_email }}</div>
                        </div>
                        @endif
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Tanggal Pesanan</div>
                            <div class="text-sm text-gray-900">{{ $order->created_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Product & Specs --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-900">
                        <i class="fas fa-box mr-2 text-gray-400"></i>Detail Produk
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        @if($order->produk && is_array($order->produk->gambar) && count($order->produk->gambar) > 0)
                        <img src="{{ asset('produk_gambar/' . $order->produk->gambar[0]) }}" alt="{{ $order->produk->nama_produk }}"
                             class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                        @else
                        <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        @endif
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-gray-900">{{ $order->produk->nama_produk ?? '-' }}</div>
                            @if($order->selected_specs_text && $order->selected_specs_text !== '-')
                            <div class="mt-2">
                                <div class="text-xs text-gray-500 mb-1">Spesifikasi:</div>
                                @foreach($order->selected_specs ?? [] as $spec)
                                <div class="inline-flex items-center gap-1 px-2 py-1 bg-gray-50 rounded-md text-xs text-gray-700 mr-1 mb-1">
                                    <span class="font-medium">{{ $spec['nama'] ?? $spec['name'] ?? '' }}:</span>
                                    <span>{{ $spec['value'] ?? '' }}</span>
                                </div>
                                @endforeach
                            </div>
                            @endif
                            <div class="mt-2 flex items-center gap-4">
                                <div class="text-sm text-gray-600">Jumlah: <span class="font-semibold text-gray-900">{{ $order->quantity }}</span></div>
                                @if($order->total_price)
                                <div class="text-sm text-gray-600">Total: <span class="font-semibold text-green-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($order->notes)
                    <div class="mt-4 p-3 bg-yellow-50 rounded-lg border border-yellow-100">
                        <div class="text-xs text-yellow-600 font-medium mb-1"><i class="fas fa-sticky-note mr-1"></i>Catatan Pelanggan:</div>
                        <div class="text-sm text-yellow-800">{{ $order->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Vendor Notes --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-900">
                        <i class="fas fa-sticky-note mr-2 text-gray-400"></i>Catatan Vendor
                    </h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('vendor.linktree.orders.status', $order->uuid) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <textarea name="vendor_notes" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                  placeholder="Tambahkan catatan untuk pesanan ini...">{{ $order->vendor_notes }}</textarea>
                        <input type="hidden" name="status" value="{{ $order->status }}">
                        <div class="mt-2 text-right">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan Catatan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right Column: Status & Actions --}}
        <div class="space-y-6">
            {{-- Order Status --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-900">
                        <i class="fas fa-info-circle mr-2 text-gray-400"></i>Status Pesanan
                    </h3>
                </div>
                <div class="p-6">
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'confirmed' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'processing' => 'bg-purple-100 text-purple-800 border-purple-200',
                            'shipped' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                            'completed' => 'bg-green-100 text-green-800 border-green-200',
                            'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                        ];
                        $colorClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                    @endphp
                    <div class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium border {{ $colorClass }} mb-4">
                        {{ $order->status_label }}
                    </div>

                    <form action="{{ route('vendor.linktree.orders.status', $order->uuid) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PUT')
                        <label class="block text-xs font-medium text-gray-600">Ubah Status</label>
                        <select name="status"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Diproses</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Dikirim</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Update Status
                        </button>
                    </form>
                </div>
            </div>

            {{-- Payment Status --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-900">
                        <i class="fas fa-credit-card mr-2 text-gray-400"></i>Status Pembayaran
                    </h3>
                </div>
                <div class="p-6">
                    @php
                        $paymentColors = [
                            'unpaid' => 'bg-red-100 text-red-800 border-red-200',
                            'proof_sent' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'confirmed' => 'bg-green-100 text-green-800 border-green-200',
                            'rejected' => 'bg-red-100 text-red-800 border-red-200',
                        ];
                        $paymentColor = $paymentColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                    @endphp
                    <div class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium border {{ $paymentColor }} mb-4">
                        {{ $order->payment_status_label }}
                    </div>

                    <form action="{{ route('vendor.linktree.orders.payment', $order->uuid) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PUT')
                        <label class="block text-xs font-medium text-gray-600">Ubah Status Pembayaran</label>
                        <select name="payment_status"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="unpaid" {{ $order->payment_status === 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                            <option value="proof_sent" {{ $order->payment_status === 'proof_sent' ? 'selected' : '' }}>Bukti Dikirim</option>
                            <option value="confirmed" {{ $order->payment_status === 'confirmed' ? 'selected' : '' }}>Pembayaran Dikonfirmasi</option>
                            <option value="rejected" {{ $order->payment_status === 'rejected' ? 'selected' : '' }}>Pembayaran Ditolak</option>
                        </select>
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Update Pembayaran
                        </button>
                    </form>
                </div>
            </div>

            {{-- WhatsApp Quick Action --}}
            @if($order->customer_phone)
            @php
                $phone = preg_replace('/[^0-9]/', '', $order->customer_phone);
                if (substr($phone, 0, 1) === '0') $phone = '62' . substr($phone, 1);
                $waMessage = urlencode("Halo {$order->customer_name}, pesanan Anda dengan nomor {$order->uuid} sedang kami proses. Terima kasih!");
            @endphp
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <a href="https://wa.me/{{ $phone }}?text={{ $waMessage }}" target="_blank" rel="noopener noreferrer"
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fab fa-whatsapp text-lg"></i> Hubungi Pelanggan via WhatsApp
                    </a>
                </div>
            </div>
            @endif

            {{-- Order UUID --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-900">
                        <i class="fas fa-hashtag mr-2 text-gray-400"></i>ID Pesanan
                    </h3>
                </div>
                <div class="p-6">
                    <div class="font-mono text-sm text-gray-700 bg-gray-50 rounded-lg p-3 break-all">{{ $order->uuid }}</div>
                    <div class="text-xs text-gray-400 mt-2">Dibuat: {{ $order->created_at->format('d M Y, H:i:s') }}</div>
                    @if($order->updated_at && $order->updated_at != $order->created_at)
                    <div class="text-xs text-gray-400">Diupdate: {{ $order->updated_at->format('d M Y, H:i:s') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
