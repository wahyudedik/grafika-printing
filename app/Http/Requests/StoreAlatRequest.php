<?php

namespace App\Http\Requests;

class StoreAlatRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'nama_alat' => 'required|string|max:255',
            'merek' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'spesifikasi_alat' => 'required|string|max:1000',
            'status' => 'required|in:aktif,maintenance,rusak',
            'tanggal_pembelian' => 'required|date|before_or_equal:today',
            'kapasitas_cetak_per_jam' => 'required|integer|min:1',
            'tersedia' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_alat.required' => 'Nama alat harus diisi.',
            'merek.required' => 'Merek harus diisi.',
            'model.required' => 'Model harus diisi.',
            'spesifikasi_alat.required' => 'Spesifikasi alat harus diisi.',
            'status.required' => 'Status harus dipilih.',
            'status.in' => 'Status tidak valid.',
            'tanggal_pembelian.required' => 'Tanggal pembelian harus diisi.',
            'tanggal_pembelian.date' => 'Format tanggal tidak valid.',
            'tanggal_pembelian.before_or_equal' => 'Tanggal pembelian tidak boleh di masa depan.',
            'kapasitas_cetak_per_jam.required' => 'Kapasitas cetak harus diisi.',
            'kapasitas_cetak_per_jam.integer' => 'Kapasitas cetak harus berupa angka.',
            'kapasitas_cetak_per_jam.min' => 'Kapasitas cetak minimal 1.',
            'tersedia.required' => 'Ketersediaan harus dipilih.',
            'tersedia.boolean' => 'Ketersediaan harus berupa boolean.',
        ];
    }
}
