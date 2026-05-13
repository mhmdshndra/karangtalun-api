<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LupaSandiResetRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'identifier'   => 'required_without:nik|string',
            'nik'          => 'required_without:identifier|string|size:16',
            'reset_token'  => 'required_without:token|string',
            'token'        => 'required_without:reset_token|string',
            'password'     => 'required|string|min:6|confirmed',
        ];
    }
}
