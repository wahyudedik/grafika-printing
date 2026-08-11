<?php

namespace App\Http\Requests;

use App\Models\Vendor\Spesifikasi;

class UpdateSpesifikasiRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'nama_spesifikasi' => 'required|string|max:255',
            'tipe_input' => 'required|in:' . implode(',', Spesifikasi::TIPE_INPUT),
            'satuan' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_spesifikasi.required' => 'Nama spesifikasi harus diisi.',
            'nama_spesifikasi.max' => 'Nama spesifikasi maksimal 255 karakter.',
            'tipe_input.required' => 'Tipe input harus dipilih.',
            'tipe_input.in' => 'Tipe input tidak valid.',
            'satuan.max' => 'Satuan maksimal 50 karakter.',
        ];
    }
}
