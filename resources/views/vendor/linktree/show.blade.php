@extends('layouts.vendor')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-link text-purple-500"></i>
                {{ $linktree->title }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                <a href="{{ route('vendor.linktree.index') }}" class="text-primary-600 hover:underline">Linktree</a>
                <span class="mx-1">/</span> Detail
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            @if($linktree->is_active)
            <x.ui.button href="{{ route('linktree.public', $linktree->custom_url) }}" variant="outline-info" size="sm" target="_blank">
                <i class="fas fa-eye mr-1"></i> Lihat Publik
            </x.ui.button>
            @endif
            <x.ui.button href="{{ route('vendor.linktree.edit', $linktree) }}" size="sm">
                <i class="fas fa-pen mr-1"></i> Edit
            </x.ui.button>
            <x.ui.button href="{{ route('vendor.linktree.template.index', $linktree) }}" variant="info" size="sm">
                <i class="fas fa-palette mr-1"></i> Template
            </x.ui.button>
            <x.ui.button href="{{ route('vendor.linktree.analytics', $linktree) }}" variant="outline-info" size="sm">
                <i class="fas fa-chart-bar mr-1"></i> Analytics
            </x.ui.button>
            <x.ui.button href="{{ route('vendor.linktree.products', $linktree) }}" variant="outline-primary" size="sm">
                <i class="fas fa-box mr-1"></i> Produk
            </x.ui.button>

            {{-- Dropdown Tools --}}
            <div x-data="{ open: false }" class="relative">
                <x.ui.button @click="open = !open" variant="outline" size="sm">
                    <i class="fas fa-folder-open mr-1"></i> Link Tools <i class="fas fa-chevron-down text-xs ml-1"></i>
                </x.ui.button>
                <div x-show="open" @click.away="open = false" x-transition
                     class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-10">
                    <a href="{{ route('vendor.linktree.export-links', $linktree) }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-download text-gray-400"></i> Export ke CSV
                    </a>
                    <a href="{{ route('vendor.linktree.import-links-form', $linktree) }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-upload text-gray-400"></i> Import dari CSV
                    </a>
                    <hr class="my-1 border-gray-100">
                    <a href="{{ route('vendor.linktree.ab-test.index', $linktree) }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-vial text-gray-400"></i> A/B Testing
                    </a>
                </div>
            </div>

            <form action="{{ route('vendor.linktree.toggle-active', $linktree) }}" method="POST" class="inline">
                @csrf
                <x.ui.button type="submit" variant="{{ $linktree->is_active ? 'warning' : 'success' }}">
                    {{ $linktree->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                </x.ui.button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Linktree Info Card --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-gray-400"></i> Informasi Linktree
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs text-gray-500">Judul</label>
                                <div class="font-semibold text-gray-900">{{ $linktree->title }}</div>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">URL Publik</label>
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="flex-1 flex items-center rounded-lg border border-gray-300 overflow-hidden">
                                        <span class="bg-gray-50 px-3 py-2 text-xs text-gray-500 border-r border-gray-300 shrink-0">{{ config('app.url') }}/l/</span>
                                        <input type="text" value="{{ $linktree->custom_url }}" readonly class="flex-1 px-3 py-2 text-sm bg-white focus:outline-none">
                                    </div>
                                    <x.ui.button onclick="copyToClipboard('{{ config('app.url') }}/l/{{ $linktree->custom_url }}')" variant="outline-primary" size="xs">
                                        <i class="fas fa-copy mr-1"></i> Salin
                                    </x.ui.button>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs text-gray-500">Status</label>
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $linktree->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $linktree->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Template</label>
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($linktree->template) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($linktree->bio)
                    <div class="mt-4">
                        <label class="text-xs text-gray-500">Bio</label>
                        <div class="text-sm text-gray-600 mt-1">{{ $linktree->bio }}</div>
                    </div>
                    @endif

                    {{-- Color Preview --}}
                    <div class="mt-4">
                        <label class="text-xs text-gray-500 mb-2 block">Warna Tema</label>
                        <div class="flex items-center gap-4">
                            @foreach(['primary_color' => 'Primary', 'secondary_color' => 'Secondary', 'bg_color' => 'Background', 'text_color' => 'Text'] as $colorField => $label)
                            <div class="flex items-center gap-1.5">
                                <div class="w-6 h-6 rounded border border-gray-200" style="background-color: {{ $linktree->$colorField }};"></div>
                                <span class="text-xs text-gray-500">{{ $label }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="text-xs text-gray-500">Gaya Tombol</label>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">{{ ucfirst($linktree->button_style) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Links List --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-link text-gray-400"></i> Links ({{ $linktree->links->count() }})
                    </h2>
                </div>
                @if($linktree->links->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    <i class="fas fa-link text-4xl text-gray-300 mb-3"></i>
                    <p class="text-sm">Belum ada link. <a href="{{ route('vendor.linktree.edit', $linktree) }}" class="text-primary-600 hover:underline">Tambahkan link sekarang.</a></p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">URL</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Clicks</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($linktree->links as $index => $link)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-400">{!! $link->icon_html !!}</span>
                                        <span class="font-medium text-gray-900">{{ $link->title }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $typeColors = ['link' => 'blue', 'whatsapp' => 'green', 'qris' => 'yellow', 'phone' => 'gray', 'email' => 'purple'];
                                        $tc = $typeColors[$link->type] ?? 'gray';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $tc }}-100 text-{{ $tc }}-800">{{ ucfirst($link->type) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ $link->url }}" target="_blank" class="text-primary-600 hover:underline truncate block max-w-[200px]" title="{{ $link->url }}">{{ $link->url }}</a>
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-gray-900">{{ number_format($link->clicks_count) }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $link->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $link->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            {{-- Social Media List --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-share-alt text-gray-400"></i> Social Media ({{ $linktree->socials->count() }})
                    </h2>
                </div>
                @if($linktree->socials->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    <i class="fas fa-share-alt text-4xl text-gray-300 mb-3"></i>
                    <p class="text-sm">Belum ada social media. <a href="{{ route('vendor.linktree.edit', $linktree) }}" class="text-primary-600 hover:underline">Tambahkan sekarang.</a></p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Platform</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">URL</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($linktree->socials as $index => $social)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span style="color: {{ $social->platform_color }};">{!! $social->icon_html !!}</span>
                                        <span class="font-medium text-gray-900 capitalize">{{ $social->platform }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ $social->url }}" target="_blank" class="text-primary-600 hover:underline truncate block max-w-sm" title="{{ $social->url }}">{{ $social->url }}</a>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $social->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $social->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Statistics Card --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-chart-pie text-gray-400"></i> Statistik
                    </h2>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ number_format($linktree->views_count) }}</div>
                            <div class="text-xs text-gray-500">Total Views</div>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ number_format($linktree->clicks_count) }}</div>
                            <div class="text-xs text-gray-500">Total Clicks</div>
                        </div>
                        <div class="p-3 bg-orange-50 rounded-lg">
                            <div class="text-2xl font-bold text-orange-600">{{ $linktree->links->count() }}</div>
                            <div class="text-xs text-gray-500">Links</div>
                        </div>
                        <div class="p-3 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">{{ $linktree->socials->count() }}</div>
                            <div class="text-xs text-gray-500">Socials</div>
                        </div>
                    </div>

                    @if($linktree->views_count > 0)
                    <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                        <span class="text-xs text-gray-500">Click Rate:</span>
                        <span class="ml-1 font-bold text-primary-600">{{ number_format(($linktree->clicks_count / max($linktree->views_count, 1)) * 100, 1) }}%</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Media Card --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-image text-gray-400"></i> Media
                    </h2>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <label class="text-xs text-gray-500 mb-2 block">Avatar</label>
                        <div class="text-center">
                            @if($linktree->avatar)
                            <img src="{{ asset('linktree/avatars/' . $linktree->avatar) }}" alt="Avatar" class="w-20 h-20 rounded-full object-cover mx-auto border-2 border-gray-200">
                            @else
                            <div class="w-20 h-20 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-3xl font-bold mx-auto">
                                {{ strtoupper(substr($linktree->title, 0, 1)) }}
                            </div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 mb-2 block">Banner</label>
                        @if($linktree->banner)
                        <img src="{{ asset('linktree/banners/' . $linktree->banner) }}" alt="Banner" class="w-full h-24 object-cover rounded-lg border border-gray-200">
                        @else
                        <div class="w-full h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                            <span class="text-xs text-gray-400">Tidak ada banner</span>
                        </div>
                        @endif
                    </div>
                    @if($linktree->show_qris && $linktree->qris_image)
                    <div>
                        <label class="text-xs text-gray-500 mb-2 block">QRIS Image</label>
                        <div class="text-center">
                            <img src="{{ asset('linktree/qris/' . $linktree->qris_image) }}" alt="QRIS" class="max-w-[120px] mx-auto rounded-lg border border-gray-200">
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- SEO Settings Card --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-search text-gray-400"></i> Pengaturan SEO
                    </h2>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <label class="text-xs text-gray-500">Meta Title</label>
                        <div class="text-sm text-gray-700 mt-0.5">{{ $linktree->meta_title ?: '-' }}</div>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Meta Description</label>
                        <div class="text-sm text-gray-700 mt-0.5">{{ $linktree->meta_description ?: '-' }}</div>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">QRIS Ditampilkan</label>
                        <div class="mt-0.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $linktree->show_qris ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $linktree->show_qris ? 'Ya' : 'Tidak' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions Card --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-bolt text-gray-400"></i> Aksi Cepat
                    </h2>
                </div>
                <div class="p-4 space-y-2">
                    <x.ui.button href="{{ route('vendor.linktree.edit', $linktree) }}" variant="outline-primary" class="w-full justify-center">
                        <i class="fas fa-pen mr-1"></i> Edit Linktree
                    </x.ui.button>
                    @if($linktree->is_active)
                    <x.ui.button onclick="generateQRCode('{{ config('app.url') }}/l/{{ $linktree->custom_url }}')" variant="outline-info" class="w-full justify-center">
                        <i class="fas fa-qrcode mr-1"></i> Generate QR Code
                    </x.ui.button>
                    @endif
                    <form id="destroy-linktree-{{ $linktree->id }}" action="{{ route('vendor.linktree.destroy', $linktree) }}" method="POST"
                          x-data @submit.prevent="confirmFormSubmit('destroy-linktree-{{ $linktree->id }}', { title: 'Hapus Linktree?', text: 'Semua link dan social media akan ikut terhapus.', confirmText: 'Ya, Hapus', confirmColor: '#d33' })">
                        @csrf
                        @method('DELETE')
                        <x.ui.button type="submit" variant="danger" class="w-full">
                            <i class="fas fa-trash mr-1"></i> Hapus Linktree
                        </x.ui.button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- QR Code Modal --}}
