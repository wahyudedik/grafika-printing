@extends('layouts.vendor')

@section('title', 'Manajemen Bahan')
@section('content')
    <div class="bg-white rounded-xl shadow-sm" x-data="bulkActions()">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row gap-3 justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Bahan</h3>
                </div>
                <div class="flex gap-2 flex-grow justify-end">
                    <form action="{{ route('vendor.materials.index') }}" method="GET" class="flex-grow">
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm"
                                placeholder="Cari bahan...">
                        </div>
                    </form>

                    {{-- Filter Stok --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary">
                            Filter Stok: {{ request('stok') ? ucfirst(request('stok')) : 'Semua' }}
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="{{ route('vendor.materials.index', array_merge(request()->except('stok'), ['stok' => ''])) }}">Semua</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="{{ route('vendor.materials.index', array_merge(request()->except('stok'), ['stok' => 'available'])) }}">Tersedia</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="{{ route('vendor.materials.index', array_merge(request()->except('stok'), ['stok' => 'low'])) }}">Stok Rendah</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="{{ route('vendor.materials.index', array_merge(request()->except('stok'), ['stok' => 'out'])) }}">Habis</a>
                        </div>
                    </div>

                    {{-- Filter Harga Grosir --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary">
                            Harga Grosir: {{ request('has_wholesale') ? (request('has_wholesale') == 'yes' ? 'Ada' : 'Tidak Ada') : 'Semua' }}
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="{{ route('vendor.materials.index', array_merge(request()->except('has_wholesale'), ['has_wholesale' => ''])) }}">Semua</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="{{ route('vendor.materials.index', array_merge(request()->except('has_wholesale'), ['has_wholesale' => 'yes'])) }}">Ada</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                href="{{ route('vendor.materials.index', array_merge(request()->except('has_wholesale'), ['has_wholesale' => 'no'])) }}">Tidak Ada</a>
                        </div>
                    </div>

                    <a href="{{ route('vendor.materials.create') }}"
                        class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                        <i class="fas fa-plus"></i>
                        Tambah Bahan
                    </a>
                </div>
            </div>
        </div>

        {{-- Bulk Action Bar --}}
        <div x-show="selectedIds.length > 0" x-transition
            class="px-6 py-3 bg-primary/5 border-b border-primary/20 flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <span class="text-sm font-medium text-primary">
                <span x-text="selectedIds.length"></span> bahan dipilih
            </span>
            <div class="flex items-center gap-2 flex-wrap">
                <select x-model="bulkField"
                    class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">Pilih field...</option>
                    <option value="stok">Stok</option>
                    <option value="hpp">HPP (Rp)</option>
                </select>
                <template x-if="bulkField === 'stok' || bulkField === 'hpp'">
                    <input type="number" x-model="bulkValue" min="0" step="1"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary w-32"
                        :placeholder="bulkField === 'stok' ? 'Jumlah stok' : 'Harga (Rp)'">
                </template>
                <button @click="submitBulk()" :disabled="!bulkField || !bulkValue"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-save text-xs"></i>
                    Terapkan
                </button>
                <button @click="clearSelection()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                    Batal
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" @change="toggleAll($event)"
                                :checked="selectedIds.length === items.length && items.length > 0"
                                class="rounded border-gray-300 text-primary focus:ring-primary">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Bahan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">HPP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Grosir</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($bahan as $item)
                        <tr class="hover:bg-gray-50" :class="selectedIds.includes({{ $item->id }}) ? 'bg-primary/5' : ''">
                            <td class="px-6 py-4">
                                <input type="checkbox" value="{{ $item->id }}"
                                    @change="toggleItem({{ $item->id }})"
                                    :checked="selectedIds.includes({{ $item->id }})"
                                    class="rounded border-gray-300 text-primary focus:ring-primary">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->nama_bahan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format((float) $item->hpp, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->satuan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->stock_status_label }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($item->wholesalePrices->count() > 0)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-primary/10 text-primary">{{ $item->wholesalePrices->count() }} tier harga</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Tidak ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('vendor.materials.show', $item->id) }}"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('vendor.materials.edit', $item->id) }}"
                                        class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors delete-btn"
                                        data-id="{{ $item->id }}" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <x-ui.empty-state icon="fas fa-box-open" title="Tidak ada data bahan" description="Silahkan tambahkan bahan baru atau ubah filter pencarian">
                                    <x-slot:actions>
                                        <a href="{{ route('vendor.materials.create') }}"
                                            class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                                            <i class="fas fa-plus"></i>
                                            Tambah Bahan
                                        </a>
                                    </x-slot:actions>
                                </x-ui.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $bahan->links('dev.components.pagination') }}
        </div>
    </div>

    <!-- Hidden delete form -->
    <form id="delete-form" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- Hidden bulk form -->
    <form id="bulk-form" action="{{ route('vendor.materials.bulk-update') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="field" :value="bulkField">
        <input type="hidden" name="value" :value="bulkValue">
        <template x-for="id in selectedIds" :key="id">
            <input type="hidden" name="ids[]" :value="id">
        </template>
    </form>

    @push('scripts')
        <script>
            function bulkActions() {
                return {
                    selectedIds: [],
                    bulkField: '',
                    bulkValue: '',
                    items: @json($bahan->pluck('id')),
                    toggleAll(event) {
                        this.selectedIds = event.target.checked ? [...this.items] : [];
                    },
                    toggleItem(id) {
                        if (this.selectedIds.includes(id)) {
                            this.selectedIds = this.selectedIds.filter(i => i !== id);
                        } else {
                            this.selectedIds.push(id);
                        }
                    },
                    clearSelection() {
                        this.selectedIds = [];
                        this.bulkField = '';
                        this.bulkValue = '';
                    },
                    submitBulk() {
                        if (!this.bulkField || !this.bulkValue || this.selectedIds.length === 0) return;
                        Swal.fire({
                            title: 'Ubah massal?',
                            text: `Perbarui ${this.selectedIds.length} bahan?`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, terapkan!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const form = document.getElementById('bulk-form');
                                const fieldInput = form.querySelector('input[name="field"]');
                                const valueInput = form.querySelector('input[name="value"]');
                                // Remove old hidden inputs for ids
                                form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
                                // Add new hidden inputs
                                this.selectedIds.forEach(id => {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'ids[]';
                                    input.value = id;
                                    form.appendChild(input);
                                });
                                fieldInput.value = this.bulkField;
                                valueInput.value = this.bulkValue;
                                form.submit();
                            }
                        });
                    }
                };
            }

            // Set delete action for external script
            document.addEventListener('DOMContentLoaded', function() {
                const deleteForm = document.getElementById('delete-form');
                if (deleteForm) {
                    deleteForm.setAttribute('data-action', '{{ route('vendor.materials.destroy', '__ID__') }}');
                }
            });
        </script>
        <script src="{{ asset('js/dashboard-common.js') }}"></script>
    @endpush
@endsection
