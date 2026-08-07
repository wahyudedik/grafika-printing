@extends('layouts.vendor')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-vial text-purple-500"></i>
                {{ $abTest->name }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                <a href="{{ route('vendor.linktree.show', $linktree) }}" class="text-primary-600 hover:underline">{{ $linktree->title }}</a>
                <span class="mx-1">/</span>
                <a href="{{ route('vendor.linktree.ab-test.index', $linktree) }}" class="text-primary-600 hover:underline">A/B Testing</a>
                <span class="mx-1">/</span> Detail
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            @if($abTest->status === 'draft')
            <form action="{{ route('vendor.linktree.ab-test.start', [$linktree, $abTest]) }}" method="POST" class="inline">
                @csrf
                <x.ui.button type="submit" variant="success">
                    <i class="fas fa-play mr-1"></i> Mulai Test
                </x.ui.button>
            </form>
            @endif
            @if($abTest->status === 'running')
            <form action="{{ route('vendor.linktree.ab-test.pause', [$linktree, $abTest]) }}" method="POST" class="inline">
                @csrf
                <x.ui.button type="submit" variant="warning">
                    <i class="fas fa-pause mr-1"></i> Jeda
                </x.ui.button>
            </form>
            <form action="{{ route('vendor.linktree.ab-test.stop', [$linktree, $abTest]) }}" method="POST" class="inline">
                @csrf
                <x.ui.button type="submit" variant="danger">
                    <i class="fas fa-stop mr-1"></i> Hentikan & Evaluasi
                </x.ui.button>
            </form>
            @endif
            @if($abTest->status === 'paused')
            <form action="{{ route('vendor.linktree.ab-test.start', [$linktree, $abTest]) }}" method="POST" class="inline">
                @csrf
                <x.ui.button type="submit" variant="success">
                    <i class="fas fa-play mr-1"></i> Lanjutkan
                </x.ui.button>
            </form>
            <form action="{{ route('vendor.linktree.ab-test.stop', [$linktree, $abTest]) }}" method="POST" class="inline">
                @csrf
                <x.ui.button type="submit" variant="danger">
                    <i class="fas fa-stop mr-1"></i> Hentikan & Evaluasi
                </x.ui.button>
            </form>
            @endif
            @if($abTest->status === 'completed' && $winner)
            <form action="{{ route('vendor.linktree.ab-test.apply-winner', [$linktree, $abTest]) }}" method="POST" class="inline"
                  x-data @submit.prevent="if(confirm('Terapkan template pemenang sebagai template utama?')) $el.submit()">
                @csrf
                <x.ui.button type="submit" variant="success">
                    <i class="fas fa-check mr-1"></i> Terapkan Pemenang
                </x.ui.button>
            </form>
            @endif
            <x.ui.button href="{{ route('vendor.linktree.ab-test.index', $linktree) }}" variant="outline">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </x.ui.button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000"
         class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fas fa-check-circle text-green-500"></i>
            <span class="text-green-800 text-sm">{{ session('success') }}</span>
        </div>
        <button @click="show = false" class="text-green-500 hover:text-green-700"><i class="fas fa-times"></i></button>
    </div>
    @endif

    {{-- Status & Meta Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="text-xs text-gray-500 mb-1">Status</div>
            @php
                $statusColors = ['draft' => 'gray', 'running' => 'green', 'paused' => 'yellow', 'completed' => 'blue'];
                $sc = $statusColors[$abTest->status] ?? 'gray';
            @endphp
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $sc }}-100 text-{{ $sc }}-800">
                {{ $abTest->status_label }}
            </span>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="text-xs text-gray-500 mb-1">Traffic Split</div>
            <div class="font-bold text-gray-900">{{ $abTest->traffic_split }}% / {{ 100 - $abTest->traffic_split }}%</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="text-xs text-gray-500 mb-1">Dimulai</div>
            <div class="font-bold text-gray-900 text-sm">{{ $abTest->started_at ? $abTest->started_at->format('d M Y H:i') : '-' }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="text-xs text-gray-500 mb-1">Berakhir</div>
            <div class="font-bold text-gray-900 text-sm">{{ $abTest->ended_at ? $abTest->ended_at->format('d M Y H:i') : '-' }}</div>
        </div>
    </div>

    {{-- Variant Comparison --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- Variant A --}}
        <div class="bg-white rounded-xl border {{ $winner === 'variant_a' ? 'border-green-500 ring-2 ring-green-200' : 'border-gray-200' }} overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Variant A: <strong>{{ ucfirst($abTest->variant_a) }}</strong></h3>
                    @if($winner === 'variant_a')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-trophy text-xs"></i> PEMENANG
                    </span>
                    @endif
                </div>
                <div class="text-xs text-gray-500 mt-1">{{ $abTest->traffic_split }}% traffic</div>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-3 gap-2 text-center mb-4">
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($statsA['impressions']) }}</div>
                        <div class="text-xs text-gray-500">Impressions</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($statsA['clicks']) }}</div>
                        <div class="text-xs text-gray-500">Klik</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold {{ $statsA['conversion_rate'] > $statsB['conversion_rate'] ? 'text-green-600' : 'text-gray-900' }}">{{ $statsA['conversion_rate'] }}%</div>
                        <div class="text-xs text-gray-500">Conversion</div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-600">Conversion Rate</span>
                        <span class="font-bold text-gray-900">{{ $statsA['conversion_rate'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full transition-all" style="width: {{ min(100, $statsA['conversion_rate'] * 10) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Variant B --}}
        <div class="bg-white rounded-xl border {{ $winner === 'variant_b' ? 'border-green-500 ring-2 ring-green-200' : 'border-gray-200' }} overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Variant B: <strong>{{ ucfirst($abTest->variant_b) }}</strong></h3>
                    @if($winner === 'variant_b')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-trophy text-xs"></i> PEMENANG
                    </span>
                    @endif
                </div>
                <div class="text-xs text-gray-500 mt-1">{{ 100 - $abTest->traffic_split }}% traffic</div>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-3 gap-2 text-center mb-4">
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($statsB['impressions']) }}</div>
                        <div class="text-xs text-gray-500">Impressions</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($statsB['clicks']) }}</div>
                        <div class="text-xs text-gray-500">Klik</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold {{ $statsB['conversion_rate'] > $statsA['conversion_rate'] ? 'text-green-600' : 'text-gray-900' }}">{{ $statsB['conversion_rate'] }}%</div>
                        <div class="text-xs text-gray-500">Conversion</div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-600">Conversion Rate</span>
                        <span class="font-bold text-gray-900">{{ $statsB['conversion_rate'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-pink-500 h-2 rounded-full transition-all" style="width: {{ min(100, $statsB['conversion_rate'] * 10) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistical Significance --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-chart-bar text-blue-500"></i>
                Signifikansi Statistik
            </h3>
        </div>
        <div class="p-4">
            @php
                $significanceBg = match($significance['color'] ?? 'gray') {
                    'success' => 'bg-green-50 border-green-200',
                    'warning' => 'bg-yellow-50 border-yellow-200',
                    'danger' => 'bg-red-50 border-red-200',
                    default => 'bg-gray-50 border-gray-200',
                };
                $significanceIcon = match($significance['level'] ?? 'low') {
                    'high' => 'fa-check-circle text-green-500',
                    'medium' => 'fa-exclamation-triangle text-yellow-500',
                    default => 'fa-info-circle text-gray-500',
                };
            @endphp
            <div class="p-4 rounded-lg border {{ $significanceBg }}">
                <div class="flex items-start gap-3">
                    <i class="fas {{ $significanceIcon }} text-lg mt-0.5"></i>
                    <div>
                        <strong class="text-sm">{{ $significance['label'] }}</strong>
                        <p class="text-sm text-gray-600 mt-1">{{ $significance['message'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Minimum Sample Progress --}}
            @php
                $totalImpressions = $statsA['impressions'] + $statsB['impressions'];
                $minTotal = $abTest->min_samples * 2;
                $sampleProgress = min(100, ($totalImpressions / $minTotal) * 100);
            @endphp
            <div class="mt-4">
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-gray-600">Progres Minimum Sampel</span>
                    <span class="font-medium text-gray-900">{{ number_format($totalImpressions) }} / {{ number_format($minTotal) }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all {{ $sampleProgress >= 100 ? 'bg-green-500' : 'bg-blue-500' }}" style="width: {{ $sampleProgress }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Winner Result --}}
    @if($abTest->status === 'completed')
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Hasil Evaluasi</h3>
        </div>
        <div class="p-8 text-center">
            @if($winner)
            <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-trophy text-2xl text-green-600"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Pemenang: <strong>{{ ucfirst($winner === 'variant_a' ? $abTest->variant_a : $abTest->variant_b) }}</strong></h2>
            <p class="text-sm text-gray-500 mb-6">
                Variant {{ strtoupper($winner) }} menghasilkan conversion rate
                <strong class="text-green-600">{{ $winner === 'variant_a' ? $statsA['conversion_rate'] : $statsB['conversion_rate'] }}%</strong>
                vs
                <strong>{{ $winner === 'variant_a' ? $statsB['conversion_rate'] : $statsA['conversion_rate'] }}%</strong>
            </p>
            @if($abTest->winner)
            <form action="{{ route('vendor.linktree.ab-test.apply-winner', [$linktree, $abTest]) }}" method="POST" class="inline"
                  x-data @submit.prevent="if(confirm('Terapkan template pemenang?')) $el.submit()">
                @csrf
                <x.ui.button type="submit" variant="success">
                    <i class="fas fa-check mr-1"></i> Terapkan Template {{ ucfirst($winner === 'variant_a' ? $abTest->variant_a : $abTest->variant_b) }}
                </x.ui.button>
            </form>
            @endif
            @else
            <div class="w-16 h-16 mx-auto mb-4 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-info-circle text-2xl text-yellow-600"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Tidak Ada Pemenang</h2>
            <p class="text-sm text-gray-500">Perbedaan conversion rate tidak cukup signifikan untuk menentukan pemenang.</p>
            @endif
        </div>
    </div>
    @endif

    {{-- Test Info --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Informasi Test</h3>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Nama Test</span>
                        <span class="font-medium text-gray-900">{{ $abTest->name }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Variant A</span>
                        <span class="inline-flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($abTest->variant_a) }}</span>
                            <span class="text-gray-600">({{ $abTest->traffic_split }}%)</span>
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Variant B</span>
                        <span class="inline-flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">{{ ucfirst($abTest->variant_b) }}</span>
                            <span class="text-gray-600">({{ 100 - $abTest->traffic_split }}%)</span>
                        </span>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Minimum Sampel</span>
                        <span class="text-gray-900">{{ number_format($abTest->min_samples) }} per varian</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Total Impressions</span>
                        <span class="text-gray-900">{{ number_format($totalImpressions ?? 0) }}</span>
                    </div>
                    @if($abTest->notes)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Catatan</span>
                        <span class="text-gray-900">{{ $abTest->notes }}</span>
                    </div>
                    @endif
                </div>
            </div>

            @if($abTest->status !== 'running' && $abTest->status !== 'completed')
            <hr class="my-4 border-gray-200">
            <form action="{{ route('vendor.linktree.ab-test.destroy', [$linktree, $abTest]) }}" method="POST" class="inline"
                  x-data @submit.prevent="if(confirm('Hapus A/B test ini? Data tidak bisa dikembalikan.')) $el.submit()">
                @csrf
                @method('DELETE')
                <x.ui.button type="submit" variant="danger" size="sm">
                    <i class="fas fa-trash mr-1"></i> Hapus Test
                </x.ui.button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