<div x-data="qrModal()" x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false"></div>
        <div class="relative bg-white rounded-xl shadow-xl max-w-sm w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">QR Code Linktree</h3>
            <div class="text-center mb-4">
                <div x-show="loading" class="py-8">
                    <div class="w-8 h-8 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin mx-auto"></div>
                </div>
                <div x-show="!loading && error" class="py-4">
                    <i class="fas fa-exclamation-triangle text-red-500 text-3xl mb-2"></i>
                    <p class="text-sm text-red-600">Gagal memuat QR Code</p>
                </div>
                <div x-show="!loading && !error" class="py-4">
                    <img :src="qrUrl" alt="QR Code" class="max-w-[200px] mx-auto rounded-lg">
                </div>
            </div>
            <p class="text-xs text-gray-500 text-center mb-4" x-text="qrText"></p>
            <div class="flex items-center justify-center gap-3">
                <x.ui.button @click="downloadQR()" variant="outline-primary" size="sm">
                    <i class="fas fa-download mr-1"></i> Download
                </x.ui.button>
                <x.ui.button @click="open = false" variant="primary" size="sm">Tutup</x.ui.button>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showCopyToast();
    }).catch(() => {
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        showCopyToast();
    });
}

function showCopyToast() {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 z-50 p-3 bg-green-600 text-white rounded-lg shadow-lg flex items-center gap-2 text-sm';
    toast.innerHTML = '<i class="fas fa-check-circle"></i> URL berhasil disalin ke clipboard!';
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function generateQRCode(url) {
    const app = document.querySelector('[x-data="qrModal()"]').__x.$data;
    app.qrText = url;
    app.loading = true;
    app.error = false;
    app.open = true;
    app.qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(url)}`;
    const img = new Image();
    img.onload = () => { app.loading = false; };
    img.onerror = () => { app.loading = false; app.error = true; };
    img.src = app.qrUrl;
}

function qrModal() {
    return {
        open: false,
        loading: false,
        error: false,
        qrUrl: '',
        qrText: '',
        downloadQR() {
            const link = document.createElement('a');
            link.href = this.qrUrl;
            link.download = 'linktree-qr-{{ $linktree->custom_url }}.png';
            link.click();
        }
    };
}
</script>
@endsection
