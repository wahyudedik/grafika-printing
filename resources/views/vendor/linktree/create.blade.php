@extends('layouts.vendor')

@section('title', 'Buat Linktree Baru')

@section('content')
<div x-data="createLinktree()" class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <x-ui.breadcrumb :items="[['label' => 'Linktree Management', 'url' => route('vendor.linktree.index')], ['label' => 'Buat Baru']]" />

    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Buat Linktree Baru</h1>
        <p class="mt-1 text-sm text-gray-500">Buat halaman linktree untuk berbagi tautan penting toko Anda</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Form --}}
        <div class="lg:col-span-2 space-y-5">
            <form action="{{ route('vendor.linktree.store') }}" method="POST">
                @csrf

                {{-- Basic Info --}}
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900">Informasi Dasar</h2>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Linktree <span class="text-red-500">*</span></label>
                            <input type="text" id="title" name="title" value="{{ old('title', $vendor->name) }}" placeholder="Nama toko atau brand Anda"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('title') border-red-500 @enderror"
                                oninput="document.getElementById('preview-title').textContent = this.value || 'Nama Toko'" required>
                            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            <p class="mt-1 text-xs text-gray-500">Judul yang ditampilkan di halaman linktree Anda.</p>
                        </div>
                        <div>
                            <label for="custom_url" class="block text-sm font-medium text-gray-700 mb-1">URL Kustom <span class="text-red-500">*</span></label>
                            <div class="flex rounded-lg border border-gray-300 overflow-hidden focus-within:ring-2 focus-within:ring-primary-500 focus-within:border-primary-500">
                                <span class="bg-gray-50 px-3 py-2 text-sm text-gray-500 border-r border-gray-300 whitespace-nowrap">{{ config('app.url', 'https://grafika.noteds.com') }}/l/</span>
                                <input type="text" id="custom_url" name="custom_url" value="{{ old('custom_url') }}" placeholder="nama-toko-anda"
                                    pattern="[a-z0-9\-]+" class="flex-1 px-3 py-2 text-sm border-0 focus:ring-0 @error('custom_url') border-red-500 @enderror" required>
                            </div>
                            @error('custom_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            <p class="mt-1 text-xs text-gray-500">Hanya huruf kecil, angka, dan tanda hubung (-). Contoh: <code class="bg-gray-100 px-1 rounded">my-print-shop</code></p>
                        </div>
                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio / Deskripsi</label>
                            <textarea id="bio" name="bio" rows="3" placeholder="Deskripsi singkat tentang toko Anda"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('bio') border-red-500 @enderror"
                                oninput="document.getElementById('preview-bio').textContent = this.value || 'Deskripsi toko Anda'">{{ old('bio') }}</textarea>
                            @error('bio')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            <p class="mt-1 text-xs text-gray-500">Maksimal 500 karakter.</p>
                        </div>
                    </div>
                </div>

                {{-- Template Selection --}}
                <div class="bg-white rounded-xl border border-gray-200 mt-5">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900">Pilih Template</h2>
                    </div>
                    <div class="p-5">
                        @php
                        $templates = [
                            'minimal' => ['name' => 'Minimal', 'desc' => 'Bersih dan simpel', 'preview' => 'bg-white border border-gray-200'],
                            'colorful' => ['name' => 'Colorful', 'desc' => 'Ceriah dan menarik', 'preview' => 'bg-gradient-to-r from-purple-500 to-pink-500'],
                            'dark' => ['name' => 'Dark', 'desc' => 'Gelap dan elegan', 'preview' => 'bg-gray-900'],
                            'professional' => ['name' => 'Professional', 'desc' => 'Formal dan terpercaya', 'preview' => 'bg-slate-800'],
                        ];
                        @endphp
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach($templates as $key => $template)
                            <label class="cursor-pointer">
                                <input type="radio" name="template" value="{{ $key }}" class="hidden" {{ old('template', 'minimal') === $key ? 'checked' : '' }} onchange="selectTemplate('{{ $key }}')">
                                <div id="template-{{ $key }}" class="border-2 rounded-xl p-3 text-center transition-all {{ old('template', 'minimal') === $key ? 'border-primary-500 shadow-md' : 'border-gray-200 hover:border-gray-300' }}">
                                    <div class="{{ $template['preview'] }} rounded-lg mb-2 h-16"></div>
                                    <div class="font-semibold text-sm text-gray-900">{{ $template['name'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $template['desc'] }}</div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('template')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Button Style --}}
                <div class="bg-white rounded-xl border border-gray-200 mt-5">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900">Pengaturan Tombol</h2>
                    </div>
                    <div class="p-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gaya Tombol</label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach(['rounded' => 'Rounded (Bulat)', 'square' => 'Square (Kotak)', 'pill' => 'Pill'] as $style => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="button_style" value="{{ $style }}" class="hidden" {{ old('button_style', 'rounded') === $style ? 'checked' : '' }}>
                                <div class="border-2 rounded-lg p-3 text-center text-sm font-medium transition-all {{ old('button_style', 'rounded') === $style ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}"
                                     style="border-radius: {{ $style === 'rounded' ? '8px' : ($style === 'square' ? '2px' : '50px') }}">
                                    {{ $label }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('button_style')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Submit --}}
                <div class="mt-6 flex items-center gap-3">
                    <x.ui.button type="submit">
                        <i class="fas fa-plus mr-1"></i>Buat Linktree
                    </x.ui.button>
                    <x.ui.button href="{{ route('vendor.linktree.index') }}" variant="outline">Batal</x.ui.button>
                </div>
            </form>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            {{-- Preview --}}
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">Preview</h2>
                </div>
                <div class="p-5">
                    <div id="preview-container" class="rounded-xl p-4 text-center" style="min-height: 200px; background: #ffffff; border: 1px solid #e5e7eb;">
                        <div class="w-12 h-12 rounded-full bg-primary-600 text-white flex items-center justify-center text-lg font-bold mx-auto mb-2">T</div>
                        <h5 id="preview-title" class="font-semibold text-gray-900 mb-1">Nama Toko</h5>
                        <p id="preview-bio" class="text-sm text-gray-500 mb-3">Deskripsi toko Anda</p>
                        <div class="space-y-2">
                            <div id="preview-btn" class="px-4 py-2 border-2 border-gray-200 rounded-lg text-sm text-gray-600">Link 1</div>
                            <div id="preview-btn2" class="px-4 py-2 border-2 border-gray-200 rounded-lg text-sm text-gray-600">Link 2</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tips --}}
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">Tips</h2>
                </div>
                <div class="p-5">
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start gap-2"><i class="fas fa-check text-emerald-500 mt-0.5"></i>Pilih URL yang mudah diingat</li>
                        <li class="flex items-start gap-2"><i class="fas fa-check text-emerald-500 mt-0.5"></i>Gunakan huruf kecil saja</li>
                        <li class="flex items-start gap-2"><i class="fas fa-check text-emerald-500 mt-0.5"></i>Tanda hubung sebagai pengganti spasi</li>
                        <li class="flex items-start gap-2"><i class="fas fa-check text-emerald-500 mt-0.5"></i>Template bisa diubah kapan saja</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function createLinktree() {
    return {
        init() {
            selectTemplate('{{ old('template', 'minimal') }}');
        }
    };
}

