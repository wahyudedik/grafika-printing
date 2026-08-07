@extends('layouts.vendor')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <x-ui.breadcrumb :items="[['label' => 'Linktree Management', 'url' => route('vendor.linktree.index')], ['label' => $linktree->title, 'url' => route('vendor.linktree.show', $linktree)], ['label' => 'A/B Testing']]" />

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-vial text-purple-500"></i>
                A/B Testing
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <x.ui.button href="{{ route('vendor.linktree.show', $linktree) }}" variant="outline" size="sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </x.ui.button>
            <x.ui.button href="{{ route('vendor.linktree.ab-test.create', $linktree) }}" size="sm">
                <i class="fas fa-plus mr-1"></i> Buat A/B Test Baru
            </x.ui.button>
        </div>
    </div>

    {{-- Info Alert --}}
    <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
            <div class="flex-1 text-sm text-blue-800">
                <strong>A/B Testing</strong> memungkinkan Anda menguji dua template berbeda secara bersamaan.
                Pengunjung akan dilihatkan salah satu varian secara acak, dan sistem akan melacak mana yang menghasilkan lebih banyak klik.
                <br><strong>Catatan:</strong> Hanya satu A/B test yang bisa berjalan per linktree.
            </div>
            <button @click="show = false" class="text-blue-500 hover:text-blue-700"><i class="fas fa-times"></i></button>
        </div>
    </div>

    @if($abTests->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200">
        <x-ui.empty-state icon="fas fa-vial" title="Belum Ada A/B Test" description="Buat A/B test pertama untuk membandingkan performa dua template berbeda.">
            <x-slot:actions>
                <x.ui.button href="{{ route('vendor.linktree.ab-test.create', $linktree) }}">
                    <i class="fas fa-plus mr-1"></i> Buat A/B Test Baru
                </x.ui.button>
            </x-slot:actions>
        </x-ui.empty-state>
    </div>
    @else
    {{-- A/B Tests Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($abTests as $test)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">{{ $test->name }}</h3>
                    @php
                        $statusColors = ['draft' => 'gray', 'running' => 'green', 'paused' => 'yellow', 'completed' => 'blue'];
                        $sc = $statusColors[$test->status] ?? 'gray';
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $sc }}-100 text-{{ $sc }}-800">
                        {{ $test->status_label }}
                    </span>
                </div>
            </div>

            <div class="p-4">
                {{-- Variant Info --}}
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div class="text-center p-3 rounded-lg {{ $test->winner === 'variant_a' ? 'bg-green-50 ring-2 ring-green-300' : 'bg-gray-50' }}">
                        <div class="text-xs text-gray-500">Variant A</div>
                        <div class="font-bold text-sm text-gray-900">{{ ucfirst($test->variant_a) }}</div>
                        <div class="text-xs text-gray-500">{{ $test->traffic_split }}% traffic</div>
                    </div>
                    <div class="text-center p-3 rounded-lg {{ $test->winner === 'variant_b' ? 'bg-green-50 ring-2 ring-green-300' : 'bg-gray-50' }}">
                        <div class="text-xs text-gray-500">Variant B</div>
                        <div class="font-bold text-sm text-gray-900">{{ ucfirst($test->variant_b) }}</div>
                        <div class="text-xs text-gray-500">{{ 100 - $test->traffic_split }}% traffic</div>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-3 gap-2 text-center mb-4">
                    <div>
                        <div class="text-xs text-gray-500">Impressions</div>
                        <div class="font-bold text-gray-900">{{ number_format($test->results_count) }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Klik</div>
                        <div class="font-bold text-gray-900">{{ number_format($test->clicks_count) }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Min. Sampel</div>
                        <div class="font-bold text-gray-900">{{ number_format($test->min_samples) }}</div>
                    </div>
                </div>

                {{-- Progress --}}
                @php $progress = min(100, ($test->results_count / $test->min_samples) * 100); @endphp
                <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                    <div class="bg-blue-500 h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                </div>
                <p class="text-xs text-gray-500 text-center">{{ round($progress) }}% dari minimum sampel tercapai</p>

                {{-- Winner Badge --}}
                @if($test->winner)
                <div class="mt-3 p-2 bg-green-50 border border-green-200 rounded-lg text-center">
                    <i class="fas fa-trophy text-green-600 text-sm mr-1"></i>
                    <strong class="text-sm text-green-800">Pemenang: {{ ucfirst($test->winner === 'variant_a' ? $test->variant_a : $test->variant_b) }}</strong>
                </div>
                @endif
            </div>

            <div class="px-4 py-3 bg-gray-50 border-t border-gray-100 flex items-center gap-2">
                <x.ui.button href="{{ route('vendor.linktree.ab-test.show', [$linktree, $test]) }}" variant="outline-primary" size="xs">
                    <i class="fas fa-eye mr-1"></i> Detail
                </x.ui.button>
                @if($test->status === 'draft')
                <form action="{{ route('vendor.linktree.ab-test.start', [$linktree, $test]) }}" method="POST" class="inline">
                    @csrf
                    <x.ui.button type="submit" variant="success" size="sm">
                        <i class="fas fa-play mr-1"></i> Mulai
                    </x.ui.button>
                </form>
                @endif
                @if($test->status === 'running')
                <form action="{{ route('vendor.linktree.ab-test.pause', [$linktree, $test]) }}" method="POST" class="inline">
                    @csrf
                    <x.ui.button type="submit" variant="warning" size="sm">
                        <i class="fas fa-pause mr-1"></i> Jeda
                    </x.ui.button>
                </form>
                @endif
                @if($test->status === 'paused')
                <form action="{{ route('vendor.linktree.ab-test.start', [$linktree, $test]) }}" method="POST" class="inline">
                    @csrf
                    <x.ui.button type="submit" variant="success" size="sm">
                        <i class="fas fa-play mr-1"></i> Lanjut
                    </x.ui.button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
