@extends('layouts.vendor')

@section('content')
<div class="page-wrapper">
    <div class="container-xl">
        {{-- Page Header --}}
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-import" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                            <path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                            <path d="M9 15l3 3l3 -3"/>
                            <path d="M12 18v-6"/>
                        </svg>
                        Import Link
                    </h2>
                    <div class="page-pretitle">
                        <a href="{{ route('vendor.linktree.show', $linktree) }}">{{ $linktree->title }}</a> / Import
                    </div>
                </div>
                <div class="col-auto">
                    <a href="{{ route('vendor.linktree.show', $linktree) }}" class="btn btn-ghost">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0"/><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/></svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="page-body">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    {{-- Flash Messages --}}
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

                    @if($errors->any())
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M12 11v2"/><path d="M12 15v.01"/></svg>
                            </div>
                            <div>
                                @foreach($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                    @endif

                    {{-- Import Form Card --}}
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/></svg>
                                Upload File CSV
                            </h3>
                            <div class="card-actions">
                                <a href="{{ route('vendor.linktree.export-links', $linktree) }}" class="btn btn-success btn-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/><path d="M12 11v6"/><path d="M9 14l3 3l3 -3"/></svg>
                                    Export Template CSV
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('vendor.linktree.import-links', $linktree) }}" method="POST" enctype="multipart/form-data" id="importForm">
                                @csrf

                                {{-- Current Links Info --}}
                                <div class="mb-3">
                                    <div class="alert alert-info">
                                        <div class="d-flex align-items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M12 11v2"/><path d="M12 15v.01"/></svg>
                                            <div class="ms-2">
                                                Saat ini linktree memiliki <strong>{{ $linkCount }}</strong> link.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- CSV File Upload --}}
                                <div class="mb-3">
                                    <label for="csv_file" class="form-label required">File CSV</label>
                                    <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv,.txt" required>
                                    <div class="form-hint">Format: CSV atau TXT. Maksimal 5MB. Kolom yang diperlukan: <strong>Judul</strong> dan <strong>URL</strong>.</div>
                                    <div id="fileInfo" class="mt-2" style="display: none;">
                                        <div class="badge bg-success">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="12" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/></svg>
                                            <span id="fileName"></span>
                                            <span id="fileSize"></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Import Mode --}}
                                <div class="mb-3">
                                    <label class="form-label required">Mode Import</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="import_mode" id="modeAppend" value="append" checked>
                                            <label class="form-check-label" for="modeAppend">
                                                <strong>Tambahkan</strong>
                                                <div class="text-muted small">Link baru ditambahkan setelah link yang sudah ada</div>
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline mt-2">
                                            <input class="form-check-input" type="radio" name="import_mode" id="modeReplace" value="replace">
                                            <label class="form-check-label" for="modeReplace">
                                                <strong>Gantikan Semua</strong>
                                                <div class="text-muted small">Hapus semua link yang ada dan ganti dengan data dari CSV</div>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Preview Table --}}
                                <div id="previewSection" class="mb-3" style="display: none;">
                                    <label class="form-label">Preview Data (5 baris pertama)</label>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered" id="previewTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Judul</th>
                                                    <th>URL</th>
                                                    <th>Aktif</th>
                                                </tr>
                                            </thead>
                                            <tbody id="previewBody"></tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- Submit --}}
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <a href="{{ route('vendor.linktree.show', $linktree) }}" class="btn btn-ghost">
                                        Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M12 21h-7a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/><path d="M12 11v6"/><path d="M9 14l3 3l3 -3"/></svg>
                                        Import Link
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Format Guide Card --}}
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M12 11v2"/><path d="M12 15v.01"/></svg>
                                Panduan Format CSV
                            </h3>
                        </div>
                        <div class="card-body">
                            <h4>Format Kolom yang Didukung</h4>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Kolom Wajib</th>
                                            <th>Alternatif Nama</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="badge bg-red">Judul</span></td>
                                            <td>title, nama, name</td>
                                            <td>Judul link yang akan ditampilkan</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-red">URL</span></td>
                                            <td>url, link, website</td>
                                            <td>URL tujuan link</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h4 class="mt-3">Kolom Opsional</h4>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Kolom</th>
                                            <th>Alternatif Nama</th>
                                            <th>Keterangan</th>
                                            <th>Default</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="badge bg-blue">Ikon</span></td>
                                            <td>icon, emoji</td>
                                            <td>Emoji atau nama icon</td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-blue">Aktif</span></td>
                                            <td>active, status</td>
                                            <td>ya/yes/true/1/aktif = aktif</td>
                                            <td>Aktif</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-blue">Urutan</span></td>
                                            <td>order, sort, posisi</td>
                                            <td>Urutan tampilan (angka)</td>
                                            <td>Auto-increment</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h4 class="mt-3">Contoh CSV</h4>
                            <div class="code-inline">
                                <pre class="mb-0"><code>Judul,URL,Ikon,Aktif,Urutan
