<?php

namespace App\Http\Requests;

class UpdatePenggunaRequest extends BaseRequest
{
    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? $this->route('pengguna')?->id;

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => 'nullable|min:8|confirmed',
            'usertype' => 'required|in:user,vendor',
            'phone' => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'usertype.required' => 'Tipe user wajib dipilih',
            'usertype.in' => 'Tipe user tidak valid',
        ];
    }
}
