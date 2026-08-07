@extends('layouts.vendor')

@section('title', 'Edit Produk')
@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-red-800 mb-2">Error!</h4>
                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('vendor.products.update', $produk->id) }}" method="POST"
            enctype="multipart/form-data" onsubmit="showLoading('Memperbarui produk...')">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Produk</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                            <input type="text"
                                class="w-full px-4 py-2 border {{ $errors->has('nama_produk') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-primary focus:border-primary' }} rounded-lg text-sm transition"
                                name="nama_produk" value="{{ old('nama_produk', $produk->nama_produk) }}"
                                placeholder="Masukkan nama produk">
                            @error('nama_produk')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                            <div class="flex">
                                <select
                                    class="flex-1 px-4 py-2 border {{ $errors->has('kategori_id') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-primary focus:border-primary' }} rounded-lg text-sm transition"
                                    name="kategori_id" id="kategori-select">
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($kategories as $kategori)
                                        <option value="{{ $kategori->id }}"
                                            {{ old('kategori_id', $produk->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                            {{ $kategori->nama_kategori }}
                                        </option>
                                    @endforeach
                                    <option value="new">+ Tambah Kategori Baru</option>
                                </select>
                                <button type="button" id="toggle-new-category"
                                    class="ml-2 px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            @error('kategori_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="new-category-container" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori Baru</label>
                            <input type="text"
                                class="w-full px-4 py-2 border {{ $errors->has('new_kategori') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-primary focus:border-primary' }} rounded-lg text-sm transition"
                                name="new_kategori" value="{{ old('new_kategori') }}"
                                placeholder="Masukkan nama kategori baru">
                            @error('new_kategori')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea
                                class="w-full px-4 py-2 border {{ $errors->has('deskripsi') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-primary focus:border-primary' }} rounded-lg text-sm transition"
                                name="deskripsi" rows="5"
                                placeholder="Masukkan deskripsi produk">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual (Rp)</label>
                            <input type="number"
                                class="w-full px-4 py-2 border {{ $errors->has('harga_jual') ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-primary focus:border-primary' }} rounded-lg text-sm transition"
                                name="harga_jual" value="{{ old('harga_jual', $produk->harga_jual) }}"
                                min="0" step="100" placeholder="0">
                            <p class="mt-1 text-xs text-gray-500">Harga default untuk produk ini (opsional). Harga bisa disesuaikan per spesifikasi.</p>
                            @error('harga_jual')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Product Images Section -->
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-base font-semibold text-gray-900 mb-3">Gambar Produk</h4>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tambah Gambar Baru</label>
                            <input type="file"
                                class="w-full px-4 py-2 border {{ $errors->has('gambar.*') ? 'border-red-300' : 'border-gray-300' }} rounded-lg text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20"
                                name="gambar[]" multiple accept="image/*">
                            <p class="mt-1 text-xs text-gray-500">Anda dapat memilih beberapa gambar. Format: JPG, PNG, GIF. Maks 2MB per file.</p>
                            @error('gambar.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        @if (!empty($produk->gambar))
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Saat Ini</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                    @foreach ($produk->gambar as $index => $image)
                                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                                            <img src="{{ asset($image) }}" alt="Product Image"
                                                class="w-full h-24 object-cover">
                                            <div class="p-2 text-center">
                                                <label class="inline-flex items-center gap-1.5 text-xs text-gray-600 cursor-pointer">
                                                    <input type="checkbox" class="w-3.5 h-3.5 text-red-600 border-gray-300 rounded focus:ring-red-500"
                                                        name="delete_image[]" value="{{ $index }}">
                                                    Hapus
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Specifications Section -->
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-base font-semibold text-gray-900 mb-1">Spesifikasi Produk</h4>
                        <p class="text-sm text-gray-500 mb-4">Kelola spesifikasi yang dapat dipilih pelanggan saat memesan produk ini</p>

                        <div id="specifications-container" class="space-y-4">
                            @foreach ($produk->spesifikasiProduk as $index => $spec)
                                <div class="border border-gray-200 rounded-lg p-4 spec-row" id="spec-row-existing-{{ $spec->id }}">
                                    <input type="hidden" name="spesifikasi[{{ $index }}][id]"
                                        value="{{ $spec->id }}">
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                                        <div class="md:col-span-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Spesifikasi</label>
                                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary spec-select"
                                                name="spesifikasi[{{ $index }}][spesifikasi_id]" required
                                                onchange="updateSpecOptions('spec-row-existing-{{ $spec->id }}')">
                                                <option value="">Pilih Spesifikasi</option>
                                                @foreach ($spesifikasis as $spesifikasi)
                                                    <option value="{{ $spesifikasi->id }}"
                                                        {{ $spec->spesifikasi_id == $spesifikasi->id ? 'selected' : '' }}>
                                                        {{ $spesifikasi->nama_spesifikasi }} ({{ $spesifikasi->tipe_input }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Wajib Diisi</label>
                                            <label class="inline-flex items-center gap-2 mt-2 cursor-pointer">
                                                <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary"
                                                    name="spesifikasi[{{ $index }}][wajib_diisi]" value="1"
                                                    {{ $spec->wajib_diisi ? 'checked' : '' }}>
                                                <span class="text-sm text-gray-700">Ya</span>
                                            </label>
                                        </div>
                                        <div class="md:col-span-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilihan (untuk dropdown/radio)</label>
                                            <div class="flex">
                                                <input type="text"
                                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg text-sm focus:ring-primary focus:border-primary"
                                                    id="spec-row-existing-{{ $spec->id }}-option-input"
                                                    placeholder="Tambahkan pilihan">
                                                <button type="button"
                                                    class="px-3 py-2 border border-l-0 border-gray-300 rounded-r-lg text-gray-600 bg-gray-50 hover:bg-gray-100 text-sm"
                                                    onclick="addOption('spec-row-existing-{{ $spec->id }}')">+</button>
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500">Tekan + untuk menambahkan setiap pilihan</p>
                                            <div class="mt-2 flex flex-wrap gap-1"
                                                id="spec-row-existing-{{ $spec->id }}-options-container">
                                                @if (!empty($spec->pilihan))
                                                    @foreach ($spec->pilihan as $pilihan)
                                                        <div class="inline-flex items-center gap-1 bg-primary/10 text-primary px-2 py-1 rounded-full text-xs font-medium option-item">
                                                            <input type="hidden"
                                                                name="spesifikasi[{{ $index }}][pilihan][]"
                                                                value="{{ $pilihan }}">
                                                            {{ $pilihan }}
                                                            <button type="button" class="text-red-500 hover:text-red-700 ml-1"
                                                                onclick="this.parentElement.remove()">
                                                                <i class="fas fa-times text-xs"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                        <div class="md:col-span-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Bahan yang Digunakan</label>
                                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary"
                                                name="spesifikasi[{{ $index }}][bahan_ids][]" multiple>
                                                @foreach ($bahans as $bahan)
                                                    <option value="{{ $bahan->id }}"
                                                        {{ $spec->bahanSpesifikasiProduk->contains($bahan->id) ? 'selected' : '' }}>
                                                        {{ $bahan->nama_bahan }} ({{ $bahan->satuan }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <p class="mt-1 text-xs text-gray-500">Tahan Ctrl untuk memilih beberapa bahan</p>
                                        </div>
                                        <div class="md:col-span-1 flex items-end justify-end">
                                            <button type="button"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition remove-existing-spec"
                                                data-id="{{ $spec->id }}"
                                                data-row="spec-row-existing-{{ $spec->id }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div id="new-specs-container" class="space-y-4">
                        </div>

                        <input type="hidden" name="deleted_spec_ids" id="deleted-spec-ids">

                        <div class="mt-4">
                            <button type="button"
                                class="inline-flex items-center gap-2 px-4 py-2 border border-dashed border-primary text-primary rounded-lg text-sm font-medium hover:bg-primary/5 transition"
                                id="add-specification-row">
                                <i class="fas fa-plus"></i>
                                Tambah Spesifikasi Baru
                            </button>
                        </div>
                    </div>

                    <!-- Production Estimates Section -->
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-base font-semibold text-gray-900 mb-1">Estimasi Produksi</h4>
                        <p class="text-sm text-gray-500 mb-4">Kelola estimasi waktu produksi untuk setiap alat yang digunakan</p>

                        <div id="estimates-container" class="space-y-4">
                            @foreach ($produk->estimasiProduk as $index => $estimasi)
                                <div class="border border-gray-200 rounded-lg p-4 estimate-row"
                                    id="estimate-row-existing-{{ $estimasi->id }}">
                                    <input type="hidden" name="estimasi[{{ $index }}][id]"
                                        value="{{ $estimasi->id }}">
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                                        <div class="md:col-span-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Alat Produksi</label>
                                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary"
                                                name="estimasi[{{ $index }}][alat_id]" required>
                                                <option value="">Pilih Alat</option>
                                                @foreach ($alats as $alat)
                                                    <option value="{{ $alat->id }}"
                                                        {{ $estimasi->alat_id == $alat->id ? 'selected' : '' }}>
                                                        {{ $alat->nama_alat }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="md:col-span-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Persiapan (menit)</label>
                                            <input type="number"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary"
                                                name="estimasi[{{ $index }}][waktu_persiapan]" step="0.01"
                                                min="0" required placeholder="Waktu setup"
                                                value="{{ $estimasi->waktu_persiapan }}">
                                        </div>
                                        <div class="md:col-span-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Per Unit (menit)</label>
                                            <input type="number"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary"
                                                name="estimasi[{{ $index }}][waktu_produksi_per_unit]"
                                                step="0.01" min="0" required
                                                placeholder="Waktu produksi per unit"
                                                value="{{ $estimasi->waktu_produksi_per_unit }}">
                                        </div>
                                        <div class="md:col-span-1 flex items-end justify-end">
                                            <button type="button"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition remove-existing-estimate"
                                                data-id="{{ $estimasi->id }}"
                                                data-row="estimate-row-existing-{{ $estimasi->id }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div id="new-estimates-container" class="space-y-4">
                        </div>

                        <input type="hidden" name="deleted_estimate_ids" id="deleted-estimate-ids">

                        <div class="mt-4">
                            <button type="button"
                                class="inline-flex items-center gap-2 px-4 py-2 border border-dashed border-primary text-primary rounded-lg text-sm font-medium hover:bg-primary/5 transition"
                                id="add-estimate-row">
                                <i class="fas fa-plus"></i>
                                Tambah Estimasi Baru
                            </button>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition">
                        <i class="fas fa-save"></i>
                        Update
                    </button>
                    <a href="{{ route('vendor.products.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                        <i class="fas fa-times"></i>
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const newSpecsContainer = document.getElementById('new-specs-container');
                const addSpecButton = document.getElementById('add-specification-row');
                const newEstimatesContainer = document.getElementById('new-estimates-container');
                const addEstimateButton = document.getElementById('add-estimate-row');
                const deletedSpecIds = document.getElementById('deleted-spec-ids');
                const deletedEstimateIds = document.getElementById('deleted-estimate-ids');
                const kategoriSelect = document.getElementById('kategori-select');
                const newCategoryContainer = document.getElementById('new-category-container');
                const toggleNewCategoryBtn = document.getElementById('toggle-new-category');
                const newKategoriInput = document.querySelector('input[name="new_kategori"]');

                let specRowCount = 0;
                let estimateRowCount = 0;
                let deletedSpecs = [];
                let deletedEstimates = [];

                // Category handling
                kategoriSelect.addEventListener('change', function() {
                    if (this.value === 'new') {
                        newCategoryContainer.classList.remove('hidden');
                        newKategoriInput.setAttribute('required', 'required');
                    } else {
                        newCategoryContainer.classList.add('hidden');
                        newKategoriInput.removeAttribute('required');
                    }
                });

                toggleNewCategoryBtn.addEventListener('click', function() {
                    if (newCategoryContainer.classList.contains('hidden')) {
                        newCategoryContainer.classList.remove('hidden');
                        kategoriSelect.value = 'new';
                        newKategoriInput.setAttribute('required', 'required');
                    } else {
                        newCategoryContainer.classList.add('hidden');
                        kategoriSelect.value = '';
                        newKategoriInput.removeAttribute('required');
                    }
                });

                // Initialize functionality for existing specs and estimates
                document.querySelectorAll('.remove-existing-spec').forEach(button => {
                    button.addEventListener('click', function() {
                        const rowId = this.getAttribute('data-row');
                        const specId = this.getAttribute('data-id');

                        // Add ID to deleted list
                        deletedSpecs.push(specId);
                        deletedSpecIds.value = deletedSpecs.join(',');

                        // Remove the row
                        document.getElementById(rowId).remove();
                    });
                });

                document.querySelectorAll('.remove-existing-estimate').forEach(button => {
                    button.addEventListener('click', function() {
                        const rowId = this.getAttribute('data-row');
                        const estimateId = this.getAttribute('data-id');

                        // Add ID to deleted list
                        deletedEstimates.push(estimateId);
                        deletedEstimateIds.value = deletedEstimates.join(',');

                        // Remove the row
                        document.getElementById(rowId).remove();
                    });
                });

                // Add new specification row
                addSpecButton.addEventListener('click', function() {
                    addSpecificationRow();
                });

                // Add new estimate row
                addEstimateButton.addEventListener('click', function() {
                    addEstimateRow();
                });

                function addSpecificationRow() {
                    const rowId = `new-spec-row-${specRowCount}`;
                    const spesifikasis = @json($spesifikasis);
                    const bahans = @json($bahans);

                    let spesifikasiOptions = '';
                    spesifikasis.forEach(spec => {
                        spesifikasiOptions +=
                            `<option value="${spec.id}">${spec.nama_spesifikasi} (${spec.tipe_input})</option>`;
                    });

                    let bahanOptions = '';
                    bahans.forEach(bahan => {
                        bahanOptions +=
                            `<option value="${bahan.id}">${bahan.nama_bahan} (${bahan.satuan})</option>`;
                    });

                    const html = `
                        <div class="border border-gray-200 rounded-lg p-4 spec-row" id="${rowId}">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                                <div class="md:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Spesifikasi</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary spec-select" name="new_spesifikasi[${specRowCount}][spesifikasi_id]" required onchange="updateSpecOptions('${rowId}')">
                                        <option value="">Pilih Spesifikasi</option>
                                        ${spesifikasiOptions}
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Wajib Diisi</label>
                                    <label class="inline-flex items-center gap-2 mt-2 cursor-pointer">
                                        <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary" name="new_spesifikasi[${specRowCount}][wajib_diisi]" value="1">
                                        <span class="text-sm text-gray-700">Ya</span>
                                    </label>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilihan (untuk dropdown/radio)</label>
                                    <div class="flex">
                                        <input type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg text-sm focus:ring-primary focus:border-primary" id="${rowId}-option-input" placeholder="Tambahkan pilihan">
                                        <button type="button" class="px-3 py-2 border border-l-0 border-gray-300 rounded-r-lg text-gray-600 bg-gray-50 hover:bg-gray-100 text-sm" onclick="addOption('${rowId}')">+</button>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Tekan + untuk menambahkan setiap pilihan</p>
                                    <div class="mt-2 flex flex-wrap gap-1" id="${rowId}-options-container">
                                    </div>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Bahan yang Digunakan</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary" name="new_spesifikasi[${specRowCount}][bahan_ids][]" multiple>
                                        ${bahanOptions}
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">Tahan Ctrl untuk memilih beberapa bahan</p>
                                </div>
                                <div class="md:col-span-1 flex items-end justify-end">
                                    <button type="button" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" onclick="removeSpecRow('${rowId}')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;

                    newSpecsContainer.insertAdjacentHTML('beforeend', html);
                    specRowCount++;
                }

                function addEstimateRow() {
                    const rowId = `new-estimate-row-${estimateRowCount}`;
                    const alats = @json($alats);

                    let alatOptions = '';
                    alats.forEach(alat => {
                        alatOptions += `<option value="${alat.id}">${alat.nama_alat}</option>`;
                    });

                    const html = `
                        <div class="border border-gray-200 rounded-lg p-4 estimate-row" id="${rowId}">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                                <div class="md:col-span-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alat Produksi</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary" name="new_estimasi[${estimateRowCount}][alat_id]" required>
                                        <option value="">Pilih Alat</option>
                                        ${alatOptions}
                                    </select>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Persiapan (menit)</label>
                                    <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary" name="new_estimasi[${estimateRowCount}][waktu_persiapan]"
                                        step="0.01" min="0" required placeholder="Waktu setup">
                                </div>
                                <div class="md:col-span-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Per Unit (menit)</label>
                                    <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary" name="new_estimasi[${estimateRowCount}][waktu_produksi_per_unit]"
                                        step="0.01" min="0" required placeholder="Waktu produksi per unit">
                                </div>
                                <div class="md:col-span-1 flex items-end justify-end">
                                    <button type="button" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" onclick="removeEstimateRow('${rowId}')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;

                    newEstimatesContainer.insertAdjacentHTML('beforeend', html);
                    estimateRowCount++;
                }

                // Initialize spec options display state based on type
                document.querySelectorAll('.spec-select').forEach(select => {
                    const rowId = select.closest('.spec-row').id;
                    updateSpecOptions(rowId);
                });
            });

            // Function to remove specification row
            function removeSpecRow(rowId) {
                document.getElementById(rowId).remove();
            }

            // Function to remove estimate row
            function removeEstimateRow(rowId) {
                document.getElementById(rowId).remove();
            }

            // Function to add option to a specification
            function addOption(rowId) {
                const optionInput = document.getElementById(`${rowId}-option-input`);
                const optionsContainer = document.getElementById(`${rowId}-options-container`);

                if (!optionInput.value.trim()) return;

                // Determine if this is an existing or new specification
                let name;
                if (rowId.startsWith('spec-row-existing-')) {
                    const hiddenInput = document.querySelector(`#${rowId} input[name^="spesifikasi"][name$="[id]"]`);
                    const match = hiddenInput.name.match(/spesifikasi\[(\d+)\]/);
                    const index = match ? match[1] : 0;
                    name = `spesifikasi[${index}][pilihan][]`;
                } else {
                    const index = rowId.split('-')[3];
                    name = `new_spesifikasi[${index}][pilihan][]`;
                }

                const html = `
                <div class="inline-flex items-center gap-1 bg-primary/10 text-primary px-2 py-1 rounded-full text-xs font-medium option-item">
                    <input type="hidden" name="${name}" value="${optionInput.value.trim()}">
                    ${optionInput.value.trim()}
                    <button type="button" class="text-red-500 hover:text-red-700 ml-1" onclick="this.parentElement.remove()">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                `;

                optionsContainer.insertAdjacentHTML('beforeend', html);
                optionInput.value = '';
            }

            // Function to update specification options based on spec type
            function updateSpecOptions(rowId) {
                const row = document.getElementById(rowId);
                if (!row) return;

                const specSelect = row.querySelector('.spec-select');
                if (!specSelect) return;

                const optionsSection = row.querySelector('[id$="-options-container"]').parentElement;
                const bahanSection = optionsSection.nextElementSibling;

                // Get the selected specification
                const spesifikasis = @json($spesifikasis);
                const selectedSpec = spesifikasis.find(spec => spec.id == specSelect.value);

                if (selectedSpec) {
                    // Show options section only for select or radio types
                    if (selectedSpec.tipe_input === 'select' || selectedSpec.tipe_input === 'radio') {
                        optionsSection.style.display = 'block';
                    } else {
                        optionsSection.style.display = 'none';
                    }

                    // Always show bahan selection
                    bahanSection.style.display = 'block';
                }
            }
        </script>
    @endpush
@endsection