Instagram,https://instagram.com/namaprofile,📸,Ya,1
WhatsApp,https://wa.me/6281234567890,💬,Ya,2
Website Toko,https://tokoku.com,🛒,Ya,3
Portfolio,https://portfolio.com,🎨,Tidak,4</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('csv_file');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const submitBtn = document.getElementById('submitBtn');
    const previewSection = document.getElementById('previewSection');
    const previewBody = document.getElementById('previewBody');

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) {
            fileInfo.style.display = 'none';
            submitBtn.disabled = true;
            previewSection.style.display = 'none';
            return;
        }

        // Show file info
        fileName.textContent = file.name;
        const sizeKB = (file.size / 1024).toFixed(1);
        fileSize.textContent = ` (${sizeKB} KB)`;
        fileInfo.style.display = 'block';
        submitBtn.disabled = false;

        // Preview CSV
        const reader = new FileReader();
        reader.onload = function(event) {
            const text = event.target.result;
            const lines = text.split('\n').filter(line => line.trim());

            if (lines.length < 2) {
                previewSection.style.display = 'none';
                return;
            }

            previewBody.innerHTML = '';
            // Skip header, show first 5 data rows
            const maxPreview = Math.min(lines.length, 6);
            for (let i = 1; i < maxPreview; i++) {
                const cols = parseCSVLine(lines[i]);
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${i}</td>
                    <td>${escapeHtml(cols[0] || '-')}</td>
                    <td><code>${escapeHtml(cols[1] || '-')}</code></td>
                    <td>${escapeHtml(cols[3] || 'Ya')}</td>
                `;
                previewBody.appendChild(row);
            }

            if (lines.length > 6) {
                const moreRow = document.createElement('tr');
                moreRow.innerHTML = `<td colspan="4" class="text-center text-muted">... dan ${lines.length - 6} baris lainnya</td>`;
                previewBody.appendChild(moreRow);
            }

            previewSection.style.display = 'block';
        };
        reader.readAsText(file);
    });

    // Form submit confirmation
    document.getElementById('importForm').addEventListener('submit', function(e) {
        const mode = document.querySelector('input[name="import_mode"]:checked').value;
        if (mode === 'replace') {
            if (!confirm('Semua link yang ada akan DIHAPUS dan diganti dengan data dari CSV. Lanjutkan?')) {
                e.preventDefault();
            }
        }
    });

    function parseCSVLine(line) {
        const result = [];
        let current = '';
        let inQuotes = false;

        for (let i = 0; i < line.length; i++) {
            const char = line[i];
            if (char === '"') {
                inQuotes = !inQuotes;
            } else if (char === ',' && !inQuotes) {
                result.push(current.trim());
                current = '';
            } else {
                current += char;
            }
        }
        result.push(current.trim());
        return result;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
@endsection
