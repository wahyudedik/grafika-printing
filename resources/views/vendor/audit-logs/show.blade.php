@extends('layouts.vendor')

@section('title', 'Detail Transaksi')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <x-ui.breadcrumb :items="[['label' => 'Riwayat Transaksi', 'url' => route('vendor.audit-logs.index')], ['label' => 'Detail']]" />

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Detail Transaksi</h3>
            <x.ui.button href="{{ route('vendor.audit-logs.index') }}" variant="outline" size="sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 6l6 6-6 6"/></svg>
                Kembali ke Daftar
            </x.ui.button>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Transaction Information --}}
                <div>
                    <h4 class="text-base font-semibold text-gray-900 mb-4">Informasi Transaksi</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Transaction ID:</span>
                            <span class="text-sm font-medium">{{ $log->id }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Action:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $log->action_type == 'approve' ? 'bg-green-100 text-green-800' : ($log->action_type == 'reject' ? 'bg-red-100 text-red-800' : 'bg-primary-100 text-primary-800') }}">{{ ucfirst($log->action_type) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Entity Type:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($log->entity_type) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Entity ID:</span>
                            <span class="text-sm font-medium">{{ $log->entity_id }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Amount:</span>
                            <span class="text-sm font-medium">@if($log->amount) Rp {{ number_format($log->amount, 0, ',', '.') }} @else <span class="text-gray-400">-</span> @endif</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Status:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $log->status == 'completed' ? 'bg-green-100 text-green-800' : ($log->status == 'failed' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">{{ ucfirst($log->status) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Reference:</span>
                            <span class="text-sm font-medium">{{ $log->transaction_reference ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Date:</span>
                            <span class="text-sm font-medium">{{ $log->created_at->format('d M Y H:i:s') }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-sm text-gray-500">Notes:</span>
                            <span class="text-sm font-medium">{{ $log->notes ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Transaction Details --}}
                <div>
                    <h4 class="text-base font-semibold text-gray-900 mb-4">Detail Transaksi</h4>

                    @if ($log->old_data || $log->new_data)
                        <div class="space-y-4 mb-6">
                            @if ($log->old_data)
                                <div>
                                    <h5 class="text-sm font-semibold text-red-600 mb-2">Data Sebelumnya</h5>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <pre class="text-xs whitespace-pre-wrap mb-0">{{ json_encode($log->masked_old_data, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            @endif

                            @if ($log->new_data)
                                <div>
                                    <h5 class="text-sm font-semibold text-green-600 mb-2">Data Saat Ini</h5>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <pre class="text-xs whitespace-pre-wrap mb-0">{{ json_encode($log->masked_new_data, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-blue-900 mb-1">🔒 Security Notice</h4>
                        <p class="text-sm text-blue-800 mb-0">Informasi sensitif seperti nomor rekening dan detail bank telah di-mask untuk keamanan. Hanya personel yang berwenang yang dapat melihat data lengkap.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
