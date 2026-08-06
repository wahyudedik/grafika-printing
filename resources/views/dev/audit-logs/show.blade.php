@extends('dev.layouts.app')

@section('title', 'Audit Log Details')
@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Audit Log Details</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Detail log audit #{{ $log->id }}</p>
            </div>
            <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm font-medium">
                <i class="fas fa-arrow-left"></i>
                <span>Back to List</span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Basic Information --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Basic Information</h2>
                </div>
                <div class="p-5">
                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Log ID</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $log->id }}</dd>
                        </div>
                        <div class="flex justify-between items-start">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">User</dt>
                            <dd class="text-right">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-medium text-primary-700 dark:text-primary-300">{{ substr($log->user->name ?? 'A', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $log->user->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->user->email ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Vendor</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $log->vendor->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Action</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $log->action_type == 'approve' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' :
                                       ($log->action_type == 'reject' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : 'bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-300') }}">
                                    {{ ucfirst($log->action_type) }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Entity Type</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300">
                                    {{ ucfirst($log->entity_type) }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Entity ID</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $log->entity_id }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Amount</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                @if($log->amount)
                                    Rp {{ number_format($log->amount, 0, ',', '.') }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $log->status == 'completed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' :
                                       ($log->status == 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300') }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Risk Level</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $log->risk_level == 'critical' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' :
                                       ($log->risk_level == 'high' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' :
                                       ($log->risk_level == 'medium' ? 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300')) }}">
                                    {{ ucfirst($log->risk_level) }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Transaction Reference</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $log->transaction_reference ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Created At</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $log->created_at->format('d M Y H:i:s') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Technical Details --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Technical Details</h2>
                </div>
                <div class="p-5">
                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">IP Address</dt>
                            <dd class="text-sm font-mono text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">{{ $log->ip_address }}</dd>
                        </div>
                        <div class="flex justify-between items-start">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">User Agent</dt>
                            <dd class="text-xs text-gray-500 dark:text-gray-400 text-right max-w-[60%] break-all">{{ Str::limit($log->user_agent, 50) }}</dd>
                        </div>
                        <div class="flex justify-between items-start">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Notes</dt>
                            <dd class="text-sm text-gray-900 dark:text-white text-right max-w-[60%]">{{ $log->notes ?? 'N/A' }}</dd>
                        </div>
                    </dl>

                    @if($log->old_data || $log->new_data)
                        <div class="mt-6 space-y-4">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Data Changes</h3>

                            @if($log->old_data)
                                <div>
                                    <h4 class="text-sm font-medium text-red-600 dark:text-red-400 mb-2">Old Data</h4>
                                    <div class="bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800 rounded-lg p-3 overflow-x-auto">
                                        <pre class="text-xs text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ json_encode($log->masked_old_data, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            @endif

                            @if($log->new_data)
                                <div>
                                    <h4 class="text-sm font-medium text-emerald-600 dark:text-emerald-400 mb-2">New Data</h4>
                                    <div class="bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-800 rounded-lg p-3 overflow-x-auto">
                                        <pre class="text-xs text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ json_encode($log->masked_new_data, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
