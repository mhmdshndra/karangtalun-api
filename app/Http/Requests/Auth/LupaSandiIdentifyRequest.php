<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LupaSandiIdentifyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'identifier'         => 'required_without:nik|string',
            'nik'                => 'required_without:identifier|string|size:16',
            'cf_turnstile_token' => config('services.turnstile.enabled') ? 'required|string' : 'nullable|string',
        ];
    }
}
