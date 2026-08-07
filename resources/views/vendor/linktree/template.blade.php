@extends('layouts.vendor')

@section('content')
<div x-data="templateBuilder()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Template Builder: {{ $linktree->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                <a href="{{ route('vendor.linktree.index') }}" class="text-primary-600 hover:underline">Linktree</a>
                <span class="mx-1">/</span>
                <a href="{{ route('vendor.linktree.edit', $linktree) }}" class="text-primary-600 hover:underline">Edit</a>
                <span class="mx-1">/</span> Template
            </p>
        </div>
        <div class="flex items-center gap-2">
            <x.ui.button href="{{ route('vendor.linktree.edit', $linktree) }}" variant="outline" size="sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </x.ui.button>
            @if($linktree->is_active)
            <x.ui.button href="{{ route('linktree.public', $linktree->custom_url) }}" variant="outline-info" size="sm" target="_blank">
                <i class="fas fa-eye mr-1"></i> Lihat Publik
            </x.ui.button>
            @endif
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

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition
         class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            <span class="text-red-800 text-sm">{{ session('error') }}</span>
        </div>
        <button @click="show = false" class="text-red-500 hover:text-red-700"><i class="fas fa-times"></i></button>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Template Selection --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-palette text-gray-400"></i> Pilih Template
                    </h2>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($templates as $key => $template)
                        <div class="template-card relative cursor-pointer rounded-xl border-2 p-3 transition-all hover:shadow-md"
                             :class="selectedTemplate === '{{ $key }}' ? 'border-primary-500 bg-primary-50 ring-2 ring-primary-200' : 'border-gray-200 hover:border-primary-300'"
                             @click="selectTemplate('{{ $key }}')">
                            {{-- Preview --}}
                            <div class="rounded-lg overflow-hidden mb-3" style="background-color: {{ $template['colors']['bg'] }}; height: 100px; padding: 12px; display: flex; flex-direction: column; gap: 8px;">
                                <div class="rounded" style="background-color: {{ $template['colors']['primary'] }}; height: 24px; width: 60%;"></div>
                                <div class="rounded" style="background-color: {{ $template['colors']['secondary'] }}; height: 16px; border-radius: {{ $template['button_style'] === 'pill' ? '9999px' : ($template['button_style'] === 'square' ? '0' : '8px') }};"></div>
                                <div class="rounded" style="background-color: {{ $template['colors']['secondary'] }}; height: 16px; opacity: 0.7; border-radius: {{ $template['button_style'] === 'pill' ? '9999px' : ($template['button_style'] === 'square' ? '0' : '8px') }};"></div>
                            </div>
                            {{-- Info --}}
                            <div class="text-center">
                                <div class="text-sm font-semibold text-gray-900">{{ $template['name'] }}</div>
                                <div class="text-xs text-gray-500">{{ $template['description'] }}</div>
                            </div>
                            {{-- Active Badge --}}
                            @if($linktree->template === $key)
                            <div class="absolute top-2 right-2 bg-primary-500 text-white px-2 py-0.5 rounded-full text-xs font-semibold flex items-center gap-1">
                                <i class="fas fa-check text-xs"></i> Aktif
                            </div>
                            @endif
                            <div x-show="selectedTemplate === '{{ $key }}' && '{{ $linktree->template }}' !== '{{ $key }}'"
                                 x-transition class="absolute top-2 right-2 bg-green-500 text-white px-2 py-0.5 rounded-full text-xs font-semibold flex items-center gap-1">
                                <i class="fas fa-check text-xs"></i> Dipilih
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Color Customization --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-swatchbook text-gray-400"></i> Kustomisasi Warna
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Primary Color --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Warna Primer</label>
                            <div class="flex items-center gap-2">
                                <input type="color" x-model="colors.primary" @input="updatePreview()" class="w-10 h-10 rounded-lg border border-gray-300 cursor-pointer shrink-0">
                                <input type="text" x-model="colors.primary" @input="syncFromText('primary')" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        {{-- Secondary Color --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Warna Sekunder</label>
                            <div class="flex items-center gap-2">
                                <input type="color" x-model="colors.secondary" @input="updatePreview()" class="w-10 h-10 rounded-lg border border-gray-300 cursor-pointer shrink-0">
                                <input type="text" x-model="colors.secondary" @input="syncFromText('secondary')" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        {{-- BG Color --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Warna Latar</label>
                            <div class="flex items-center gap-2">
                                <input type="color" x-model="colors.bg" @input="updatePreview()" class="w-10 h-10 rounded-lg border border-gray-300 cursor-pointer shrink-0">
                                <input type="text" x-model="colors.bg" @input="syncFromText('bg')" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        {{-- Text Color --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Warna Teks</label>
                            <div class="flex items-center gap-2">
                                <input type="color" x-model="colors.text" @input="updatePreview()" class="w-10 h-10 rounded-lg border border-gray-300 cursor-pointer shrink-0">
                                <input type="text" x-model="colors.text" @input="syncFromText('text')" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        {{-- Button Style --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gaya Tombol</label>
                            <select x-model="buttonStyle" @change="updatePreview()" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="rounded">Bulat (Rounded)</option>
                                <option value="pill">Pill (Super Bulat)</option>
                                <option value="square">Kotak (Square)</option>
                            </select>
                        </div>
                        {{-- Reset --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reset ke Default</label>
                            <x.ui.button type="button" @click="resetColors()" variant="outline" class="w-full justify-center">
                                <i class="fas fa-undo mr-1"></i> Reset Warna
                            </x.ui.button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Apply Button --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-semibold text-gray-900">Terapkan Template</h3>
                        <p class="text-sm text-gray-500">Template akan langsung aktif di halaman publik Anda.</p>
                    </div>
                    <form action="{{ route('vendor.linktree.template.apply', $linktree) }}" method="POST">
                        @csrf
                        <input type="hidden" name="template" :value="selectedTemplate">
                        <input type="hidden" name="primary_color" :value="colors.primary">
                        <input type="hidden" name="secondary_color" :value="colors.secondary">
                        <input type="hidden" name="bg_color" :value="colors.bg">
                        <input type="hidden" name="text_color" :value="colors.text">
                        <input type="hidden" name="button_style" :value="buttonStyle">
                        <x.ui.button type="submit">
                            <i class="fas fa-check mr-1"></i> Terapkan Template
                        </x.ui.button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar: Live Preview --}}
        <div class="lg:col-span-1">
            <div class="sticky top-4">
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-mobile-alt text-gray-400"></i> Preview Langsung
                        </h3>
                    </div>
                    <div class="p-4">
                        {{-- Phone Frame --}}
                        <div class="mx-auto max-w-[280px] rounded-[2rem] bg-gray-800 p-3 shadow-2xl">
                            {{-- Notch --}}
                            <div class="w-24 h-5 bg-gray-800 rounded-b-2xl mx-auto -mt-3 relative z-10 mb-2"></div>
                            {{-- Screen --}}
                            <div class="rounded-[1.5rem] overflow-y-auto" :style="`background-color: ${colors.bg}; min-height: 400px; max-height: 500px;`">
                                <div class="flex flex-col items-center px-4 py-6">
                                    {{-- Avatar --}}
                                    <div class="mb-3">
                                        @if($linktree->avatar_path)
                                        <img src="{{ asset('storage/' . $linktree->avatar_path) }}" alt="Avatar" class="w-16 h-16 rounded-full object-cover border-2 border-white">
                                        @else
                                        <div class="w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl font-bold" :style="`background-color: ${colors.primary}`">
                                            {{ strtoupper(substr($linktree->title, 0, 1)) }}
                                        </div>
                                        @endif
                                    </div>

                                    {{-- Title --}}
                                    <h3 class="text-sm font-bold mb-1" :style="`color: ${colors.text}`">{{ $linktree->title }}</h3>

                                    {{-- Bio --}}
                                    @if($linktree->bio)
                                    <p class="text-xs text-center mb-4" :style="`color: ${colors.secondary}; opacity: 0.8;`">{{ Str::limit($linktree->bio, 60) }}</p>
                                    @endif

                                    {{-- Links --}}
                                    <div class="w-full space-y-2 mb-4">
                                        @forelse($linktree->activeLinks->take(4) as $link)
                                        <div class="py-2.5 px-4 text-center text-xs font-semibold" :style="`background-color: ${colors.secondary}; color: ${colors.bg}; border-radius: ${buttonStyle === 'pill' ? '9999px' : (buttonStyle === 'square' ? '0' : '8px')}`">
                                            {{ $link->title }}
                                        </div>
                                        @empty
                                        <div class="py-2.5 px-4 text-center text-xs font-semibold bg-gray-200 text-gray-500 rounded-lg">Belum ada link</div>
                                        @endforelse
                                    </div>

                                    {{-- Socials --}}
                                    @if($linktree->activeSocials->count() > 0)
                                    <div class="flex gap-3">
                                        @foreach($linktree->activeSocials->take(4) as $social)
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs" style="background-color: {{ $social->platform_color }}">
                                            {!! $social->icon_html !!}
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                            {{-- Home indicator --}}
                            <div class="w-10 h-1 bg-gray-600 rounded-full mx-auto mt-3"></div>
                        </div>

                        {{-- Template Info --}}
                        <div class="mt-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800" x-text="currentTemplateName"></span>
                            <p class="text-xs text-gray-500 mt-2" x-text="currentTemplateDesc"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function templateBuilder() {
    const templates = @json($templates);
    const currentTemplate = '{{ $linktree->template }}';

    return {
        selectedTemplate: currentTemplate,
        colors: {
            primary: templates[currentTemplate].colors.primary,
            secondary: templates[currentTemplate].colors.secondary,
            bg: templates[currentTemplate].colors.bg,
            text: templates[currentTemplate].colors.text,
        },
        buttonStyle: templates[currentTemplate].button_style,

        get currentTemplateName() {
            return templates[this.selectedTemplate]?.name || '';
        },
        get currentTemplateDesc() {
            return templates[this.selectedTemplate]?.description || '';
        },

        selectTemplate(key) {
            this.selectedTemplate = key;
            const t = templates[key];
            this.colors.primary = t.colors.primary;
            this.colors.secondary = t.colors.secondary;
            this.colors.bg = t.colors.bg;
            this.colors.text = t.colors.text;
            this.buttonStyle = t.button_style;
        },

        syncFromText(type) {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.colors[type])) {
                this.updatePreview();
            }
        },

        updatePreview() {
            // Colors are reactive via x-model, preview updates automatically
        },

        resetColors() {
            this.selectTemplate(this.selectedTemplate);
        }
    };
}
</script>
@endsection
