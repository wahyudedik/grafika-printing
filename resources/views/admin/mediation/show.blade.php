@extends('dev.layouts.app')

@section('title', 'Detail Mediasi #' . $mediationRequest->id)

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">Admin Panel</p>
            <h1 class="text-2xl font-bold text-gray-900">Detail Mediasi #{{ $mediationRequest->id }}</h1>
        </div>
        <x-ui.button variant="outline" :href="route('admin.mediation.index')">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </x-ui.button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main Content --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Mediasi</h3>
                    <div class="flex items-center gap-2">
                        @php
                            $statusBadge = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'in_review' => 'bg-blue-100 text-blue-800',
                                'resolved' => 'bg-green-100 text-green-800',
                                'closed' => 'bg-gray-100 text-gray-800',
                            ];
                        @endphp
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $statusBadge[$mediationRequest->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $mediationRequest->status_label }}
                        </span>
                        @if($mediationRequest->admin_decision)
                            @php
                                $decisionBadge = [
                                    'favor_user' => 'bg-green-100 text-green-800',
                                    'favor_vendor' => 'bg-blue-100 text-blue-800',
                                    'compromise' => 'bg-yellow-100 text-yellow-800',
                                    'no_fault' => 'bg-gray-100 text-gray-800',
                                ];
                            @endphp
                            <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $decisionBadge[$mediationRequest->admin_decision] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $mediationRequest->decision_label }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-1">Alasan</label>
                    <p class="text-sm text-gray-700">{{ $mediationRequest->reason }}</p>
                </div>
                @if($mediationRequest->description)
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-1">Deskripsi</label>
                    <p class="text-sm text-gray-700">{{ $mediationRequest->description }}</p>
                </div>
                @endif
                @if($mediationRequest->evidence_files && count($mediationRequest->evidence_files) > 0)
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Bukti</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($mediationRequest->evidence_files as $file)
                        <x-ui.button variant="outline-info" :href="Storage::url($file)" size="sm">
                            <i class="fas fa-file mr-1"></i> Bukti {{ $loop->iteration }}
                        </x-ui.button>
                        @endforeach
                    </div>
                </div>
                @endif
                @if($mediationRequest->resolution)
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-1">Resolusi</label>
                    <p class="text-sm text-gray-700">{{ $mediationRequest->resolution }}</p>
                </div>
                @endif
                @if($mediationRequest->admin_notes)
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-1">Catatan Admin</label>
                    <p class="text-sm text-gray-500">{{ $mediationRequest->admin_notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        {{-- Info Pelaku --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Pelaku</h3>
            </div>
            <div class="p-6 space-y-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-0.5">Pengguna</label>
                    <p class="text-sm font-bold text-gray-900">{{ $mediationRequest->user->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-0.5">Vendor</label>
                    <p class="text-sm font-bold text-gray-900">{{ $mediationRequest->vendor->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-0.5">Diminta Oleh</label>
                    <p class="text-sm font-bold text-gray-900">{{ $mediationRequest->requestedBy->name ?? 'N/A' }}</p>
                </div>
                @if($mediationRequest->resolvedBy)
                <div>
                    <label class="block text-xs text-gray-500 mb-0.5">Diselesaikan Oleh</label>
                    <p class="text-sm font-bold text-gray-900">{{ $mediationRequest->resolvedBy->name }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Info Lelang --}}
        @if($mediationRequest->auction)
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Lelang</h3>
            </div>
            <div class="p-6 space-y-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-0.5">Judul</label>
                    <p class="text-sm font-bold text-gray-900">{{ $mediationRequest->auction->title ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-0.5">Status</label>
                    <p class="text-sm text-gray-900">{{ $mediationRequest->auction->status ?? 'N/A' }}</p>
                </div>
                <a href="{{ route('admin.auctions.show', $mediationRequest->auction) }}" class="block w-full text-center px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-300 rounded-lg hover:bg-blue-100">
                    Lihat Lelang
                </a>
            </div>
        </div>
        @endif

        {{-- Info Keuangan --}}
        @if($mediationRequest->compensation_amount || $mediationRequest->penalty_amount)
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Keuangan</h3>
            </div>
            <div class="p-6 space-y-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-0.5">Kompensasi</label>
                    <p class="text-sm font-bold text-gray-900">Rp {{ number_format($mediationRequest->compensation_amount ?? 0, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-0.5">Denda</label>
                    <p class="text-sm font-bold text-gray-900">Rp {{ number_format($mediationRequest->penalty_amount ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Timeline --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Timeline</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center">
                            <i class="fas fa-plus text-yellow-600 text-xs"></i>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900">Dibuat</div>
                            <div class="text-xs text-gray-500">{{ $mediationRequest->created_at->format('d M Y H:i') }}</div>
                        </div>
                    </div>
                    @if($mediationRequest->status !== 'pending')
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-eye text-blue-600 text-xs"></i>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900">Review Dimulai</div>
                            <div class="text-xs text-gray-500">-</div>
                        </div>
                    </div>
                    @endif
                    @if($mediationRequest->resolved_at)
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-check text-green-600 text-xs"></i>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900">Diselesaikan</div>
                            <div class="text-xs text-gray-500">{{ $mediationRequest->resolved_at->format('d M Y H:i') }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Action Buttons --}}
@if($mediationRequest->status === 'pending')
<div class="mt-6 bg-white rounded-xl shadow-sm">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Aksi</h3>
    </div>
    <div class="p-6">
        <div class="flex gap-3">
            <form action="{{ route('admin.mediation.start-review', $mediationRequest) }}" method="POST">
                @csrf
                <x-ui.button variant="primary" type="submit">
                    <i class="fas fa-eye mr-2"></i> Mulai Review
                </x-ui.button>
            </form>
            <form id="close-mediation-form" action="{{ route('admin.mediation.close', $mediationRequest) }}" method="POST">
                @csrf
                <x-ui.button variant="outline-danger" type="submit" onclick="event.preventDefault(); confirmAction({ title: 'Tutup Mediasi', text: 'Tutup mediasi ini?', icon: 'warning', confirmText: 'Ya, Tutup', onConfirm: () => document.getElementById('close-mediation-form').submit() })">
                    <i class="fas fa-times mr-2"></i> Tutup
                </x-ui.button>
            </form>
        </div>
    </div>
</div>
@endif

@if($mediationRequest->status === 'in_review')
<div class="mt-6 bg-white rounded-xl shadow-sm">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Keputusan Mediasi</h3>
    </div>
    <div class="p-6">
        <form action="{{ route('admin.mediation.resolve', $mediationRequest) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keputusan <span class="text-red-500">*</span></label>
                    <select name="admin_decision" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">Pilih Keputusan</option>
                        <option value="favor_user">Favor Pengguna</option>
                        <option value="favor_vendor">Favor Vendor</option>
                        <option value="compromise">Kompromi</option>
                        <option value="no_fault">Tanpa Kesalahan</option>
                    </select>
                    @error('admin_decision') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kompensasi (Rp)</label>
                    <input type="number" name="compensation_amount" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" min="0" step="1000" value="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Denda (Rp)</label>
                    <input type="number" name="penalty_amount" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" min="0" step="1000" value="0">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Resolusi <span class="text-red-500">*</span></label>
                    <textarea name="resolution" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" rows="3" required placeholder="Jelaskan resolusi mediasi..."></textarea>
                    @error('resolution') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Admin</label>
                    <textarea name="admin_notes" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" rows="2" placeholder="Catatan internal..."></textarea>
                </div>
                <div class="md:col-span-2">
                    <x-ui.button type="submit" variant="success" onclick="event.preventDefault(); confirmAction({ title: 'Selesaikan Mediasi', text: 'Selesaikan mediasi dengan keputusan ini?', icon: 'question', confirmText: 'Ya, Selesaikan', onConfirm: () => this.closest('form').submit() })">
                        <i class="fas fa-check mr-2"></i> Selesaikan Mediasi
                    </x-ui.button>
                    <x-ui.button type="button" variant="outline-danger" class="ml-2" onclick="document.querySelector('[name=admin_decision]').value=''; this.closest('form').action='{{ route('admin.mediation.close', $mediationRequest) }}'; this.closest('form').submit();">
                        <i class="fas fa-times mr-2"></i> Tutup
                    </x-ui.button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
