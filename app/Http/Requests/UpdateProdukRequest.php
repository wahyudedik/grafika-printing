<?php

namespace App\Http\Requests;

class UpdateProdukRequest extends BaseRequest
{
    public function rules(): array
    {
        $rules = [
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_jual' => 'nullable|numeric|min:0',
            'gambar.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'spesifikasi' => 'nullable|array',
            'spesifikasi.*.id' => 'nullable|exists:spesifikasi_produks,id',
            'spesifikasi.*.spesifikasi_id' => 'required|exists:spesifikasis,id',
            'spesifikasi.*.wajib_diisi' => 'boolean',
            'spesifikasi.*.pilihan' => 'nullable|array',
            'spesifikasi.*.bahan_ids' => 'nullable|array',
            'spesifikasi.*.bahan_ids.*' => 'exists:bahans,id',
            'new_spesifikasi' => 'nullable|array',
            'new_spesifikasi.*.spesifikasi_id' => 'required|exists:spesifikasis,id',
            'new_spesifikasi.*.wajib_diisi' => 'boolean',
            'new_spesifikasi.*.pilihan' => 'nullable|array',
            'new_spesifikasi.*.bahan_ids' => 'nullable|array',
            'new_spesifikasi.*.bahan_ids.*' => 'exists:bahans,id',
            'estimasi' => 'nullable|array',
            'estimasi.*.id' => 'nullable|exists:estimasi_produks,id',
            'estimasi.*.alat_id' => 'required|exists:alats,id',
            'estimasi.*.waktu_persiapan' => 'required|numeric|min:0',
            'estimasi.*.waktu_produksi_per_unit' => 'required|numeric|min:0',
            'new_estimasi' => 'nullable|array',
            'new_estimasi.*.alat_id' => 'required|exists:alats,id',
            'new_estimasi.*.waktu_persiapan' => 'required|numeric|min:0',
            'new_estimasi.*.waktu_produksi_per_unit' => 'required|numeric|min:0',
            'delete_image' => 'nullable|array',
            'deleted_spec_ids' => 'nullable|string',
            'deleted_estimate_ids' => 'nullable|string',
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
            'spesifikasi.*.spesifikasi_id.required' => 'Spesifikasi wajib dipilih',
            'spesifikasi.*.spesifikasi_id.exists' => 'Spesifikasi tidak valid',
            'estimasi.*.alat_id.required' => 'Alat wajib dipilih',
            'estimasi.*.alat_id.exists' => 'Alat tidak valid',
            'estimasi.*.waktu_persiapan.required' => 'Waktu persiapan wajib diisi',
            'estimasi.*.waktu_produksi_per_unit.required' => 'Waktu produksi per unit wajib diisi',
        ];
    }
}
