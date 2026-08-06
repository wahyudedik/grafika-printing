@extends('layouts.vendor')

@section('title', 'Detail Kategori Produk')
@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Detail Kategori Produk</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Nama Kategori</label>
                            <p class="text-gray-900">{{ $kategoriProduk->nama_kategori }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Slug</label>
                            <p class="text-gray-900 font-mono text-sm bg-gray-50 px-3 py-2 rounded-lg">
                                {{ $kategoriProduk->slug }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Dibuat</label>
                            <p class="text-gray-900">{{ $kategoriProduk->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Terakhir Diperbarui</label>
                            <p class="text-gray-900">{{ $kategoriProduk->updated_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Related Products Section --}}
                <div class="mt-8">
                    <h4 class="text-base font-semibold text-gray-900 mb-4">Produk dalam Kategori Ini</h4>

                    @if ($kategoriProduk->produk && $kategoriProduk->produk->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Produk</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($kategoriProduk->produk as $produk)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $produk->nama_produk }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="{{ route('vendor.products.show', $produk->id) }}"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700 transition-colors">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-info-circle text-blue-500 text-lg"></i>
                                <div>
                                    <h4 class="text-sm font-semibold text-blue-800">Tidak ada produk dalam kategori ini</h4>
                                    <p class="text-sm text-blue-700">Kategori ini belum memiliki produk yang terkait.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="border-t border-gray-200 px-6 py-4 flex items-center justify-end gap-3">
                <a href="{{ route('vendor.categories.edit', $kategoriProduk->id) }}"
                    class="px-4 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <a href="{{ route('vendor.categories.index') }}"
                    class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
@endsection
