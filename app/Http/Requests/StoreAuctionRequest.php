<?php

namespace App\Http\Requests;

class StoreAuctionRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'budget' => 'required|numeric|min:1000',
            'deadline' => 'required|date|after:today',
            'specifications' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'alamat_pengiriman' => 'required|string',
            'no_telp' => 'required|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]+$/',
            'email_pengiriman' => 'nullable|email',
            'catatan_khusus' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul lelang wajib diisi',
            'description.required' => 'Deskripsi lelang wajib diisi',
            'category.required' => 'Kategori wajib dipilih',
            'quantity.required' => 'Jumlah produksi wajib diisi',
            'quantity.min' => 'Jumlah produksi minimal 1',
            'budget.required' => 'Budget wajib diisi',
            'budget.min' => 'Budget minimal Rp 1.000',
            'deadline.required' => 'Deadline wajib diisi',
            'deadline.after' => 'Deadline harus setelah hari ini',
            'no_telp.required' => 'Nomor telepon wajib diisi',
            'no_telp.regex' => 'Format nomor telepon tidak valid',
            'alamat_pengiriman.required' => 'Alamat pengiriman wajib diisi',
            'file.max' => 'Ukuran file maksimal 10MB',
            'file.mimes' => 'Format file harus pdf, doc, docx, jpg, jpeg, atau png',
        ];
    }
}
