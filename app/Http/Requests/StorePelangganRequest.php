<?php

namespace App\Http\Requests;

class StorePelangganRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama pelanggan wajib diisi',
            'nama.max' => 'Nama pelanggan maksimal 255 karakter',
            'email.email' => 'Format email tidak valid',
            'no_telp.max' => 'Nomor telepon maksimal 20 karakter',
        ];
    }
}
