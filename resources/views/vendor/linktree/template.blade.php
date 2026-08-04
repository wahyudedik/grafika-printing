@extends('layouts.vendor')

@section('content')
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">Template Builder: {{ $linktree->title }}</h2>
                    <div class="page-pretitle">
                        <a href="{{ route('vendor.linktree.index') }}">Linktree</a> /
                        <a href="{{ route('vendor.linktree.edit', $linktree) }}">Edit</a> / Template
                    </div>
                </div>
                <div class="col-auto">
                    <a href="{{ route('vendor.linktree.edit', $linktree) }}" class="btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l-4.5 -4.5"/><path d="M5 14l7 -7"/><path d="M14 7l4.5 4.5"/><path d="M17 14l-7 -7"/></svg>
                        Kembali
                    </a>
                    @if($linktree->is_active)
                    <a href="{{ route('linktree.public', $linktree->custom_url) }}" target="_blank" class="btn btn-info">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/></svg>
                        Lihat Publik
                    </a>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <div class="d-flex">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                </div>
                <div>{{ session('success') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex">
                <div>{{ session('error') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        @endif

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Template Selection -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"/><path d="M4 12l5 5"/><path d="M12 4l5 5"/></svg>
                            Pilih Template
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3" id="templateGrid">
                            @foreach($templates as $key => $template)
                            <div class="col-md-6 col-lg-3">
                                <div class="template-card {{ $linktree->template === $key ? 'active' : '' }}"
                                     data-template="{{ $key }}"
                                     onclick="selectTemplate('{{ $key }}')">
                                    <div class="template-preview" style="background-color: {{ $template['colors']['bg'] }}">
                                        <div class="preview-header" style="background-color: {{ $template['colors']['primary'] }}"></div>
                                        <div class="preview-button" style="background-color: {{ $template['colors']['secondary'] }}; border-radius: {{ $template['button_style'] === 'pill' ? '9999px' : ($template['button_style'] === 'square' ? '0' : '8px') }}"></div>
                                        <div class="preview-button" style="background-color: {{ $template['colors']['secondary'] }}; border-radius: {{ $template['button_style'] === 'pill' ? '9999px' : ($template['button_style'] === 'square' ? '0' : '8px') }}; opacity: 0.7"></div>
                                    </div>
                                    <div class="template-info">
                                        <h4 class="template-name">{{ $template['name'] }}</h4>
                                        <p class="template-desc">{{ $template['description'] }}</p>
                                    </div>
                                    @if($linktree->template === $key)
                                    <div class="template-badge">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                                        Aktif
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Color Customization -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 3h-4a2 2 0 0 0 -2 2v12a4 4 0 0 0 8 0v-5a2 2 0 0 0 -2 -2h-4"/><path d="M13 7.5l-2 -2l-2 2l-2 -2"/><path d="M10 11.5l-2 -2l-2 2l-2 -2"/><path d="M7 17l2 2l4 -4"/></svg>
                            Kustomisasi Warna
                        </h3>
                    </div>
                    <div class="card-body">
                        <form id="colorForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Warna Primer</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="primaryColor"
                                               value="{{ $templates[$linktree->template]['colors']['primary'] }}"
                                               onchange="updatePreview()">
                                        <input type="text" class="form-control" id="primaryColorText"
                                               value="{{ $templates[$linktree->template]['colors']['primary'] }}"
                                               onchange="syncColorPicker('primary')">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Warna Sekunder</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="secondaryColor"
                                               value="{{ $templates[$linktree->template]['colors']['secondary'] }}"
                                               onchange="updatePreview()">
                                        <input type="text" class="form-control" id="secondaryColorText"
                                               value="{{ $templates[$linktree->template]['colors']['secondary'] }}"
                                               onchange="syncColorPicker('secondary')">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Warna Latar</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="bgColor"
                                               value="{{ $templates[$linktree->template]['colors']['bg'] }}"
                                               onchange="updatePreview()">
                                        <input type="text" class="form-control" id="bgColorText"
                                               value="{{ $templates[$linktree->template]['colors']['bg'] }}"
                                               onchange="syncColorPicker('bg')">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Warna Teks</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="textColor"
                                               value="{{ $templates[$linktree->template]['colors']['text'] }}"
                                               onchange="updatePreview()">
                                        <input type="text" class="form-control" id="textColorText"
                                               value="{{ $templates[$linktree->template]['colors']['text'] }}"
                                               onchange="syncColorPicker('text')">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gaya Tombol</label>
                                    <select class="form-select" id="buttonStyle" onchange="updatePreview()">
                                        <option value="rounded" {{ $templates[$linktree->template]['button_style'] === 'rounded' ? 'selected' : '' }}>Bulat (Rounded)</option>
                                        <option value="pill" {{ $templates[$linktree->template]['button_style'] === 'pill' ? 'selected' : '' }}>Pill (Super Bulat)</option>
                                        <option value="square" {{ $templates[$linktree->template]['button_style'] === 'square' ? 'selected' : '' }}>Kotak (Square)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Reset ke Default</label>
                                    <button type="button" class="btn btn-outline-secondary w-100" onclick="resetColors()">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l-4.5 -4.5"/><path d="M5 14l7 -7"/><path d="M14 7l4.5 4.5"/><path d="M17 14l-7 -7"/></svg>
                                        Reset Warna
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Apply Button -->
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('vendor.linktree.template.apply', $linktree) }}" method="POST" id="applyForm">
                            @csrf
                            <input type="hidden" name="template" id="selectedTemplate" value="{{ $linktree->template }}">
                            <input type="hidden" name="primary_color" id="applyPrimaryColor" value="{{ $templates[$linktree->template]['colors']['primary'] }}">
                            <input type="hidden" name="secondary_color" id="applySecondaryColor" value="{{ $templates[$linktree->template]['colors']['secondary'] }}">
                            <input type="hidden" name="bg_color" id="applyBgColor" value="{{ $templates[$linktree->template]['colors']['bg'] }}">
                            <input type="hidden" name="text_color" id="applyTextColor" value="{{ $templates[$linktree->template]['colors']['text'] }}">
                            <input type="hidden" name="button_style" id="applyButtonStyle" value="{{ $templates[$linktree->template]['button_style'] }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-1">Terapkan Template</h4>
                                    <p class="text-muted mb-0">Template akan langsung aktif di halaman publik Anda.</p>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                                    Terapkan Template
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Live Preview -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 1rem;">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/></svg>
                            Preview Langsung
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <!-- Phone Frame -->
                        <div class="phone-frame mx-auto">
                            <div class="phone-notch"></div>
                            <div class="phone-screen" id="previewScreen"
                                 style="background-color: {{ $templates[$linktree->template]['colors']['bg'] }}">
                                <!-- Avatar -->
                                <div class="preview-avatar">
                                    @if($linktree->avatar_path)
                                    <img src="{{ asset('storage/' . $linktree->avatar_path) }}" alt="Avatar" class="avatar-img">
                                    @else
                                    <div class="avatar-placeholder" style="background-color: {{ $templates[$linktree->template]['colors']['primary'] }}">
                                        {{ strtoupper(substr($linktree->title, 0, 1)) }}
                                    </div>
                                    @endif
                                </div>

                                <!-- Title -->
                                <h2 class="preview-title" style="color: {{ $templates[$linktree->template]['colors']['text'] }}">
                                    {{ $linktree->title }}
                                </h2>

                                <!-- Bio -->
                                @if($linktree->bio)
                                <p class="preview-bio" style="color: {{ $templates[$linktree->template]['colors']['secondary'] }}">
                                    {{ $linktree->bio }}
                                </p>
                                @endif

                                <!-- Links -->
                                <div class="preview-links">
                                    @forelse($linktree->activeLinks->take(4) as $link)
                                    <div class="preview-link"
                                         style="background-color: {{ $templates[$linktree->template]['colors']['secondary'] }};
                                                color: {{ $templates[$linktree->template]['colors']['bg'] }};
                                                border-radius: {{ $templates[$linktree->template]['button_style'] === 'pill' ? '9999px' : ($templates[$linktree->template]['button_style'] === 'square' ? '0' : '8px') }}">
                                        {{ $link->title }}
                                    </div>
                                    @empty
                                    <div class="preview-link" style="background-color: #E5E7EB; color: #6B7280;">
                                        Belum ada link
                                    </div>
                                    @endforelse
                                </div>

                                <!-- Socials -->
                                @if($linktree->activeSocials->count() > 0)
                                <div class="preview-socials">
                                    @foreach($linktree->activeSocials->take(4) as $social)
                                    <div class="social-icon" style="background-color: {{ $social->platform_color }}">
                                        {!! $social->icon_html !!}
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            <div class="phone-home"></div>
                        </div>

                        <!-- Template Info -->
                        <div class="p-3">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-blue-lt" id="templateBadge">{{ $templates[$linktree->template]['name'] }}</span>
                            </div>
                            <p class="text-muted small mb-0" id="templateDesc">{{ $templates[$linktree->template]['description'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Template Card Styles */
    .template-card {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        background: #fff;
    }
    .template-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        transform: translateY(-2px);
    }
    .template-card.active {
        border-color: #3b82f6;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    }
    .template-preview {
        height: 120px;
        border-radius: 8px;
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 12px;
    }
    .preview-header {
        height: 30px;
        border-radius: 6px;
        width: 60%;
    }
    .preview-button {
        height: 20px;
        width: 100%;
    }
    .template-info {
        text-align: center;
    }
    .template-name {
        font-size: 14px;
        font-weight: 600;
        margin: 0 0 4px 0;
        color: #1f2937;
    }
    .template-desc {
        font-size: 11px;
        color: #6b7280;
        margin: 0;
    }
    .template-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #3b82f6;
        color: #fff;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Phone Frame */
    .phone-frame {
        width: 280px;
        background: #1f2937;
        border-radius: 32px;
        padding: 12px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }
    .phone-notch {
        width: 100px;
        height: 24px;
        background: #1f2937;
        border-radius: 0 0 16px 16px;
        margin: -12px auto 8px;
        position: relative;
        z-index: 10;
    }
    .phone-screen {
        border-radius: 20px;
        min-height: 400px;
        padding: 24px 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        overflow-y: auto;
        max-height: 500px;
    }
    .phone-home {
        width: 40px;
        height: 4px;
        background: #4b5563;
        border-radius: 2px;
        margin: 8px auto 0;
    }

    /* Preview Elements */
    .preview-avatar {
        margin-bottom: 12px;
    }
    .avatar-img {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        object-fit: cover;
    }
    .avatar-placeholder {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 24px;
        font-weight: 700;
    }
    .preview-title {
        font-size: 16px;
        font-weight: 700;
        margin: 0 0 4px 0;
        text-align: center;
    }
    .preview-bio {
        font-size: 11px;
        text-align: center;
        margin: 0 0 16px 0;
        opacity: 0.8;
    }
    .preview-links {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .preview-link {
        padding: 10px 16px;
        text-align: center;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .preview-socials {
        display: flex;
        gap: 12px;
        margin-top: 16px;
    }
    .social-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
    }
    .social-icon svg {
        width: 16px;
        height: 16px;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .phone-frame {
            width: 240px;
        }
        .phone-screen {
            min-height: 350px;
            max-height: 450px;
        }
    }
</style>

<script>
    // Template data from controller
    const templates = @json($templates);
    let selectedTemplate = '{{ $linktree->template }}';

    // Select template
    function selectTemplate(template) {
        selectedTemplate = template;

        // Update UI
        document.querySelectorAll('.template-card').forEach(card => {
            card.classList.remove('active');
            const badge = card.querySelector('.template-badge');
            if (badge) badge.remove();
        });

        const selectedCard = document.querySelector(`[data-template="${template}"]`);
        if (selectedCard) {
            selectedCard.classList.add('active');
            const badge = document.createElement('div');
            badge.className = 'template-badge';
            badge.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg> Aktif';
            selectedCard.appendChild(badge);
        }

        // Update colors
        const templateData = templates[template];
        document.getElementById('primaryColor').value = templateData.colors.primary;
        document.getElementById('primaryColorText').value = templateData.colors.primary;
        document.getElementById('secondaryColor').value = templateData.colors.secondary;
        document.getElementById('secondaryColorText').value = templateData.colors.secondary;
        document.getElementById('bgColor').value = templateData.colors.bg;
        document.getElementById('bgColorText').value = templateData.colors.bg;
        document.getElementById('textColor').value = templateData.colors.text;
        document.getElementById('textColorText').value = templateData.colors.text;
        document.getElementById('buttonStyle').value = templateData.button_style;

        // Update form
        document.getElementById('selectedTemplate').value = template;
        document.getElementById('applyPrimaryColor').value = templateData.colors.primary;
        document.getElementById('applySecondaryColor').value = templateData.colors.secondary;
        document.getElementById('applyBgColor').value = templateData.colors.bg;
        document.getElementById('applyTextColor').value = templateData.colors.text;
        document.getElementById('applyButtonStyle').value = templateData.button_style;

        // Update preview
        updatePreview();

        // Update sidebar info
        document.getElementById('templateBadge').textContent = templateData.name;
        document.getElementById('templateDesc').textContent = templateData.description;
    }

    // Sync color picker from text input
    function syncColorPicker(type) {
        const textInput = document.getElementById(type + 'ColorText');
        const colorInput = document.getElementById(type + 'Color');
        if (/^#[0-9A-Fa-f]{6}$/.test(textInput.value)) {
            colorInput.value = textInput.value;
            updatePreview();
        }
    }

    // Update preview
    function updatePreview() {
        const primary = document.getElementById('primaryColor').value;
        const secondary = document.getElementById('secondaryColor').value;
        const bg = document.getElementById('bgColor').value;
        const text = document.getElementById('textColor').value;
        const buttonStyle = document.getElementById('buttonStyle').value;

        // Sync text inputs
        document.getElementById('primaryColorText').value = primary;
        document.getElementById('secondaryColorText').value = secondary;
        document.getElementById('bgColorText').value = bg;
        document.getElementById('textColorText').value = text;

        // Update preview screen
        const screen = document.getElementById('previewScreen');
        screen.style.backgroundColor = bg;

        // Update title color
        const title = screen.querySelector('.preview-title');
        if (title) title.style.color = text;

        // Update bio color
        const bio = screen.querySelector('.preview-bio');
        if (bio) bio.style.color = secondary;

        // Update link buttons
        screen.querySelectorAll('.preview-link').forEach(link => {
            link.style.backgroundColor = secondary;
            link.style.color = bg;
            link.style.borderRadius = buttonStyle === 'pill' ? '9999px' : (buttonStyle === 'square' ? '0' : '8px');
        });

        // Update avatar placeholder
        const avatarPlaceholder = screen.querySelector('.avatar-placeholder');
        if (avatarPlaceholder) avatarPlaceholder.style.backgroundColor = primary;

        // Update form hidden inputs
        document.getElementById('applyPrimaryColor').value = primary;
        document.getElementById('applySecondaryColor').value = secondary;
        document.getElementById('applyBgColor').value = bg;
        document.getElementById('applyTextColor').value = text;
        document.getElementById('applyButtonStyle').value = buttonStyle;
    }

    // Reset colors to template default
    function resetColors() {
        selectTemplate(selectedTemplate);
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updatePreview();
    });
</script>
@endsection
