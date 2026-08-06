@extends('layouts.vendor')

@section('title', 'Import Link')

@section('content')
<div x-data="importHandler()" class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Breadcrumb --}}
    <nav class="mb-4">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li><a href="{{ route('vendor.linktree.show', $linktree) }}" class="hover:text-primary-600">{{ $linktree->title }}</a></li>
            <li><span class="mx-1">/</span></li>
            <li class="text-gray-900 font-medium">Import</li>
        </ol>
    </nav>

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-file-import mr-2 text-primary-600"></i>Import Link
            </h1>
            <p class="mt-1 text-sm text-gray-500">Import link dari file CSV ke linktree Anda</p>
        </div>
        <a href="{{ route('vendor.linktree.show', $linktree) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="max-w-3xl mx-auto space-y-5">
        {{-- Flash Messages --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-600"></i>
                <span class="text-sm text-emerald-800">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-600 hover:text-emerald-800"><i class="fas fa-times"></i></button>
        </div>
        @endif

        @if($errors->any())
        <div x-data="{ show: true }" x-show="show" x-transition class="p-4 bg-red-50 border border-red-200 rounded-xl flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-600"></i>
                <div class="text-sm text-red-800">
                    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            </div>
            <button @click="show = false" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
        @endif

        {{-- Import Form --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900">
                    <i class="fas fa-file-csv mr-2 text-primary-600"></i>Upload File CSV
                </h2>
                <a href="{{ route('vendor.linktree.export-links', $linktree) }}" class="inline-flex items-center px-3 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-medium rounded-lg hover:bg-emerald-200">
                    <i class="fas fa-download mr-1"></i>Export Template CSV
                </a>
            </div>
            <div class="p-5">
                <form action="{{ route('vendor.linktree.import-links', $linktree) }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf

                    {{-- Current Links Info --}}
                    <div class="mb-5 p-4 bg-blue-50 border border-blue-200 rounded-xl flex items-center gap-3">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        <span class="text-sm text-blue-800">Saat ini linktree memiliki <strong>{{ $linkCount }}</strong> link.</span>
                    </div>

                    {{-- CSV File Upload --}}
                    <div class="mb-5">
                        <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-1">File CSV <span class="text-red-500">*</span></label>
                        <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            @change="handleFile($event)">
                        <p class="mt-1 text-xs text-gray-500">Format: CSV atau TXT. Maksimal 5MB. Kolom yang diperlukan: <strong>Judul</strong> dan <strong>URL</strong>.</p>
                        <div x-show="fileName" x-transition class="mt-2 inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-700">
                            <i class="fas fa-file-csv"></i>
                            <span x-text="fileName"></span>
                            <span x-text="fileSize" class="text-emerald-500"></span>
                        </div>
                    </div>

                    {{-- Import Mode --}}
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mode Import <span class="text-red-500">*</span></label>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
                                <input type="radio" name="import_mode" value="append" checked class="mt-0.5 text-primary-600 focus:ring-primary-500">
                                <div>
                                    <div class="font-medium text-sm text-gray-900">Tambahkan</div>
                                    <div class="text-xs text-gray-500">Link baru ditambahkan setelah link yang sudah ada</div>
                                </div>
                            </label>
                            <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
                                <input type="radio" name="import_mode" value="replace" class="mt-0.5 text-primary-600 focus:ring-primary-500">
                                <div>
                                    <div class="font-medium text-sm text-gray-900">Gantikan Semua</div>
                                    <div class="text-xs text-gray-500">Hapus semua link yang ada dan ganti dengan data dari CSV</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Preview Table --}}
                    <div x-show="previewData.length > 0" x-transition class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preview Data (5 baris pertama)</label>
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">#</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Judul</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">URL</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Aktif</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(row, index) in previewData" :key="index">
                                        <tr class="bg-white">
                                            <td class="px-4 py-2 text-gray-500" x-text="index + 1"></td>
                                            <td class="px-4 py-2 font-medium text-gray-900" x-text="row[0] || '-'"></td>
                                            <td class="px-4 py-2 text-gray-500"><code class="text-xs bg-gray-100 px-1 rounded" x-text="row[1] || '-'"></code></td>
                                            <td class="px-4 py-2 text-gray-500" x-text="row[3] || 'Ya'"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="flex items-center justify-between">
                        <a href="{{ route('vendor.linktree.show', $linktree) }}" class="text-sm text-gray-500 hover:text-gray-700">Batal</a>
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="!fileSelected" id="submitBtn">
                            <i class="fas fa-file-import mr-2"></i>Import Link
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Format Guide --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200">
                <h2 class="text-base font-semibold text-gray-900">
                    <i class="fas fa-question-circle mr-2 text-primary-600"></i>Panduan Format CSV
                </h2>
            </div>
            <div class="p-5 space-y-5">
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Format Kolom yang Didukung</h4>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Kolom Wajib</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Alternatif Nama</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr><td class="px-4 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Judul</span></td><td class="px-4 py-2 text-gray-600">title, nama, name</td><td class="px-4 py-2 text-gray-600">Judul link yang akan ditampilkan</td></tr>
                                <tr><td class="px-4 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">URL</span></td><td class="px-4 py-2 text-gray-600">url, link, website</td><td class="px-4 py-2 text-gray-600">URL tujuan link</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Kolom Opsional</h4>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Kolom</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Alternatif Nama</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Keterangan</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Default</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr><td class="px-4 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Ikon</span></td><td class="px-4 py-2 text-gray-600">icon, emoji</td><td class="px-4 py-2 text-gray-600">Emoji atau nama icon</td><td class="px-4 py-2 text-gray-500">-</td></tr>
                                <tr><td class="px-4 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Aktif</span></td><td class="px-4 py-2 text-gray-600">active, status</td><td class="px-4 py-2 text-gray-600">ya/yes/true/1/aktif = aktif</td><td class="px-4 py-2 text-gray-500">Aktif</td></tr>
                                <tr><td class="px-4 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Urutan</span></td><td class="px-4 py-2 text-gray-600">order, sort, posisi</td><td class="px-4 py-2 text-gray-600">Urutan tampilan (angka)</td><td class="px-4 py-2 text-gray-500">Auto-increment</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Contoh CSV</h4>
                    <pre class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-xs text-gray-700 overflow-x-auto"><code>Judul,URL,Ikon,Aktif,Urutan
Instagram,https://instagram.com/namaprofile,📸,Ya,1
WhatsApp,https://wa.me/6281234567890,💬,Ya,2
Website Toko,https://tokoku.com,🛒,Ya,3
Portfolio,https://portfolio.com,🎨,Tidak,4</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function importHandler() {
    return {
        fileSelected: false,
        fileName: '',
        fileSize: '',
        previewData: [],
        handleFile(e) {
            const file = e.target.files[0];
            if (!file) {
                this.fileSelected = false;
                this.fileName = '';
                this.previewData = [];
                return;
            }
            this.fileSelected = true;
            this.fileName = file.name;
            this.fileSize = '(' + (file.size / 1024).toFixed(1) + ' KB)';

            const reader = new FileReader();
            reader.onload = (event) => {
                const lines = event.target.result.split('\n').filter(l => l.trim());
                if (lines.length < 2) { this.previewData = []; return; }
                this.previewData = lines.slice(1, 6).map(line => this.parseCSVLine(line));
            };
            reader.readAsText(file);
        },
        parseCSVLine(line) {
            const result = [];
            let current = '';
            let inQuotes = false;
            for (let i = 0; i < line.length; i++) {
                const char = line[i];
                if (char === '"') { inQuotes = !inQuotes; }
                else if (char === ',' && !inQuotes) { result.push(current.trim()); current = ''; }
                else { current += char; }
            }
            result.push(current.trim());
            return result;
        }
    };
}

document.getElementById('importForm').addEventListener('submit', function(e) {
    const mode = document.querySelector('input[name="import_mode"]:checked').value;
    if (mode === 'replace') {
        if (!confirm('Semua link yang ada akan DIHAPUS dan diganti dengan data dari CSV. Lanjutkan?')) {
            e.preventDefault();
        }
    }
});
</script>
@endpush
@endsection
