@extends('layouts.vendor')

@section('title', 'Analytics - ' . $linktree->title)

@section('content')
<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <x-ui.breadcrumb :items="[['label' => 'Linktree Management', 'url' => route('vendor.linktree.index')], ['label' => 'Analytics: ' . $linktree->title]]" />

    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">
            <i class="fas fa-chart-bar mr-2 text-primary-600"></i>Analytics: {{ $linktree->title }}
        </h1>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="text-sm text-gray-500 mb-1">Total Views</div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($linktree->views_count) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="text-sm text-gray-500 mb-1">Total Clicks</div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($linktree->clicks_count) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="text-sm text-gray-500 mb-1">Conversion Rate</div>
            <div class="text-2xl font-bold {{ $conversionRate > 50 ? 'text-emerald-600' : ($conversionRate > 20 ? 'text-amber-600' : 'text-red-600') }}">{{ $conversionRate }}%</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="text-sm text-gray-500 mb-1">Active Links</div>
            <div class="text-2xl font-bold text-gray-900">{{ $linktree->links->count() }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Performance Chart --}}
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900"><i class="fas fa-chart-line mr-2 text-primary-600"></i>Performa Linktree</h2>
                </div>
                <div class="p-5">
                    {{-- Views Bar --}}
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-500">Views</span>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($linktree->views_count) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-6">
                            <div class="bg-primary-500 rounded-full h-6" style="width: 100%"></div>
                        </div>
                    </div>
                    {{-- Clicks Bar --}}
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-500">Clicks</span>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($linktree->clicks_count) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-6">
                            <div class="bg-emerald-500 rounded-full h-6" style="width: {{ $linktree->views_count > 0 ? min(($linktree->clicks_count / $linktree->views_count) * 100, 100) : 0 }}%"></div>
                        </div>
                    </div>

                    {{-- Insight --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                        <span class="text-2xl">💡</span>
                        <div class="text-sm text-blue-800">
                            <strong>Insight:</strong>
                            @if($conversionRate > 50)
                                Konversi sangat baik! Pengunjung aktif mengklik link Anda.
                            @elseif($conversionRate > 20)
                                Konversi cukup baik. Pertimbangkan untuk menambah CTA yang lebih menarik.
                            @elseif($linktree->views_count > 0)
                                Konversi masih rendah. Coba perbarui link dan deskripsi untuk lebih menarik.
                            @else
                                Belum ada data kunjungan. Bagikan linktree Anda!
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top Links --}}
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900"><i class="fas fa-link mr-2 text-primary-600"></i>Top Links by Clicks</h2>
                </div>
                <div class="p-0">
                    @if($topLinks->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Link</th>
                                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">URL</th>
                                        <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase">Clicks</th>
                                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase w-40">Performa</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($topLinks as $index => $link)
                                        @php
                                            $maxClicks = $topLinks->first()->clicks_count ?? 1;
                                            $percentage = $maxClicks > 0 ? round(($link->clicks_count / $maxClicks) * 100) : 0;
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-5 py-3 text-gray-500">{{ $index + 1 }}</td>
                                            <td class="px-5 py-3 font-medium text-gray-900">
                                                {{ $link->title }}
                                                @if(!$link->is_active)
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-3 text-gray-500 max-w-xs truncate"><a href="{{ $link->url }}" target="_blank" class="hover:text-primary-600">{{ $link->url }}</a></td>
                                            <td class="px-5 py-3 text-center"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">{{ number_format($link->clicks_count) }}</span></td>
                                            <td class="px-5 py-3">
                                                <div class="w-full bg-gray-100 rounded-full h-1.5">
                                                    <div class="bg-emerald-500 rounded-full h-1.5" style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-4xl mb-3">📭</div>
                            <p class="text-gray-500 font-medium">Belum ada data clicks</p>
                            <p class="text-sm text-gray-400 mt-1">Link clicks akan muncul di sini setelah pengunjung mulai mengklik link Anda.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Quick Info --}}
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900"><i class="fas fa-info-circle mr-2 text-primary-600"></i>Info</h2>
                </div>
                <div class="p-5">
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">URL Publik</dt>
                            <dd><a href="{{ url('/l/' . $linktree->custom_url) }}" target="_blank" class="text-primary-600 hover:underline">/l/{{ $linktree->custom_url }}</a></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Template</dt>
                            <dd><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($linktree->template) }}</span></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Status</dt>
                            <dd><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $linktree->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">{{ $linktree->is_active ? 'Aktif' : 'Nonaktif' }}</span></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Social Links</dt>
                            <dd class="font-medium text-gray-900">{{ $socialCount }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">QRIS</dt>
                            <dd><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $linktree->show_qris ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">{{ $linktree->show_qris ? 'Aktif' : 'Nonaktif' }}</span></dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-3">
                <h3 class="text-base font-semibold text-gray-900 mb-3"><i class="fas fa-bolt mr-2 text-primary-600"></i>Aksi</h3>
                <x.ui.button href="{{ route('vendor.linktree.show', $linktree) }}" variant="outline-primary" class="w-full justify-center">
                    <i class="fas fa-link mr-1"></i>Lihat Linktree
                </x.ui.button>
                <x.ui.button href="{{ url('/l/' . $linktree->custom_url) }}" variant="outline-success" class="w-full justify-center" target="_blank">
                    <i class="fas fa-globe mr-1"></i>Buka Halaman Publik
                </x.ui.button>
                <x.ui.button href="{{ route('vendor.linktree.template.index', $linktree) }}" variant="outline-info" class="w-full justify-center">
                    <i class="fas fa-palette mr-1"></i>Template Builder
                </x.ui.button>
            </div>
        </div>
    </div>
</div>
@endsection
