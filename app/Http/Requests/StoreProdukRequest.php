<?php

namespace App\Http\Requests;

class StoreProdukRequest extends BaseRequest
{
    public function rules(): array
    {
        $rules = [
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_jual' => 'nullable|numeric|min:0',
            'gambar.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'spesifikasi' => 'nullable|array',
            'spesifikasi.*.spesifikasi_id' => 'required|exists:spesifikasis,id',
            'spesifikasi.*.wajib_diisi' => 'boolean',
            'spesifikasi.*.pilihan' => 'nullable|array',
            'spesifikasi.*.bahan_ids' => 'nullable|array',
            'spesifikasi.*.bahan_ids.*' => 'exists:bahans,id',
            'estimasi' => 'nullable|array',
            'estimasi.*.alat_id' => 'required|exists:alats,id',
            'estimasi.*.waktu_persiapan' => 'required|numeric|min:0',
            'estimasi.*.waktu_produksi_per_unit' => 'required|numeric|min:0',
        ];

        // Conditional validation for category
        if ($this->input('kategori_id') === 'new') {
            $rules['new_kategori'] = 'required|string|max:255';
        } else {
            $rules['kategori_id'] = 'required|exists:kategori_produks,id';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nama_produk.required' => 'Nama produk wajib diisi',
            'nama_produk.max' => 'Nama produk maksimal 255 karakter',
            'kategori_id.required' => 'Kategori wajib dipilih',
            'kategori_id.exists' => 'Kategori tidak valid',
            'new_kategori.required' => 'Nama kategori baru wajib diisi',
            'harga_jual.numeric' => 'Harga harus berupa angka',
            'gambar.*.image' => 'File harus berupa gambar',
            'gambar.*.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif',
            'gambar.*.max' => 'Ukuran gambar maksimal 2MB',
        ];
    }
}
