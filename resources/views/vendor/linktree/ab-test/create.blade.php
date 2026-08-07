@extends('layouts.vendor')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <x-ui.breadcrumb :items="[['label' => 'Linktree Management', 'url' => route('vendor.linktree.index')], ['label' => $linktree->title, 'url' => route('vendor.linktree.show', $linktree)], ['label' => 'A/B Testing', 'url' => route('vendor.linktree.ab-test.index', $linktree)], ['label' => 'Buat Baru']]" />

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-vial text-purple-500"></i>
                Buat A/B Test Baru
            </h1>
                <span class="mx-1">/</span> Baru
            </p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto">
        <form action="{{ route('vendor.linktree.ab-test.store', $linktree) }}" method="POST">
            @csrf

            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Konfigurasi A/B Test</h2>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Test Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Test <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror"
                               placeholder="Contoh: Template Color Test Q4" required>
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Current Template Info --}}
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg flex items-center gap-3">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        <span class="text-sm text-blue-800">Template saat ini: <strong>{{ ucfirst($linktree->template) }}</strong></span>
                    </div>

                    {{-- Variant Selection --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="variant_a" class="block text-sm font-medium text-gray-700 mb-1">Variant A (Test) <span class="text-red-500">*</span></label>
                            <select id="variant_a" name="variant_a" required
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('variant_a') border-red-500 @enderror">
                                <option value="">Pilih Template...</option>
                                @foreach($templates as $template)
                                <option value="{{ $template }}" {{ old('variant_a') === $template ? 'selected' : '' }}>{{ ucfirst($template) }}</option>
                                @endforeach
                            </select>
                            @error('variant_a') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="variant_b" class="block text-sm font-medium text-gray-700 mb-1">Variant B (Control) <span class="text-red-500">*</span></label>
                            <select id="variant_b" name="variant_b" required
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('variant_b') border-red-500 @enderror">
                                <option value="">Pilih Template...</option>
                                @foreach($templates as $template)
                                <option value="{{ $template }}" {{ old('variant_b') === $template ? 'selected' : '' }}>{{ ucfirst($template) }}</option>
                                @endforeach
                            </select>
                            @error('variant_b') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Variant Preview --}}
                    <div x-data="{ show: false }" x-init="$watch('$refs.varA?.value || $refs.varB?.value', () => { show = $refs.varA?.value && $refs.varB?.value })" x-show="show" x-transition>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preview Varian</label>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="border border-gray-200 rounded-lg p-3 text-center">
                                <div class="font-semibold text-sm text-gray-900 mb-2" x-text="document.getElementById('variant_a')?.value?.charAt(0).toUpperCase() + document.getElementById('variant_a')?.value?.slice(1) || '-'"></div>
                                <div class="rounded-lg p-3 text-sm" :style="`background-color: ${templateColors[document.getElementById('variant_a')?.value]?.bg || '#f3f4f6'}; color: ${templateColors[document.getElementById('variant_a')?.value]?.text || '#374151'}`">
                                    <span :style="`color: ${templateColors[document.getElementById('variant_a')?.value]?.primary || '#6b7280'}`">●</span> Preview
                                </div>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-3 text-center">
                                <div class="font-semibold text-sm text-gray-900 mb-2" x-text="document.getElementById('variant_b')?.value?.charAt(0).toUpperCase() + document.getElementById('variant_b')?.value?.slice(1) || '-'"></div>
                                <div class="rounded-lg p-3 text-sm" :style="`background-color: ${templateColors[document.getElementById('variant_b')?.value]?.bg || '#f3f4f6'}; color: ${templateColors[document.getElementById('variant_b')?.value]?.text || '#374151'}`">
                                    <span :style="`color: ${templateColors[document.getElementById('variant_b')?.value]?.primary || '#6b7280'}`">●</span> Preview
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-200">

                    {{-- Traffic Split --}}
                    <div x-data="{ split: {{ old('traffic_split', 50) }} }">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Traffic Split <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-4">
                            <input type="range" min="10" max="90" step="5" name="traffic_split"
                                   x-model="split" class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-primary-600">
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" x-text="split + '%'"></span>
                                <span class="text-gray-400">:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800" x-text="(100 - split) + '%'"></span>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Persentase pengunjung yang melihat Variant A. Sisa melihat Variant B.</p>
                    </div>

                    {{-- Min Samples --}}
                    <div>
                        <label for="min_samples" class="block text-sm font-medium text-gray-700 mb-1">Minimum Sampel <span class="text-red-500">*</span></label>
                        <input type="number" id="min_samples" name="min_samples" value="{{ old('min_samples', 100) }}"
                               min="50" max="10000" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('min_samples') border-red-500 @enderror">
                        @error('min_samples') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-gray-500">Jumlah minimum impresi per varian sebelum evaluasi bisa dilakukan. Default: 100.</p>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                  placeholder="Catatan tentang tujuan test ini...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                    <x.ui.button href="{{ route('vendor.linktree.ab-test.index', $linktree) }}" variant="outline">
                        <i class="fas fa-arrow-left mr-1"></i> Batal
                    </x.ui.button>
                    <x.ui.button type="submit">
                        <i class="fas fa-plus mr-1"></i> Buat A/B Test
                    </x.ui.button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
const templateColors = {
    minimal: { bg: '#FFFFFF', text: '#1F2937', primary: '#374151' },
    colorful: { bg: '#F5F3FF', text: '#1F2937', primary: '#8B5CF6' },
    dark: { bg: '#111827', text: '#F9FAFB', primary: '#6366F1' },
    professional: { bg: '#F1F5F9', text: '#0F172A', primary: '#1E3A5F' },
};
</script>
@endsection
