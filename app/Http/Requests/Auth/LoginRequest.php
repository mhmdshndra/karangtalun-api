<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'identifier'         => 'required|string',
            'password'           => 'required|string',
            'login_type'         => 'sometimes|in:warga,petugas',
            'cf_turnstile_token' => config('services.turnstile.enabled') ? 'required|string' : 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required'         => 'NIK atau ID Petugas wajib diisi.',
            'password.required'           => 'Password wajib diisi.',
            'cf_turnstile_token.required'  => 'Verifikasi keamanan diperlukan.',
        ];
    }
}
