<?php

namespace App\Http\Requests;

class StoreTransaksiRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'payment_method' => 'required|string|in:cash,transfer,credit_card,debit_card,e_wallet',
            'estimasi_selesai' => 'required|date|after:today',
            'catatan' => 'nullable|string|max:1000',
            'terbayar' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produks,id',
            'items.*.kuantitas' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'pelanggan_id.required' => 'Pelanggan harus dipilih.',
            'pelanggan_id.exists' => 'Pelanggan tidak valid.',
            'payment_method.required' => 'Metode pembayaran harus dipilih.',
            'payment_method.in' => 'Metode pembayaran tidak valid.',
            'estimasi_selesai.required' => 'Estimasi selesai harus diisi.',
            'estimasi_selesai.after' => 'Estimasi selesai harus setelah hari ini.',
            'items.required' => 'Minimal harus ada 1 item.',
            'items.min' => 'Minimal harus ada 1 item.',
            'items.*.produk_id.required' => 'Produk harus dipilih.',
            'items.*.produk_id.exists' => 'Produk tidak valid.',
            'items.*.kuantitas.required' => 'Kuantitas harus diisi.',
            'items.*.kuantitas.integer' => 'Kuantitas harus berupa angka.',
            'items.*.kuantitas.min' => 'Kuantitas minimal 1.',
            'items.*.harga_satuan.required' => 'Harga satuan harus diisi.',
            'items.*.harga_satuan.numeric' => 'Harga satuan harus berupa angka.',
            'items.*.harga_satuan.min' => 'Harga satuan minimal 0.',
        ];
    }
}
