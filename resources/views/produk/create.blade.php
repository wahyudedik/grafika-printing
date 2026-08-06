@extends('layouts.vendor')

@section('title', 'Tambah Produk')
@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6">
            <div>
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <h4 class="text-sm font-medium text-red-800 mb-2">Error!</h4>
                        <ul class="list-disc list-inside text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('vendor.products.store') }}" method="POST" enctype="multipart/form-data"
                    onsubmit="showLoading('Menambahkan produk...')">
                    @csrf
                    <div class="bg-white rounded-xl shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Tambah Produk Baru</h3>
                        </div>
                        <div class="px-6 py-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Nama Produk <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nama_produk" value="{{ old('nama_produk') }}"
                                        placeholder="Masukkan nama produk"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm @error('nama_produk') border-red-500 @enderror">
                                    @error('nama_produk')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Kategori <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex gap-2">
                                        <select name="kategori_id" id="kategori-select"
                                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm @error('kategori_id') border-red-500 @enderror">
                                            <option value="">Pilih Kategori</option>
                                            @foreach ($kategories as $kategori)
                                                <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                                    {{ $kategori->nama_kategori }}
                                                </option>
                                            @endforeach
                                            <option value="new">+ Kategori Baru</option>
                                        </select>
                                        <button type="button" id="toggle-new-category"
                                            class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    @error('kategori_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror

                                    <div id="new-category-container" class="mt-2 hidden">
                                        <input type="text" name="new_kategori" value="{{ old('new_kategori') }}"
                                            placeholder="Masukkan nama kategori baru"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm @error('new_kategori') border-red-500 @enderror">
                                        @error('new_kategori')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                    <textarea name="deskripsi" rows="4" placeholder="Masukkan deskripsi produk"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi') }}</textarea>
                                    @error('deskripsi')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Produk</label>
                                    <input type="file" name="gambar[]" multiple accept="image/*"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm @error('gambar.*') border-red-500 @enderror">
                                    <p class="mt-1 text-xs text-gray-500">Anda dapat memilih beberapa gambar. Format: JPG, PNG, GIF. Maks 2MB per file.</p>
                                    @error('gambar.*')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Specifications Section -->
                            <div class="mt-6">
                                <h4 class="text-sm font-semibold text-gray-900">Spesifikasi Produk</h4>
                                <p class="text-sm text-gray-500 mb-3">Tambahkan spesifikasi yang dapat dipilih pelanggan saat memesan produk ini</p>

                                <div id="specifications-container"></div>

                                <div class="mt-3">
                                    <button type="button" class="inline-flex items-center gap-2 border border-primary text-primary px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/5 transition-colors" id="add-specification-row">
                                        <i class="fas fa-plus"></i>
                                        Tambah Spesifikasi
                                    </button>
                                </div>
                            </div>

                            <!-- Production Estimates Section -->
                            <div class="mt-6">
                                <h4 class="text-sm font-semibold text-gray-900">Estimasi Produksi</h4>
                                <p class="text-sm text-gray-500 mb-3">Tambahkan estimasi waktu produksi untuk setiap alat yang digunakan</p>

                                <div id="estimates-container"></div>

                                <div class="mt-3">
                                    <button type="button" class="inline-flex items-center gap-2 border border-primary text-primary px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/5 transition-colors" id="add-estimate-row">
                                        <i class="fas fa-plus"></i>
                                        Tambah Estimasi Produksi
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                            <button type="submit"
                                class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                                <i class="fas fa-save"></i>
                                Simpan
                            </button>
                            <a href="{{ route('vendor.products.index') }}"
                                class="inline-flex items-center gap-2 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
                                <i class="fas fa-times"></i>
                                Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const specificationsContainer = document.getElementById('specifications-container');
                const addSpecificationButton = document.getElementById('add-specification-row');
                const estimatesContainer = document.getElementById('estimates-container');
                const addEstimateButton = document.getElementById('add-estimate-row');
                const kategoriSelect = document.getElementById('kategori-select');
                const newCategoryContainer = document.getElementById('new-category-container');
                const toggleNewCategoryBtn = document.getElementById('toggle-new-category');
                const newKategoriInput = document.querySelector('input[name="new_kategori"]');

                let specRowCount = 0;
                let estimateRowCount = 0;

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

                addSpecificationButton.addEventListener('click', function() {
                    addSpecificationRow();
                });

                addEstimateButton.addEventListener('click', function() {
                    addEstimateRow();
                });

                function addSpecificationRow() {
                    const rowId = `spec-row-${specRowCount}`;
                    const spesifikasis = @json($spesifikasis);
                    const bahans = @json($bahans);

                    let spesifikasiOptions = '<option value="">Pilih Spesifikasi</option>';
                    spesifikasis.forEach(spec => {
                        spesifikasiOptions += `<option value="${spec.id}">${spec.nama_spesifikasi} (${spec.tipe_input})</option>`;
                    });

                    let bahanOptions = '<option value="">Pilih Bahan</option>';
                    bahans.forEach(bahan => {
                        bahanOptions += `<option value="${bahan.id}">${bahan.nama_bahan} (${bahan.satuan})</option>`;
                    });

                    const html = `
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mb-3 spec-row items-end" id="${rowId}">
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Spesifikasi</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm spec-select"
                                name="spesifikasi[${specRowCount}][spesifikasi_id]" required onchange="updateSpecOptions('${rowId}')">
                                ${spesifikasiOptions}
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Wajib Diisi</label>
                            <div class="flex items-center mt-2">
                                <input type="checkbox" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary"
                                    name="spesifikasi[${specRowCount}][wajib_diisi]" value="1">
                                <label class="ml-2 text-sm text-gray-700">Ya</label>
                            </div>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilihan (untuk dropdown/radio)</label>
                            <div class="flex gap-1">
                                <input type="text" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm"
                                    id="${rowId}-option-input" placeholder="Tambahkan pilihan">
                                <button type="button" class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors text-sm"
                                    onclick="addOption('${rowId}')">+</button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Tekan + untuk menambahkan setiap pilihan</p>
                            <div class="mt-2 flex flex-wrap gap-1" id="${rowId}-options-container"></div>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bahan yang Digunakan</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm"
                                name="spesifikasi[${specRowCount}][bahan_ids][]" multiple>
                                ${bahanOptions}
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Tahan Ctrl untuk memilih beberapa bahan</p>
                        </div>
                        <div class="md:col-span-1 flex items-end">
                            <button type="button" class="w-full px-3 py-2 border border-red-500 text-red-500 rounded-lg text-sm hover:bg-red-50 transition-colors"
                                onclick="removeSpecRow('${rowId}')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    `;

                    specificationsContainer.insertAdjacentHTML('beforeend', html);
                    specRowCount++;
                }

                function addEstimateRow() {
                    const rowId = `estimate-row-${estimateRowCount}`;
                    const alats = @json($alats);

                    let alatOptions = '<option value="">Pilih Alat</option>';
                    alats.forEach(alat => {
                        alatOptions += `<option value="${alat.id}">${alat.nama_alat}</option>`;
                    });

                    const html = `
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mb-3 estimate-row items-end" id="${rowId}">
                        <div class="md:col-span-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alat Produksi</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm"
                                name="estimasi[${estimateRowCount}][alat_id]" required>
                                ${alatOptions}
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Persiapan (menit)</label>
                            <input type="number" step="0.01" min="0" required placeholder="Waktu setup"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm"
                                name="estimasi[${estimateRowCount}][waktu_persiapan]">
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Per Unit (menit)</label>
                            <input type="number" step="0.01" min="0" required placeholder="Waktu produksi per unit"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm"
                                name="estimasi[${estimateRowCount}][waktu_produksi_per_unit]">
                        </div>
                        <div class="md:col-span-1 flex items-end">
                            <button type="button" class="w-full px-3 py-2 border border-red-500 text-red-500 rounded-lg text-sm hover:bg-red-50 transition-colors"
                                onclick="removeEstimateRow('${rowId}')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    `;

                    estimatesContainer.insertAdjacentHTML('beforeend', html);
                    estimateRowCount++;
                }

                addSpecificationRow();
                addEstimateRow();
            });

            function removeSpecRow(rowId) {
                document.getElementById(rowId).remove();
            }

            function removeEstimateRow(rowId) {
                document.getElementById(rowId).remove();
            }

            function addOption(rowId) {
                const optionInput = document.getElementById(`${rowId}-option-input`);
                const optionsContainer = document.getElementById(`${rowId}-options-container`);

                if (!optionInput.value.trim()) return;

                const specIndex = rowId.split('-')[2];
                const html = `
                <div class="inline-flex items-center gap-1 bg-primary/10 text-primary px-2 py-1 rounded-full text-xs">
                    <input type="hidden" name="spesifikasi[${specIndex}][pilihan][]" value="${optionInput.value.trim()}">
                    <span>${optionInput.value.trim()}</span>
                    <button type="button" class="text-primary hover:text-red-500" onclick="this.parentElement.remove()">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                `;

                optionsContainer.insertAdjacentHTML('beforeend', html);
                optionInput.value = '';
            }

            function updateSpecOptions(rowId) {
                const row = document.getElementById(rowId);
                if (!row) return;

                const specSelect = row.querySelector('.spec-select');
                if (!specSelect) return;

                const optionsSection = row.querySelector('[id$="-options-container"]').parentElement;
                const bahanSection = optionsSection.nextElementSibling;

                const spesifikasis = @json($spesifikasis);
                const selectedSpec = spesifikasis.find(spec => spec.id == specSelect.value);

                if (selectedSpec) {
                    if (selectedSpec.tipe_input === 'select' || selectedSpec.tipe_input === 'radio') {
                        optionsSection.style.display = 'block';
                    } else {
                        optionsSection.style.display = 'none';
                    }
                    bahanSection.style.display = 'block';
                }
            }
        </script>
    @endpush
@endsection
