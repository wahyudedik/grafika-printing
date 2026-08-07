@extends('layouts.vendor')

@section('content')
<div x-data="linktreeEditor()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <x-ui.breadcrumb :items="[['label' => 'Linktree Management', 'url' => route('vendor.linktree.index')], ['label' => 'Edit: ' . $linktree->title]]" />

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Linktree: {{ $linktree->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                <a href="{{ route('vendor.linktree.index') }}" class="text-primary-600 hover:underline">Linktree</a>
                <span class="mx-1">/</span> Edit
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($linktree->is_active)
            <x.ui.button href="{{ route('linktree.public', $linktree->custom_url) }}" variant="info" size="sm" target="_blank">
                <i class="fas fa-eye"></i> Lihat Publik
            </x.ui.button>
            @endif
            <form action="{{ route('vendor.linktree.toggle-active', $linktree) }}" method="POST" class="inline">
                @csrf
                <x.ui.button type="submit" variant="{{ $linktree->is_active ? 'warning' : 'success' }}" size="sm">
                    <i class="fas fa-{{ $linktree->is_active ? 'pause' : 'play' }}"></i>
                    {{ $linktree->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                </x.ui.button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Settings Card --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-cog text-gray-400"></i> Pengaturan
                    </h2>
                </div>
                <form action="{{ route('vendor.linktree.update', $linktree) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                            <input type="text" id="title" name="title" value="{{ old('title', $linktree->title) }}" required
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('title') border-red-500 @enderror">
                            @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="custom_url" class="block text-sm font-medium text-gray-700 mb-1">URL Kustom <span class="text-red-500">*</span></label>
                            <div class="flex rounded-lg overflow-hidden border border-gray-300 focus-within:ring-2 focus-within:ring-primary-500 focus-within:border-primary-500">
                                <span class="bg-gray-50 px-3 py-2 text-sm text-gray-500 border-r border-gray-300 shrink-0">{{ config('app.url') }}/l/</span>
                                <input type="text" id="custom_url" name="custom_url" value="{{ old('custom_url', $linktree->custom_url) }}"
                                       pattern="[a-z0-9\-]+" required class="flex-1 px-3 py-2 text-sm focus:outline-none @error('custom_url') border-red-500 @enderror">
                            </div>
                            @error('custom_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                            <textarea id="bio" name="bio" rows="2"
                                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('bio') border-red-500 @enderror">{{ old('bio', $linktree->bio) }}</textarea>
                            @error('bio') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="template" class="block text-sm font-medium text-gray-700 mb-1">Template</label>
                                <select id="template" name="template" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @foreach(['minimal' => 'Minimal', 'colorful' => 'Colorful', 'dark' => 'Dark', 'professional' => 'Professional'] as $val => $lbl)
                                    <option value="{{ $val }}" {{ old('template', $linktree->template) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="button_style" class="block text-sm font-medium text-gray-700 mb-1">Gaya Tombol</label>
                                <select id="button_style" name="button_style" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @foreach(['rounded' => 'Rounded', 'square' => 'Square', 'pill' => 'Pill'] as $val => $lbl)
                                    <option value="{{ $val }}" {{ old('button_style', $linktree->button_style) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Color Settings --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Warna Utama</label>
                                <input type="color" id="primary_color" name="primary_color" value="{{ old('primary_color', $linktree->primary_color) }}"
                                       class="w-full h-10 rounded-lg border border-gray-300 cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Warna Sekunder</label>
                                <input type="color" id="secondary_color" name="secondary_color" value="{{ old('secondary_color', $linktree->secondary_color) }}"
                                       class="w-full h-10 rounded-lg border border-gray-300 cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Warna Background</label>
                                <input type="color" id="bg_color" name="bg_color" value="{{ old('bg_color', $linktree->bg_color) }}"
                                       class="w-full h-10 rounded-lg border border-gray-300 cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Warna Teks</label>
                                <input type="color" id="text_color" name="text_color" value="{{ old('text_color', $linktree->text_color) }}"
                                       class="w-full h-10 rounded-lg border border-gray-300 cursor-pointer">
                            </div>
                        </div>

                        {{-- Meta --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title (SEO)</label>
                            <input type="text" name="meta_title" value="{{ old('meta_title', $linktree->meta_title) }}"
                                   placeholder="Judul untuk SEO" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description (SEO)</label>
                            <textarea name="meta_description" rows="2" placeholder="Deskripsi untuk SEO"
                                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('meta_description', $linktree->meta_description) }}</textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center gap-3">
                        <x.ui.button type="submit">
                            <i class="fas fa-save mr-1"></i> Simpan Pengaturan
                        </x.ui.button>
                        <x.ui.button type="button" @click="showDeleteModal = true" variant="danger">
                            <i class="fas fa-trash mr-1"></i> Hapus Linktree
                        </x.ui.button>
                    </div>
                </form>
            </div>

            {{-- Links Management --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-link text-gray-400"></i> Links ({{ $linktree->links->count() }})
                    </h2>
                    <x.ui.button @click="showAddLinkModal = true" variant="primary" size="sm">
                        <i class="fas fa-plus mr-1"></i> Tambah Link
                    </x.ui.button>
                </div>
                <div class="divide-y divide-gray-100">
                    @if($linktree->links->isEmpty())
                    <x-ui.empty-state icon="fas fa-link" title="Belum ada link" description="Klik "Tambah Link" untuk menambahkan link pertama." size="sm" />
                    @else
                    <div id="links-list">
                        @foreach($linktree->links as $link)
                        <div class="px-4 py-3 flex items-center gap-3 hover:bg-gray-50 transition" data-id="{{ $link->id }}">
                            <span class="text-gray-300 cursor-move" title="Drag untuk mengurutkan">
                                <i class="fas fa-grip-vertical"></i>
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-sm text-gray-900">{{ $link->title }}</div>
                                <div class="text-xs text-gray-500 truncate max-w-xs">{{ $link->url }}</div>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $link->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $link->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                            <span class="text-xs text-gray-400 hidden sm:inline">{{ number_format($link->clicks_count) }} clicks</span>
                            <div class="flex items-center gap-1">
                                <x.ui.button @click="editLink({{ $link->id }}, '{{ addslashes($link->title) }}', '{{ addslashes($link->url) }}', '{{ $link->type }}', {{ $link->is_active ? 'true' : 'false' }})" variant="ghost" size="icon-sm" title="Edit">
                                    <i class="fas fa-pen text-xs"></i>
                                </x.ui.button>
                                <form id="destroy-link-{{ $link->id }}" action="{{ route('vendor.linktree.links.destroy', [$linktree, $link]) }}" method="POST" class="inline"
                                      x-data @submit.prevent="confirmFormSubmit('destroy-link-{{ $link->id }}', { title: 'Hapus Link?', text: 'Hapus link ini?', confirmText: 'Ya, Hapus', confirmColor: '#d33' })">
                                    @csrf
                                    @method('DELETE')
                                    <x.ui.button type="submit" variant="ghost" size="icon-sm" title="Hapus">
                                        <i class="fas fa-trash text-xs"></i>
                                    </x.ui.button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Social Media Management --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-share-alt text-gray-400"></i> Social Media ({{ $linktree->socials->count() }})
                    </h2>
                    <x.ui.button @click="showAddSocialModal = true" variant="primary" size="sm">
                        <i class="fas fa-plus mr-1"></i> Tambah Social
                    </x.ui.button>
                </div>
                <div class="divide-y divide-gray-100">
                    @if($linktree->socials->isEmpty())
                    <x-ui.empty-state icon="fas fa-share-alt" title="Belum ada social media" description="Tambahkan akun media sosial dari halaman edit." size="sm" />
                    @else
                    @foreach($linktree->socials as $social)
                    <div class="px-4 py-3 flex items-center gap-3 hover:bg-gray-50 transition">
                        <span style="color: {{ $social->platform_color }};">{!! $social->icon_html !!}</span>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-sm text-gray-900 capitalize">{{ $social->platform }}</div>
                            <div class="text-xs text-gray-500 truncate max-w-xs">{{ $social->url }}</div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $social->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ $social->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                        <div class="flex items-center gap-1">
                            <x.ui.button @click="editSocial({{ $social->id }}, '{{ $social->platform }}', '{{ addslashes($social->url) }}', {{ $social->is_active ? 'true' : 'false' }})" variant="ghost" size="icon-sm" title="Edit">
                                <i class="fas fa-pen text-xs"></i>
                            </x.ui.button>
                            <form id="destroy-social-{{ $social->id }}" action="{{ route('vendor.linktree.socials.destroy', [$linktree, $social]) }}" method="POST" class="inline"
                                  x-data @submit.prevent="confirmFormSubmit('destroy-social-{{ $social->id }}', { title: 'Hapus Social Media?', text: 'Hapus social media ini?', confirmText: 'Ya, Hapus', confirmColor: '#d33' })">
                                @csrf
                                @method('DELETE')
                                <x.ui.button type="submit" variant="ghost" size="icon-sm" title="Hapus">
                                    <i class="fas fa-trash text-xs"></i>
                                </x.ui.button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>

            {{-- Media Upload --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-image text-gray-400"></i> Media
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        {{-- Avatar --}}
                        <div class="text-center">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Avatar</label>
                            <div class="mb-3">
                                @if($linktree->avatar)
                                <img src="{{ asset('linktree/avatars/' . $linktree->avatar) }}" alt="Avatar" class="w-20 h-20 rounded-full object-cover mx-auto border-2 border-gray-200">
                                @else
                                <div class="w-20 h-20 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-3xl font-bold mx-auto">
                                    {{ strtoupper(substr($linktree->title, 0, 1)) }}
                                </div>
                                @endif
                            </div>
                            <form action="{{ route('vendor.linktree.upload-avatar', $linktree) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="file" name="avatar" accept="image/*" class="hidden" id="avatar-input" onchange="this.form.submit()">
                                <x.ui.button type="button" onclick="document.getElementById('avatar-input').click()" variant="outline-primary" size="sm">
                                    <i class="fas fa-upload mr-1"></i> Upload Avatar
                                </x.ui.button>
                            </form>
                        </div>

                        {{-- Banner --}}
                        <div class="text-center">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Banner</label>
                            <div class="mb-3">
                                @if($linktree->banner)
                                <img src="{{ asset('linktree/banners/' . $linktree->banner) }}" alt="Banner" class="w-full h-16 object-cover rounded-lg border border-gray-200">
                                @else
                                <div class="w-full h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                                @endif
                            </div>
                            <form action="{{ route('vendor.linktree.upload-banner', $linktree) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="file" name="banner" accept="image/*" class="hidden" id="banner-input" onchange="this.form.submit()">
                                <x.ui.button type="button" onclick="document.getElementById('banner-input').click()" variant="outline-primary" size="sm">
                                    <i class="fas fa-upload mr-1"></i> Upload Banner
                                </x.ui.button>
                            </form>
                        </div>

                        {{-- QRIS --}}
                        <div class="text-center">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gambar QRIS</label>
                            <div class="mb-3">
                                @if($linktree->qris_image)
                                <img src="{{ asset('linktree/qris/' . $linktree->qris_image) }}" alt="QRIS" class="w-20 h-20 object-cover rounded-lg mx-auto border border-gray-200">
                                @else
                                <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center mx-auto">
                                    <span class="text-xs text-gray-400">QRIS</span>
                                </div>
                                @endif
                            </div>
                            <form action="{{ route('vendor.linktree.upload-qris', $linktree) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="file" name="qris_image" accept="image/*" class="hidden" id="qris-input" onchange="this.form.submit()">
                                <x.ui.button type="button" onclick="document.getElementById('qris-input').click()" variant="outline-primary" size="sm">
                                    <i class="fas fa-upload mr-1"></i> Upload QRIS
                                </x.ui.button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar: Live Preview --}}
        <div class="lg:col-span-1">
            <div class="sticky top-4">
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900">Live Preview</h3>
                    </div>
                    <div class="p-4">
                        {{-- Phone Frame --}}
                        <div class="mx-auto max-w-[280px] rounded-2xl border-2 border-gray-200 overflow-hidden" style="background: {{ $linktree->bg_color }};">
                            @if($linktree->banner)
                            <img src="{{ asset('linktree/banners/' . $linktree->banner) }}" alt="Banner" class="w-full h-20 object-cover">
                            @else
                            <div class="h-10" style="background: {{ $linktree->primary_color }}30;"></div>
                            @endif
                            <div class="text-center p-4">
                                @if($linktree->avatar)
                                <img src="{{ asset('linktree/avatars/' . $linktree->avatar) }}" alt="Avatar" class="w-16 h-16 rounded-full object-cover mx-auto mb-2 border-2 border-white">
                                @else
                                <div class="w-16 h-16 rounded-full mx-auto mb-2 flex items-center justify-center text-white text-2xl font-bold" style="background: {{ $linktree->primary_color }};">
                                    {{ strtoupper(substr($linktree->title, 0, 1)) }}
                                </div>
                                @endif
                                <h5 class="font-bold text-sm" style="color: {{ $linktree->text_color }};">{{ $linktree->title }}</h5>
                                <p class="text-xs mt-0.5" style="color: {{ $linktree->text_color }}80;">{{ Str::limit($linktree->bio, 60) }}</p>
                            </div>
                            <div class="px-3 pb-3 space-y-2">
                                @foreach($linktree->links->where('is_active', true) as $link)
                                <div class="p-2 text-center text-white text-xs font-medium" style="background: {{ $linktree->primary_color }}; border-radius: {{ $linktree->button_style === 'pill' ? '50px' : ($linktree->button_style === 'square' ? '4px' : '8px') }};">
                                    {{ $link->title }}
                                </div>
                                @endforeach
                                @if($linktree->show_qris && $linktree->qris_image)
                                <div class="text-center pt-2">
                                    <img src="{{ asset('linktree/qris/' . $linktree->qris_image) }}" alt="QRIS" class="max-w-[100px] mx-auto rounded">
                                </div>
                                @endif
                            </div>
                            @if($linktree->socials->where('is_active', true)->count() > 0)
                            <div class="text-center pb-3 flex justify-center gap-3">
                                @foreach($linktree->socials->where('is_active', true) as $social)
                                <span class="text-lg">{!! $social->icon_html !!}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Link Modal --}}
    <div x-show="showAddLinkModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showAddLinkModal = false"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tambah Link Baru</h3>
                <form action="{{ route('vendor.linktree.links.store', $linktree) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Link <span class="text-red-500">*</span></label>
                            <input type="text" name="title" required placeholder="Contoh: Website Toko"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">URL <span class="text-red-500">*</span></label>
                            <input type="url" name="url" required placeholder="https://..."
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Link</label>
                            <select name="type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="link">🔗 Link Umum</option>
                                <option value="whatsapp">📱 WhatsApp</option>
                                <option value="qris">💳 QRIS</option>
                                <option value="phone">📞 Telepon</option>
                                <option value="email">📧 Email</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-6">
                        <x.ui.button type="button" @click="showAddLinkModal = false" variant="outline" size="sm">Batal</x.ui.button>
                        <x.ui.button type="submit" variant="primary" size="sm">Tambah Link</x.ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Link Modal --}}
    <div x-show="showEditLinkModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditLinkModal = false"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit Link</h3>
                <form :action="editLinkFormAction" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Link <span class="text-red-500">*</span></label>
                            <input type="text" name="title" x-model="editLinkData.title" required
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">URL <span class="text-red-500">*</span></label>
                            <input type="url" name="url" x-model="editLinkData.url" required
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Link</label>
                            <select name="type" x-model="editLinkData.type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="link">🔗 Link Umum</option>
                                <option value="whatsapp">📱 WhatsApp</option>
                                <option value="qris">💳 QRIS</option>
                                <option value="phone">📞 Telepon</option>
                                <option value="email">📧 Email</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" x-model="editLinkData.active" id="edit-link-active"
                                   class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <label for="edit-link-active" class="text-sm font-medium text-gray-700">Aktif</label>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-6">
                        <x.ui.button type="button" @click="showEditLinkModal = false" variant="outline" size="sm">Batal</x.ui.button>
                        <x.ui.button type="submit" variant="primary" size="sm">Simpan Perubahan</x.ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Social Modal --}}
    <div x-show="showAddSocialModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showAddSocialModal = false"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tambah Social Media</h3>
                <form action="{{ route('vendor.linktree.socials.store', $linktree) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Platform <span class="text-red-500">*</span></label>
                            <select name="platform" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Pilih Platform</option>
                                <option value="instagram">📷 Instagram</option>
                                <option value="facebook">📘 Facebook</option>
                                <option value="twitter">🐦 Twitter/X</option>
                                <option value="tiktok">🎵 TikTok</option>
                                <option value="youtube">📺 YouTube</option>
                                <option value="whatsapp">💬 WhatsApp</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">URL Profil <span class="text-red-500">*</span></label>
                            <input type="url" name="url" required placeholder="https://..."
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-6">
                        <x.ui.button type="button" @click="showAddSocialModal = false" variant="outline" size="sm">Batal</x.ui.button>
                        <x.ui.button type="submit" variant="primary" size="sm">Tambah</x.ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Social Modal --}}
    <div x-show="showEditSocialModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditSocialModal = false"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit Social Media</h3>
                <form :action="editSocialFormAction" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Platform <span class="text-red-500">*</span></label>
                            <select name="platform" x-model="editSocialData.platform" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="instagram">📷 Instagram</option>
                                <option value="facebook">📘 Facebook</option>
                                <option value="twitter">🐦 Twitter/X</option>
                                <option value="tiktok">🎵 TikTok</option>
                                <option value="youtube">📺 YouTube</option>
                                <option value="whatsapp">💬 WhatsApp</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">URL Profil <span class="text-red-500">*</span></label>
                            <input type="url" name="url" x-model="editSocialData.url" required
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" x-model="editSocialData.active" id="edit-social-active"
                                   class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <label for="edit-social-active" class="text-sm font-medium text-gray-700">Aktif</label>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-6">
                        <x.ui.button type="button" @click="showEditSocialModal = false" variant="outline" size="sm">Batal</x.ui.button>
                        <x.ui.button type="submit" variant="primary" size="sm">Simpan Perubahan</x.ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="showDeleteModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDeleteModal = false"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <form action="{{ route('vendor.linktree.destroy', $linktree) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="text-center">
                        <div class="w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Hapus Linktree</h3>
                        <p class="text-sm text-gray-500 mb-1">Apakah Anda yakin ingin menghapus linktree <strong>{{ $linktree->title }}</strong>?</p>
                        <p class="text-xs text-gray-400 mb-6">Semua link dan social media yang terkait juga akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <div class="flex items-center justify-center gap-3">
                        <x.ui.button type="button" @click="showDeleteModal = false" variant="outline" size="sm">Batal</x.ui.button>
                        <x.ui.button type="submit" variant="danger" size="sm">Ya, Hapus</x.ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function linktreeEditor() {
    return {
        showAddLinkModal: false,
        showEditLinkModal: false,
        showAddSocialModal: false,
        showEditSocialModal: false,
        showDeleteModal: false,
        editLinkFormAction: '',
        editLinkData: { title: '', url: '', type: 'link', active: true },
        editSocialFormAction: '',
        editSocialData: { platform: 'instagram', url: '', active: true },

        editLink(id, title, url, type, active) {
            this.editLinkData = { title, url, type, active };
            this.editLinkFormAction = '{{ route("vendor.linktree.links.update", [$linktree, "__ID__"]) }}'.replace('__ID__', id);
            this.showEditLinkModal = true;
        },

        editSocial(id, platform, url, active) {
            this.editSocialData = { platform, url, active };
            this.editSocialFormAction = '{{ route("vendor.linktree.socials.update", [$linktree, "__ID__"]) }}'.replace('__ID__', id);
            this.showEditSocialModal = true;
        }
    };
}
</script>
@endsection
