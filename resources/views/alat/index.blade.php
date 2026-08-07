@extends('layouts.vendor')

@section('title', 'Manajemen Alat')
@section('content')
    <div class="bg-white rounded-xl shadow-sm" x-data="bulkActions()">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row gap-3 justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Alat</h3>
                </div>
                <div class="flex gap-2 flex-grow justify-end">
                    <form action="{{ route('vendor.tools.index') }}" method="GET" class="flex-grow" data-no-loading>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm"
                                placeholder="Cari alat...">
                        </div>
                    </form>

                    {{-- Filter Status --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary"
                            data-no-loading>
                            Filter Status: {{ request('status') ? ucfirst(request('status')) : 'Semua' }}
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-no-loading
                                href="{{ route('vendor.tools.index', array_merge(request()->except('status'), ['status' => ''])) }}">Semua</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-no-loading
                                href="{{ route('vendor.tools.index', array_merge(request()->except('status'), ['status' => 'aktif'])) }}">Aktif</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-no-loading
                                href="{{ route('vendor.tools.index', array_merge(request()->except('status'), ['status' => 'maintenance'])) }}">Maintenance</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-no-loading
                                href="{{ route('vendor.tools.index', array_merge(request()->except('status'), ['status' => 'rusak'])) }}">Rusak</a>
                        </div>
                    </div>

                    {{-- Filter Ketersediaan --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary"
                            data-no-loading>
                            Ketersediaan: {{ request('tersedia') !== null ? (request('tersedia') === 'yes' ? 'Tersedia' : 'Tidak Tersedia') : 'Semua' }}
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-no-loading
                                href="{{ route('vendor.tools.index', array_merge(request()->except('tersedia'), ['tersedia' => ''])) }}">Semua</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-no-loading
                                href="{{ route('vendor.tools.index', array_merge(request()->except('tersedia'), ['tersedia' => 'yes'])) }}">Tersedia</a>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-no-loading
                                href="{{ route('vendor.tools.index', array_merge(request()->except('tersedia'), ['tersedia' => 'no'])) }}">Tidak Tersedia</a>
                        </div>
                    </div>

                    <a href="{{ route('vendor.tools.create') }}"
                        class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                        <i class="fas fa-plus"></i>
                        Tambah Alat
                    </a>
                </div>
            </div>
        </div>

        {{-- Bulk Action Bar --}}
        <div x-show="selectedIds.length > 0" x-transition
            class="px-6 py-3 bg-primary/5 border-b border-primary/20 flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <span class="text-sm font-medium text-primary">
                <span x-text="selectedIds.length"></span> alat dipilih
            </span>
            <div class="flex items-center gap-2 flex-wrap">
                <select x-model="bulkField"
                    class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">Pilih field...</option>
                    <option value="status">Status</option>
                    <option value="tersedia">Ketersediaan</option>
                </select>
                <template x-if="bulkField === 'status'">
                    <select x-model="bulkValue"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                        <option value="">Pilih status...</option>
                        <option value="aktif">Aktif</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="rusak">Rusak</option>
                    </select>
                </template>
                <template x-if="bulkField === 'tersedia'">
                    <select x-model="bulkValue"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                        <option value="">Pilih ketersediaan...</option>
                        <option value="1">Tersedia</option>
                        <option value="0">Tidak Tersedia</option>
                    </select>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Alat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merek</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spesifikasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pembelian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kapasitas/Jam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tersedia</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($alat as $item)
                        <tr class="hover:bg-gray-50" :class="selectedIds.includes({{ $item->id }}) ? 'bg-primary/5' : ''">
                            <td class="px-6 py-4">
                                <input type="checkbox" value="{{ $item->id }}"
                                    @change="toggleItem({{ $item->id }})"
                                    :checked="selectedIds.includes({{ $item->id }})"
                                    class="rounded border-gray-300 text-primary focus:ring-primary">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->nama_alat }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->merek }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->model }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($item->spesifikasi_alat, 50) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($item->status === 'aktif') bg-green-100 text-green-700
                                    @elseif($item->status === 'maintenance') bg-amber-100 text-amber-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->tanggal_pembelian->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->kapasitas_cetak_per_jam }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $item->tersedia ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $item->tersedia ? 'Ya' : 'Tidak' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('vendor.tools.show', $item->id) }}"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('vendor.tools.edit', $item->id) }}"
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
                            <td colspan="10" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-tools text-6xl text-gray-300 mb-4"></i>
                                    <p class="text-lg font-medium text-gray-900 mb-1">Tidak ada data alat</p>
                                    <p class="text-sm text-gray-500 mb-4">Silahkan tambahkan alat baru atau ubah filter pencarian</p>
                                    <a href="{{ route('vendor.tools.create') }}"
                                        class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                                        <i class="fas fa-plus"></i>
                                        Tambah Alat
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $alat->links('components.pagination') }}
        </div>
    </div>

    <!-- Hidden delete form -->
    <form id="delete-form" action="" method="POST" style="display: none;" data-no-loading>
        @csrf
        @method('DELETE')
    </form>

    <!-- Hidden bulk form -->
    <form id="bulk-form" action="{{ route('vendor.tools.bulk-update') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="field" :value="bulkField">
        <input type="hidden" name="value" :value="bulkValue">
    </form>

    @push('scripts')
        <script>
            function bulkActions() {
                return {
                    selectedIds: [],
                    bulkField: '',
                    bulkValue: '',
                    items: @json($alat->pluck('id')),
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
                            text: `Perbarui ${this.selectedIds.length} alat?`,
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
                    deleteForm.setAttribute('data-action', '{{ route('vendor.tools.destroy', '__ID__') }}');
                }
            });
        </script>
        <script src="{{ asset('js/dashboard-common.js') }}"></script>
    @endpush
@endsection