function selectTemplate(template) {
    document.querySelectorAll('[id^="template-"]').forEach(card => {
        card.classList.remove('border-primary-500', 'shadow-md');
        card.classList.add('border-gray-200');
    });
    const selected = document.getElementById('template-' + template);
    if (selected) {
        selected.classList.remove('border-gray-200');
        selected.classList.add('border-primary-500', 'shadow-md');
    }

    const colors = {
        minimal: { bg: '#ffffff', btn: '#374151', text: '#1F2937' },
        colorful: { bg: '#F5F3FF', btn: '#8B5CF6', text: '#1F2937' },
        dark: { bg: '#111827', btn: '#6366F1', text: '#F9FAFB' },
        professional: { bg: '#F1F5F9', btn: '#1E3A5F', text: '#0F172A' },
    };

    const c = colors[template];
    const preview = document.getElementById('preview-container');
    preview.style.background = c.bg;
    preview.style.borderColor = template === 'minimal' ? '#e5e7eb' : 'transparent';

    document.getElementById('preview-title').style.color = c.text;
    document.getElementById('preview-bio').style.color = template === 'minimal' ? '#6B7280' : c.text + '99';

    ['preview-btn', 'preview-btn2'].forEach(id => {
        const btn = document.getElementById(id);
        if (template === 'colorful') {
            btn.style.background = 'linear-gradient(to right, #8B5CF6, #EC4899)';
            btn.style.border = 'none';
            btn.style.color = '#fff';
            btn.style.borderRadius = '8px';
        } else if (template === 'dark') {
            btn.style.background = '#374151';
            btn.style.border = 'none';
            btn.style.color = '#fff';
            btn.style.borderRadius = '8px';
        } else if (template === 'professional') {
            btn.style.background = '#1E3A5F';
            btn.style.border = 'none';
            btn.style.color = '#fff';
            btn.style.borderRadius = '8px';
        } else {
            btn.style.background = 'transparent';
            btn.style.border = '2px solid #e5e7eb';
            btn.style.color = '#374151';
            btn.style.borderRadius = '8px';
        }
    });
}
</script>
@endpush
@endsection
