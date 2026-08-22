@extends('layouts.user')

@section('title', 'Detail Konfirmasi Pengiriman')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6 space-y-6" x-data="disputeModal()">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Konfirmasi Pengiriman</h1>
            <p class="text-sm text-gray-500">Detail status pengiriman pesanan Anda</p>
        </div>
        <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold py-2 px-4 rounded-lg transition">
            <i class="fas fa-arrow-left text-xs"></i> Kembali
        </a>
    </div>

    {{-- Status Badge --}}
    @php
        $statusConfig = [
            'pending' => ['icon' => 'fa-clock', 'bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'border' => 'border-amber-200', 'label' => 'Menunggu Konfirmasi'],
            'delivered' => ['icon' => 'fa-box', 'bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'border' => 'border-blue-200', 'label' => 'Barang Diterima'],
            'confirmed' => ['icon' => 'fa-check', 'bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-200', 'label' => 'Dikonfirmasi'],
            'disputed' => ['icon' => 'fa-exclamation-triangle', 'bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-200', 'label' => 'Ada Masalah'],
            'resolved' => ['icon' => 'fa-check-circle', 'bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-200', 'label' => 'Selesai'],
        ];
        $config = $statusConfig[$confirmation->delivery_status] ?? ['icon' => 'fa-question', 'bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-200', 'label' => 'Unknown'];
    @endphp
    <div class="bg-white rounded-xl border {{ $config['border'] }} p-6 text-center">
        <div class="w-16 h-16 mx-auto {{ $config['bg'] }} rounded-full flex items-center justify-center mb-3">
            <i class="fas {{ $config['icon'] }} {{ $config['text'] }} text-2xl"></i>
        </div>
        <h3 class="text-lg font-semibold {{ $config['text'] }}">{{ $config['label'] }}</h3>
        @if($confirmation->delivery_date)
            <p class="text-sm text-gray-500 mt-1">{{ $confirmation->delivery_date->format('d M Y H:i') }}</p>
        @endif
    </div>

    {{-- Info Cards --}}
    <div class="grid md:grid-cols-2 gap-4">
        {{-- Info Pengiriman --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Info Pengiriman</h3>
            </div>
            <div class="p-4 space-y-3">
                <div>
                    <label class="text-xs text-gray-500">Tanggal Pengiriman</label>
                    <p class="text-sm font-medium text-gray-900">{{ $confirmation->delivery_date ? $confirmation->delivery_date->format('d M Y H:i') : '-' }}</p>
                </div>
                @if($confirmation->delivery_notes)
                <div>
                    <label class="text-xs text-gray-500">Catatan</label>
                    <p class="text-sm text-gray-700">{{ $confirmation->delivery_notes }}</p>
                </div>
                @endif
                @if($confirmation->confirmed_at)
                <div>
                    <label class="text-xs text-gray-500">Dikonfirmasi Pada</label>
                    <p class="text-sm font-medium text-gray-900">{{ $confirmation->confirmed_at->format('d M Y H:i') }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Info Vendor --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Vendor</h3>
            </div>
            <div class="p-4 space-y-3">
                <div>
                    <label class="text-xs text-gray-500">Nama Vendor</label>
                    <p class="text-sm font-medium text-gray-900">{{ $confirmation->vendor->name ?? 'N/A' }}</p>
                </div>
                @if($confirmation->auction)
                <div>
                    <label class="text-xs text-gray-500">Lelang</label>
                    <p class="text-sm text-gray-700">{{ $confirmation->auction->title ?? 'N/A' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Rating & Feedback --}}
    @if($confirmation->user_rating || $confirmation->user_feedback)
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-900">Rating & Feedback</h3>
        </div>
        <div class="p-4 space-y-3">
            @if($confirmation->user_rating)
            <div>
                <label class="text-xs text-gray-500">Rating</label>
                <div class="flex items-center gap-1 mt-1">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= $confirmation->user_rating ? 'text-amber-400' : 'text-gray-200' }} text-lg"></i>
                    @endfor
                    <span class="text-sm font-bold text-gray-900 ml-2">{{ $confirmation->user_rating }}/5</span>
                </div>
            </div>
            @endif
            @if($confirmation->user_feedback)
            <div>
                <label class="text-xs text-gray-500">Feedback</label>
                <p class="text-sm text-gray-700 mt-1">{{ $confirmation->user_feedback }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Foto Bukti --}}
    @if($confirmation->photos && count($confirmation->photos) > 0)
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-900">Foto Bukti</h3>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-3 gap-2">
                @foreach($confirmation->photos as $photo)
                <a href="{{ Storage::url($photo) }}" target="_blank" class="block">
                    <img src="{{ Storage::url($photo) }}" alt="Bukti {{ $loop->iteration }}" class="w-full h-32 sm:h-40 object-cover rounded-lg border border-gray-200 hover:opacity-80 transition-opacity">
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Dispute Info --}}
    @if($confirmation->hasDispute())
    <div class="bg-white rounded-xl border border-red-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-red-200 bg-red-50">
            <h3 class="text-sm font-semibold text-red-800 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i> Sengketa
            </h3>
        </div>
        <div class="p-4 space-y-3">
            <div>
                <label class="text-xs text-gray-500">Alasan Sengketa</label>
                <p class="text-sm font-medium text-gray-900">{{ $confirmation->dispute_reason ?? '-' }}</p>
            </div>
            @if($confirmation->dispute_resolved_at)
            <div>
                <label class="text-xs text-gray-500">Diselesaikan Pada</label>
                <p class="text-sm text-gray-700">{{ $confirmation->dispute_resolved_at->format('d M Y H:i') }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Action: Request Dispute --}}
    @if(in_array($confirmation->delivery_status, ['delivered', 'confirmed']) && !$confirmation->hasDispute())
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-900">Ada Masalah?</h3>
        </div>
        <div class="p-4">
            <p class="text-sm text-gray-500 mb-3">Jika barang yang diterima tidak sesuai atau ada masalah lainnya, Anda bisa mengajukan sengketa.</p>
            <button type="button" @click="openModal()" class="inline-flex items-center justify-center border border-red-300 text-red-700 hover:bg-red-50 font-semibold py-2 px-4 rounded-lg transition">
                <i class="fas fa-exclamation-triangle text-xs"></i> Ajukan Sengketa
            </button>
        </div>
    </div>
    @endif

    {{-- Dispute Modal (Alpine.js) --}}
    <div x-show="showModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" @click="closeModal()"></div>

        {{-- Modal Panel --}}
        <div class="flex min-h-full items-end sm:items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl w-full sm:max-w-lg transform transition-all"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Ajukan Sengketa</h3>
                    <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <form action="{{ route('user.delivery-confirmation.resolve-dispute', $confirmation) }}" method="POST">
                    @csrf
                    <div class="px-6 py-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Alasan Sengketa <span class="text-red-500">*</span>
                        </label>
                        <textarea name="dispute_reason" rows="4" required
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                  placeholder="Jelaskan masalah yang Anda hadapi..."></textarea>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                        <button type="button" @click="closeModal()" class="inline-flex items-center justify-center border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold py-2 px-4 rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                            Kirim Sengketa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function disputeModal() {
    return {
        showModal: false,
        openModal() {
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
        }
    }
}
</script>
@endpush
@endsection
