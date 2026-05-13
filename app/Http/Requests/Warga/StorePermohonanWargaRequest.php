<?php

namespace App\Http\Requests\Warga;

use Illuminate\Foundation\Http\FormRequest;

class StorePermohonanWargaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'tujuan_permohonan' => 'required|string|max:2000',
            'informasi_diminta' => 'required|string|max:5000',
            'lampiran' => 'nullable|array|max:5',
            'lampiran.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }
}
