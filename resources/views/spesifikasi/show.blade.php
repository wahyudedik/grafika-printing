@extends('layouts.vendor')

@section('title', 'Detail Spesifikasi')
@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Detail Spesifikasi</h3>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Spesifikasi</label>
                        <p class="text-sm text-gray-900">{{ $spesifikasi->nama_spesifikasi }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Input</label>
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                            {{ $spesifikasi->isNumeric() ? 'bg-blue-100 text-blue-700' : ($spesifikasi->isSelect() ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700') }}">
                            {{ $spesifikasi->tipe_input }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                        <p class="text-sm text-gray-900">{{ $spesifikasi->satuan ?? '-' }}</p>
                    </div>
                </div>

                @if ($spesifikasi->spesifikasiProduk->count() > 0)
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-base font-semibold text-gray-900 mb-3">Produk yang Menggunakan Spesifikasi Ini</h4>
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($spesifikasi->spesifikasiProduk as $spek)
                                        <tr>
                                            <td class="px-6 py-3 text-sm text-gray-900">{{ $spek->produk->nama_produk }}</td>
                                            <td class="px-6 py-3 text-sm text-gray-500">{{ $spek->nilai }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('vendor.specifications.edit', $spesifikasi->id) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition">
                    <i class="fas fa-edit"></i>
                    Edit
                </a>
                <a href="{{ route('vendor.specifications.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>
            </div>
        </div>
    </div>
@endsection
