@extends('dev.layouts.app')

@section('title', 'Manajemen Mediasi')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">Admin Panel</p>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Mediasi</h1>
        </div>
        <a href="{{ route('admin.mediation.statistics') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-300 rounded-lg hover:bg-blue-100">
            <i class="fas fa-chart-bar mr-2"></i> Statistik
        </a>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('admin.mediation.index') }}">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_review" {{ request('status') === 'in_review' ? 'selected' : '' }}>In Review</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" value="{{ request('date_from') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" value="{{ request('date_to') }}">
            </div>
            <div>
                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Permintaan Mediasi</h3>
            <span class="text-sm text-gray-500">{{ $mediationRequests->total() }} permintaan</span>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lelang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alasan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keputusan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($mediationRequests as $request)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">#{{ $request->id }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($request->auction)
                            <a href="{{ route('admin.auctions.show', $request->auction) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                {{ Str::limit($request->auction->title ?? 'N/A', 30) }}
                            </a>
                        @else
                            <span class="text-sm text-gray-400">N/A</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-900">{{ $request->vendor->name ?? 'N/A' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-900">{{ $request->user->name ?? 'N/A' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-500">{{ Str::limit($request->reason, 40) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusBadge = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'in_review' => 'bg-blue-100 text-blue-800',
                                'resolved' => 'bg-green-100 text-green-800',
                                'closed' => 'bg-gray-100 text-gray-800',
                            ];
                            $badgeClass = $statusBadge[$request->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $badgeClass }}">{{ $request->status_label }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($request->admin_decision)
                            @php
                                $decisionBadge = [
                                    'favor_user' => 'bg-green-100 text-green-800',
                                    'favor_vendor' => 'bg-blue-100 text-blue-800',
                                    'compromise' => 'bg-yellow-100 text-yellow-800',
                                    'no_fault' => 'bg-gray-100 text-gray-800',
                                ];
                                $decBadge = $decisionBadge[$request->admin_decision] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $decBadge }}">{{ $request->decision_label }}</span>
                        @else
                            <span class="text-sm text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-500">{{ $request->created_at->format('d M Y') }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <a href="{{ route('admin.mediation.show', $request) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg inline-flex">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center">
                        <i class="fas fa-comments-slash text-4xl text-gray-300 mb-2"></i>
                        <p class="text-sm text-gray-500">Tidak ada permintaan mediasi</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($mediationRequests->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 flex justify-center">
        {{ $mediationRequests->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
