<?php

namespace App\Http\Requests;

class UpdateLinktreeRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'custom_url' => 'required|string|max:50|regex:/^[a-z0-9\-]+$/',
            'bio' => 'nullable|string|max:500',
            'template' => 'required|in:minimal,colorful,dark,professional',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'bg_color' => 'nullable|string|max:7',
            'text_color' => 'nullable|string|max:7',
            'button_style' => 'required|in:rounded,square,pill',
            'is_active' => 'boolean',
            'show_qris' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul wajib diisi',
            'custom_url.required' => 'Custom URL wajib diisi',
            'custom_url.regex' => 'Custom URL hanya boleh huruf kecil, angka, dan strip',
            'template.required' => 'Template wajib dipilih',
            'template.in' => 'Template tidak valid',
            'button_style.required' => 'Gaya tombol wajib dipilih',
            'button_style.in' => 'Gaya tombol tidak valid',
        ];
    }
}
