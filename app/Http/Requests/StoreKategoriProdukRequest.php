<?php

namespace App\Http\Requests;

class StoreKategoriProdukRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'nama_kategori' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kategori.required' => 'Nama kategori harus diisi.',
            'nama_kategori.max' => 'Nama kategori maksimal 255 karakter.',
        ];
    }
}
