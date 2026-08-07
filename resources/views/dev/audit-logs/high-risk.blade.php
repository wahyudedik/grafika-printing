@extends('dev.layouts.app')

@section('title', 'High Risk Audit Logs')
@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>High Risk Transactions
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Transaksi dengan risiko tinggi yang perlu dipantau</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                    {{ $logs->count() ?? 0 }} transaksi berisiko tinggi
                </span>
                <x.ui.button type="button" variant="outline" href="{{ route('admin.audit-logs.index') }}">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </x.ui.button>
                </a>
            </div>
        </div>

        {{-- Content --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            @if($logs->isEmpty())
                <x-ui.empty-state icon="fas fa-check-circle" title="Tidak ada transaksi berisiko tinggi" description="Semua transaksi dalam kondisi aman." />
            @else
                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Entity</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Risk</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $log->id }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                                                <span class="text-sm font-medium text-red-700 dark:text-red-300">{{ substr($log->user->name ?? 'A', 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $log->user->name ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->user->email ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $log->action_type == 'approve' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' :
                                               ($log->action_type == 'reject' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : 'bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-300') }}">
                                            {{ ucfirst($log->action_type) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300">{{ ucfirst($log->entity_type) }}</span>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">ID: {{ $log->entity_id }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-red-600 dark:text-red-400">
                                        @if($log->amount)
                                            Rp {{ number_format($log->amount, 0, ',', '.') }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $log->risk_level == 'critical' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' :
                                               ($log->risk_level == 'high' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' :
                                               ($log->risk_level == 'medium' ? 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300')) }}">
                                            {{ ucfirst($log->risk_level) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $log->status == 'completed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' :
                                               ($log->status == 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300') }}">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $log->created_at->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->created_at->format('H:i:s') }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('admin.audit-logs.show', $log->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-red-300 dark:border-red-600 text-red-700 dark:text-red-300 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-xs font-medium transition-colors">
                                            <i class="fas fa-eye"></i>
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                                                <i class="fas fa-shield-alt text-gray-400 text-lg"></i>
                                            </div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">Tidak ada data</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Belum ada transaksi berisiko tinggi.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($logs as $log)
                        <div class="p-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-medium text-red-700 dark:text-red-300">{{ substr($log->user->name ?? 'A', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $log->user->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->user->email ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $log->risk_level == 'critical' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ ucfirst($log->risk_level) }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-gray-500">Entity:</span>
                                    <span class="ml-1 text-gray-900 dark:text-white">{{ ucfirst($log->entity_type) }} #{{ $log->entity_id }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Amount:</span>
                                    <span class="ml-1 text-red-600 dark:text-red-400 font-medium">{{ $log->amount ? 'Rp ' . number_format($log->amount, 0, ',', '.') : '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Action:</span>
                                    <span class="ml-1 text-gray-900 dark:text-white">{{ ucfirst($log->action_type) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Date:</span>
                                    <span class="ml-1 text-gray-900 dark:text-white">{{ $log->created_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                            <a href="{{ route('admin.audit-logs.show', $log->id) }}" class="block w-full text-center px-3 py-1.5 border border-red-300 text-red-700 rounded-lg text-xs font-medium">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </a>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Tidak ada data</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Belum ada transaksi berisiko tinggi.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if(method_exists($logs, 'links'))
                    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                        {{ $logs->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
