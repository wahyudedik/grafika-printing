@extends('layouts.vendor')

@section('content')
<div class="page-wrapper">
    <div class="container-xl">
        {{-- Page Header --}}
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-a-b" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M16 21v-2a4 4 0 0 0 -4 -4h-4a4 4 0 0 0 -4 4v2"/>
                            <path d="M8.5 4l3.5 16"/>
                            <path d="M13 4l-3.5 16"/>
                        </svg>
                        Buat A/B Test Baru
                    </h2>
                    <div class="page-pretitle">
                        <a href="{{ route('vendor.linktree.show', $linktree) }}">{{ $linktree->title }}</a> /
                        <a href="{{ route('vendor.linktree.ab-test.index', $linktree) }}">A/B Testing</a> / Baru
                    </div>
                </div>
            </div>
        </div>

        <div class="page-body">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <form action="{{ route('vendor.linktree.ab-test.store', $linktree) }}" method="POST">
                        @csrf

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Konfigurasi A/B Test</h3>
                            </div>
                            <div class="card-body">
                                {{-- Test Name --}}
                                <div class="mb-3">
                                    <label for="name" class="form-label required">Nama Test</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}"
                                           placeholder="Contoh: Template Color Test Q4" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Current Template Info --}}
                                <div class="mb-3 p-3 rounded bg-azure-lt">
                                    <div class="d-flex align-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M12 11v2"/><path d="M12 15v.01"/></svg>
                                        <div class="ms-2">
                                            Template saat ini: <strong>{{ ucfirst($linktree->template) }}</strong>
                                        </div>
                                    </div>
                                </div>

                                {{-- Variant Selection --}}
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="variant_a" class="form-label required">Variant A (Test)</label>
                                        <select class="form-select @error('variant_a') is-invalid @enderror"
                                                id="variant_a" name="variant_a" required>
                                            <option value="">Pilih Template...</option>
                                            @foreach($templates as $template)
                                            <option value="{{ $template }}" {{ old('variant_a') === $template ? 'selected' : '' }}>
                                                {{ ucfirst($template) }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('variant_a')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="variant_b" class="form-label required">Variant B (Control)</label>
                                        <select class="form-select @error('variant_b') is-invalid @enderror"
                                                id="variant_b" name="variant_b" required>
                                            <option value="">Pilih Template...</option>
                                            @foreach($templates as $template)
                                            <option value="{{ $template }}" {{ old('variant_b') === $template ? 'selected' : '' }}>
                                                {{ ucfirst($template) }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('variant_b')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Variant Preview --}}
                                <div class="mb-3" id="variantPreview" style="display: none;">
                                    <label class="form-label">Preview Varian</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="card card-sm" id="previewA">
                                                <div class="card-body text-center p-3">
                                                    <div class="mb-2 fw-bold" id="previewAName">-</div>
                                                    <div id="previewAColor" class="rounded p-3" style="min-height: 60px;">
                                                        <span class="text-muted small">Preview</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card card-sm" id="previewB">
                                                <div class="card-body text-center p-3">
                                                    <div class="mb-2 fw-bold" id="previewBName">-</div>
                                                    <div id="previewBColor" class="rounded p-3" style="min-height: 60px;">
                                                        <span class="text-muted small">Preview</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                {{-- Traffic Split --}}
                                <div class="mb-3">
                                    <label for="traffic_split" class="form-label required">Traffic Split</label>
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <input type="range" class="form-range" min="10" max="90" step="5"
                                                   id="traffic_split" name="traffic_split" value="{{ old('traffic_split', 50) }}">
                                        </div>
                                        <div class="col-auto">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-azure" id="splitA">50%</span>
                                                <span class="text-muted">:</span>
                                                <span class="badge bg-pink" id="splitB">50%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-hint">Persentase pengunjung yang melihat Variant A. Sisa melihat Variant B.</div>
                                </div>

                                {{-- Min Samples --}}
                                <div class="mb-3">
                                    <label for="min_samples" class="form-label required">Minimum Sampel</label>
                                    <input type="number" class="form-control @error('min_samples') is-invalid @enderror"
                                           id="min_samples" name="min_samples" value="{{ old('min_samples', 100) }}"
                                           min="50" max="10000" required>
                                    @error('min_samples')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-hint">Jumlah minimum impresi per varian sebelum evaluasi bisa dilakukan. Default: 100.</div>
                                </div>

                                {{-- Notes --}}
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Catatan (Opsional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"
                                              placeholder="Catatan tentang tujuan test ini...">{{ old('notes') }}</textarea>
                                </div>
                            </div>

                            {{-- Submit --}}
                            <div class="card-footer">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('vendor.linktree.ab-test.index', $linktree) }}" class="btn btn-ghost">
                                        Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                                        Buat A/B Test
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const templateColors = {
    minimal: { bg: '#FFFFFF', text: '#1F2937', primary: '#374151' },
    colorful: { bg: '#F5F3FF', text: '#1F2937', primary: '#8B5CF6' },
    dark: { bg: '#111827', text: '#F9FAFB', primary: '#6366F1' },
    professional: { bg: '#F1F5F9', text: '#0F172A', primary: '#1E3A5F' },
};

document.getElementById('variant_a').addEventListener('change', updatePreview);
document.getElementById('variant_b').addEventListener('change', updatePreview);
document.getElementById('traffic_split').addEventListener('input', updateSplit);

function updatePreview() {
    const a = document.getElementById('variant_a').value;
    const b = document.getElementById('variant_b').value;
    const preview = document.getElementById('variantPreview');

    if (a && b) {
        preview.style.display = 'block';

        if (templateColors[a]) {
            document.getElementById('previewAName').textContent = a.charAt(0).toUpperCase() + a.slice(1);
            document.getElementById('previewAColor').style.backgroundColor = templateColors[a].bg;
            document.getElementById('previewAColor').style.color = templateColors[a].text;
            document.getElementById('previewAColor').innerHTML = `<span style="color: ${templateColors[a].primary}">●</span> Template ${a}`;
        }

        if (templateColors[b]) {
            document.getElementById('previewBName').textContent = b.charAt(0).toUpperCase() + b.slice(1);
            document.getElementById('previewBColor').style.backgroundColor = templateColors[b].bg;
            document.getElementById('previewBColor').style.color = templateColors[b].text;
            document.getElementById('previewBColor').innerHTML = `<span style="color: ${templateColors[b].primary}">●</span> Template ${b}`;
        }
    } else {
        preview.style.display = 'none';
    }
}

function updateSplit() {
    const val = document.getElementById('traffic_split').value;
    document.getElementById('splitA').textContent = val + '%';
    document.getElementById('splitB').textContent = (100 - val) + '%';
}
</script>
@endsection
