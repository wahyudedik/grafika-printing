<?php

namespace App\Http\Requests;

class UpdateBahanRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'nama_bahan' => 'required|string|max:255',
            'hpp' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:50',
            'stok' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_bahan.required' => 'Nama bahan harus diisi.',
            'nama_bahan.max' => 'Nama bahan maksimal 255 karakter.',
            'hpp.required' => 'HPP harus diisi.',
            'hpp.numeric' => 'HPP harus berupa angka.',
            'hpp.min' => 'HPP minimal 0.',
            'satuan.required' => 'Satuan harus diisi.',
            'satuan.max' => 'Satuan maksimal 50 karakter.',
            'stok.required' => 'Stok harus diisi.',
            'stok.numeric' => 'Stok harus berupa angka.',
            'stok.min' => 'Stok minimal 0.',
        ];
    }
}
