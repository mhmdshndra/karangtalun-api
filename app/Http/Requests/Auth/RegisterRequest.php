<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nik'                => 'required|string|size:16',
            'nama_lengkap'      => 'required|string|max:255',
            'password'           => 'required|string|min:6|confirmed',
            'telepon'            => 'required|string|max:20',
            'cf_turnstile_token' => config('services.turnstile.enabled') ? 'required|string' : 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nik.required'     => 'NIK wajib diisi.',
            'nik.size'         => 'NIK harus 16 digit.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'telepon.required'  => 'Nomor telepon wajib diisi.',
        ];
    }
}
